<?php

class CardController extends CiiDashboardController
{
	/**
	 * Disable layout rendering for this controller
	 * @var string $layout
	 */
	public $layout = NULL;

	/**
	 * Creates a new Dashboard card with the $id dashboard_card_{ID_HERE}
	 * $id is a randomly generated hash based upon the unique data of the card.json + name, and contains 2 key pieces of information
	 *     1) The class name
	 *     2) The path in.dot.format
	 *
	 * Once created, the unique instance is added to the user's specific dashboard. This is an Ajax specific request
	 * @param  string  $id  The unique id of the card as defined on creation of said card type
	 * @return bool
	 */
	public function actionAdd($id)
	{
		if ($id == NULL)
			throw new CHttpException(400, Yii::t('Dashboard.main', 'An ID must be specified'));

		$config = Configuration::model()->findByAttributes(array('key' => $id));

		if ($config == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'No card type exists with that ID'));

		$name = CJSON::decode($config->value);

		Yii::import($name['path'].'.*');

		$card = new $name['class'];
		$card->create($id, $name['path']);

		$data = CJSON::decode($card->getJSON($name['path']));

		$data['id'] = $card->id;

		// Update the user's card information
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));

		if ($meta == NULL)
		{
			$meta = new UserMetadata();
			$meta->key = 'dashboard';
			$meta->user_id = Yii::app()->user->id;
			$meta->value = array();
		}

		if (!is_array($meta->value))
			$order = CJSON::decode($meta->value);
		else
			$order = $meta->value;

		$order[] = $card->id;
		$meta->value = CJSON::encode($order);
		$meta->save();

		return $card->render();
	}

	/**
	 * Generic method for updating a card
	 * @param  string $id  The id of the card
	 * @return boolean     If the card was updated or not
	 */
	public function actionUpdate($id)
	{
		$card = $this->getCardById($id);

		return $this->submitPost($card);
	}

	/**
	 * Allows cards to have their own definable methods to be publicly called and to retrieve data from
	 * @param  string $id     The id of the card to init
	 * @param  string $id     The method name
	 */
	public function actionCallMethod($id, $method)
	{
		$card = $this->getCardById($id);

		try {
			echo $card->$method($_POST);
		} catch (Exception $e) {
			throw new CHttpException(500,  Yii::t('Dashboard.main', 'Invalid Method'));
		}
		return;
	}

	/**
	 * Provides callback for deleting a given instance of a card
	 * @param  string $id The card UID
	 */
	public function actionDelete($id)
	{
		$card = $this->getCardById($id);

		if(!$card->delete())
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'There was an unknown error processing your request'));
		
		// Delete the user metadata object if the delete was successful
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));

		$uids = json_decode($meta->value, true);
		unset($uids[$id]);
		$meta->value = json_encode($uids);
		return $meta->save();
		
	}

	/**
	 * Provides functionality for resizing any widget based upon it's ID
	 * NOTE, that this widget DOES NOT perform any validation. Validation is done client side.
	 * @param  string $id  The card ID
	 */
	public function actionResize($id)
	{
		if (Cii::get($_POST, 'activeSize'))
		{
			$card = $this->getCardById($id);

			$data = json_decode($card->getJSON(), true);
			$data['activeSize'] = $_POST['activeSize'];

			if ($card->update($data))
				return true;
		}

		throw new CHttpException(400, Yii::t('Dashboard.main',  'Missing POST data'));
	}

	/**
	 * Persists the user's dashboard re-arrangement and acts like a cleanup script 
	 * for cards
	 */
	public function actionRearrange()
	{
		if (Cii::get($_POST, 'cards') != NULL)
		{
			$data =json_encode($_POST['cards']);
			$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));
			$meta->value = $data;
			if ($meta->save())
				return;
		}

		throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing POST data'));
	}

	/**
	 * Retrieves cards for the dashboard
	 * @return bool true
	 */
	public function actionGetCards()
	{
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));
		if ($meta == NULL)
			return true;

		$uids = json_decode($meta->value, true);

		foreach ($uids as $id)
		{
			$name = Yii::app()->db->createCommand("SELECT value FROM `configuration` LEFT JOIN `cards` ON `cards`.`name` = `configuration`.`key` WHERE `cards`.`uid` = :uid")->bindParam(':uid', $id)->queryScalar();
			$name = json_decode($name, true);

			// This seems to happen more than often
			// If for some reason we get a path that is blank, delete the card reference from the user
			if ($name['path'] == '') 
			{
				unset($uids[$id]);
				$meta->value = json_encode($uids);
				$meta->save();
				continue;
			}

			Yii::import($name['path'].'.*');
			$card = new $name['class']($id);
			$card->render();
		}

		return true;
	}

	/**
	 * Retrieves a card by a given ID
	 * @param  string $id
	 * @return [type]     [description]
	 */
	public function actionCard($id=NULL)
	{
		$meta = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'dashboard'));
		if ($meta == NULL)
			return true;

		$uids = json_decode($meta->value, true);

		if (in_array($id, $uids))
			return $this->getCardById($id)->render();

		throw new CHttpException(400,  Yii::t('Dashboard.main', 'You do not have permission to access this card'));
	}

	/**
	 * JSON Response to determine if there is an update for a card
	 * @param  string $id  The card ID
	 * @return JSON
	 */
	public function actionIsUpdateAvailable($id=NULL)
	{
		header('Content-Type: application/json');
		$card = CJSON::decode($this->getBaseCardById($id));

		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'https://raw.github.com/' . str_replace('https://www.github.com/', '', $card['repository']) . '/master/card.json',
		    CURLOPT_FOLLOWLOCATION => true
		));

		$response = CJSON::decode(curl_exec($curl));
		if ($card['version'] != $response['version'])
		{
			echo CJSON::encode(array('update' => true));
			return true;
		}

		echo CJSON::encode(array('update' => false));
		return true;
	}

	/**
	 * Updates the card and the associated data
	 * @param  string $id  The card ID
	 * @return JSON
	 */
	public function actionUpdateCard($id=NULL)
	{
		header('Content-Type: application/json');
		$card = $this->getBaseCardConfig($id);
		$cardData = CJSON::decode($this->getBaseCardById($id));

		// Determine the runtime directory
		$runtimeDirectory = Yii::getPathOfAlias('application.runtime');
		$downloadPath = $runtimeDirectory . DIRECTORY_SEPARATOR . 'cards' . DIRECTORY_SEPARATOR . $card['folderName'] . '.zip';
		if (!is_writable($runtimeDirectory))
			throw new CHttpException(500,  Yii::t('Dashboard.main', 'Runtime directory is not writable'));

		$targetFile = fopen($downloadPath, 'w' );

        // Initiate the CURL request
        $ch = curl_init('https://github.com/' . str_replace('https://www.github.com/', '', $cardData['repository']) . '/archive/master.zip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $targetFile);
        curl_exec($ch);
        
        // Extract the file
        $zip = new ZipArchive;
        $res = $zip->open($downloadPath);

        // If we can open the file
        if ($res === true)
        {
        	// Extract it to the appropriate location
        	$extraction = $zip->extractTo(str_replace('.zip', '', $downloadPath));

        	// If we can extract it
        	if ($extraction)
        	{
        		// Update all the cards
        		$cards = Cards::model()->findAllByAttributes(array('name' => $id));

        		foreach ($cards as $c)
        		{
        			$currentData = CJSON::decode($c['data']);
        			$activeSize = $currentData['activeSize'];
        			$newData = CMap::mergeArray($currentData, $cardData);
        			$newData['activeSize'] = $activeSize;
        			$c->data = CJSON::encode($newData);
        			$c->save();
        		}

        		echo CJSON::encode(array('updated' => true));
        		return true;
        	}
        }

        echo CJSON::encode(array('updated' => false));
        return true;
	}

	/**
	 * Retrieves the baseconfig for a card
	 * @param  string $id  The card ID
	 * @return JSON
	 */
	private function getBaseCardConfig($id)
	{
		if ($id == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'An ID must be specified'));
		
		$json = json_decode(Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :name")->bindParam(':name', $id)->queryScalar(), true);

		if ($json == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'No card with that ID exists'));

		return $json;
	}

	/**
	 * Retrieves the baseconfig for a card
	 * @param  string $id  The card ID
	 * @return JSON
	 */
	private function getBaseCardById($id)
	{
		$json = $this->getBaseCardConfig($id);

		$data = file_get_contents(Yii::getPathOfAlias($json['path']) . DIRECTORY_SEPARATOR . 'card.json');

		return $data;
	}

	/**
	 * Retrieves a card given a particular $id
	 * @return string $id
	 */
	private function getCardById($id=NULL)
	{
		if ($id == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'An ID must be specified'));

		$name = Yii::app()->db->createCommand("SELECT name FROM `cards` WHERE `uid` = :id")->bindParam(':id', $id)->queryScalar();

		if ($name === false)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'No card with that ID exists'));
		
		$json = json_decode(Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :name")->bindParam(':name', $name)->queryScalar(), true);

		Yii::import($json['path'].'.*');

		return new $json['class']($id);
	}

	/**
	 * Generic handler for sacing $model data since the model is completely generic.
	 * @param  CiiSettingsModel $model The model we are working with
	 */
	private function submitPost(&$model)
	{
		if (Cii::get($_POST, get_class($model)) !== NULL)
		{
			$model->populate($_POST);

			if ($model->save())
				return true;
		}

		return false;
	}
}
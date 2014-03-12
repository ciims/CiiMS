<?php

class CardController extends CiiDashboardAddonController implements CiiDashboardAddonInterface
{
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

		$uids = CJSON::decode($meta->value, true);
		unset($uids[$id]);
		$meta->value = CJSON::encode($uids);
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

			$data = CJSON::decode($card->getJSON(), true);
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
			$data = CJSON::encode($_POST['cards']);
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

		$uids = CJSON::decode($meta->value, true);

		foreach ($uids as $id)
		{
			$name = Yii::app()->db->createCommand("SELECT value FROM `configuration` LEFT JOIN `cards` ON `cards`.`name` = `configuration`.`key` WHERE `cards`.`uid` = :uid")->bindParam(':uid', $id)->queryScalar();
			$name = CJSON::decode($name, true);

			// This seems to happen more than often
			// If for some reason we get a path that is blank, delete the card reference from the user
			if ($name['path'] == '') 
			{
				unset($uids[$id]);
				$meta->value = CJSON::encode($uids);
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

		$uids = CJSON::decode($meta->value, true);

		if (in_array($id, $uids))
			return $this->getCardById($id)->render();

		throw new CHttpException(400,  Yii::t('Dashboard.main', 'You do not have permission to access this card'));
	}

	public function actionIsUpdateAvailable($id=NULL) {}
    public function actionUpdateAddon($id=NULL) { }    
    
    /** 
     * Installs a card from CiiMS.org
     * @param string $id the UUID of the card
     * @return JSON
     */
    public function actionInstall($id=NULL)
    {

    }

    /**
	 * Deletes a card and all associated files from the system
	 * @param  string $id 	The id of the card we want to delete
	 * @return boolean    	If the card was deleted or not
	 */
    public function actionUninstall($id=NULL)
    {
    	if ($id == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'You must specify a card to delete'));

		$card = Configuration::model()->findByAttributes(array('key' => $id));

		if ($card == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'There are no dashboard cards with that id'));

		$card->value = CJSON::decode($card->value);
				
		return $card->fullDelete($card->value['folderName']);
    }
    
    public function actionListInstalled() {}

	/**
	 * Retrieves the baseconfig for a card
	 * @param  string $id  The card ID
	 * @return JSON
	 */
	private function getBaseCardConfig($id)
	{
		if ($id == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'An ID must be specified'));
		
		$json = CJSON::decode(Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :name")->bindParam(':name', $id)->queryScalar(), true);

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
		
		$json = CJSON::decode(Yii::app()->db->createCommand("SELECT value FROM `configuration` WHERE `key` = :name")->bindParam(':name', $name)->queryScalar(), true);

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

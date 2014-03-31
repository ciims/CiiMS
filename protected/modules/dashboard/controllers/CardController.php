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
	 * @return JSON
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

	/**
	 * Implements if the item is installed or not
	 * @param  string $id  The Card ID
	 * @return boolean
	 */
	public function isInstalled($id=NULL)
	{
		if ($id == NULL)
			return false;

		$filePath = Yii::getPathOfAlias('application.runtime.cards') . DIRECTORY_SEPARATOR . $id;

        if ((file_exists($filePath) && is_dir($filePath)))
        	return true;

        return false;
	}

	/**
	 * Determines if a card is up to date
	 * @param  string $id The card ID
	 * @return JSON
	 */
	public function actionIsUpdateAvailable($id=NULL)
	{
		// Retrieve the value from cache if it is set
		$response = Yii::app()->cache->get($id . '_updatecheck');

		// Otherwise, retrieve it from the origin server
		if ($response === false)
		{
			// Get the current configuration of the card
			$card = Configuration::model()->findByAttributes(array('key' => $id));
			if ($card == NULL)
				throw new CHttpException(500, Yii::t('Dashboard.main', 'Unable to find card. Fatal error'));

			$card = CJSON::decode($card->value);

			// Get the base ID
			$baseID = str_replace('dashboard_card_', '', $id);

			// Get the details of the card with that baseID from ciims.org
			$this->_returnResponse = true;
	        $details  = $this->actionDetails($baseID);
	        $this->_returnResponse = false;

	        $response = array(
	        	'status' => 200, 
	        	'message' => NULL,
	        	'response' => array(
        			'update' => $card['version'] != $details['response']['version'],
        			'currentVersion' => $card['version'],
        			'latestVersion' => $details['response']['version']
        	));

	        // Cache the value for 4 hours
        	Yii::app()->cache->set($id . '_updatecheck', $response, 14400);
		}
		
		// Return the response
        return parent::renderResponse($response);
	}

	/**
	 * Performs a hard update of the card by downloading the package then overwriting the existing config
	 * @param  string $id The card ID
	 * @return JSON
	 */
    public function actionUpgrade($id=NULL)
    {
    	// Get the read ID
    	$baseID = str_replace('dashboard_card_', '', $id);

    	// Perform a forced install
    	$response = $this->actionInstall($baseID, true);

    	// Then return the actual response as JSON
    	$this->_returnResponse = false;
    	return parent::renderResponse($response);
    }    
    
    /** 
     * Installs a card from CiiMS.org
     * @param string $id the UUID of the card
     * @return JSON
     */
    public function actionInstall($id=NULL, $force = false)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing ID'));

        // Generate the folder path
        $filePath = Yii::getPathOfAlias('application.runtime.cards') . DIRECTORY_SEPARATOR . $id;

        if ((file_exists($filePath) && is_dir($filePath)) && !$force)
        	throw new CHttpException(409, Yii::t('Dashboard.main', 'Card is already installed'));

        // Force the response to be returned instead of outputted
        $this->_returnResponse = true;
        $details  = $this->actionDetails($id);

        if ($details['status'] != 200)
            throw new CHttpException(404, $details['message']);
        
        $this->actionRegister($id);
        
        // Downloads the ZIP package to the "cards" directory
        $this->downloadPackage($id, $details['response']['file'], Yii::getPathOfAlias('application.runtime.cards'));
        $zip = new ZipArchive;

        // If we can open the file
        if ($zip->open($filePath . '.zip') === true)
        {
            // And we were able to extract it
            if ($zip->extractTo($filePath))
            {
            	$zip->close();
                unlink($filePath . '.zip');

                if (!$force)
               		$config = new Configuration;
               	else
               		$config = Configuration::model()->findByAttributes(array('key' => 'dashboard_card_' . $id));
                
                $json = CJSON::decode(file_get_contents($filePath . DIRECTORY_SEPARATOR . 'card.json'));

				$config->key = 'dashboard_card_' . $id;
				$config->value = CJSON::encode(array(
					'name'  =>  Cii::get(Cii::get($json, 'name'), 'displayName'),
					'class' => Cii::get(Cii::get($json, 'name'), 'name'),
					'path' => 'application.runtime.cards.' . $id,
					'folderName' => $id,
					'uuid' => $id,
					'version' => $details['response']['version']
				));

				$config->save();

				Yii::app()->cache->delete('dashboard_cards_available');
	            Yii::app()->cache->delete('cards_in_category');
                
                if ($force)
                	$this->_returnResponse = true;
                else
                	$this->_returnResponse = false;

                Yii::app()->cache->set($id . '_updatecheck', false, 0);

                return parent::renderResponse(array(
                	'status' => 200, 
                	'message' => NULL, 
                	'response' => array(
                		'details' => $details['response'], 
                		'json' => $json
                )));
            }
        }
        die();

        // If anything went wrong, do a full deletion cleanup
        if (!$force)
        {
            $config = new Configuration;
            $config->fullDelete($filePath, 'theme');
        }
        unlink($filePath . '.zip');
    
        // And throw a JSON error for the client to catch and deal with
        throw new CHttpException(500, Yii::t('Dashboard.main', 'Failed to download and install archive'));
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
		
		Yii::app()->cache->delete('dashboard_cards_available');
	    Yii::app()->cache->delete('cards_in_category');
	    
		return $card->fullDelete($card->value['folderName']);
    }
    
    /**
     * Lists all the Cards that have been installed by scanning the directory
     */
    public function actionInstalled()
    {
    	$files = array('status' => 200, 'message' => NULL, 'response' => array());
    	$directories = glob(Yii::getPathOfAlias('application.runtime.cards') . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);
    	foreach($directories as $dir)
    		$files['response'][] = str_replace(Yii::getPathOfAlias('application.runtime.cards') . DIRECTORY_SEPARATOR, '', $dir);
    	return parent::renderResponse($files);
    }

    /**
     * Retrieves all the cards that are NOT currently installed but are associated to this instance
     */
    public function actionUninstalled()
    {
    	$this->_returnResponse = true;
    	$uninstalled = array('status' => 200, 'message' => NULL, 'response' => array());

    	$installed = $this->actionInstalled();
    	$registered = $this->actionRegistered();

    	foreach ($registered['response'] as $card)
    	{
    		if (!in_array($card['uuid'], $installed['response']))
    			$uninstalled['response'][] = $card;
    	}

    	$this->_returnResponse = false;
    	return parent::renderResponse($uninstalled);
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
		
		$config = Configuration::model()->findByAttributes(array('key' => $id));
		$json = CJSON::decode($config->value);

		if ($json == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'No card with that ID exists'));

		return $json;
	}

	/**
	 * Retrieves the baseconfig for a card
	 * @param  string $id  The card ID
	 * @return string
	 */
	private function getBaseCardById($id)
	{
		$json = $this->getBaseCardConfig($id);

		$data = file_get_contents(Yii::getPathOfAlias($json['path']) . DIRECTORY_SEPARATOR . 'card.json');

		return $data;
	}

	/**
	 * Retrieves a card given a particular $id
	 * @param string $id
	 * @return CiiSettingsModel $id
	 */
	private function getCardById($id=NULL)
	{
		if ($id == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'An ID must be specified'));

		$card = Cards::model()->findByAttributes(array('uid' => $id));
		$name = $card->name;

		if ($name === false)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'No card with that ID exists'));
		
		$config = Configuration::model()->findByAttributes(array('key' => $name));
		$json = CJSON::decode($config->value);

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

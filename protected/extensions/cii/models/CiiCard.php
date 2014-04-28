<?php

/**
 * Dashboard cards are nothing more than _extremely_ elaborate forms that by default render a specified viewfile
 * Consequently, this class inherits _a lot_ of it's core behaviors and functionality from CiiSettingsModel
 */
class CiiCard extends CiiSettingsModel
{
	/**
	 * Each card has a unique ID which can be used to reference itself against the database
	 * @var string $id
	 */
	public $id = NULL;


	/**
	 * Overload the construction so we can pull this items attributes
	 * @param string $id 
	 */
	public function __construct($id = NULL)
	{
		$this->id = $id;

		if ($id !== NULL)
		{
			$exists = Yii::app()->db->createCommand("SELECT uid FROM `cards` WHERE uid = :id")->bindParam(':id', $id)->queryScalar();

			if ($exists === false)
				throw new CHttpException(400, 'No card with that ID exists');
		}

		return parent::__construct();
	}

	/**
	 * Retrieves the appropriate sizes for a card
	 * @return string is a default value
	 */
	public function getSize()
	{
		return 'normal';
	}

	/**
	 * Retrieves the viewpath for a given card
	 */
	private function getViewPath()
	{
		$id = $this->id;
		$data = Yii::app()->db->createCommand("SELECT value FROM `configuration` LEFT JOIN `cards` ON `cards`.`name` = `configuration`.`key` WHERE uid = :id")->bindParam(':id', $id)->queryScalar();
		
		if ($data !== false)
			return CJSON::decode($data);
		
		return false;
	}

	/**
	 * Unless otherwise defined, Cards will render the viewfiles specified in their corresponding views/index.php file
	 * @return string path
	 */
	public function getView()
	{
		$data = $this->getViewPath();

		if ($data === false)
			return NULL;

		return $data['path'] . '.views.index';
	}

	/**
	 * Retrieves the settings  for a given card
	 * @return string path
	 */
	public function getSettingsView()
	{
		$data = $this->getViewPath();

		if ($data === false)
			return NULL;

		return $data['path'] . '.views.settings';
	}

	/**
	 * Retrieves the asset path
	 * @return string path
	 */
	public function getAssetPath()
	{
		$data = $this->getViewPath();

		if ($data === false)
			return NULL;

		return $data['path'] . '.assets';
	}

	/**
	 * Retrieves the name of the card from card.json
	 * @return string
	 */
	public function getName()
	{
		$data = CJSON::decode($this->getJSON());

		return $data['name']['displayName'];
	}

	/**
	 * Allows footer text information to be modified by the card (eg disclaimers instead of the card name...)
	 * By default this will return the Card's name
	 * 
	 * @return string $name
	 */
	public function getFooterText()
	{
		return $this->name;
	}

	/**
	 * Overload the __getter so that it checks for data in the following order
	 * 1) Pull From db/cache (Cii::getConfig now does caching of elements for improved performance) for global/user config
	 * 2) Check for __protected__ property, which we consider the default vlaue
	 * 3) parent::__get()
	 *
	 * In order for this to work with __default__ values, the properties in classes that extend from this
	 * MUST be protected. If they are public it will bypass this behavior.
	 * 
	 * @param  mixed $name The variable name we want to retrieve from the calling class
	 * @return mixed
	 */
	public function __get($name)
	{
		if (strpos($name, 'global_') !== false)
			$data = Cii::getConfig(get_class($this).'_'.$name);
		else
			$data = Cii::getUserConfig($this->id.'_'.$name);

		if ($data !== NULL && $data !== "" && !isset($this->attributes[$name]))
			return $data;

		if (property_exists($this, $name))
			return $this->$name;

		return parent::__get($name);
	}

	/**
	 * Creates a new instance of the card
	 * @return  bool
	 */
	public function create($id, $path)
	{
		$rnd_id = crypt(uniqid(mt_rand(),1)); 
		$rnd_id = strip_tags(stripslashes($rnd_id)); 
		$rnd_id = str_replace(".","",$rnd_id); 
		$rnd_id = strrev(str_replace("/","",$rnd_id)); 
		$rnd_id = str_replace("$", '', substr($rnd_id,0,20)); 

		$data = $this->getJSON($path);

		// Set the id of the new object
		$this->id = $rnd_id;

		return Yii::app()->db->createCommand("INSERT INTO `cards` VALUES (NULL, :name, :uid, :data, UTC_TIMESTAMP()); ")
				  ->bindParam(':name', $id)
				  ->bindParam(':uid', $rnd_id)
				  ->bindParam(':data', $data)
				  ->execute();
	}

	/**
	 * Deletes the current card
	 * @return bool   If deletion was successful
	 */
	public function delete()
	{
		return Yii::app()->db->createCommand("DELETE FROM `cards` WHERE uid = :id")->bindParam(':id', $this->id)->execute();
	}

	/**
	 * Retrieves the JSON data for a particular card
	 * @return json encoded array
	 */
	public function getJSON($path = NULL)
	{
		// Retrieve the data from the database
		
		if ($path == NULL)
		{
			$data = Yii::app()->db->createCommand("SELECT data FROM `cards` WHERE uid = :uid")->bindParam('uid', $this->id)->queryScalar();
			if ($data !== NULL && $this->id !== NULL)
				return $data;
		}
		else
		{
			// If the data is empty, provide the options from the file config instead
			$fileHelper = new CFileHelper;
			$files = $fileHelper->findFiles(Yii::getPathOfAlias($path), array('fileTypes'=>array('json'), 'level'=>1));

			return file_get_contents($files[0]);
		}

		return array();
	}

	/**
	 * Allows data to be dynamically updated independently of the core data associated to the model settings
	 * @param  array $data  JSON data to save
	 * @return bool         If save was successful
	 */
	public function update($data)
	{
		$data = CJSON::encode($data);
		return Yii::app()->db->createCommand("UPDATE `cards` SET data = :data WHERE uid = :uid")
						 ->bindParam(':data', $data)
						 ->bindParam(':uid', $this->id)
						 ->execute();
	}

	/**
	 * Provides rendering functionality to display cards
	 */
	public function render()
	{
		$json = CJSON::decode($this->getJSON());

		if ($json['activeSize'] == 'normal')
			$dataSSColspan = 1;
		else
			$dataSSColspan = 2;

		$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($this->AssetPath), true, -1, YII_DEBUG);

		$reflection = new ReflectionClass($this);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
		
		// Main Card View
		echo CHtml::openTag('div', array('id' => $this->id, 'data-attr-js-name' => $this->scriptName, 'data-attr-js' => Yii::app()->baseUrl.$asset. '/js/card.js', 'class' => 'base-card card-' . str_replace('card-', '', $json['activeSize']), 'data-ss-colspan' => $dataSSColspan, 'data-attr-sizes' => implode(',', $json['sizes'])));
	    	
	    	echo CHtml::openTag('div', array('class' => 'body', 'id' => $this->scriptName . '-body')); 
	    		Yii::app()->controller->renderPartial($this->view, array('model' => $this, 'asset' => $asset));
	    	echo CHtml::closeTag('div'); 

	    	echo CHtml::openTag('div', array('class' => 'footer', 'id' => $this->scriptName . '-footer')); 
	    		echo CHtml::tag('span', array('class' => 'pull-left footer-text'), $this->footerText); 

	    		if (count($json['sizes']) > 1)
	    			echo CHtml::tag('span', array('class' => 'icon-resize-full pull-right icon-padding'), NULL);

	    		if ($this->settingsView !== false || count($properties) > 0)
	    			echo CHtml::tag('span', array('class' => 'icon-gear pull-right icon-padding'), NULL);  

	    		echo CHtml::tag('span', array('class' => 'icon-flip icon-info-sign pull-right icon-padding'), NULL);

	    	echo CHtml::closeTag('div');
	    echo CHtml::closeTag('div'); 

	    // Flip pane view
	    // Versions, and functionality to delete card
	    echo CHtml::openTag('div', array('data-attr-id' => $this->id, 'class' => $this->id.'-settings settings', 'style' => 'display:none'));

	    	echo CHtml::openTag('div', array('class' => 'body')); 
	    		if ($this->settingsView !== false)
	    			Yii::app()->controller->renderPartial($this->settingsView, array('model' => $this, 'asset' => $asset));
	    	echo CHtml::closeTag('div'); 

		 	echo CHtml::openTag('div', array('class' => 'footer')); 
				echo CHtml::tag('span', array('class' => 'pull-left footer-text'), 'Card Info'); 
				echo CHtml::tag('span', array('class' => 'icon-trash pull-right icon-padding'), NULL); 
				echo CHtml::tag('span', array('class' => 'icon-reverse-flip icon-info-sign pull-right icon-padding'), NULL);  
		 	echo CHtml::closeTag('div');

		echo CHtml::closeTag('div');

		echo CHtml::openTag('div', array('data-attr-id' => $this->id, 'class' => $this->id.'-modal modal', 'style' => 'display:none'));
			Yii::app()->controller->widget('application.modules.dashboard.components.CiiSettingsForm', 
				array(
					'action' => Yii::app()->createUrl('/dashboard/card/update', array('id' => $this->id)),
					'model' => $this,
					'displayHeader' => false
				)
			);
		echo CHtml::closeTag('div');


	    return;
	}

	/**
	 * Save function for Configuration
	 * Everything should be wrapped inside of a transaction - if there is an error saving any of the items then there was an error saving all of them
	 * and we should abort
	 * @return boolean|null      Whether or not the save succedded or not
	 */
	public function save($runValidation=true)
	{
		if ($this->beforeSave())
		{
			// If we want to run validation AND the validation failed, give up
			if ($runValidation && !$this->validate())
				return false;
			
			$connection  = Yii::app()->db;
			$transaction = $connection->beginTransaction();

			try 
			{
				foreach($this->attributes as $key=>$value)
				{
					$uid = Yii::app()->user->id;

					$entity_type = 1;
					if (strpos($key, 'global_') !== false)
					{
						$PDOKey  = get_class($this).'_'.$key;
						$command = $connection->createCommand('INSERT INTO `configuration` VALUES (:key, :value, :entity_type, UTC_TIMESTAMP(), UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE value = :value2, entity_type = :entity_type, updated = UTC_TIMESTAMP()');
					}
					else
					{
						$PDOKey  = $this->id . '_' . $key;
						$command = $connection->createCommand('INSERT INTO `user_metadata` VALUES (:uid, :key, :value, :entity_type, UTC_TIMESTAMP(), UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE value = :value2, entity_type = :entity_type, updated = UTC_TIMESTAMP()')->bindParam(':uid', $uid);
					}
					
					$command->bindParam('entity_type', $entity_type);
					$command->bindParam(':key', $PDOKey);
					$command->bindParam(':value', $value);
					$command->bindParam(':value2', $value);
					$command->execute();

					Yii::app()->cache->delete('settings_'.$PDOKey);
				}
			} catch (Exception $e) {
				$transaciton->rollBack();
				return false;
			}

			$transaction->commit();
			$this->afterSave();

			// Return the commit response
			return true;
		}
	}
}

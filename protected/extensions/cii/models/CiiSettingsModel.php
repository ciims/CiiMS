<?php

/**
 * CiiSettingsModel provides basic form functionality for {$key} => {$value} table Configuration
 * These Models act as wrappers to contained sections of models/Configuration
 *
 * Some overhead is inherited by this, but it makes the end UI/UX much better.
 */
class CiiSettingsModel extends CFormModel
{
	/**
	 * Deliberate overload of $attributes for internal use by this class
	 * 
	 * $this->attributes _will_ behaved differently than CActiveRecord. After the class loads it WILL be empty
	 * which means you can't check $this->attributes['name'] BEFORE populate is called. If you NEED to know
	 * what the current attributes are, you SHOULD call $this->getAttributes()
	 * 
	 * @var array $attributes   Attributes
	 */
	public $attributes = array();

	/**
	 * Provide the ability to supply a custom form. This should be in alias to be parsed by Yii::getPathOfAlias()
	 * eg application.dashboard.views.settings.form._myFormElement
	 * @var string alias
	 */
	public $form = NULL;

	/**
	 * Alias to a view file to be displayed directly below the header but above all the content. This should be in alias 
	 * to be parsed by Yii::getPathOfAlias()
	 *
	 * This might be useful when you want to inject custom functionality into a form, but just want to provide a regular CiiSettingsModel
	 * as the datasource
	 * 
	 * @var string alias
	 */
	public $preContentView = NULL;

	/**
	 * Constructor for CiiSettingsModel
	 *
	 * When this class is initialized, load up the appropriate properties using __get. This ensures that we can reference
	 * protected properties inside the class while we're using it (see Themes)
	 */
	public function __construct()
	{
		// Protected properties s
		$reflection = new ReflectionClass($this);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

		foreach ($properties as $property)
			$this->{$property->name} = $this->__get($property->name);

		return parent::__construct();
	}

	/**
	 * Overload the __getter so that it checks for data in the following order
	 * 1) Pull From db/cache (Cii::getConfig now does caching of elements for improved performance)
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
		$data = Cii::getConfig($name);

		if ($data !== NULL && $data !== "" && !isset($this->attributes[$name]))
			return $data;

		if (property_exists($this, $name))
			return $this->$name;

		return parent::__get($name);
	}

	/**
	 * Generic setter
	 * Since @protected properties exists, we can't take advantage of __set(), so we have to roll our own
	 * @param string $name 	The name of the varliable
	 * @param mixed  $value The value to set
	 */
	private function set($name, $value)
	{
		if (property_exists($this, $name))
		{
			$this->$name = $value;
			$this->attributes[$name] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Allows settings to be grouped together in logical sections
	 * @return array
	 */
	public function groups()
	{
		return array();
	}

	/**
	 * Provides a generic method for populating data
	 * @param  array  $data    $_POST data
	 * @param  bool   $direct  If the data should be accessed directly rather than by getting it through the class name
	 * @return bool
	 */
	public function populate($data = array(), $direct = false)
	{
		if (!$direct)
			$data = Cii::get($data, get_class($this));

		foreach ($data as $attribute=>$value)
			$this->set($attribute, $value);

		return true;
	}

	/**
	 * Validates passwords by encrypting them for storage
	 * @param  mixed $attribute
	 * @param  mixed $params
	 * @return boolean
	 */
	public function password($attribute, $params)
	{
		$this->attributes[$attribute] = $this->$attribute = Cii::encrypt($this->$attribute);
		return true;
	}

	/**
	 * Allow for beforeSave events
	 * @return bool   If beforeSave passed
	 */
	protected function beforeSave()
	{
	    if($this->hasEventHandler('onBeforeSave'))
	    {
	        $event=new CModelEvent($this);
	        $this->onBeforeSave($event);
	        return $event->isValid;
	    }
	    else
	        return true;
	}

	/**
	 * Allow for afterSave events
	 * @return bool   If afterSave passed
	 */
	protected function afterSave()
	{
	    if($this->hasEventHandler('onAfterSave'))
	        $this->onAfterSave(new CEvent($this));

	    // Store new config values in cache
	    foreach($this->attributes as $key=>$value)
	    	Yii::app()->cache->set('settings_'.$key, $value);

	    return true;
	}

	/**
	 * Gets the validator types as strings to be used for the form parser
	 * @param  string     $attribute  The property name we want to work with
	 * @param  CValidator $validators Often times we may already have the validator, so if this is provided, it will be used instead of fetching
	 *                                the validators for the property
	 * @return array      The validators as clean strings (required, boolean, string, url, number...etc)
	 */
	public function getStringValidator($attribute=NULL, $validators=NULL)
	{
		if ($attribute == NULL && $validators == NULL)
			return array();

		$v = array();
		if ($validators == NULL && $attribute !== NULL)
			$validators = $this->getValidators($attribute);

		$validators = array_values($validators);

		foreach ($validators as $validator)
		{
			$ve = strtolower(str_replace('Validator', '', substr(get_class($validator), 1, strlen(get_class($validator)))));
			if ($ve == 'inline')
				$v[] = $validator->method;
			else
				$v[] = $ve;
		}

		return $v;
	}
	
	/**
	 * Allows for PreValidation functionality
	 */
	public function beforeValidate()
	{
		return true;
	}

	/**
	 * Allows for PostValidation functionality
	 */
	public function afterValidate()
	{
		return true;
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
			
			if ($this->beforeValidate())
			{
				if ($runValidation && !$this->validate())
					return false;
			}
			else
				return false;

			$this->afterValidate();
			
			$connection = Yii::app()->db;
			$transaction = $connection->beginTransaction();

			try {
				foreach($this->attributes as $key=>$value)
				{
					$command = $connection->createCommand('INSERT INTO `configuration` VALUES (:key, :value, UTC_TIMESTAMP(), UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE value = :value2, updated = UTC_TIMESTAMP()');
					$command->bindParam(':key', $key);
					$command->bindParam(':value', $value);
					$command->bindParam(':value2', $value);
					$command->execute();
					Yii::app()->cache->delete('settings_'.$key);
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
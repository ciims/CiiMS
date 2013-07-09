<?php

/**
 * CiiSettingsModel provides basic form functionality for {$key} => {$value} table Configuration
 * These Models act as wrappers to contained sections of models/Configuration
 *
 * Some overhead is inherited by this, but it makes the end UI/UX much better
 */
class CiiSettingsModel extends CFormModel
{
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

		if ($data !== NULL)
			return $data;

		if (property_exists($this, $name))
			return $this->$name;

		return parent::__get($name);
	}

	/**
	 * Save function for Configuration
	 * @return bool      Whether or not the save succedded or not
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		foreach($this->attributes as $field)
		{
			Cii::debug($field);
		}
	}
}
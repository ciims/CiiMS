<?php

/**
 * CiiSettingsModel provides basic form functionality for {$key} => {$value} table Configuration
 * These Models act as wrappers to contained sections of models/Configuration
 *
 * Some overhead is inherited by this, but it makes the end UI/UX much better
 */
class CiiSettingsModel extends CFormModel
{
	public $attributes = array();

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

		if ($data !== NULL && $data !== "")
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
	 * Provides a generic method for populating data
	 * @param  array  $data $_POST data
	 * @return bool
	 */
	public function populate($data = array())
	{
		$data = Cii::get($data, get_class($this));

		foreach ($data as $attribute=>$value)
			$this->set($attribute, $value);
		return true;
	}


	/**
	 * Save function for Configuration
	 * @return bool      Whether or not the save succedded or not
	 */
	public function save($runValidation=true)
	{
		
		if (!$runValidation || $this->validate())
		{
			$connection = Yii::app()->db;
			$transaction = $connection->beginTransaction();

			try {
				foreach($this->attributes as $field=>$value)
				{
					$command = $connection->createCommand('INSERT INTO `configuration` VALUES (:field, :value, NOW(), NOW()) ON DUPLICATE KEY UPDATE value = :value2, updated = NOW()');
					$command->bindParam(':field', $field);
					$command->bindParam(':value', $value);
					$command->bindParam(':value2', $value);
					$ret = $command->execute();
				}
			} catch (Exception $e) {
				$transaciton->rollBack();
				return false;
			}

			return $transaction->commit();
		}
		return false;
	}
}
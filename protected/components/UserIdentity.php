<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
			
	public function authenticate($force=false)
	{
		$record=Users::model()->findByAttributes(array('email'=>$this->username));
		
		if (!function_exists('password_hash'))
			Yii::import('ext.bcrypt.*');

		// Check the database for the cost, use 13 as a default value
		$cost = Cii::get(Configuration::model()->findByAttributes(array('key'=>'bcrypt_cost'), 'value'), 13);
		if ($cost <= 12)
			$cost = 13;

		// We still want to secure our password using this algorithm
		$hash = Users::model()->encryptHash($this->username, $this->password, Yii::app()->params['encryptionKey']);

		if($record===null)
		{
			// If we can't find the user's email, return identity failure
		    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else if ($record->status == 3 || $record->status == 0)
		{
			// If the user is banned or unactivated, return identity failure
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else if(!password_verify($hash, $record->password))
		{
			// If the hash isn't in bcrypt format, see if it is in the old format and update it if necessary
			if($record->password == $hash)
				$this->updateRecord($record);
			else
		    	$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else
		{
			// If the old password format is being used, or the password needs to be rehashed to use a new cost format
			if ($record->password == $hash || password_needs_rehash($hash, PASSWORD_BCRYPT, array('cost' => $cost))
				$this->updateRecord($record);
		}
		
		if ($force && $record != NULL)
		{
			$this->_id = $record->id;			
			$this->setState('email', $record->email);
			$this->setState('displayName', $record->displayName);
			$this->setState('status', $record->status);
		  	$this->setState('role', $record->user_role);
		    $this->errorCode=self::ERROR_NONE;
		}
		
		return !$this->errorCode;
    }

	
	private function updateRecord(&$record, &$force=false)
	{
		$record->password = password_hash($hash, PASSWORD_BCRYPT, array('cost' => $cost));
		$record->save();

		// Allow the user to proceed
		$force = true;
	}

	/**
	 * Gets the id for Yii::app()->user->id
	 * @return int 	the user id
	 */
	public function getId()
	{
    		return $this->_id;
	}
}

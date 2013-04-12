<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	const ERROR_PASSWORD_LOCKOUT=3;

	private $_id;
	
	private $force = false;

	private $hash = NULL;

	private $cost = NULL;

	public function authenticate($force=false)
	{
		$record = Users::model()->findByAttributes(array('email'=>$this->username));
		if (!function_exists('password_hash'))
			require_once(dirname(__FILE__) . '/../extensions/bcrypt/bcrypt.php');

		// Check the database for the cost, use 13 as a default value
		$this->cost = Cii::get(Configuration::model()->findByAttributes(array('key'=>'bcrypt_cost'), 'value'), 13);
		if ($this->cost <= 12)
			$this->cost = 13;

		// We still want to secure our password using this algorithm
		$this->hash = Users::model()->encryptHash($this->username, $this->password, Yii::app()->params['encryptionKey']);

		// We need to pull metadata about the user 
		if ($record !== null)
		{
			// Pull the lockout attempt count
			$meta 	= UserMetadata::model()->findbyAttributes(array('user_id' => $record->id, 'key' => 'passwordAttempts'));
			$meta2 	= UserMetadata::model()->findbyAttributes(array('user_id' => $record->id, 'key' => 'passwordLockoutReset'));

			// Create a new temporary object, since we may want to save it later
			if ($meta === null)
			{
				$meta = new UserMetadata;
				$meta->user_id = $record->id;
				$meta->key = 'passwordAttempts';
				$meta->value = 0;
			}

			// Create a new temporary object, since we may want to save it later
			if ($meta2 === null)
			{
				$meta2 = new UserMetadata;
				$meta2->user_id = $record->id;
				$meta2->key = 'passwordLockoutReset';
				$meta2->value = 0;
			}
		}

		if($record===null)
		{
			// If we can't find the user's email, return identity failure
		    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else if ($record->status == 3 || $record->status == 0)
		{
			// If the user is banned or unactivated, locked out, or exceeded their max login attempts
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else if(!password_verify($this->hash, $record->password))
		{
			// If the hash isn't in bcrypt format, see if it is in the old format and update it if necessary
			if($record->password == $this->hash)
				$this->updateRecord($record);
			else
		    	$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else
		{
			// If the old password format is being used, or the password needs to be rehashed to use a new cost format
			if ($record->password == $this->hash || password_needs_rehash($this->hash, PASSWORD_BCRYPT, array('cost' => $this->cost)))
				$this->updateRecord($record);
		}
		
		// If the user has 5 or more password attempts
		if ($meta->value >= 5)
		{
			// Check to see if there is a lockout time. If it is >= the current time, then we should block the login
			if ($meta2->value >= time())
				$this->force = false;
		}

		if ($this->force && $record != NULL)
		{
			// Delete some metadata
			if (!$meta->isNewRecord)
				$meta->delete();
			if (!$meta2->isNewRecord)
				$meta2->delete();

			$this->_id = $record->id;			
			$this->setState('email', $record->email);
			$this->setState('displayName', $record->displayName);
			$this->setState('status', $record->status);
		  	$this->setState('role', $record->user_role);
		    $this->errorCode=self::ERROR_NONE;
		}
		else
		{
			// Cap at 5 to prevent potential int overflow attacks on the db
			$meta->value = min($meta->value + 1, 5);
			$meta->save();
			if ($meta->value >= 5)
			{
				// Lock out for 5 minutes
				$meta2->value = time() + 300;
				$meta2->save();
			}
		}
		
		return !$this->errorCode;
    }

	
	private function updateRecord(&$record)
	{
		$record->password = password_hash($this->hash, PASSWORD_BCRYPT, array('cost' => $this->cost));
		$record->save();

		// Allow the user to proceed
		$this->force = true;
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

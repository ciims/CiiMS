<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Constant variable for defining lockout status
	 * @var const ERROR_PASSWORD_LOCKOUT
	 */
	const ERROR_PASSWORD_LOCKOUT = 3;

	/**
	 * The user id
	 * @var int $_id
	 */
	private $_id;
	
	/**
	 * Whether or not to allow login or not
	 * Possibly should be renamed to doLogin
	 * @var boolean $force
	 */
	private $force = false;

	/**
	 * Password Hash of user
	 * @var string $hash
	 */
	private $hash = NULL;

	/**
	 * bcrypt hashing cost
	 * @var int
	 */
	private $cost = 13;

	/**
	 * The Application to use for API generation
	 * @var string
	 */
	public $app_name = NULL;
	
	/**
	 * Authenticates the user into the system
	 * @param  boolean $force 				Whether or not to bypass the login process (passwordless logins from HybridAuth)
	 * @return UserIdentity::$errorCode 	The error code associated to the login process
	 */
	public function authenticate($force=false)
	{
		$this->force = $force;
		$record 	= Users::model()->findByAttributes(array('email'=>$this->username));
		$this->cost = Cii::getBcryptCost($this->cost);
		$meta 		= $meta2 = NULL;	// Define this up here

		// Load the bcrypt hashing tools if the user is running a version of PHP < 5.5.x
		if (!function_exists('password_hash'))
			require_once YiiBase::getPathOfAlias('ext.bcrypt.bcrypt').'.php';

		// We still want to secure our password using this algorithm
		$this->hash = Users::model()->encryptHash($this->username, $this->password, Yii::app()->params['encryptionKey']);

		if ($record !== NULL)
		{
			// Pull the lockout attempt count
			$meta 	= UserMetadata::model()->findbyAttributes(array('user_id' => $record->id, 'key' => 'passwordAttempts'));
			$meta2 	= UserMetadata::model()->findbyAttributes(array('user_id' => $record->id, 'key' => 'passwordLockoutReset'));

			// Create a new temporary object, since we may want to save it later
			if ($meta === NULL)
			{
				$meta 			= new UserMetadata;
				$meta->user_id 	= $record->id;
				$meta->key 		= 'passwordAttempts';
				$meta->value 	= 0;
			}

			// Create a new temporary object, since we may want to save it later
			if ($meta2 === NULL)
			{
				$meta2 			= new UserMetadata;
				$meta2->user_id = $record->id;
				$meta2->key 	= 'passwordLockoutReset';
				$meta2->value 	= 0;
			}
		}

		// Begin login tests
		if($record === NULL)
		{
			// If we can't find the user's email, return identity failure
		    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;

		    // Return early if the record is NULL. Bad things seem to happen with the $meta if we don't =(
		    return !$this->errorCode;
		}
		else if ($this->password == password_hash($record->email, PASSWORD_BCRYPT, array('cost' => 13)) && !$this->force)
		{
			// This is a socially authenticated user who hasn't set a password. Do not allow them to login using the wildcard password
			// If we can't find the user's email, return identity failure
		    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;

		    // Return early if the record is NULL. Bad things seem to happen with the $meta if we don't =(
		    return !$this->errorCode;
		}
		else if ($record->status == Users::BANNED || $record->status == Users::INACTIVE || $record->status == Users::PENDING_INVITATION)
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
			// Delete some metadata if necessary
			if (!$meta->isNewRecord)
				$meta->delete();

			if (!$meta2->isNewRecord)
				$meta2->delete();

			$this->_id 					  = $record->id;			
			$this->setState('email', 		$record->email);
			$this->setState('displayName', 	$record->displayName);
			$this->setState('status', 		$record->status);
		  	$this->setState('role', 		$record->user_role);

		  	// Create an API key
		  	$apiKey = UserMetadata::model()->findByAttributes(array('user_id' => $this->_id, 'key' => 'api_key' . $this->app_name));
		  	if ($apiKey != NULL)
		  		$apiKey->delete();

		  	$apiKey = new UserMetadata;
		  	$apiKey->user_id = $this->_id;
		  	$apiKey->key     = 'api_key' . $this->app_name;
		  	$apiKey->value   = hash_hmac('ripemd160', $record->email . $record->id . time(), $this->app_name);

		  	$apiKey->save();

		  	$this->setState('api_key', $apiKey->value);

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

	/**
	 * Creates a bcrypt password hash
	 * @param  User $record   The UserModel associated to the particular user
	 */
	private function updateRecord(&$record)
	{
		$record->password = password_hash($this->hash, PASSWORD_BCRYPT, array('cost' => $this->cost));
		$record->save();

		// Allow the user to proceed
		$this->force = true;
	}

	/**
	 * Public access method to generate a new password
	 * @param  string $email    The email we want to change
	 * @param  string $password The password we want to set to
	 * @return string           bcrypt hash
	 */
	public function updatePassword($email, $password)
	{
		$hash = Users::model()->encryptHash($email, $password, Yii::app()->params['encryptionKey']);
		return password_hash($hash, PASSWORD_BCRYPT, array('cost' => $this->cost));
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

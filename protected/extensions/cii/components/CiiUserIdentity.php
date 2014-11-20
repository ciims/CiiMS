<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 * @method string updatePassword(string $email, string $password)
 */
class CiiUserIdentity extends CUserIdentity
{
	/**
	 * Constant variable for defining lockout status
	 * @var const ERROR_PASSWORD_LOCKOUT
	 */
	const ERROR_PASSWORD_LOCKOUT = 3;

	/**
	 * The Application to use for API generation
	 * @var string
	 */
	public $app_name = NULL;

	/**
	 * The user id
	 * @var int $_id
	 */
	protected $_id;

	/**
	 * Whether or not to allow login or not
	 * Possibly should be renamed to doLogin
	 * @var boolean $force
	 */
	protected $force = false;

    /**
     * The Users ActiveRecord record response
     * @var Users $_user
     */
    protected $_user;

    /**
     * The bcrypt password hash cost
     * @var int $_cost
     */
    private $_cost;

    /**
     * The number of password attempts
     * @var int $_attempts
     */
    private $_attempts;

    /**
	 * Gets the id for Yii::app()->user->id
	 * @return int 	the user id
	 */
	public function getId()
	{
    	return $this->_id;
	}

    /**
     * Retrieves the user's model, and presets an error code if one does not exists
     * @return Users $this->_user
     */
    protected function getUser()
    {
		$this->_user = Users::model()->findByAttributes(array('email'=>$this->username));

        if ($this->_user == NULL)
            $this->errorCode = YII_DEBUG ? self::ERROR_USERNAME_INVALID : self::ERROR_UNKNOWN_IDENTITY;

        return $this->_user;
    }

    /**
     * Handles setting up all the data necessary for the workflow
     * @return void
     */
    private function setup($force = false)
    {
        // Set a default error code to indicate if anything changes
        $this->errorCode = NULL;

        // Indicate if this is a forced procedure
        $this->force = $force;

        // Load the current user
        $this->getUser();

        // Get the current bcrypt cost
		$this->_cost = Cii::getBcryptCost();

        // Preload the number of password attempts. As of 1.10.0
        $this->getPasswordAttempts();

        return;
    }

    /**
     * Retrieves the number of password login attempts so that we can automatically lock users out of they attempt a brute force attack
     * @return UserMetadata
     */
    protected function getPasswordAttempts()
    {
        if ($this->_user == NULL)
            return false;

        $meta = UserMetadata::model()->findbyAttributes(array('user_id' => $this->_user->id, 'key' => 'passwordAttempts'));

        if ($meta === NULL)
        {
            $meta 			= new UserMetadata;
            $meta->attributes = array(
                'user_id' => $this->_user->id,
                'key'     => 'passwordAttempts',
                'value'   => 0
            );
            $meta->save();
        }

        $this->_attempts = $meta;
        return $meta;
    }

	/**
	 * Authenticates the user into the system
	 * @param  boolean $force 				Whether or not to bypass the login process (passwordless logins from HybridAuth)
	 * @return UserIdentity::$errorCode 	The error code associated to the login process
	 */
	public function authenticate($force=false)
	{
        // Setup everything first
		$this->setup($force);

        // If something bad happened during the setup process, immediately bail with the error code
        if ($this->errorCode != NULL)
            return !$this->errorCode;

        // If the user is banned, inactive,
        if ($this->_user->status == Users::BANNED || $this->_user->status == Users::INACTIVE || $this->_user->status == Users::PENDING_INVITATION)
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		else if(!$this->password_verify_with_rehash($this->password, $this->_user->password))
			$this->errorCode= YII_DEBUG ? self::ERROR_PASSWORD_INVALID : self::ERROR_UNKNOWN_IDENTITY;

        // If this user has 5 or more failed password attempts
        if ($this->_attempts->value >= 5)
        {
            // And if they the updated time is still more than 15 minutes ago
            if ((strtotime($this->_attempts->updated) + strtotime("+15 minutes")) >= time())
            {
                // Resave the attempts model so that the updated time is reset
                $this->_attempts->save();

                // Throw an unknown identity error
                $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
            }
            else
            {
                // Automatically re-adjust the lockout time if this has passed.
                $this->_attempts->value = 0;
                $this->_attempts->save();
            }
        }

        // At this point, we should should know if the validation has succeeded or not. If the errorCode has been altered, immediately bail
        if ($this->errorCode != NULL)
            return !$this->errorCode;
        else
            $this->setIdentity();

        return !$this->errorCode;
    }

    /**
     * Sets the identity attributes
     * @return void
     */
    protected function setIdentity()
    {
        $this->_id 					  = $this->_user->id;
        $this->setState('email', 		$this->_user->email);
        $this->setState('username', 	$this->_user->username);

        // TODO: Replace all instances of displayName with username
        $this->setState('displayName',  $this->_user->username);
        $this->setState('status', 		$this->_user->status);
        $this->setState('role', 		$this->_user->user_role);
        $this->setstate('apiKey',       $this->generateAPIKey());

        $this->errorCode = self::ERROR_NONE;

        return;
    }

    /**
     * Generates a new API key for this application
     * @return string
     */
    protected function generateApiKey()
    {
        // Load the hashing factory
        $factory = new CryptLib\Random\Factory;

        // Load the current API key if one exists
        $apiKey = UserMetadata::model()->findByAttributes(array('user_id' => $this->_id, 'key' => 'api_key' . $this->app_name));

	if ($apiKey == NULL)
		$apiKey = new UserMetadata;

        $apiKey->user_id = $this->_id;
        $apiKey->key     = 'api_key' . $this->app_name;
        $apiKey->value   = $factory->getLowStrengthGenerator()->generateString(16);

        // Then save the API key
        $apiKey->save();

        return $apiKey->value;
    }

    /**
     * https://gist.github.com/nikic/3707231
     * Checks if a password is valid against our database
     * @param string $password_hash     The password to check against
     * @return boolean
     */
    protected function password_verify_with_rehash($password_hash, $bcryt_hash)
    {
        if (!password_verify($password_hash, $bcryt_hash))
           return false;

        if (password_needs_rehash($bcryt_hash, PASSWORD_BCRYPT, array('cost' => $this->_cost)))
        {
            // Update the hash in the db
            $this->_user->password = $this->password;
            $this->_user->save();

            // Return verification that the rehash worked
            return password_verify(Users::model()->encryptHash($this->username, $this->password, Yii::app()->params['encryptionKey']), $this->_user->password);
        }

        // Otherwise return true
        return true;
    }
}

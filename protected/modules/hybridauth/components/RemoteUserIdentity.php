<?php

class RemoteUserIdentity extends CBaseUserIdentity {

	public $id;
	public $userData;
	public $loginProviderIdentity;
	private $_adapter;

	/**
	 * Authenticates a user.
	 * @return boolean|null whether authentication succeeds.
	 */
	public function authenticate($provider=NULL)
	{
		Yii::import('application.modules.hybridauth.Hybrid.Hybrid_Auth');
		
		if (strtolower($provider) == 'openid')
		{
			if (!isset($_GET['openid-identity']))
			{
				throw new CException(Yii::t('Hybridauth.main', "You chose OpenID but didn't provide an OpenID identifier"));
			} 
			else
			{
				$params = array("openid_identifier" => $_GET['openid-identity']);
			}
		}
		else
		{
			$params = array();
		}
	
		$hybridauth = new Hybrid_Auth($this->_getConfig());
		
		$adapter = $hybridauth->authenticate($provider,$params);
		
		if ($adapter->isUserConnected())
		{
			$this->userData = (array)$adapter->getUserProfile();
			$this->userData['id'] = $this->userData['identifier'];
			
			// Map an email address if we aren't given one
			if ($this->userData['email'] == NULL)
				$this->userData['email'] = $this->userData['id'] . '@' . $provider . '.com';
			
			// Attempt to find the user by the email address
			$user = Users::model()->findByAttributes(array('email'=>$this->userData['email']));
			$meta = false;
			
			// If we didn't find a match via email, check to see if they have logged in before by their provider id
			if ($user === NULL)
			{
				$meta = true;
				$user = UserMetadata::model()->findByAttributes(array('key'=>$provider.'Provider', 'value'=>$this->userData['id']));
			}
			
			// Set a default error code
			$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
			
			// Check to see if the email binding worked
			if ($user === NULL)
			{
				// If the user doesn't exist
				$this->errorCode = self::ERROR_USERNAME_INVALID;
			}
			else
			{
				// If the user does exist
				$this->id = $meta ? $user->user_id : $user->id;
				$this->errorCode = self::ERROR_NONE;
			}
			
			return !$this->errorCode;
			
		}

	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	* Get config
	* @return string rewritten configuration
	*/
	private function _getConfig()
	{
		return Yii::app()->controller->module->getConfig();
	}

	/**
	 * Returns the Adapter provided by Hybrid_Auth.  See http://hybridauth.sourceforge.net
	 * for details on how to use this
	 * @return Hybrid_Provider_Adapter adapter
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}
}

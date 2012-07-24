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
		
		if($record===null)
		    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		else if($record->password!==Users::model()->encryptHash($this->username, $this->password, Yii::app()->params['encryptionKey']))
		    $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		else if ($record->status == 3 || $record->status == 0)
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		else
		{
			$force = true;
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

		
	public function getId()
	{
    		return $this->_id;
	}
}

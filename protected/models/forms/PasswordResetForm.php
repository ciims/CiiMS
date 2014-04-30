<?php

class PasswordResetForm extends CFormModel
{
    /**
     * The user's new password
     * @var string $password
     */
    public $password;

    /**
     * The user's new password repeated
     * @var string $password_repeat
     */
    public $password_repeat;

    /**
     *
     * @var string $reset_key
     */
    public $reset_key;

    /**
     * The user model
     * @var Users $_user
     */
    private $_user;

    /**
     * The hash model
     * @var UserMetadata $_hash
     */
    private $_hash;

    /**
     * The expires model
     * @var UserMetadata $_expires
     */
    private $_expires;

    /**
     * Validation rules
     * @return array
     */
    public function rules()
    {
        return array(
            array('password, password_repeat, reset_key', 'required'),
            array('password', 'compare'),
            array('password', 'length', 'min'=>8),
            array('reset_key', 'validateResetKey')
        );
    }

    /**
     * Attribute labels
     * @return array
     */
    public function attributeLabels()
    {
        return array(
            'password' => Yii::t('ciims.models.PasswordResetForm', 'Your New Password'),
            'password_repeat' => Yii::t('ciims.models.PasswordResetForm', 'Your New Password (again)'),
            'reset_key' => Yii::t('ciims.models.PasswordResetForm', 'Your Password Reset Token'),
        );
    }

    /**
     * Validates that the reset key is valid and that it belongs to a user
     * @param array $attributes
     * @param array $params
     * @return boolean
     */
    public function validateResetKey($attributes=array(), $params=array())
    {
        // Validate that we have a hash for this user
        $this->_hash = UserMetadata::model()->findByAttributes(array('key'=>'passwordResetCode', 'value'=>$this->reset_key));
        if ($this->_hash == NULL)
        {
            $this->addError('reset_key', Yii::t('ciims.models.PasswordResetForm', 'The activation key you provided is invalid'));
            return false;
        }

        // Validate that the expiration time has not passed
        $this->_expires = UserMetadata::model()->findByAttributes(array('user_id'=>$this->_hash->user_id, 'key'=>'passwordResetExpires'));
        if ($this->_expires == NULL || time() > $this->_expires->value)
        {
            $this->addError('reset_key', Yii::t('ciims.models.PasswordResetForm', 'The activation key you provided is invalid'));
            return false;
        }

        // Retrieve the user
        $this->_user = Users::model()->findByPk($this->_hash->user_id);
        if ($this->_user == NULL)
        {
            $this->addError('reset_key', Yii::t('ciims.models.PasswordResetForm', 'The activation key you provided is invalid'));
            return false;
        }

        return true;
    }

    /**
     * Resets the user's password
     * @return boolean
     */
    public function save()
    {
        if (!$this->validate())
            return false;

        // Update the user's password
        $this->_user->password = $this->password;

        if ($this->_user->save())
        {
            // Delete the hash and expires to prevent reuse attemps
            $this->_hash->delete();
            $this->_expires->delete();

            return true;
        }

        return false;
    }
}

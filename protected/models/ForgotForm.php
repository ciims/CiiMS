<?php

class ForgotForm extends CFormModel
{
    /**
     * @var string $email     The user's email address
     */
    public $email;

    /**
     * @var Users $_user     The user's model
     */
    private $_user = NULL;

    /**
     * Validation rules
     * @return array
     */
    public function rules()
    {
        return array(
            array('email', 'required'),
            array('email', 'email'),
            array('email', 'exists')
        );
    }

    public function attributeLabels()
    {
        return array(
            'email' => Yii::t('ciims.models.ForgotForm', 'Email Address')
        );
    }

    /**
     * Determines if we have a user in our database with that email address
     * @param array $attributes
     * @param array $params
     * @return boolean
     */
    public function exists($attributes, $params)
    {
        $this->_user = Users::model()->findByAttributes(array('email' => $this->email));

        if ($this->_user == NULL)
        {
            $this->addError('email', Yii::t('ciims.models.ForgotForm', 'The email address you entered is either invalid, or does not belong to a user in our system.'));
            return false;
        }

        return true;
    }

    /**
     * Initiates the password reset process on behalf of the user
     * Generates a unique hash and an expiration time that the hash is valid up until (defaults to 15 minutes)
     * This key will internally expire (but not be expunged) after that time
     */
    public function initPasswordResetProcess()
    {
        if (!$this->validate())
            return false;

        // Generate a secure token that isn't vulnerable to a timing attack
        $factory = new CryptLib\Random\Factory;
        $hash = $factory->getHighStrengthGenerator()->generateString(16);

        $expires = strtotime("+15 minutes");

        $meta = UserMetadata::model()->findByAttributes(array('user_id'=>$this->_user->id, 'key'=>'passwordResetCode'));
        if ($meta === NULL)
            $meta = new UserMetadata;

        $meta->user_id = $this->_user->id;
        $meta->key = 'passwordResetCode';
        $meta->value = $hash;
        $meta->save();

        $meta = UserMetadata::model()->findByAttributes(array('user_id'=>$this->_user->id, 'key'=>'passwordResetExpires'));
        if ($meta === NULL)
            $meta = new UserMetadata;

        $meta->user_id = $this->_user->id;
        $meta->key = 'passwordResetExpires';
        $meta->value = $expires;
        $meta->save();

        Yii::app()->controller->sendEmail($this->_user, Yii::t('ciims.email', 'Your Password Reset Information'), '//email/forgot', array('user' => $this->_user, 'hash' => $hash), true, true);

        // Set success flash
        Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Site', 'An email has been sent to {{email}} with further instructions on how to reset your password', array(
            '{{email}}' => $this->email
        )));

        return true;
    }
}

<?php

class RegisterForm extends CFormModel
{
	/**
	 * The submitted email address
	 * @var string|email
	 */
	public $email;

	/**
	 * The submitted password
	 * @var string
	 */
	public $password;

	/**
	 * The password verification
	 * @var string
	 */
	public $password_repeat;

	/**
	 * The submitted first name if it was supplied
	 * @var string
	 */
	public $firstName;

	/**
	 * The submitted last name if it was supplied
	 * @var string
	 */
	public $lastName;

	/**
	 * The display name as we will show it on the site
	 * @var string
	 */
	public $displayName;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email, password, password_repeat, displayName', 'required'),
			array('password', 'compare'),
			array('password', 'length', 'min'=>8),
			array('email', 'email'),
            array('email', 'isEmailUnique')
		);
	}

    /**
     * Determines if an email is already taken or not
     * @param array $attributes
     * @param array $params
     * @return boolean
     */
    public function isEmailUnique($attributes, $params)
    {
        $user = Users::model()->findByAttributes(array('email' => $this->email));

        if ($user != NULL)
        {
            $this->addError('email', Yii::t('ciims.models.RegisterForm', 'That email address is already in use'));
            return false;
        }

        return true;
    }

    /**
     * Model attribute labels
     * @return array
     */
	public function attributeLabels()
	{
		return array(
			'email'           => Yii::t('ciims.models.RegisterForm', 'Your Email Address'),
			'password'        => Yii::t('ciims.models.RegisterForm', 'Your Password'),
			'password_repeat' => Yii::t('ciims.models.RegisterForm', 'Your Password (again)'),
			'displayName'     => Yii::t('ciims.models.RegisterForm', 'Your Display Name on the Site')
		);
	}

    /**
     * Creates a new user, and sends the appropriate messaging out
     * @return boolean
     */
    public function save()
    {
        if (!$this->validate())
            return false;

        $user = new Users;

        // Set the model attributes
        $user->attributes = array(
            'email'       => $this->email,
            'password'    => $this->password,
            'firstName'   => NULL,
            'lastName'    => NULL,
            'displayName' => $this->displayName,
            'user_role'   => 1,
            'status'      => Users::PENDING_INVITATION
        );

        if($user->save())
        {
            $hash = mb_strimwidth(hash("sha256", md5(time() . md5(hash("sha512", time())))), 0, 16);
            $meta = new UserMetadata;
            $meta->user_id = $user->id;
            $meta->key = 'activationKey';
            $meta->value = $hash;
            $meta->save();

            // Send the registration email
            Yii::app()->controller->sendEmail($user, Yii::t('ciims.email','Activate Your Account'), '//email/register', array('user' => $user, 'hash' => $hash), true, true);

            return true;
        }

        return false;
    }
}

<?php

class InvitationForm extends CFormModel
{
	/**
	 * The email of the user to invite
	 * @var string $email
	 */
	public $email;

	/**
	 * Validation rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('email', 'required'),
			array('email', 'email'),
			array('email', 'userExists')
		);
	}

	/**
	 * Validates that a user with that email address isn't already invited
	 * @param array $attributes
	 * @param array $params
	 * @return boolean
	 */
	public function userExists($attributes, $params)
	{
		$user = Users::model()->findByAttributes(array('email' => $this->email));

		if ($user == NULL)
			return true;

		$this->addError('email', Yii::t('ciims.models.InvitationForm', 'A user with that email already exists.'));
		return false;
	}

	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'email' => Yii::t('ciims.models.InvitationForm', 'Email Address')
		);
	}

	/**
	 * Sends an invite to a new user
	 * @return boolean
	 */
	public function invite()
	{
		if (!$this->validate())
			return false;

		$user = new Users;
		$user->attributes = array(
			'email' 		=> $this->email,
			'firstName'		=> null,
			'lastName' 		=> null,
			'displayName' 	=> null,
			'password' 		=> null,
			'user_role' 	=> 5,
			'status' 		=> Users::PENDING_INVITATION
		);

		// Create a new user, but bypass validation
		if ($user->save(false))
		{
			$factory = new CryptLib\Random\Factory;
			$meta = new UserMetadata;
			$meta->attributes = array(
				'user_id' => $user->id,
				'key' => 'invitationKey',
				'value' => str_replace('/', '', $factory->getLowStrengthGenerator()->generateString(16))
			);

			// If the key was savedm send the email out
			if ($meta->save())
			{
				Yii::app()->controller->sendEmail(
					$user,
					Yii::t('ciims.models.InvitationForm', "You've Been Invited..."),
					'webroot.themes.' . Cii::getConfig('theme', 'default') .'.views.email.invite',
					array(
						'user' => $user,
						'hash' => $meta->value
					),
					true,
					true
				);

				return true;
			}

			$user->delete();
		}

		return false;
	}
}
<?php

Yii::import('application.modules.dashboard.components.CiiSettingsModel');
class EmailSettings extends CiiSettingsModel
{

	protected $SMTPHost = NULL;

	protected $SMTPPort = NULL;

	protected $SMTPUser = NULL;

	protected $SMTPPass = NULL;

	protected $notifyName = NULL;

	protected $notifyEmail = NULL;

	public $preContentView = 'application.modules.dashboard.views.settings.email-test';

	public function rules()
	{
		return array(
			array('notifyName, notifyEmail', 'required'),
			array('notifyEmail', 'email'),
			array('SMTPPort', 'numerical', 'integerOnly' => true, 'min' => 0),
			array('notifyName, SMTPPass, SMTPUser', 'length', 'max' => 255)
		);
	}

	public function attributeLabels()
	{
		return array(
			'SMTPHost' => 'SMTP Hostname',
			'SMTPPort' => 'SMTP Port Number',
			'SMTPUser' => 'SMTP Username',
			'SMTPPass' => 'SMTP Password',
			'notifyName' => 'System From Name',
			'notifyEmail' => 'System Email Address'
		);
	}
}
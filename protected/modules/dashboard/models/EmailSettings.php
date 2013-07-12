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

	/**
	 * Overload the getter so we can the site name from Yii::app()
	 * @see CiiSettingsModel::__get($name);
	 * @return  bool
	 */
	public function __get($name)
	{
		$ret = NULL;
		if ($name == 'notifyName')
			 $ret = $this->getNotifyName();
		else if ($name == 'notifyEmail')
			$ret = $this->getNotifyEmail();
		else
			$ret = NULL;

		if ($ret !== NULL)
			return $ret;

		return parent::__get($name);
	}

	public function getNotifyName()
	{
		if ($this->notifyName === NULL && !isset($this->attributes['notifyName']))
			return 'CiiMS No-Reply';

		return NULL;
	}

	public function getNotifyEmail()
	{
		if ($this->notifyEmail === NULL && !isset($this->attributes['notifyEmail']))
			return 'no-reply@' . $_SERVER['HTTP_HOST'];
		return NULL;
	}

	public function rules()
	{
		return array(
			array('notifyName, notifyEmail', 'required'),
			array('notifyEmail', 'email'),
			array('SMTPHost', 'url'),
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
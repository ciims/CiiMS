<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $firstName
 * @property string $lastName
 * @property string $username
 * @property string $about
 * @property integer $user_role
 * @property integer $status
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Comments[] $comments
 * @property Content[] $contents
 * @property Tags[] $tags
 * @property UserMetadata[] $userMetadatas
 * @property UserRoles $userRole
 */
class Users extends CiiModel
{
	const INACTIVE = 0;
	const ACTIVE = 1;
	const BANNED = 2;
	const PENDING_INVITATION = 3;

	public $pageSize = 15;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, password, username, user_role, status', 'required'),
			array('email', 'email'),
			array('user_role, status', 'numerical', 'integerOnly'=>true),
			array('email, username', 'length', 'max'=>255),
			array('password', 'length', 'max'=>64),
			// The following rule is used by search().
			array('id, email, password, username, about, user_role, status, created, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'comments' 	=> array(self::HAS_MANY, 'Comments', 'user_id'),
			'content' 	=> array(self::HAS_MANY, 'Content', 'author_id'),
			'metadata' 	=> array(self::HAS_MANY, 'UserMetadata', 'user_id', 'condition' => '`metadata`.`entity_type` = 0'),
			'role' 		=> array(self::BELONGS_TO, 'UserRoles', 'user_role'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 		  => Yii::t('ciims.models.Users', 'ID'),
			'email' 	  => Yii::t('ciims.models.Users', 'Email'),
			'password' 	  => Yii::t('ciims.models.Users', 'Password'),
			'username'    => Yii::t('ciims.models.Users', 'User Name'),
			'user_role'   => Yii::t('ciims.models.Users', 'User Role'),
			'status'	  => Yii::t('ciims.models.Users', 'Active'),
			'created' 	  => Yii::t('ciims.models.Users', 'Created'),
			'updated' 	  => Yii::t('ciims.models.Users', 'Updated'),
		);
	}

	/**
	 * Gets the first and last name instead of the username
	 */
	public function getName()
	{
		return $this->username;
	}

	/**
	 * Retrieves the username
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Retrieves the username
	 * @todo This is technical debt. At some point this should be completely depricated
	 */
	public function getDisplayName()
	{
		Yii::log('Users::displayName has been deprecated. Use Users::username moving forward', 'warning', 'models.users');
		return $this->getUsername();
	}

	/**
	 * Retrieves the reputation for a given user
	 * @param boolean $model 	Whether an instance of UserMetadata should be returned or not
	 * @return mixed
	 */
	public function getReputation($model=false)
	{
		$reputation = UserMetadata::model()->getPrototype('UserMetadata', array('user_id' => $this->id, 'key' => 'reputation'), array('value' => 150));

		if ($model === true)
			return $reputation;

		return $reputation->value;
	}

	/**
	 * Updates a user's reputation
	 * @return boolean
	 */
	public function setReputation($rep = 10)
	{
		$reputation = $this->getReputation(true);
		$reputation->value += $rep;
		return $reputation->save();
	}

	/**
	 * Retrieves all comments that the user has flagged
	 * @param boolean $model 	Whether an instance of UserMetadata should be returned or not
	 * @return mixed
	 */
	public function getFlaggedComments($model=false)
	{
		$flags = UserMetadata::model()->getPrototype('UserMetadata', array('user_id' => $this->id, 'key' => 'flaggedComments'), array('value' => CJSON::encode(array())));
		
		if ($model === true)
			return $flags;

		return CJSON::decode($flags->value);
	}

	/**
	 * Flags a comment with a given ID
	 * @return boolean
	 */
	public function flagComment($id)
	{
		$flaggedComments = $this->getFlaggedComments(true);
		$flags = CJSON::decode($flaggedComments->value);

		// If the comment has already been flagged, just return true
		if (in_array($id, $flags))
			return true;

		$flags[] = $id;
		$flaggedComments->value = CJSON::encode($flags);

		return $flaggedComments->save();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('user_role',$this->user_role);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->order = "user_role DESC, created DESC";

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => $this->pageSize
			)
		));
	}

	/**
	 * Sets some default values for the user record.
	 * @see CActiveRecord::beforeValidate()
	 **/
	public function beforeValidate()
	{
		// If the password is nulled, or unchanged
		if ($this->password == NULL || $this->password == Cii::get($this->_oldAttributes, 'password', false))
		{
			if (!$this->isNewRecord)
				$this->password = $this->_oldAttributes['password'];
		}
		else
		{
			$this->password = password_hash($this->password, PASSWORD_BCRYPT, array('cost' => Cii::getBcryptCost()));

			if (!$this->isNewRecord)
			{
				$emailSettings = new EmailSettings;
        		$emailSettings->send(
					$this,
					Yii::t('ciims.models.Users', 'CiiMS Password Change Notification'),
					'webroot.themes.' . Cii::getConfig('theme', 'default') .'.views.email.passwordchange',
					array('user' => $this)
				);
			}
		}

		return parent::beforeValidate();
	}

	/**
	 * Lets us know if the user likes a given content post or not
	 * @param  int $id The id of the content we want to know about
	 * @return bool    Whether or not the user likes the post
	 */
	public function likesPost($id = NULL)
	{
		if ($id === NULL)
			return false;

		$likes = UserMetadata::model()->findByAttributes(array('user_id' => $this->id, 'key' => 'likes'));

		if ($likes === NULL)
			return false;

		$likesArray = json_decode($likes->value, true);
		if (in_array($id, array_values($likesArray)))
			return true;

		return false;
	}

	/**
	 * Helper method to get the usermetadata object rather than calling getPrototype everywhere
	 * @param string $key
	 * @param mixed $value
	 * @return UserMetadata prototype object
	 */
	public function getMetadataObject($key, $value=NULL)
	{
		return UserMetadata::model()->getPrototype('UserMetadata', array(
				'user_id' => $this->id,
				'key' => $key
			),array(
				'user_id' => $this->id,
				'key' => $key,
				'value' => $value,
		));
	}

	/**
	 * Helper method to set the usermetadata object rather than calling getPrototype everywhere
	 * @param string $key
	 * @param mixed $value
	 * @return UserMetadata prototype object
	 */
	public function setMetadataObject($key, $value)
	{
		$metadata = $this->getMetadataObject();

		$metadata->value = $value;

		return $metadata->save();
	}

	/**
     * Determines if two factor authentication code is required
     * @return boolean
     */
    public function needsTwoFactorAuth()
    {
    	$metadata = $this->getMetadataObject('OTPSeed', false);

    	if ($metadata->value !== false)
    		return true;

    	return false;
    }

	/**
	 * Returns the gravatar image url for a particular user
	 * The beauty of this is that you can call User::model()->findByPk()->gravatarImage() and not have to do anything else
	 * Implemention details borrowed from Hypatia Cii User Extensions with permission
	 * @param  integer $size		The size of the image we want to display
	 * @param  string $default	The default image to be displayed if none is found
	 * @return string gravatar api image
	 */
	public function gravatarImage($size=20, $default=NULL)
	{
		return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($this->email))).'?s='.$size;
	}
}

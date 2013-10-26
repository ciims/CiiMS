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
 * @property string $displayName
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
	 * @return array primary key of the table
	 **/	 
	public function primaryKey()
	{
		return array('id');
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, password, displayName, user_role, status', 'required'),
			array('email', 'email'),
			array('user_role, status', 'numerical', 'integerOnly'=>true),
			array('email, firstName, lastName, displayName', 'length', 'max'=>255),
			array('password', 'length', 'max'=>64),
			// The following rule is used by search().
			array('id, email, password, firstName, lastName, displayName, about, user_role, status, created, updated', 'safe', 'on'=>'search'),
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
			'comments' => array(self::HAS_MANY, 'Comments', 'user_id'),
			'content' => array(self::HAS_MANY, 'Content', 'author_id'),
			'tags' => array(self::HAS_MANY, 'Tags', 'user_id'),
			// Don't pull in dashboard junk...
			'metadata' => array(self::HAS_MANY, 'UserMetadata', 'user_id', 'condition' => '`metadata`.`entity_type` = 0'),
			'role' => array(self::BELONGS_TO, 'UserRoles', 'user_role'),
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
			'firstName'   => Yii::t('ciims.models.Users', 'First Name'),
			'lastName' 	  => Yii::t('ciims.models.Users', 'Last Name'),
			'displayName' => Yii::t('ciims.models.Users', 'Display Name'),
			'about'		  => Yii::t('ciims.models.Users', 'About'),
			'user_role'   => Yii::t('ciims.models.Users', 'User Role'),
			'status'	  => Yii::t('ciims.models.Users', 'Active'),
			'created' 	  => Yii::t('ciims.models.Users', 'Created'),
			'updated' 	  => Yii::t('ciims.models.Users', 'Updated'),
		);
	}

    /**
     * Gets the first and last name instead of the displayname
     */
    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
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
		$criteria->compare('firstName',$this->firstName,true);
		$criteria->compare('lastName',$this->lastName,true);
		$criteria->compare('displayName',$this->displayName,true);
		$criteria->compare('about',$this->about,true);
		$criteria->compare('user_role',$this->user_role);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->addCondition('status != ' . self::PENDING_INVITATION);
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
	 * TODO: This should have been moved to CiiModel
	 * @see CActiveRecord::beforeValidate()
	 **/
	public function beforeValidate()
    {
        if ($this->about == NULL || $this->about == '')
            $this->about = ' ';

        return parent::beforeValidate();
    }

    /**
     * Bind behaviors for changing the user's email, and allow them to make the appropriate changes on their end.
     * The intention behind this, is that the user has to first, verify that they requested the change, and second
     * verify that they own both email addresses.
     *
     * The intention behind this is to protect the user from changes to their account, either by an administrator or a malicious user.
     * This doesn't protect from database attacks, it only protects from malicious attacks from within CiiMS.
     * 
     * @return parent::afterSave();
     */
    public function beforeSave()
    {
    	// If the user's email address is about to change
    	if (isset($this->_oldAttributes['email']) && $this->_oldAttributes['email'] != $this->email)
    	{
    		// Store the new email address
    		$newEmail = $this->email;

    		// Reset the email addres and block the change internally
    		$this->email = $this->_oldAttributes['email'];

    		// Save the NEW email address in the database as a metadata record
    		$meta = UserMetadata::model()->findByAttributes(array('user_id' => $this->id, 'key' => 'newEmailAddress'));
    		if ($meta === NULL)
    			$meta = new UserMetadata;
    		$meta->user_id = $this->id;
    		$meta->key = 'newEmailAddress';
    		$meta->value = $newEmail;
    		$meta->save();

    		$meta = UserMetadata::model()->findByAttributes(array('user_id' => $this->id, 'key' => 'newEmailAddressChangeKey'));
    		if ($meta === NULL)
    			$meta = new UserMetadata;

    		$meta->user_id = $this->id;
    		$meta->key = 'newEmailAddressChangeKey';
    		$key = $meta->value = md5(md5($newEmail . time()) . Yii::app()->params['encryptionKey']);
    		$meta->save();

    		// Delete all API tokens associated to this account
    		$response = Yii::app()->db->createCommand('DELETE FROM user_metadata WHERE `key` LIKE "api_key%" AND user_id = :id')->bindParam(':id', $this->id)->execute();

    		// Fire off an email to the OLD email address asking them VERIFY the change
    		$response = Yii::app()->controller->sendEmail($this,  Yii::t('Dashboard.email', 'CiiMS Email Change Notification'), 'application.modules.dashboard.views.email.email-change', array('key' => $key));
    	}

    	return parent::beforeSave();
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
	 * Creates an encrypted hash to be used as a password
	 * @param string $email 	The user email
	 * @param string $password	The password to be encrypted
	 * @param string $_dbsalt	The salt value to be used (Yii::app()->params['encryptionKey'])
	 * @return 64 character encrypted string
	 */
	public function encryptHash($email, $password, $_dbsalt)
	{
		return mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5($password . md5($email)))) . hash("sha512", md5($password . md5($_dbsalt))) . $_dbsalt), 0, 64);	
	}

	/**
	 * Returns the gravatar image url for a particular user
	 * The beauty of this is that you can call User::model()->findByPk()->gravatarImage() and not have to do anything else
	 * Implemention details borrowed from Hypatia Cii User Extensions with permission
	 * @param  string $size		The size of the image we want to display
	 * @param  string $default	The default image to be displayed if none is found
	 * @return string gravatar api image
	 */
	public function gravatarImage($size=20, $default=NULL)
	{
		return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($this->email))).'?s='.$size;
	}
}

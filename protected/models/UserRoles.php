<?php

/**
 * This is the model class for table "user_roles".
 *
 * The followings are the available columns in table 'user_roles':
 * @property integer $id
 * @property string $name
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Users[] $users
 */
class UserRoles extends CiiModel
{

	/**
	 * Returns a key => value array of userRole => bitwise permissions
	 *
	 * Permissions apply per role, with the exception of publisher and admin, whose permissions apply to everything
	 * Note that these permissions only apply to content, and management of CiiMS' settings
	 *
	 * --------------------------------------------------------------------------------
	 * | role/id | manage | publish other | publish | delete | update | create | read |
	 * --------------------------------------------------------------------------------
	 * |  user/1 |   0    |        0      |    0    |    0   |    0   |    0   |  1   |
	 * --------------------------------------------------------------------------------
	 * | clb/5   |   0    |        0      |    0    |    0   |    1   |    1   |  1   |
	 * --------------------------------------------------------------------------------
	 * | auth/7  |   0    |        0      |    1    |    1   |    1   |    1   |  1   |
	 * --------------------------------------------------------------------------------
	 * | pub/8   |   0    |        1      |    1    |    1   |    1   |    1   |  1   |
	 * --------------------------------------------------------------------------------
	 * | admin/9 |   1    |        1      |    1    |    1   |    1   |    1   |  1   |
	 * --------------------------------------------------------------------------------
	 * @return array
	 */
	public function getPermissions()
	{
		return array(
			'1' => 1,		// User
			'2' => 0,		// Pending
			'3' => 0,		// Suspended
			'5' => 7,		// Collaborator
			'7' => 16,		// Author
			'8' => 32,		// Publisher
			'9' => 64		// Admin
		);
	}

	public function isA($roleName, $role=false)
	{
		if ($role === false)
			$role = Yii::app()->user->role;

		$roleName = strtolower($roleName);

		switch ($roleName)
		{
			case 'user':
				return $role <= 1;
			case 'collaborator':
				return $role == 5;
			case 'author':
				return $role == 7;
			case 'publisher':
				return $role == 8;
			case 'admin':
				return $role == 9;
		}

		return false;
	}

	/**
	 * Returns the bitwise permissions associated to each activity
	 * @return array
	 */
	public function getActivities()
	{
		return array(
			'read' 			=> 1,
			'comment' 		=> 1,
			'create' 		=> 3,
			'update' 		=> 4,
			'modify' 		=> 7,
			'delete' 		=> 8,
			'publish' 		=> 16,
			'publishOther' 	=> 32,
			'manage' 		=> 64
		);
	}

	/**
	 * Determines if a user with a given role has permission to perform a given activity
	 * @param string $permission   The permissions we want to lookup
	 * @param int 	 $role 			The user role. If not provided, will be applied to the current user
	 * @return boolean
	 */
	public function hasPermission($permission, $role=NULL)
	{
		if ($role === NULL)
		{
			if (isset($this->id))
				$role = $this->id;
			else if (Yii::app()->user->isGuest)
				$role = 1;
			else
				$role = Yii::app()->user->role;
		}

		$permissions = $this->getPermissions();
		$activities = $this->getActivities();

		// If the permission doesn't exist for that role, return false;
		if (!isset($permissions[$role]))
			return false;

		return $activities[$permission] <= $permissions[$role];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserRoles the static model class
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
		return 'user_roles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>100),
			// The following rule is used by search().
			array('id, name, created, updated', 'safe', 'on'=>'search'),
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
			'users' => array(self::HAS_MANY, 'Users', 'user_role'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 	  => Yii::t('ciims.models.UserRoles', 'ID'),
			'name' 	  => Yii::t('ciims.models.UserRoles', 'Name'),
			'created' => Yii::t('ciims.models.UserRoles', 'Created'),
			'updated' => Yii::t('ciims.models.UserRoles', 'Updated'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}

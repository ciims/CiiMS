<?php

/**
 * This is the model class for table "user_groups".
 *
 * The followings are the available columns in table 'user_groups':
 * @property integer $id
 * @property integer $group_id
 * @property integer $user_id
 * @property string $created
 * @property string $updated
 */
class UserGroups extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserGroups the static model class
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
		return 'user_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group_id, user_id, created, updated', 'required'),
			array('group_id, user_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			array('id, group_id, user_id, created, updated', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 	   => Yii::t('ciims.models.UserGroups', 'ID'),
			'group_id' => Yii::t('ciims.models.UserGroups', 'Group'),
			'user_id'  => Yii::t('ciims.models.UserGroups', 'User'),
			'created'  => Yii::t('ciims.models.UserGroups', 'Created'),
			'updated'  => Yii::t('ciims.models.UserGroups', 'Updated'),
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
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Sets the created and updated attributes
	 * @return [type]
	 */
	public function beforeSave()
	{
	    if ($this->isNewRecord)
	    	$this->created = new CDbExpression('NOW()');

	    $this->updated = new CDbExpression('NOW()');
	 
	    return parent::beforeSave();
	}
}

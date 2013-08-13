<?php

/**
 * This is the model class for table "user_metadata".
 *
 * The followings are the available columns in table 'user_metadata':
 * @property integer $id
 * @property integer $user_id
 * @property string $key
 * @property string $value
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class UserMetadata extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserMetadata the static model class
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
		return 'user_metadata';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, key, value', 'required'),
			array('user_id, entity_type', 'numerical', 'integerOnly'=>true),
			array('key', 'length', 'max'=>50),
			// The following rule is used by search().
			array('id, user_id, key, value, entity_type, created, updated', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 		  => Yii::t('ciims.models.UserMetadata', 'ID'),
			'user_id' 	  => Yii::t('ciims.models.UserMetadata', 'User'),
			'key' 		  => Yii::t('ciims.models.UserMetadata', 'Key'),
			'value' 	  => Yii::t('ciims.models.UserMetadata', 'Value'),
			'entity_type' => Yii::t('ciims.models.UserMetadata', 'Entity Type'),
			'created' 	  => Yii::t('ciims.models.UserMetadata', 'Created'),
			'updated'     => Yii::t('ciims.models.UserMetadata', 'Updated'),
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('entity_type',$this->entity_type,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Sets the created and updated attributes
	 */
	public function beforeSave()
	{
    	if ($this->isNewRecord)
			$this->created = new CDbExpression('NOW()');
		
		$this->updated = new CDbExpression('NOW()');

	    return parent::beforeSave();
	}
}

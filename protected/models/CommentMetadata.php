<?php

/**
 * This is the model class for table "comment_metadata".
 *
 * The followings are the available columns in table 'comment_metadata':
 * @property integer $comment_id
 * @property string $key
 * @property string $value
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Comments $comment
 */
class CommentMetadata extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CommentMetadata the static model class
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
		return 'comment_metadata';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('comment_id, key, value', 'required'),
			array('comment_id', 'numerical', 'integerOnly'=>true),
			array('key, value', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('comment_id, key, value, created, updated', 'safe', 'on'=>'search'),
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
			'comment' => array(self::BELONGS_TO, 'Comments', 'comment_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'comment_id' => 'Comment',
			'key' => 'Key',
			'value' => 'Value',
			'created' => 'Created',
			'updated' => 'Updated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('comment_id',$this->comment_id);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function beforeSave() {
	    	if ($this->isNewRecord)
	    	{
			$this->created = new CDbExpression('NOW()');
			$this->updated = new CDbExpression('NOW()');
		}
	   	else
			$this->updated = new CDbExpression('NOW()');
	 
	    	return parent::beforeSave();
	}
}

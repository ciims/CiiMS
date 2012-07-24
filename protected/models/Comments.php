<?php

/**
 * This is the model class for table "comments".
 *
 * The followings are the available columns in table 'comments':
 * @property integer $id
 * @property integer $content_id
 * @property integer $user_id
 * @property integer $parent_id
 * @property string $comment
 * @property integer $approved
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property CommentMetadata[] $commentMetadatas
 * @property Content $parent
 * @property Content $content
 * @property Users $user
 */
class Comments extends CiiModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comments the static model class
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
		return 'comments';
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
			array('content_id, user_id, parent_id, comment, approved', 'required'),
			array('content_id, user_id, parent_id, approved', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, content_id, user_id, parent_id, comment, approved, created, updated', 'safe', 'on'=>'search'),
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
			'metadata' => array(self::HAS_MANY, 'CommentMetadata', 'comment_id'),
			'parent' => array(self::BELONGS_TO, 'Content', 'parent_id'),
			'content' => array(self::BELONGS_TO, 'Content', 'content_id'),
			'author' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'content_id' => 'Content',
			'user_id' => 'User',
			'parent_id' => 'Parent',
			'comment' => 'Comment',
			'approved' => 'Approved',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('content_id',$this->content_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('approved',$this->approved);
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
	 
	 	if (Content::model()->findByPk($this->content_id)->commentable)
	    	return parent::beforeSave();
		else 
			return false;
	}
}

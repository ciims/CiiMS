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
	 * API Attribute for retrieving the counts
	 * @var int
	 */
	public $count = 0;

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
			'parent' => array(self::BELONGS_TO, 'Comments', 'parent_id'),
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
			'id'		 => Yii::t('ciims.models.Comments', 'ID'),
			'content_id' => Yii::t('ciims.models.Comments', 'Content'),
			'user_id' 	 => Yii::t('ciims.models.Comments', 'User'),
			'parent_id'  => Yii::t('ciims.models.Comments', 'Parent'),
			'comment' 	 => Yii::t('ciims.models.Comments', 'Comment'),
			'approved' 	 => Yii::t('ciims.models.Comments', 'Approved'),
			'created' 	 => Yii::t('ciims.models.Comments', 'Created'),
			'updated' 	 => Yii::t('ciims.models.Comments', 'Updated'),
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
	
	/**
	 * Set the created and updated records
	 */
	public function beforeSave() 
	{	 
	 	if (Content::model()->findByPk($this->content_id)->commentable)
	    	return parent::beforeSave();
		else 
			return false;
	}
    
    /**
     * After Save, incriments the comment count of the parent content
     * @return  bool
     */
    public function afterSave()
    {
        $content = Content::model()->findByPk($this->content_id);
        if ($content === NULL)
            return true;
        
        if (!$this->isNewRecord)
        	return true;
        
        $content->comment_count = $content->getCommentCount();
        $content->save();

	    $user = Users::model()->findByPk(Yii::app()->user->id);
		
		// Send an email to the author if someone makes a comment on their blog
		if ($content->author->id != Yii::app()->user->id && Cii::getConfig('notifyAuthorOnComment', 0) == 1)
			Yii::app()->controller->sendEmail($user, Yii::t('ciims.email', 'New Comment Posted On {{title}}', array('{{title}}' => $content->title)), '//email/comment', array('content'=>$content, 'comment'=>$this));

        return parent::afterSave();
    }
    
    /**
     * After Delete method, decriments the comment count of the parent content
     * @return  bool
     */
    public function afterDelete()
    {
        $content = Content::model()->findByPk($this->content_id);
        if ($content === NULL)
            return true;
        
        $content->comment_count = $content->comment_count = max($content->comment_count - 1, 0);
        
        $content->save();
        return parent::afterDelete();
    }
}

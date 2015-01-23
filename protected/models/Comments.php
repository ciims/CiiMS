<?php

/**
 * This is the model class for table "comments".
 *
 * The followings are the available columns in table 'comments':
 * @property integer $id
 * @property integer $content_id
 * @property integer $author_id
 * @property string $comment
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
	 * AfterSave IsNewRecord
	 * @var boolean
	 */
	private $_isNewRecord = false;

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
	 * @return string[] primary key of the table
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
			array('content_id, author_id, comment', 'required'),
			array('content_id, author_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			array('id, content_id, author_id, comment, created, updated', 'safe', 'on'=>'search'),
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
			'content' => array(self::BELONGS_TO, 'Content', 'content_id'),
			'author' => array(self::BELONGS_TO, 'Users', 'author_id'),
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
			'author_id'  => Yii::t('ciims.models.Comments', 'User'),
			'comment' 	 => Yii::t('ciims.models.Comments', 'Comment'),
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
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Returns the API attributes for the model
	 * @return array
	 */
	public function getApiAttributes($params = array(), $relations = false)
	{
		$data = parent::getApiAttributes($params, $relations);
        $user = Users::model()->findByPk($this->author_id);
        $attributes = $user->getApiAttributes();
        $data['user'] = array(
            'firstName' => $attributes['firstName'],
            'lastName' => $attributes['lastName'],
            'username' => $attributes['username'],
        );

        $content = Content::model()->findByPk($data['content_id']);
        $data['content'] = array(
        	'id' => $data['content_id'],
        	'title' => $content->title,
        	'slug' => $content->slug
        );

        // If this user cannot comment without approval
        if ($user->getReputation() < 100)
        	$data['banned_comment'] = true;

        return $data;
	}

	/**
	 * Set the created and updated records
	 * @see CiiModel::beforeSave();
	 */
	public function beforeSave() 
	{	 
		if ($this->isNewRecord)
			$this->_isNewRecord = true;

	 	if (Content::model()->findByPk($this->content_id)->commentable)
	    	return parent::beforeSave();
		else 
			return false;
	}

	/**
	 * After a new comment is posted, set the reputation += 10
	 * @see parent::afterSave();
	 */
	public function afterSave()
	{
		if ($this->_isNewRecord)
		{
			$user = Users::model()->findByPk($this->author_id);
			$user->setReputation(10);
		}

		return parent::afterSave();
	}
}

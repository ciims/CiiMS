<?php

/**
 * This is the model class for table "content".
 *
 * The followings are the available columns in table 'content':
 * @property integer $id
 * @property integer $vid
 * @property integer $author_id
 * @property string $title
 * @property string $content
 * @property string $except
 * @property integer $status
 * @property integer $commentable
 * @property integer $parent_id
 * @property integer $category_id
 * @property integer $type_id
 * @property string $password
 * @property integer $comment_count
 * @property string $slug
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Comments[] $comments
 * @property Comments[] $comments1
 * @property Users $author
 * @property Content $parent
 * @property Content[] $contents
 * @property Categories $category
 * @property ContentMetadata[] $contentMetadatas
 */
class Content extends CiiModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Content the static model class
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
		return 'content';
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
			array('vid, author_id, title, content, status, commentable, parent_id, category_id', 'required'),
			array('vid, author_id, status, commentable, parent_id, category_id, type_id, comment_count', 'numerical', 'integerOnly'=>true),
			array('title, password, slug', 'length', 'max'=>150),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, vid, author_id, title, content, extract, status, commentable, parent_id, category_id, type_id, password, comment_count, slug, created, updated', 'safe', 'on'=>'search'),
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
			'comments' => array(self::HAS_MANY, 'Comments', 'content_id'),
			'author' => array(self::BELONGS_TO, 'Users', 'author_id'),
			'parent' => array(self::BELONGS_TO, 'Content', 'parent_id'),
			'category' => array(self::BELONGS_TO, 'Categories', 'category_id'),
			'metadata' => array(self::HAS_MANY, 'ContentMetadata', 'content_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'vid' => 'Vid',
			'author_id' => 'Author',
			'title' => 'Title',
			'content' => 'Content',
			'extract' => 'Extract',
			'status' => 'Status',
			'commentable' => 'Commentable',
			'parent_id' => 'Parent',
			'category_id' => 'Category',
			'type_id' => 'Type',
			'password' => 'Password',
			'comment_count' => 'Comments',
			'slug' => 'Slug',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('content',$this->slug,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
		$criteria->order = "id DESC";
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
         * Finds all active records with the specified primary keys.
         * Overloaded to support composite primary keys. For our content, we want to find the latest version of that primary key, defined as MAX(vid) WHERE id = pk
         * See {@link find()} for detailed explanation about $condition and $params.
         * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
         * @param mixed $condition query condition or criteria.
         * @param array $params parameters to be bound to an SQL statement.
         * @return array the records found. An empty array is returned if none is found.
         */
	public function findByPk($pk, $condition='', $params=array())
	{
		// If we do not supply a condition or parameters, use our overwritten method
		if ($condition == '' && empty($params))
		{			
			// Trace
			Yii::trace(get_class($this).'.findByPk() Override','system.db.ar.CActiveRecord');
			$criteria = new CDbCriteria;
			$criteria->addCondition("t.id={$pk}");
			$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id={$pk})");
			return $this->query($criteria);
		}
		
		return parent::findByPk($pk, $conditions, $params);
	}
	
	public function beforeValidate()
	{
    	if ($this->isNewRecord)
    	{
    		// Implicit flush to delete the URL rules
			$this->created = new CDbExpression('NOW()');
			$this->comment_count = 0;
		}
	   	
	   	$this->updated = new CDbExpression('NOW()');
		
		if (strlen($this->extract) == 0)
    		$this->extract = $this->myTruncate($this->content, 250, '.', '');
	 	
	    return parent::beforeValidate();
	}
	
	public function beforeSave()
	{
		if ($this->isNewRecord)
		{			
    		Yii::app()->cache->delete('content');
    		Yii::app()->cache->delete('content-listing');
			Yii::app()->cache->delete('WFF-content-url-rules');
		}
		
		$this->slug = $this->verifySlug($this->slug, $this->title);
		
		return parent::beforeSave();
	}
	
	public function afterSave()
	{
		Yii::import('ext.autokeywords.*');
		$params['content'] = strip_tags($this->content); //page content
		
		//set the length of keywords you like
		$params['min_word_length'] = 5;  //minimum length of single words
		$params['min_word_occur'] = 2;  //minimum occur of single words

		$params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
		$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
		$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase

		$params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
		$params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
		$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase

		$keyword = new AutoKeywords($params, "iso-8859-1");
		$keywords = $keyword->get_keywords();
		
		$command  = Yii::app()->db->createCommand('INSERT INTO content_metadata (content_id, `key`, value, created, updated) VALUES (:content_id, "keywords", :value, NOW(), NOW()) ON DUPLICATE KEY UPDATE content_id = :content_id, `key` = `key`, value = :value, created = created, updated = NOW()');
		$id = $this->id;
		$command->bindParam(':content_id',$id,PDO::PARAM_INT);
		$command->bindParam(':value',$keywords,PDO::PARAM_STR);
		$command->execute();
		
		return parent::afterSave();
	}
	
	public function beforeDelete()
	{		
		Yii::app()->cache->delete('content');
		Yii::app()->cache->delete('content-listing');
		Yii::app()->cache->delete('WFF-content-url-rules');
		
		return parent::beforeDelete();
	}
	
	private function myTruncate($string, $limit, $break=".", $pad="...")
	{
		// return with no change if string is shorter than $limit
		if(strlen($string) <= $limit) return $string;

		// is $break present between $limit and the end of the string?
		if(false !== ($breakpoint = strpos($string, $break, $limit)))
		{
			if($breakpoint < strlen($string) - 1) {
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}

		return $string;
	}
	
	/**
	 * checkSlug - Recursive method to verify that the slug can be used
	 * This method is purposfuly declared here to so that Content::findByPk is used instead of CiiModel::findByPk
	 * @param string $slug - the slug to be checked
	 * @param int $id - the numeric id to be appended to the slug if a conflict exists
	 * @return string $slug - the final slug to be used
	 */
	public function checkSlug($slug, $id=NULL)
	{
		// Find the number of items that have the same slug as this one
		$count = $this->countByAttributes(array('slug'=>$slug . $id));
		
		// If we found an item that matched, it's possible that it is the current item, in which case we don't need to alter the slug
		if ($count >= 1)
		{
			if (!$this->isNewRecord)
			{
				// Pull the data that matches
				$data = $this->findByPk($this->id);
				
				// Check the pulled data id to the current item
				if ($data->id == $this->id)
					$count = 0;	
			}
		}
		
		if ($count == 0 && !in_array($slug, $this->forbiddenRoutes))
			return $slug . $id;
		else
		{
			if ($id == NULL)
				$id = 1;
			else 
				$id++;
			return $this->checkSlug($slug, $id);
		}
	}
}

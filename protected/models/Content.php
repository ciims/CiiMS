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
 * @property integer $like_count
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
    public $pageSize = 9;
	
    public $viewFile = 'blog';
    
    public $layoutFile = 'blog';
    
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
			array('vid, author_id, status, commentable, parent_id, category_id, type_id, comment_count, like_count', 'numerical', 'integerOnly'=>true),
			array('title, password, slug', 'length', 'max'=>150),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, vid, author_id, title, content, extract, status, commentable, parent_id, category_id, type_id, password, comment_count, like_count, slug, created, updated', 'safe', 'on'=>'search'),
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
			'like_count' => 'Likes',
			'tags' => 'Tags',
			'slug' => 'Slug',
			'created' => 'Created',
			'updated' => 'Updated',
		);
	}
	
	public function getCommentCount()
	{
		return Comments::model()->countByAttributes(array('content_id' => $this->id));
	}

	/**
	 * Gets keyword tags for this entry
	 * @return array
	 */
	public function getTags()
	{
		$tags = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'keywords'));
		return $tags === NULL ? array() : json_decode($tags->value, true);
	}
	
	/**
	 * Adds a tag to the model
	 * @param string $tag	The tag to add
	 * @return bool			If the insert was successful or not
	 */
	public function addTag($tag)
	{
		$tags = $this->tags;
		if (in_array($tag, $tags)  || $tag == "")
			return false;
		
		$tags[] = $tag;
		$tags = json_encode($tags);
		$metaTag = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'keywords'));
		if ($metaTag == false)
		{
			$metaTag = new ContentMetadata;
			$metaTag->content_id = $this->id;
			$metaTag->key = 'keywords';
		}
		
		$metaTag->value = $tags;		
		return $metaTag->save();
	}
	
	/**
	 * Removes a tag from the model
	 * @param string $tag	The tag to remove
	 * @return bool			If the removal was successful
	 */
	public function removeTag($tag)
	{
		$tags = $this->tags;
		if (!in_array($tag, $tags) || $tag == "")
			return false;
		
		$key = array_search($tag, $tags);
		unset($tags[$key]);
		$tags = json_encode($tags);
		
		$metaTag = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'keywords'));
		$metaTag->value = $tags;		
		return $metaTag->save();
	}
	
	/**
	 * Gets a flattened list of keyword tags for jQuery.tag.js
	 * @return string
	 */
	public function getTagsFlat()
	{
		return implode(',', $this->tags);
	}
    
    /**
     * Retrieves the layout used from Metadata
     * We cache this to speed up the viewfile
     */
    public function getLayout()
    {
        $layout = Yii::app()->cache->get('content-' . $this->id . '-layout');
        if ($layout === false)
        {
            $model  = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'layout'));
            $layout = $model === NULL ? 'blog' : $model->value;
            Yii::app()->cache->set('content-' . $this->id . '-layout', $layout);
        }
        
        return $layout;
    }
    
    /**
     * Retrieves the viewfile used from Metadata
     * We cache this to speed up the viewfile
     */
    public function getView()
    {
        $view = Yii::app()->cache->get('content-' . $this->id . '-view');
        if ($view === false)
        {
            $model  = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'view'));
            $view = $model === NULL ? 'blog' : $model->value;
            Yii::app()->cache->set('content-' . $this->id . '-view', $view);
        }
        
        return $view;
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
        
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
                'defaultOrder' => 'created DESC'
            ),
            'pagination' => array(
                'pageSize' => $this->pageSize
            )
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
			$criteria = new CDbCriteria;
			$criteria->addCondition("t.id={$pk}");
			$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id={$pk})");
			return $this->query($criteria);
		}
		
		return parent::findByPk($pk, $conditions, $params);
	}
	
    /**
     * BeforeValidate
     * @see CActiveRecord::beforeValidate
     */
	public function beforeValidate()
	{   

		// If this is a new record and we don't have a created data pre-populated
		// Failing to check the $this->created results in posts losing their original creation date
    	if ($this->isNewRecord && $this->created == NULL)
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
	
    /**
     * BeforeSave
     * Clears caches for rebuilding, creates the end slug that we are going to use
     * @see CActiveRecord::beforeSave();
     */
	public function beforeSave()
	{
		$this->slug = $this->verifySlug($this->slug, $this->title);		
		Yii::app()->cache->delete('content');
		Yii::app()->cache->delete('content-listing');
		Yii::app()->cache->delete('WFF-content-url-rules');
		
		Yii::app()->cache->set('content-' . $this->id . '-layout', $this->layoutFile);
        Yii::app()->cache->set('content-' . $this->id . '-view', $this->viewFile);
        
		return parent::beforeSave();
	}
	
    /**
     * AfterSave
     * Updates the layout and view if necessary
     * @see CActiveRecord::afterSave()
     */
    public function afterSave()
    {
        $this->saveLayoutAndView();
        return parent::afterSave();
    }
    
    /**
     * BeforeDelete
     * Clears caches for rebuilding
     * @see CActiveRecord::beforeDelete
     */
	public function beforeDelete()
	{		
		Yii::app()->cache->delete('content');
		Yii::app()->cache->delete('content-listing');
		Yii::app()->cache->delete('WFF-content-url-rules');
		Yii::app()->cache->delete('content-' . $this->id . '-layout');
        Yii::app()->cache->delete('content-' . $this->id . '-view');
        
		return parent::beforeDelete();
	}
	
    /**
     * Saves the layout and view file to the database if they are different from blog
     * If either are blog, we won't worry about applying it since we can pick this up pretty cheaply inline via caching
     */
    private function saveLayoutAndView()
    {
        $this->saveLayout();
        $this->saveView();
    }
    
    /**
     * Saves or deletes the layout in the db if necessary 
     */
    private function saveLayout()
    {
        $model = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'layout'));
        
        // If we don't have anything in ContentMetadata and the layout file is blog
        if ($model === NULL && $this->layoutFile === 'blog')
            return;
        
        if ($model === NULL)
        {
            $model = new ContentMetadata();
            $model->content_id = $this->id;
            $model->key = 'layout';
        }
        
        // If this is an existing record, and we're changing it to blog, delete it instead of saving.
        if ($this->layoutFile == 'blog' && !$model->isNewRecord)
            return $model->delete();
        
        $model->value = $this->layoutFile;
        return $model->save();
    }
    
    /**
     * Saves or deletes the view in the db if necessary
     */
    private function saveView()
    {
        $model = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'view'));
        
        // If we don't have anything in ContentMetadata and the layout file is blog
        if ($model === NULL && $this->viewFile === 'blog')
            return;
        
        if ($model === NULL)
        {
            $model = new ContentMetadata();
            $model->content_id = $this->id;
            $model->key = 'layout';
        }
        
        // If this is an existing record, and we're changing it to blog, delete it instead of saving.
        if ($this->viewFile == 'blog' && !$model->isNewRecord)
            return $model->delete();
        
        $model->value = $this->viewFile;
        return $model->save();
    }
    
    /**
     * Fancy truncate function to help clean up our strings for the extract
     * @param string $string    The string we want to apply the text to
     * @param int    $limit     How many characters we want to break into
     * @param string $break     Characters we want to break on if possible
     * @param string $pad       The padding we want to apply
     * @return string           Truncated string
     */
	private function myTruncate($string, $limit, $break=".", $pad="...")
	{
		// return with no change if string is shorter than $limit
		if(strlen($string) <= $limit) 
		  return $string;

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
	    $category = false;
		// Find the number of items that have the same slug as this one
		$count = $this->countByAttributes(array('slug'=>$slug . $id));
        
        // Make sure we don't have a collision with
		if ($count == 0)
        {
            $category = true;
            $count = Categories::model()->countByAttributes(array('slug'=>$slug . $id));
        }
        
		// If we found an item that matched, it's possible that it is the current item (or a previous version of it)
		// in which case we don't need to alter the slug
		if ($count)
		{
		    // Ensures we don't have a collision on category
		    if ($category)
                return $this->checkSlug($slug, ($id == NULL ? 1 : ($id+1)));
            
		    // Pull the data that matches
		    $data = $this->findByPk($this->id == NULL ? -1 : $this->id);
		    
		    // Check the pulled data id to the current item
		    if ($data !== NULL && $data->id == $this->id)
			    return $slug;
		}
		
		if ($count == 0 && !in_array($slug, $this->forbiddenRoutes))
			return $slug . $id;
		else
			return $this->checkSlug($slug, ($id == NULL ? 1 : ($id+1)));
	}
}

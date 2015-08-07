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
 * @property integer $category_id
 * @property integer $type_id
 * @property string $password
 * @property integer $like_count
 * @property string $slug
 * @property string $published
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Comments[] $comments
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

	public $autosavedata = false;

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
			array('vid, author_id, title, content, status, commentable, category_id', 'required'),
			array('vid, author_id, status, commentable, category_id, type_id, like_count', 'numerical', 'integerOnly'=>true),
			array('title, password, slug', 'length', 'max'=>150),
			// The following rule is used by search().
			array('id, vid, author_id, title, content, excerpt, status, commentable, category_id, type_id, password, like_count, slug, published, created, updated', 'safe', 'on'=>'search'),
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
			'comments' 	=> array(self::HAS_MANY, 'Comments', 'content_id'),
			'author' 	=> array(self::BELONGS_TO, 'Users', 'author_id'),
			'category' 	=> array(self::BELONGS_TO, 'Categories', 'category_id'),
			'metadata' 	=> array(self::HAS_MANY, 'ContentMetadata', 'content_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 			=> Yii::t('ciims.models.Content', 'ID'),
			'vid' 			=> Yii::t('ciims.models.Content', 'Version'),
			'author_id' 	=> Yii::t('ciims.models.Content', 'Author'),
			'title' 		=> Yii::t('ciims.models.Content', 'Title'),
			'content' 		=> Yii::t('ciims.models.Content', 'Content'),
			'excerpt' 		=> Yii::t('ciims.models.Content', 'excerpt'),
			'status' 		=> Yii::t('ciims.models.Content', 'Status'),
			'commentable' 	=> Yii::t('ciims.models.Content', 'Commentable'),
			'category_id' 	=> Yii::t('ciims.models.Content', 'Category'),
			'type_id' 		=> Yii::t('ciims.models.Content', 'Type'),
			'password' 		=> Yii::t('ciims.models.Content', 'Password'),
			'like_count' 	=> Yii::t('ciims.models.Content', 'Likes'),
			'tags' 			=> Yii::t('ciims.models.Content', 'Tags'),
			'slug' 			=> Yii::t('ciims.models.Content', 'Slug'),
			'published' 	=> Yii::t('ciims.models.Content', 'Published'),
			'created' 		=> Yii::t('ciims.models.Content', 'Created'),
			'updated' 		=> Yii::t('ciims.models.Content', 'Updated'),
		);
	}

	/**
	 * Returns a safe output to the theme
	 * This includes setting nofollow tags on links, forcing them to open in new windows, and safely encoding the text
	 * @return string
	 */
	public function getSafeOutput()
	{
		$md = new CMarkdownParser;
		$dom = new DOMDocument();
		$dom->loadHtml('<?xml encoding="UTF-8">'.$md->safeTransform($this->content));
		$x = new DOMXPath($dom);

		foreach ($x->query('//a') as $node)
		{
			$element = $node->getAttribute('href');
			if (isset($element[0]) && $element[0] !== "/")
			{
				$node->setAttribute('rel', 'nofollow');
				$node->setAttribute('target', '_blank');
			}
		}

		return $md->safeTransform($dom->saveHtml());
	}

	public function getExtract()
	{
		Yii::log(Yii::t('ciims.models.Content', 'Use of property "extract" is deprecated in favor of "excerpt"'), 'system.db.ar.CActiveRecord', 'info');
		return $this->excerpt;
	}

	/**
	 * Correctly retrieves the number of likes for a particular post.
	 *
	 * This was added to address an issue with the like count changing if an article was updated
	 * @return int    The number of likes for this post
	 */

	public function getLikeCount()
	{
		$meta = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'likes'));
		if ($meta === NULL)
			return 0;

		return $meta->value;
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
		$meta = $this->getPrototype('ContentMetadata', array('content_id' => $this->id, 'key' => 'keywords'));

		$meta->value = $tags;
		return $meta->save();
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

		$meta = $this->getPrototype('ContentMetadata', array('content_id' => $this->id, 'key' => 'keywords'));
		$meta->value = $tags;
		return $meta->save();
	}

	/**
	 * Provides a base criteria for status, uniqueness, and published states
	 * @return CDBCriteria
	 */
	public function getBaseCriteria()
	{
		$criteria = new CDbCriteria();
		return $criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)")
					    ->addCondition('status = 1')
					    ->addCondition('UNIX_TIMESTAMP() >= published');
	}	

	/**
	 * Returns the appropriate status' depending up the user's role
	 * @return string[]
	 */
	public function getStatuses()
	{

		if (Yii::app()->user->role == 5 || Yii::app()->user->role == 7)
			return array(0 => Yii::t('ciims.models.Content', 'Draft'));

		return array(
			1 => Yii::t('ciims.models.Content', 'Published'),
			2 => Yii::t('ciims.models.Content', 'Ready for Review'),
			0 => Yii::t('ciims.models.Content', 'Draft')
		);
	}

	/**
	 * Determines if an article is published or not
	 * @return boolean
	 */
	public function isPublished()
	{
		return ($this->status == 1 && ($this->published <= time())) ? true : false;
	}

	/**
	 * Determines if a given articled is scheduled or not
	 * @return boolean
	 */
	public function isScheduled()
	{
		return ($this->status == 1 && ($this->published > time())) ? true : false;
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
		$model  = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'layout'));
		return $model === NULL ? 'blog' : $model->value;
	}

	/**
	 * Sets the layout
	 * @param string $data  the layout file
	 * @return boolean
	 */
	public function setLayout($data)
	{
		$meta = $this->getPrototype('ContentMetadata', array('content_id' => $this->id, 'key' => 'layout'));
		$meta->value = $data;
		return $meta->save();
	}

	/**
	 * Sets the view
	 * @param string $data  The view file
	 * @return boolean
	 */
	public function setView($data)
	{
		$meta = $this->getPrototype('ContentMetadata', array('content_id' => $this->id, 'key' => 'view'));
		$meta->value = $data;
		return $meta->save();
	}

	/**
	 * Retrieves the viewfile used from Metadata
	 * We cache this to speed up the viewfile
	 */
	public function getView()
	{
		$model  = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'view'));
		return $model === NULL ? 'blog' : $model->value;
	}

	/**
	 * Updates the like_count after finding new data
	 */
	protected function afterFind()
	{
		parent::afterFind();
		$this->like_count = $this->getLikeCount();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('author_id',$this->author_id,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('status', $this->status, true);

		// Handle publishing with a true/false value simply to do this calculation. Otherwise default to compare
		if (is_bool($this->published))
		{
			if ($this->published)
				$criteria->addCondition('published <= UNIX_TIMESTAMP()');
			else
				$criteria->addCondition('published > UNIX_TIMESTAMP()');
		}
		else
			$criteria->compare('published', $this->published,true);
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");

		// TODO: Figure out how to restore CActiveDataProvidor by getCommentCount
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'published DESC'
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
		if ($condition == '' && empty($params) && $pk != null)
		{
			if (!is_numeric($pk))
				throw new CHttpException(400, Yii::t('ciims.models.Content', 'The content ID provided was invalid.'));
				
			$criteria = new CDbCriteria;
			$criteria->addCondition("t.id=:pk");
			$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=:pk)");
			$criteria->params = array(
				':pk' => $pk
			);
			return $this->query($criteria);
		}

		return parent::findByPk($pk, $condition, $params);
	}

	/**
	 * Lists all revisions in the database for a givenid
	 * @param  int $id [description]
	 * @return array
	 */
	public function findRevisions($id)
	{
		if (!is_numeric($id))
			throw new CHttpException(400, Yii::t('ciims.models.Content', 'The content ID provided was invalid.'));
				
		$criteria = new CDbCriteria;
		$criteria->addCondition("id=:id");
		$criteria->params = array(
			':id' => $id
		);
		$criteria->order = 'vid DESC';

		return $this->query($criteria, true);
	}

	/**
	 * BeforeValidate
	 * @see CActiveRecord::beforeValidate
	 */
	public function beforeValidate()
	{
		// Allow publication times to be set automatically
		if (empty($this->published))
			$this->published = time();

		if (strlen($this->excerpt) == 0)
			$this->excerpt = $this->myTruncate($this->content, 250, '.', '');

		return parent::beforeValidate();
	}

	/**
	 * Saves a prototype copy of the model so that we can get an id back to work with
	 * @return boolean 	$model->save(false) without any validation rules
	 */
	public function savePrototype($author_id)
	{
		$this->title = '';
		$this->content = '';
		$this->excerpt = '';
		$this->commentable = 1;
		$this->status = 0;
		$this->category_id = 1;
		$this->type_id = 2;
		$this->password = null;
		$this->created = time();
		$this->updated = time();
		$this->published = time();
		$this->vid = 1;
		$this->slug = "";
		$this->author_id = $author_id;

		// TODO: Why doesn't Yii return the PK id field? But it does return VID? AutoIncriment bug?
		if ($this->save(false))
		{
			$data = Content::model()->findByAttributes(array('created' => $this->created));
			$this->id = $data->id;
			return true;
		}

		return false;
	}

	/**
	 * BeforeSave
	 * Clears caches for rebuilding, creates the end slug that we are going to use
	 * @see CActiveRecord::beforeSave();
	 */
	public function beforeSave()
	{
		$this->slug = $this->verifySlug($this->slug, $this->title);
		Yii::app()->cache->delete('CiiMS::Content::list');
		Yii::app()->cache->delete('CiiMS::Routes');

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
		// Delete the AutoSave document on update
		if ($this->isPublished())
		{
			$autosaveModel = ContentMetadata::model()->findByAttributes(array('content_id' => $this->id, 'key' => 'autosave'));
			if ($autosaveModel != NULL)
				$autosaveModel->delete();
		}

		return parent::afterSave();
	}

	/**
	 * BeforeDelete
	 * Clears caches for rebuilding
	 * @see CActiveRecord::beforeDelete
	 */
	public function beforeDelete()
	{
		Yii::app()->cache->delete('CiiMS::Content::list');
		Yii::app()->cache->delete('CiiMS::Routes');
		Yii::app()->cache->delete('content-' . $this->id . '-layout');
		Yii::app()->cache->delete('content-' . $this->id . '-view');

		return parent::beforeDelete();
	}


	/**
	 * Retrieves the available view files under the current theme
	 * @return array    A list of files by name
	 */
	public function getViewFiles($theme=null)
	{
		return $this->getFiles($theme, 'views.content');
	}

	/**
	 * Retrieves the available layouts under the current theme
	 * @return array    A list of files by name
	 */
	public function getLayoutFiles($theme=null)
	{
		return $this->getFiles($theme, 'views.layouts');
	}

	/**
	 * Retrieves view files for a particular path
	 * @param  string $theme  The theme to reference
	 * @param  string $type   The view type to lookup
	 * @return array $files   An array of files
	 */
	private function getFiles($theme=null, $type='views')
	{
		if ($theme === null)
			$theme = Cii::getConfig('theme', 'default');

		$folder = $type;

		if ($type == 'view')
			$folder = 'content';

		$returnFiles = array();

		if (!file_exists(YiiBase::getPathOfAlias('webroot.themes.' . $theme)))
			$theme = 'default';

		$files = Yii::app()->cache->get($theme.'-available-' . $type);

		if ($files === false)
		{
			$fileHelper = new CFileHelper;
			$files = $fileHelper->findFiles(Yii::getPathOfAlias('webroot.themes.' . $theme .'.' . $folder), array('fileTypes'=>array('php'), 'level'=>0));
			Yii::app()->cache->set($theme.'-available-' . $type, $files);
		}

		foreach ($files as $file)
		{
			$f = str_replace('content', '', str_replace('/', '', str_replace('.php', '', substr( $file, strrpos( $file, '/' ) + 1 ))));

			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
				$f = trim(substr($f, strrpos($f, '\\') + 1));

			if (!in_array($f, array('all', 'password', '_post')))
				$returnFiles[$f] = $f;
		}

		return $returnFiles;
	}

	/**
	 * Fancy truncate function to help clean up our strings for the excerpt
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
			if($breakpoint < strlen($string) - 1)
			{
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

		// Make sure we don't have a collision with a Category
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
				return $this->checkSlug($slug, ($id === NULL ? 1 : ($id+1)));

			// Pull the data that matches
			$data = $this->findByPk($this->id);

			// Check the pulled data id to the current item
			if ($data !== NULL && $data->id == $this->id && $data->slug == $this->slug)
				return $slug;
		}

		if ($count == 0 && !in_array($slug, $this->forbiddenRoutes))
			return $slug . $id;
		else
			return $this->checkSlug($slug, ($id === NULL ? 1 : ($id+1)));
	}
}

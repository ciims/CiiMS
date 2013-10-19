<?php

/**
 * This is the model class for table "categories".
 *
 * The followings are the available columns in table 'categories':
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $slug
 * @property string $created
 * @property string $updated
 *
 * The followings are the available model relations:
 * @property Categories $parent
 * @property Categories[] $categories
 * @property CategoriesMetadata[] $categoriesMetadatas
 * @property Content[] $contents
 */
class Categories extends CiiModel
{
	public $pageSize = 15;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Categories the static model class
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
		return 'categories';
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
			array('parent_id, name', 'required'),
			array('parent_id', 'numerical', 'integerOnly'=>true),
			array('name, slug', 'length', 'max'=>150),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent_id, name, slug', 'safe', 'on'=>'search'),
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
			'parent' => array(self::BELONGS_TO, 'Categories', 'parent_id'),
			'metadata' => array(self::HAS_MANY, 'CategoriesMetadata', 'category_id'),
			'content' => array(self::HAS_MANY, 'Content', 'category_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' 		=> Yii::t('ciims.models.Categories', 'ID'),
			'parent_id' => Yii::t('ciims.models.Categories', 'Parent'),
			'name'      => Yii::t('ciims.models.Categories', 'Name'),
			'slug'      => Yii::t('ciims.models.Categories', 'Slug'),
			'created'   => Yii::t('ciims.models.Categories', 'Created'),
			'updated'   => Yii::t('ciims.models.Categories', 'Updated'),
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
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->order = "id DESC";
		
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->pageSize
            )
		));
	}
	
	/**
	 * Verifies the slug before validating the model
	 */
	public function beforeValidate()
	{
		$this->slug = $this->verifySlug($this->slug, $this->name);

		return parent::beforeValidate();
	}
     
    /**
     * Flushes URL data from the cache before the model is updated
     */
	public function afterSave()
	{		
        Yii::app()->cache->delete('categories-listing');
		Yii::app()->cache->delete('categories');
		Yii::app()->cache->delete('WFF-categories-url-rules');
		Yii::app()->cache->delete('categories-pid');

	    return parent::afterSave();
	}
	
	/**
	 * Automatically corrects parent tree issues that arise when a parent category node
	 * is deleted.
	 * @return [type]
	 */
	public function beforeDelete()
	{
		// Prevents the main "uncategorized category from being deleted"
		if ($this->id == 1)
		{
			Yii::app()->user->setFlash('error', Yii::t('ciims.models.Categories', 'This category cannot be deleted'));
			return false;
		}
		
		Yii::app()->cache->delete('categories');
        Yii::app()->cache->delete('categories-listing');
		Yii::app()->cache->delete('WFF-categories-url-rules');
		Yii::app()->cache->delete('categories-pid');
		
		$parent = $this->parent_id;
		$id = $this->id;

		// Reassign all posts to the parent category
		Yii::app()->db->createCommand('UPDATE content SET category_id = :parent_id WHERE category_id = :id')
					  ->bindParam(':parent_id', $parent)
					  ->bindParam(':id', $id)
					  ->execute();

		// Reassign all child categories to the parent category
		$data = $this->findAllByAttributes(array('parent_id' => $id));
		
		foreach ($data as $row)
		{
			$id = $row->id;
			Yii::app()->db->createCommand('UPDATE categories SET parent_id = :parent_id WHERE id = :id')
					  ->bindParam(':parent_id', $parent)
					  ->bindParam(':id', $id)
					  ->execute();
		}

		return parent::beforeDelete();
	}

	/**
	 * Retrieves the parent categories for a given category_id
	 * @param  int $id
	 * @return [type]
	 */
	public function getParentCategories($id)
	{
		// Retrieve the data from cache if necessary
		$response = Yii::app()->cache->get('categories-pid');
		if ($response == NULL)
		{
			$response = Yii::app()->db->createCommand('SELECT id, parent_id, name, slug FROM categories')->queryAll();
			Yii::app()->cache->set('categories-pid', $response);
		}
		
		return $this->_getParentCategories($response, $id);
	}
	
	/**
	 * Recursive callback for retrieving parent categories
	 * @param  array $all    An array of all categories currently in the system
	 * @param  int  $id      The category we're seeking
	 * @param  array  $stack A stack to hold the entire tree
	 * @return Stack
	 */
	private function _getParentCategories($all_categories, $id, array $stack = array())
	{
		if ($id == 1)
			return array_reverse($stack);
		
		foreach ($all_categories as $k=>$v)
		{
			if ($v['id'] == $id)
			{
				$stack[$v['name']] = array(str_replace(Yii::app()->baseUrl, NULL, Yii::app()->createUrl($v['slug'])));
				return $this->_getParentCategories($all_categories, $v['parent_id'], $stack);
			}
		}
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
        $content = false;
        
        // Find the number of items that have the same slug as this one
        $count = $this->countByAttributes(array('slug'=>$slug . $id));
        
        if ($count == 0)
        {
            $content = true;
            $count = Content::model()->countByAttributes(array('slug'=>$slug . $id));
        }
        
        // If we found an item that matched, it's possible that it is the current item (or a previous version of it)
        // in which case we don't need to alter the slug
        if ($count >= 1)
        {
            if ($content)
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
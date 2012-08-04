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
			'contents' => array(self::HAS_MANY, 'Content', 'category_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'name' => 'Name',
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
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->order = "id DESC";
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function beforeValidate()
	{
		$this->slug = $this->verifySlug($this->slug, $this->name);
			
		return parent::beforeValidate();
	}
	
	public function beforeSave()
	{
    	if ($this->isNewRecord)
    	{
    		Yii::app()->cache->delete('categories');
			Yii::app()->cache->delete('WFF-categories-url-rules');
			Yii::app()->cache->delete('categories-pid');
			$this->created = new CDbExpression('NOW()');			
			$this->updated = new CDbExpression('NOW()');
		}
	   	else
			$this->updated = new CDbExpression('NOW()');
		
	    return parent::beforeSave();
	}
	
	public function beforeDelete()
	{			
		Yii::app()->cache->delete('categories');
		Yii::app()->cache->delete('ctegories-listing');
		Yii::app()->cache->delete('WFF-categories-url-rules');
		Yii::app()->cache->delete('categories-pid');
		return parent::beforeDelete();
	}
	
	public function getParentCategories($id)
	{
		// Retrieve the data from cache if necessary
		$response = Yii::app()->cache->get('categories-pid');
		if ($response == NULL)
		{
			$response = Yii::app()->db->createCommand('SELECT id, parent_id, name, slug FROM categories')->queryAll();
			Yii::app()->cache->set('categories-pid', $response);
		}
		
		return $this->__getParentCategories($response, $id);
	}
	
	private function __getParentCategories($all_categories, $id, array $stack = array())
	{
		if ($id == 1)
		{
			return array_reverse($stack);
		}
		
		foreach ($all_categories as $k=>$v)
		{
			if ($v['id'] == $id)
			{
				$stack[$v['name']] = array(str_replace(Yii::app()->baseUrl, NULL, Yii::app()->createUrl($v['slug'])));
				return $this->__getParentCategories($all_categories, $v['parent_id'], $stack);
			}
		}
	}
}

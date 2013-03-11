<?php

/**
 * This is the model class for table "configuration".
 *
 * The followings are the available columns in table 'configuration':
 * @property string $key
 * @property string $value
 * @property string $created
 * @property string $updated
 */
class Configuration extends CActiveRecord
{
	
	/**
	 * Improves page performance by caching static options
	 * @see CActiveRecord::__get()
	 * @return mixed $element
	 */
	public function __get($attribute)
	{
		// MySQL should be faster than CFileCache. QueryCache should be sufficient
		if (get_class(Yii::app()->cache) == 'CFileCache')
			return parent::__get();
		
		// Other caching systems should be faster than MySQL, so store the attribute in that system, and poll from that
		$element = Yii::app()->cache->get(get_class($this) . $attribute);
		if($element===false)
		{
			// Request from MySQL if we don't have it already, then store it in cache so long as it isn't NULL. Let __get() throw
			// And error on NULL attributes
		    $element = parent::__get($attribute);
			if ($element !== NULL)
				Yii::app()->cache->set(get_class($this) . $attribute, $element);
		}
		
		return $element;
	}
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Configuration the static model class
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
        return 'configuration';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('key, value, created, updated', 'required'),
            array('key', 'length', 'max'=>64),
            array('value', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('key, value, created, updated', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
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

        $criteria->compare('key',$this->key,true);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('updated',$this->updated,true);
		$criteria->order = "created DESC";
		
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
	public function beforeValidate()
	{
		if ($this->isNewRecord)
		{
			// Implicit flush to delete the URL rules
			$this->created = new CDbExpression('NOW()');
			$this->updated = new CDbExpression('NOW()');
		}
		else
			$this->updated = new CDbExpression('NOW()');

		return parent::beforeValidate();
	}
}

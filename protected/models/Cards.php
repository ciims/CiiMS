<?php

/**
 * This is the model class for table "cards".
 *
 * The followings are the available columns in table 'cards':
 * @property integer $id
 * @property string $name
 * @property string $uid
 * @property string $data
 * @property string $created
 */
class Cards extends CiiModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cards';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, uid, created', 'required'),
			array('name', 'length', 'max'=>150),
			array('uid', 'length', 'max'=>20),
			array('data', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, uid, data, created', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'name' => 'Name',
			'uid' => 'Uid',
			'data' => 'Data',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('uid',$this->uid,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cards the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
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

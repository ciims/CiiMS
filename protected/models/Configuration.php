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
class Configuration extends CiiModel
{	
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
            // The following rule is used by search().
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
            'key'     => Yii::t('ciims.models.Configuration', 'Key'),
            'value'   => Yii::t('ciims.models.Configuration', 'Value'),
            'created' => Yii::t('ciims.models.Configuration', 'Created'),
            'updated' => Yii::t('ciims.models.Configuration', 'Updated'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('t.key',$this->key,true);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('updated',$this->updated,true);
		$criteria->order = "created DESC";
		
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Generates a unique id
     * @return string
     */
    public function generateUniqueId()
    {
        $rnd_id = crypt(uniqid(mt_rand(),1)); 
        $rnd_id = strip_tags(stripslashes($rnd_id)); 
        $rnd_id = str_replace(".","",$rnd_id); 
        $rnd_id = strrev(str_replace("/","",$rnd_id)); 
        $rnd_id = str_replace("$", '', substr($rnd_id,0,20));

        return $rnd_id;
    }

    /**
     * This will do a full recursive deletion of a card from bothe the filesystem and from
     * @param  string $name The folder name in runtiome
     * @return boolean      If the recursive delete was successful or not
     */
    public function fullDelete($name)
    {
        $path = Yii::getPathOfAlias('application.runtime.cards.' . $name);
        try {
            if ($this->removeDirectory($path))
                $this->delete();

        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * This is a beyond terrifying function that recursivly removes everything in a directory
     *
     * Permanently.
     *
     * Do not call this function directory. ALWAYS sanatize the user's input before attempting to run this. Running this WITHOUT proper santization
     * can result in rm -rf /*. Don't do that.
     * 
     * @param  string  $directory The directory we want to recursively delete
     * @return boolean            If this was successful for not
     */
    protected function removeDirectory($directory)
    {
        $fileHelper = new CFileHelper;
        $files = $fileHelper->findFiles($directory);

        // Delete all the files only in that directory
        foreach ($files as $file)
            unlink($file);

        // Remove all directories in that folder
        $this->removeDirectoryRecursively($directory);

        // If we can delete the root directory, delete the card
        if (rmdir($directory . "/"))
            return $this->delete();

        // Otherwise, hard fail
        return false;
    }

    /**
     * This is a less terrifying function that can only delete directories. Fortunatly, PHP can't do recursive deletes if a file still exists in the folder
     * So this isn't nearly as terrifying.
     * 
     * @param  string $directory The directory that we want to delete
     */
    protected function removeDirectoryRecursively($directory)
    {
        $mainDir = glob($directory . '/*' , GLOB_ONLYDIR);
 
        if (count($mainDir) != 0)
        {
            foreach ($mainDir as $dir)
            {
                $this->removeDirectoryRecursively($dir);
                rmdir($dir);
            }
        }

        return;
    }
}

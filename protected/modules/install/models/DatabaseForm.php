<?php

/**
 * DatabaseForm class.
 * DatabaseForm is a data keeping structure for storing information about the database
 *
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @package CiiMS https://www.github.com/charlesportwoodii/CiiMS
 * @license MIT License
 * @copyright 2011-2014 Charles R. Portwood II
 *
 * @notice  This file is part of CiiMS, and likely will not function without the necessary CiiMS classes
 */
class DatabaseForm extends CFormModel
{
    /**
     * @var string $username
     * Username we want to connect to
     */
    public $username;
    
    /**
     * @var string $password
     * Password we want to connection with
     */     
    public $password;
    
    /**
     * @var string $host
     * The host we want to connect to
     */
    public $host = '127.0.0.1';
    
    /**
     * @var string $dbname
     * The database name we want to connect to
     */
    public $dbname;
    
    /**
     * @var string $dsn
     * Data connection string
     */
    public $dsn;
    
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('username, host, dbname', 'required'),
            array('password', 'safe'),
        );
    }

    /**
     * Validator for connection to MySQL
     * @return bool     Whether or not we could connect to MySQL
     */
    public function validateConnection()
    {
        // Make sure all fields are provided
        if ($this->validate())
        {
            // Just turning the connection on and off. A CDbException will be thrown if something goes wrong
            try
            {
                $connection = new CDbConnection("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
                $connection->setActive(true);
                $connection->setActive(false);
				$this->dsn = $connection->connectionString;
                return true;
            }
            catch (Exception $e)
            {
                // Add errors to all fields for the visual indicator
                $this->addError('username', '');
                $this->addError('password', '');
                $this->addError('dbname', '');
                $this->addError('host', '');
                
                $this->addError('dsn',Yii::t('Install.main', 'Unable to connect to database using the provided credentials.'));
                return false;
            }
        }
        $this->addError('dsn',Yii::t('Install.main', 'Unable to connect to database using the provided credentials.'));
        return false;
    }
    
    /**
     * Gets the CDbConnection
     * @return CDbConnection
     */
    public function getConnection()
    {
        return new CDbConnection("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
    }
    
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'username' => Yii::t('Install.main','Username'),
            'password' => Yii::t('Install.main','Password'),
            'dbname'   => Yii::t('Install.main','Database Name'),
            'host'     => Yii::t('Install.main','Database Host'),
        );
    }
}

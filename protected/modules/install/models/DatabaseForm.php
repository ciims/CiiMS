<?php

/**
 * DatabaseForm class.
 * DatabaseForm is a data keeping structure for storing information about the database
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
    public $host;
    
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
            array('username, password, host, dbname', 'required')
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
                
                $this->addError('dsn', 'Unable to connect to database using the provided credentials.');
                return false;
            }
        }
        $this->addError('dsn', 'Unable to connect to database using the provided credentials.');
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
            'username' => 'Username',
            'password' => 'Password',
            'dbname'   => 'Database Name',
            'host'     => 'Database Host',
        );
    }
}
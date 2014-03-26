<?php

/**
 *
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @package CiiMS https://www.github.com/charlesportwoodii/CiiMS
 * @license MIT License
 * @copyright 2011-2014 Charles R. Portwood II
 *
 * @notice  This file is part of CiiMS, and likely will not function without the necessary CiiMS classes
 */
class UserForm extends CFormModel
{
    /**
     * @var string $email
     * The user's email address
     */
    public $email;
    
    /**
     * @var string $password
     * The users password
     */
    public $password;
    
    /**
     * @var string $firstName
     * The user's First Name
     */
    public $firstName;
    
    /**
     * @var string $lastName
     * The user's Last Name
     */
    public $lastName;
    
    /**
     * @var string $displayName
     * The user's desired display name
     */
    public $displayName;
    
    /**
     * @var string $siteName
     * Kinda unrelated to the user, but this is our only change to get what they want the Site Name to be
     */
    public $siteName;
    
    /**
     * @var string $encryptionKey
     * This is our private encryption key which we are going to store in our config file
     * It is very important that this doesn't get leaked out, otherwise you could fake user authentication
     * Hashing is one way though, so you shouldn't be able to derive the user's password unless you brute force the algorithm
     */
    public $encryptionKey;
    
    /**
     * @var string $encryptedPassword
     * This is the encrypted password that will be placed into the database
     */
    public $encryptedPassword;
    
    /**
     * @var bool $isConfigDirWritable
     * Makes sure the configuration directory is writable.
     */
    public $isConfigDirWritable = false;
    
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('email, password, firstName, lastName, displayName, siteName', 'required'),
            array('email', 'email'),
            array('email, password, firstName, lastName, displayName, siteName', 'length', 'max'=>255),
            array('isConfigDirWritable', 'boolean', 'trueValue'=>true),
            array('isConfigDirWritable', 'checkConfigDir')
        );
    }
    
    /**
     * Attribute labels
     * @see CModel::attributeLabels
     */
    public function attributeLabels()
    {
        return array(
            'email' => Yii::t('Install.main','Email Address'),
            'password' => Yii::t('Install.main','Password'),
            'firstName' => Yii::t('Install.main','First Name'),
            'lastName' => Yii::t('Install.main','Last Name'),
            'displayName' => Yii::t('Install.main','Display Name'),
            'siteName' => Yii::t('Install.main','Site Name')
        );
    }
    
    /**
     * Validates the model, and provides the encrypted data stream for hashing
     * @return bool     If the model validated or not
     */
    public function validateForm()
    {
        // Validates the model
        if ($this->validate())
        {
            // Getters and setters don't work in CFormModel? So set them manually
            $this->encryptionKey        = $this->getEncryptionKey();
            $this->encryptedPassword    = $this->getEncryptedPassword();
            return true;
        }
        
        return false;
    }

    /**
     * This method will save the admin user into the database,
     */
    public function save()
    {
        if (!$this->validateForm())
            return false;
        
        try
        {
            // Store some data in session temporarily
            Yii::app()->session['encryptionKey']    = $this->encryptionKey;
            Yii::app()->session['siteName']         = $this->siteName;
            Yii::app()->session['primaryEmail']     = $this->email;
            
            // Try to save the record into the database
            $connection = new CDbConnection(Yii::app()->session['dsn']['dsn'], Yii::app()->session['dsn']['username'], Yii::app()->session['dsn']['password']);
            $connection->setActive(true);
            $connection->createCommand('INSERT INTO users (id, email, password, firstName, lastName, displayName, user_role, status, created, updated) VALUES (1, :email, :password, :firstName, :lastName, :displayName, 9, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP())')
                       ->bindParam(':email',        $this->email)
                       ->bindParam(':password',     $this->encryptedPassword)
                       ->bindParam(':firstName',    $this->firstName)
                       ->bindParam(':lastName',     $this->lastName)
                       ->bindParam(':displayName',  $this->displayName)
                       ->execute();
            return true;
        }
        catch (CDbException $e)
        {
            $this->addError('password', Yii::t('Install.main','There was an error saving your details to the database.'));
            return false;
        }
        
        return false;
    }
    
    /**
     * Validator for checkConfigDir
     */
    public function checkConfigDir()
    {
        if (is_writable(dirname(__FILE__) . '/../../../config'))
            return true;
        
        $this->addError('isConfigDirWritable', Yii::t('Install.main','Configuration directory is not writable. This must be corrected before your settings can be applied.'));
        return false;
    }
    
    /**
     * Generates secure encryption key which we can use for salting passwords
     * @return string   Hashed 120 characters long
     */
    public function getEncryptionKey()
    {
        return mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5(time() . md5(time())))) . hash("sha512", time()) . time()), 0, 120);
    }
    
    /**
     * Returns the password in encrypted form
     * @return string   Hashed password
     */
    public function getEncryptedPassword()
    {
        $hash = $this->encryptHash($this->email, $this->password, $this->encryptionKey);
        return password_hash($hash, PASSWORD_BCRYPT, array('cost' => 13));
    }
    
    /**
     * Creates an encrypted hash to be used as a password
     * @param string $email     The user email
     * @param string $password  The password to be encrypted
     * @param string $_dbsalt   The salt value to be used (Yii::app()->params['encryptionKey'])
     * @return 64 character encrypted string
     */
    private function encryptHash($email, $password, $_dbsalt)
    {
        return mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5($password . md5($email)))) . hash("sha512", md5($password . md5($_dbsalt))) . $_dbsalt), 0, 64);   
    }
}

<?php

/**
 * CiiOpenCloud Component
 * Assists in managing CiiOpenCloud Resources (namely)
 */
class CiiOpenCloud extends CComponent
{
	// Identity URL
	public $identity		= NULL;

	// Openstack Username
	public $username		= NULL;

	// Openstack APIKey
	public $apiKey			= NULL;

	// Whether or not we should use rackspace cloudfiles
	public $useRackspace 	= false;

	// Default region
	public $region 			= 'IAD';

	// Client interface
	private $_client = NULL;

	// Service interface
	private $_service = NULL;

	// Container
	private $_container = NULL;

	// Control file for certain overrides
	private $_overrideControl = array();

	// A list of valid extensions
	public $allowedExtensions = array(
        'png',
        'jpeg',
        'jpg',
        'gif',
        'bmp'
    );

    protected $_file = false;

	/**
	 * Constructor for CiiOpenCloud Utility Helper
	 * @param string  $username     Openstack Username
	 * @param string  $apiKey       Openstack APIKey
	 * @param boolean $useRackspace Whether or not we should use Rackspace CLoudfiles
	 * @param url     $identity     Identity URL
	 * @param string  $region       Default region we should store assets in
	 */
	public function __construct($username, $apiKey, $useRackspace = false, $identity = NULL, $region='IAD')
	{
		$this->identity = $identity;
		$this->username = $username;
		$this->apiKey = $apiKey;
		$this->useRackspace = $useRackspace;
		$this->region = $region;
	}

	/**
	 * Retrieves the Openstack interface client
	 * @return self::$_client
	 */
	public function getClient()
	{
		if ($this->_client != NULL)
			return $this->_client;

		if ($this->useRackspace)
		{
			if ($this->identity == NULL)
				$this->identity = OpenCloud\Rackspace::US_IDENTITY_ENDPOINT;

			$this->_client = new OpenCloud\Rackspace($this->identity, array(
	            'username' => $this->username,
	            'apiKey'   => $this->apiKey
	        ));
		}
		else
		{
			$this->_client = new OpenCloud\OpenStack($this->identity, array(
	            'username' => $this->username,
	            'apiKey'   => $this->apiKey
			));
		}

		return $this->_client;
	}

	/**
	 * Retrieves the cloudfiles service
	 * @return self::$_service
	 */
	public function getService()
	{
		$this->_service = $this->getClient()->objectStoreService('cloudFiles', $this->region);
		return $this->_service;
	}

	/**
	 * Retrieves the cloudfiles container by name
	 * @param  string $name The name of the contianer
	 * @return Openstack\Container object
	 */
	public function getContainer($name=NULL)
	{
		if ($this->_container != NULL)
			return $this->_container;

		if ($name == NULL)
			return $this->_container = false;

        $this->_container = $this->getService()->getContainer($name);

        // Enforce the quote if one is defined in our override control
        if (($container_max_size = Cii::get($this->_overrideControl, 'max_container_size', 0)) != 0)
        	$this->_container->setBytesQuota($container_max_size);

        return $this->_container;
	}

	/**
	 * Uploads a file to an OpenStack Conainter
	 */
	public function uploadFile()
	{
		// Validate the container
		$this->_container = $this->getContainer();
		if ($this->_container == NULL)
			return array('error' => Yii::t('ciims.misc', 'Unable to attach OpenStack Container.'));

		$validation = $this->validateFile();
        
        if (isset($validation['error']))
        	return $validation;
        else
        {
        	$filename = $validation['data']['filename'];
        	$data = $validation['data']['data'];
        	$ext = $validation['data']['ext'];
        }

        try {
        	$response = $this->_container->uploadObject($filename.'.'.$ext, $data, array());
        	if ($response)
	        	 return array('success'=>true,'filename'=> $filename.'.'.$ext, 'url' => $this->_container->getCDN()->getMetadata()->getProperty('Ssl-Uri'));
	        else
	        	return array('error'=> Yii::t('ciims.misc', 'Could not save uploaded file. The upload was cancelled, or server error encountered'));
        } catch (Exception $e) {
        	return array('error'=> Yii::t('ciims.misc', 'The server encountered an error during uploading. Please verify that you have saufficient space in the container and that your quota has not been reached.'));
        }
	}

	/**
	 * Handles file validation
	 * @return array
	 */
	private function validateFile()
	{
		// Perform file validation
		$this->_file = isset($_FILES['file']) ? $_FILES['file'] : false;

		// Abort the upload if we have a bad file
		if (!$this->_file)
			return array('error' => Yii::t('ciims.misc', 'No files were uploaded.'));

		// Abord the upload if the file is empty
		if ($this->_file['size'] == 0)
			return array('error' => Yii::t('ciims.misc', 'File is empty'));

		if (($max_filesize = Cii::get($this->_overrideControl, 'max_filesize', 0)) != 0)
		{
			if ($this->_file['size'] > $max_filesize)
				return array('error' => Yii::t('ciims.misc', 'File is too large'));
		}

		$pathinfo = pathinfo($this->_file['name']);
        $filename = $pathinfo['filename'];

        $ext = $pathinfo['extension'];

        if(!in_array(strtolower($ext), $this->allowedExtensions))
        {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => Yii::t('ciims.misc', "File has an invalid extension, it should be one of {{these}}.", array('{{these}}' => $these)));
        }

        $filename = 'upload-'. md5(md5($filename) . rand(10, 99) . time());

        $data = fopen($this->_file['tmp_name'], 'r+');

        return array('success' => true, 'data' => array('filename' => $filename, 'data' => $data, 'ext' => $ext));
	}
}
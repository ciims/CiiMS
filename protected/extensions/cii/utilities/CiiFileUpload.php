<?php

class CiiFileUpload
{

	// The Content ID
	private $_id = NULL;

	// Whether or not we should promote the content or not
	private $_promote = false;

	// The end response
	private $_response = NULL;

	// The response object
	public $_result = array();

	/**
	 * Constructor for handling uploads
	 * @param int $id      The content id
	 * @param int $promote Whether or not the image should be promoted or not
	 */
	public function __construct($id, $promote=0)
	{
		$this->_id = $id;
		$this->_promote = $promote;
	}

	/**
     * Handles all uploads
     */
	public function uploadFile()
	{
        if (defined('CII_CONFIG') && isset(Yii::app()->params['CiiMS']['upload_class']))
        {
            $className = Yii::app()->params['CiiMS']['upload_class'];
            $class = new $className(Yii::app()->params['CiiMS']);
            $this->_result = $class->upload();
            $this->_response = $this->_handleResourceUpload($this->_result['url']);
        }
        elseif (Cii::getConfig('useOpenstackCDN'))
            $this->_response = $this->_uploadCDNFile();
        else
            $this->_response = $this->_uploadFile();

        return $this->_response;
	}

	/**
     * Handle normal file uploads
     * @return string
     */
    private function _uploadFile()
    {
        $path = '/';
        $folder = Yii::app()->getBasePath() .'/../uploads' . $path;

        $sizeLimit = Yii::app()->params['max_fileupload_size'];
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
        $uploader = new CiiFileUploader($allowedExtensions, $sizeLimit);

        $this->_result = $uploader->handleUpload($folder);

        return $this->_handleResourceUpload('/uploads/' . $this->_result['filename']);
    }

    /**
     * Handle CDN related Uploads
     * @return string
     */
    private function _uploadCDNFile()
    {
        if (Cii::getConfig('useRackspaceCDN'))
            $openCloud = new CiiOpenCloud(Cii::getConfig('openstack_username'), Cii::decrypt(Cii::getConfig('openstack_apikey')), true, NULL, Cii::getConfig('openstack_region'));
        else
            $openCloud = new CiiOpenCloud(Cii::getConfig('openstack_username'), Cii::decrypt(Cii::getConfig('openstack_apikey')), false, Cii::getConfig('openstack_identity'), Cii::getConfig('openstack_region'));

        $container = $openCloud->getContainer(Cii::getConfig('openstack_container'));
        $this->_result = $openCloud->uploadFile($container);

        return $this->_handleResourceUpload($this->_result['url'] . '/' . $this->_result['filename']);
    }

    /**
     * Generic function to handle all resource uploads
     * @param  string $value    The value that should be assigned to $meta->value
     * @return string
     */
    private function _handleResourceUpload($value)
    {
      if (Cii::get($this->_result,'success', false) == true)
        {
            $meta = ContentMetadata::model()->findbyAttributes(array('content_id' => $this->_id, 'key' => $this->_result['filename']));

            if ($meta == NULL)
                $meta = new ContentMetadata;

            $meta->content_id = $this->_id;
            $meta->key = $this->_result['filename'];
            $meta->value = $value;
            if ($meta->save())
            {
                if ($this->_promote)
                    $this->_promote($this->_result['filename']);
                $this->_result['filepath'] = $value;
                return htmlspecialchars(CJSON::encode($this->_result), ENT_NOQUOTES);
            }
            else
                throw new CHttpException(400,  Yii::t('Dashboard.main', 'Unable to save uploaded image.'));
        }
        else
        {
            return htmlspecialchars(CJSON::encode($this->_result), ENT_NOQUOTES);
            throw new CHttpException(400, $this->_result['error']);
        }  
    }


    /**
     * Promotes an image to blog-image
     * @param string $key   The key to be promoted
     * @return boolean      If the change was sucessfuly commited
     */
    private function _promote($key = NULL)
    {
        $promotedKey = 'blog-image';

        // Only proceed if we have valid date
        if ($this->_id == NULL || $key == NULL)
            return false;
        
        $model = ContentMetadata::model()->findByAttributes(array('content_id' => $this->_id, 'key' => $key));
        
        // If the current model is already blog-image, return true (consider it a successful promotion, even though we didn't do anything)
        if ($model->key == $promotedKey)
            return true;
        
        $model2 = ContentMetadata::model()->findByAttributes(array('content_id' => $this->_id, 'key' => $promotedKey));
        if ($model2 === NULL)
        {
            $model2 = new ContentMetadata;
            $model2->content_id = $this->_id;
            $model2->key = $promotedKey;
        }
        
        $model2->value = $model->value;
        
        if (!$model2->save())
            return false;
        
        return true;
    }
}

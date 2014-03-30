<?php

class CiiFile {

    /**
     * @param string $path
     */
    public function save($path)
    {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $path))
            return false;

        return true;
    }

    public function __get($name)
    {
        if (isset($_FILES['file'][$name]))
            return $_FILES['file'][$name];

        return NULL;
    }
}

class CiiFileUploader {

    private $allowedExtensions = array(
        'png',
        'jpeg',
        'jpg',
        'gif',
        'bmp'
    );

    private $sizeLimit = 10485760;

    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        $this->file = false;
        if (Cii::get($_FILES, 'file') !== NULL)
           $this->file = new CiiFile();
    }

    private function checkServerSettings()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            $json = CJSON::encode(array(
                'error' => Yii::t('ciims.misc', 'increase post_max_size and upload_max_filesize to {{size}}', array('{{size}}' => $size))
            ));
            die($json);
        }
    }

    /**
     * @param string $str
     */
    private function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last)
        {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     * @param string $uploadDirectory
     */
    public function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
    {
        if (!is_writable($uploadDirectory))
            return array('error' => Yii::t('ciims.misc', "{{dir}} Server error. Upload directory isn't writable.", array('{{dir}}' => $uploadDirectory)));
        
        if (!$this->file)
            return array('error' => Yii::t('ciims.misc', 'No files were uploaded.'));
        
        $size = $this->file->size;

        if ($size == 0) 
            return array('error' => Yii::t('ciims.misc', 'File is empty'));
        
        if ($size > $this->sizeLimit) 
            return array('error' => Yii::t('ciims.misc', 'File is too large'));
        
        $pathinfo = pathinfo($this->file->name);
        $filename = $pathinfo['filename'];

        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if(!in_array(strtolower($ext), $this->allowedExtensions))
        {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => Yii::t('ciims.misc', "File has an invalid extension, it should be one of {{these}}.", array('{{these}}' => $these)));
        }

        $filename = 'upload-'.md5($filename);

		if(!$replaceOldFile)
        {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext))
            {
                $filename .= rand(10, 99);
            }
	    }
		
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext))
            return array('success'=>true,'filename'=>$filename.'.'.$ext);
        else 
            return array('error'=> Yii::t('ciims.misc', 'Could not save uploaded file. The upload was cancelled, or server error encountered'));
        
    }
}

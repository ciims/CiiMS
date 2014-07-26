<?php

class CiiAssetManager extends CAssetManager
{
	protected $_published;

	public function getBasePath()
	{
		 if (php_sapi_name() === 'cli')
		 	return Yii::getPathOfAlias('webroot.assets');

		 return parent::getBasePath();
	}

	public function getBaseRelUrl()
	{
		 return parent::getBaseUrl();
	}

	/**
	 * @param string $file
	 */
	protected function generatePath($file, $hashByName=false)
	{
		$hash = $this->hash($file);

		if (defined('CII_CONFIG'))
		 	return str_replace('.', '_', CII_CONFIG).'/'.$hash;
    	return $hash;
	}

	/**
	 * @param string $path
	 */
	protected function hash($path)
	{
	    return substr(md5($path), 0, 10);
	}

	public function publish($path,$hashByName=false,$level=-1,$forceCopy=null)
	{
	    if($forceCopy===null)
	        $forceCopy=$this->forceCopy;

	    if($forceCopy && $this->linkAssets)
	        throw new CException(Yii::t('yii','The "forceCopy" and "linkAssets" cannot be both true.'));

	    if(isset($this->_published[$path]))
	        return $this->_published[$path];
	    else if(($src=realpath($path))!==false)
	    {
	        $dir=$this->generatePath($src,$hashByName);

	        $dstDir=$this->getBasePath().DIRECTORY_SEPARATOR.$dir;
	        if(is_file($src))
	        {
	            $fileName=basename($src);
	            $dstFile=$dstDir.DIRECTORY_SEPARATOR.$fileName;

	            if(!is_dir($dstDir))
	            {
	                mkdir($dstDir,$this->newDirMode,true);
	                chmod($dstDir,$this->newDirMode);
	            }

	            if($this->linkAssets && !is_file($dstFile)) symlink($src,$dstFile);
	            elseif(@filemtime($dstFile)<@filemtime($src))
	            {
	                copy($src,$dstFile);
	                chmod($dstFile,$this->newFileMode);
	            }

	            return $this->_published[$path]=$this->getBaseRelUrl()."/$dir/$fileName";
	        }
	        elseif(is_dir($src))
	        {
	            if($this->linkAssets && !is_dir($dstDir))
	            {
	                symlink($src,$dstDir);
	            }
	            elseif(!is_dir($dstDir) || $forceCopy)
	            {
	                CFileHelper::copyDirectory($src,$dstDir,array(
	                    'exclude'=>$this->excludeFiles,
	                    'level'=>$level,
	                    'newDirMode'=>$this->newDirMode,
	                    'newFileMode'=>$this->newFileMode,
	                ));
	            }

	            return $this->_published[$path]=$this->getBaseRelUrl().'/'.$dir;
	        }
	    }

	    throw new CException(Yii::t('yii','The asset "{asset}" to be published does not exist.',
	        array('{asset}'=>$path)));
	}
}
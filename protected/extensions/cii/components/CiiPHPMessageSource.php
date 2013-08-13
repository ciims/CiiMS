<?php

class CiiPHPMessageSource extends CPhpMessageSource
{
	private $_files = array();

	protected $extensionPaths;

	public function __construct()
	{
		if (Yii::app()->controller->module != NULL && Yii::app()->controller->module->name != NULL)
			$this->basePath = Yii::getPathOfAlias('application.modules.' . Yii::app()->controller->module->name . '.messages');
		else
			$this->basePath = Yii::getPathOfAlias('webroot.themes.' . Yii::app()->theme->name . '.messages');
	}

	/**
	 * Direct overload of getMessageFile
	 *
	 * This method is overloaded to allow modules, themes, and CiiMS Core to have their own unique message sources
	 * instead of having everything grouped together in protected/messages
	 * 
	 * Modules and Themes now should have their own /messages folder to store translations
	 *
	 * @param string $category   The category we want to work with
	 * @param string $language   The language we want the string translated to
	 * @return string            File path to the message translation source
	 */
	protected function getMessageFile($category,$language)
	{
	    if(!isset($this->_files[$category][$language]))
	    {
	        if(($pos=strpos($category,'.'))!==false && strpos($category,'ciims') === false)
	        {
	            $extensionClass=substr($category,0,$pos);
	            $extensionCategory=substr($category,$pos+1);

	            // First check if there's an extension registered for this class.
	            if(isset($this->extensionPaths[$extensionClass]))
	                $this->_files[$category][$language]=Yii::getPathOfAlias($this->extensionPaths[$extensionClass]).DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$extensionCategory.'.php';
	            else
	            {
	                // No extension registered, need to find it.
	                $class=new ReflectionClass($extensionClass);
	                $this->_files[$category][$language]=dirname($class->getFileName()).DIRECTORY_SEPARATOR.'messages'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$extensionCategory.'.php';
	            }
	        }
	        else
	        {
	        	if (strpos($category,'ciims') !== false)
	        	{
	        		$this->basePath = Yii::getPathOfAlias('application.messages.');
	        		$this->_files[$category][$language]=$this->basePath.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.str_replace('.', '/', str_replace('ciims.', '', $category)).'.php';
	        	}
	        	else
					$this->_files[$category][$language]=$this->basePath.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$category.'.php';
	        }
	    }

	    return $this->_files[$category][$language];
	}
}
<?php

class CiiPHPMessageSource extends CPhpMessageSource
{
	/**
	 * An array of files
	 * @var array
	 */
	private $_files = array();

	/**
	 * Where the extension path is located at
	 * @var array
	 */
	public $extensionPaths = array();

	/**
	 * Constructor
	 * Sets the default basePath to webroot.themes.{{themename}}
	 */
	public function init()
	{
        Yii::app()->language = Cii::setApplicationLanguage();
        parent::init();
		if (isset(Yii::app()->theme) && isset(Yii::app()->theme->name))
			$this->basePath = Yii::getPathOfAlias('webroot.themes.' . Yii::app()->theme->name . '.messages');
        else if (isset(Yii::app()->controller->module->id))
            $this->basePath = Yii::getPathOfAlias('application.modules.' . Yii::app()->controller->module->id);
		else
			$this->basePath = Yii::getPathOfAlias('application.modules.install');
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
	                $this->_files[$category][$language]=Yii::getPathOfAlias($this->extensionPaths[$extensionClass]).DS.$language.DS.$extensionCategory.'.php';
	            else
	            {
                    if (strpos($extensionClass, 'themes') !== false)
                    {
                    	$baseClass = explode('.', $extensionCategory);
                    	$theme = $baseClass[0];
                    	unset($baseClass[0]);
                    	$baseClass = implode('.', $baseClass);
                    	$this->_files[$category][$language] = Yii::getPathOfAlias("webroot.themes.$theme.messages").DS.$language.DS.$baseClass.'.php';
                    }
                    else 
                    {
                        // No extension registered, need to find it.
                        if (isset(Yii::app()->controller->module->id))
                            $extensionClass .= 'Module';

                        $class=new ReflectionClass($extensionClass);
                        $this->_files[$category][$language]=dirname($class->getFileName()).DS.'messages'.DS.$language.DS.$extensionCategory.'.php';
                    }
	            }
	        }
	        else
	        {
	        	if (strpos($category,'ciims.') !== false)
	        	{
	        		$this->basePath = Yii::getPathOfAlias('application.messages.');
	        		$this->_files[$category][$language]=$this->basePath.DS.$language.DS.str_replace('.', '/', str_replace('ciims.', '', $category)).'.php';
	        	}
	        	else
					$this->_files[$category][$language]=$this->basePath.DS.$language.DS.$category.'.php';
	        }
	    }

	    return $this->_files[$category][$language];
	}
}

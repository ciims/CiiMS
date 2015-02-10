<?php
/**
 * This is an alternative implementation of the MessageCommand class, designed to work with CiiMS' modules and themes
 */
Yii::import('cii.commands.CiiConsoleCommand');
class CiiMessageCommand extends CiiConsoleCommand
{
	/**
	 *
	 * @return array
	 */
	private function getArgs()
	{
		return array(
			'type'			=> 'core',
			'sourcePath'	=> Yii::getPathOfAlias('application').DS,
			'messagePath'	=> Yii::getPathOfAlias('application.messages').DS,
			'languages'		=> array('en_us'),
			'fileTypes'		=> array('php'),
			'overwrite'		=> true,
			'exclude'		=> array(
				'assets',
				'css',
				'js',
				'images',
				'.svn',
				'.gitignore',
				'.git',
				'yiilite.php',
				'yiit.php',
				'i18n/data',
				'messages',
				'vendor',
				'tests',
				'runtime'
			)
		);
	}

	public function actionThemes($name=NULL)
	{
		if ($name == NULL)
			$this->usageError('A theme was not specified for translations');

		$config = $this->getArgs();
		$config['type'] = 'theme';
		array_push($config['exclude'], 'modules');
		$config['sourcePath'] .= '..'.DS.'themes' . DS . $name . DS;
		$config['messagePath'] = $config['sourcePath'].'messages';
		$this->execute($config);
	}

	public function actionModules($name=NULL)
	{
		if ($name == NULL)
			$this->usageError('A module was not specified for translations');

		$config = $this->getArgs();
		$config['type'] = 'module';
		array_push($config['exclude'], 'themes');
		unset($config['exclude']['modules']);
		$config['sourcePath'] = Yii::getPathOfAlias('application.modules') . DS . $name . DS;
		$config['messagePath'] = $config['sourcePath'].'messages';
		$this->execute($config);
	}

	public function actionIndex()
	{
		$config = $this->getArgs();
		array_push($config['exclude'], 'modules');
		array_push($config['exclude'], 'themes');
		$this->execute($config);
		die("INDEX CALLED");
	}

	/**
	 * Execute the action.
	 * @param array $args command line parameters specific for this command
	 */
	private function execute($config)
	{
		$translator='Yii::t';
		extract($config);

		if(!isset($sourcePath,$messagePath,$languages))
			$this->usageError('The configuration file must specify "sourcePath", "messagePath" and "languages".');

		if(!is_dir($sourcePath))
			$this->usageError("The source path $sourcePath is not a valid directory.");

		if(!is_dir($messagePath))
			$this->usageError("The message path $messagePath is not a valid directory.");

		if(empty($languages))
			$this->usageError("Languages cannot be empty.");

		if(!isset($overwrite))
			$overwrite = false;

		if(!isset($removeOld))
			$removeOld = false;

		if(!isset($sort))
			$sort = true;

		$options=array();

		if(isset($fileTypes))
			$options['fileTypes']=$fileTypes;

		if(isset($exclude))
			$options['exclude']=$exclude;

		$files=CFileHelper::findFiles(realpath($sourcePath),$options);

		// Strip out all extensions EXCEPT for Cii
		foreach ($files as $k=>$file)
		{
			if (strpos($file, 'extensions') !== false && strpos($file, 'extensions/cii') === false)
				unset($files[$k]);
		}

		reset($files);

		$messages=array();

		foreach($files as $file)
		$messages=array_merge_recursive($messages,$this->extractMessages($file,$translator));

		foreach($languages as $language)
		{
			$dir=$messagePath.DS.$language;

			if(!is_dir($dir))
				@mkdir($dir);

			foreach ($messages as $category=>$msgs)
			{
				$msgs=array_values(array_unique($msgs));

				$dir = $config['messagePath'].DS.$language;
				if ($config['type']  == 'theme')
				{
					$data = explode('.', $category);
					unset($data[0]);
					$dirPath = implode(DS, $data);
				}
				else if ($config['type'] == 'module')
				{
					$data = explode('.', $category);
					unset($data[0]);
					unset($data[1]);
					$dirPath = implode(DS, $data);
				}
				else
					$dirPath = implode(DS, explode('.', $category));

				if ($dirPath == "")
					continue;
				@mkdir($dir . DS . $dirPath, 0777, true);
				@mkdir($dir.DS . $language.DS, 0777, true);
				$this->generateMessageFile($msgs,$dir.DS.$dirPath.'.php',$overwrite,$removeOld,$sort);
			}
		}
	}

	/**
	 * @param string $translator
	 */
	protected function extractMessages($fileName,$translator)
	{
		//echo $fileName;
//        echo "Extracting messages from $fileName...\n";
		$subject=file_get_contents($fileName);
		$messages=array();
		if(!is_array($translator))
			$translator=array($translator);

		foreach ($translator as $currentTranslator)
		{
			$n=preg_match_all('/\b'.$currentTranslator.'\s*\(\s*(\'[\w.\/]*?(?<!\.)\'|"[\w.]*?(?<!\.)")\s*,\s*(\'.*?(?<!\\\\)\'|".*?(?<!\\\\)")\s*[,\)]/s',$subject,$matches,PREG_SET_ORDER);

			for($i=0; $i<$n; ++$i)
			{
				if(($pos=strpos($matches[$i][1],'.'))!==false)
				{
					if (strpos($matches[$i][1],'Dashboard')!==false || strpos($matches[$i][1],'Hybridauth')!==false || strpos($matches[$i][1],'Install')!==false)
						$category='module.'.substr($matches[$i][1],1,-1);
					else if (strpos($matches[$i][1],'Theme')!==false)
						$category=$matches[$i][1];
					else
						$category=substr($matches[$i][1],$pos+1,-1);
				}
				else
					$category=substr($matches[$i][1],1,-1);


				$message=$matches[$i][2];

				$category = str_replace("'", '', $category);
				$messages[$category][]=eval("return $message;");  // use eval to eliminate quote escape
			}
		}
		return $messages;
	}

	/**
	 * @param string $fileName
	 * @param boolean $overwrite
	 * @param boolean $removeOld
	 * @param boolean $sort
	 */
	protected function generateMessageFile($messages,$fileName,$overwrite,$removeOld,$sort)
	{
		echo "Saving messages to $fileName...";
		if(is_file($fileName))
		{
			$translated=require($fileName);
			sort($messages);
			ksort($translated);
			if(array_keys($translated)==$messages)
			{
				echo "nothing new...skipped.\n";
				return;
			}

			$merged=array();
			$untranslated=array();

			foreach($messages as $message)
			{
				if(array_key_exists($message,$translated) && strlen($translated[$message])>0)
					$merged[$message]=$translated[$message];
				else
					$untranslated[]=$message;
			}

			ksort($merged);
			sort($untranslated);
			$todo=array();

			foreach($untranslated as $message)
				$todo[$message]='';

			ksort($translated);

			foreach($translated as $message=>$translation)
			{
				if(!isset($merged[$message]) && !isset($todo[$message]) && !$removeOld)
				{
					if(substr($translation,0,2)==='@@' && substr($translation,-2)==='@@')
						$todo[$message]=$translation;
					else if ($translation == '')
						$todo[$message] = '';
					else
						$todo[$message]='@@'.$translation.'@@';
				}
			}

			$merged=array_merge($todo,$merged);

			if($sort)
				ksort($merged);

			if($overwrite === false)
				$fileName.='.merged';

			echo "translation merged.\n";
		}
		else
		{
			$merged=array();
			foreach($messages as $message)
				$merged[$message]='';

			ksort($merged);
			echo "saved.\n";
		}
		$array=str_replace("\r",'',var_export($merged,true));
		$content=<<<EOD
<?php
/**
 * Message translations.
 *
 * This file is automatically generated by 'yiic ciimessage' command.
 * It contains the localizable messages extracted from source code.
 * You may modify this file by translating the extracted messages.
 *
 * Each array element represents the translation (value) of a message (key).
 * If the value is empty, the message is considered as not translated.
 * Messages that no longer need translation will have their translations
 * enclosed between a pair of '@@' marks.
 *
 * Message string can be used with plural forms format. Check i18n section
 * of the guide for details.
 *
 * NOTE, this file must be saved in UTF-8 encoding.
 */
return $array;

EOD;
		file_put_contents($fileName, $content);
	}
}

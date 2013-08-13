<?php
// Import System/Cli/Commands/MessageCommand
Yii::import('system.cli.commands.MessageCommand');

/**
 * This class overrides the default behavior of MessageCommand to ensure translation files are written out to the right spot.
 */
class CiiMessageCommand extends MessageCommand
{
	private function getArgs()
	{
		return array(
			'sourcePath'=>Yii::getPathOfAlias('application') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR,
			'messagePath'=>Yii::getPathOfAlias('application.messages'),
			'languages'=>array('ar', 'bg', 'bs', 'cs', 'de', 'el', 'es', 'fa_ir', 'fr', 'he', 'hu', 'id', 'it', 'ja', 'kk', 'ko_kr', 'lt', 'lv', 'nl', 'no', 'pl', 'pt', 'pt_br', 'ro', 'ru', 'sk', 'sr_sr', 'sr_yu', 'sv', 'ta_in', 'th', 'tr', 'uk', 'vi', 'zh_cn', 'zh_tw'),
			'fileTypes'=>array('php'),
			'overwrite'=>true,
			'exclude'=>array(
				'assets',
				'css',
				'images',
				'.svn',
				'.gitignore',
				'.git',
				'yiilite.php',
				'yiit.php',
				'/i18n/data',
				'/messages',
				'/vendors',
				'/web/js',
				'runtime',
			)
		);
	}

	/**
	 * Execute the action.
	 * @param array $args command line parameters specific for this command
	 */
	public function run($args)
	{
		$config = $this->getArgs();

		if (isset($args[0]) && $args[0] == 'themes')
			$config['sourcePath'] .= 'themes' . DIRECTORY_SEPARATOR;

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
			$sort = false;

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
			$dir=$messagePath.DIRECTORY_SEPARATOR.$language;

			if(!is_dir($dir))
				@mkdir($dir);

			foreach($messages as $category=>$msgs)
			{
				$msgs=array_values(array_unique($msgs));

				// If this is part of CiiMS Core
				if (strpos($category, 'Theme') !== false)
				{
					$originalCategory = $category;
					$category = strtolower(str_replace('Theme', '', $category));
					$dir=$messagePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$category.DIRECTORY_SEPARATOR.'messages'.DIRECTORY_SEPARATOR.$language;
					$dirPath = implode(DIRECTORY_SEPARATOR, explode('.', $category));

					@mkdir($dir . DIRECTORY_SEPARATOR . $dirPath, '777', true);
					echo $originalCategory . ' ' . $dir.DIRECTORY_SEPARATOR.$dirPath.DIRECTORY_SEPARATOR.$originalCategory.'.php' . "\n";

				}
				else
				{
					$dirPath = implode(DIRECTORY_SEPARATOR, explode('.', $category));
					echo $category . ' ' . $dir.DIRECTORY_SEPARATOR.$dirPath.'.php' . "\n";

					// Attempt to make the directories
					@mkdir($dir . DIRECTORY_SEPARATOR . $dirPath, '777', true);
					//$this->generateMessageFile($msgs,$dir.DIRECTORY_SEPARATOR.$dirPath.DIRECTORY_SEPARATOR.'.php',$overwrite,$removeOld,$sort);
				}
			}
		}
	}
}
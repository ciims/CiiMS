<?php

Yii::import('cii.commands.CiiConsoleCommand');
class CiiClassMapCommand extends CiiConsoleCommand
{
	public function actionIndex()
	{
		$data = "<?php\n";
		$data .= '$basePath = dirname(__FILE__) . \'/..\';' . "\n";
		$data .= 'Yii::$classMap = ' . "array(\n";

		$this->updateDataPath(Yii::getPathOfAlias('ext'), $data);
		$this->updateDataPath( Yii::getPathOfAlias('application.models'), $data);
		$this->updateDataPath(Yii::getPathOfAlias('application.controllers'), $data);

		$data .=  ");\n";

		$handle = fopen(Yii::getPathOfAlias('application.config') . DIRECTORY_SEPARATOR . 'classmap.php', 'w+');
		fwrite($handle, $data);
		fclose($handle);
		return;
	}

	private function updateDataPath($path, &$data)
	{
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object) {
		    if (strpos($name, '.php') !== false && strpos($name, 'gii') == false)
		    {
		        $id = str_replace('.php', '', substr( $name, strrpos( $name, '/' )+1 ));
		        if ($this->starts_with_upper($id))
		       		$data .=  "    '" . $id . "' => " . '$basePath . \'' . str_replace('/var/www/ciims/protected', '', $name) . "',\n";
		    }
		}

		return $data;
	}

	/**
	 * @param string $str
	 */
	private function starts_with_upper($str)
	{
	    $chr = mb_substr ($str, 0, 1, "UTF-8");
	    return mb_strtolower($chr, "UTF-8") != $chr;
	}
}
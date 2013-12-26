<?php

class CiiClassMapCommand extends CConsoleCommand
{
	public function run($args = array())
	{
		$data = "<?php\n";
		$data .= '$basePath = dirname(__FILE__) . \'/..\';' . "\n";
		$data .= 'Yii::$classMap = ' . "array(\n";
		$path = realpath('/var/www/ciims/protected/extensions');

		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object) {
		    if (strpos($name, '.php') !== false && strpos($name, 'gii') == false)
		    {
		        $id = str_replace('.php', '', substr( $name, strrpos( $name, '/' )+1 ));
		        if ($this->starts_with_upper($id))
		        	$data .=  "    '" . $id . "' => " . '$basePath . \'' . str_replace('/var/www/ciims/protected', '', $name) . "',\n";
		    }
		}

		$path = realpath('/var/www/ciims/protected/models');

		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object) {
		    if (strpos($name, '.php') !== false && strpos($name, 'gii') == false)
		    {
		        $id = str_replace('.php', '', substr( $name, strrpos( $name, '/' )+1 ));
		        if ($this->starts_with_upper($id))
		       		$data .=  "    '" . $id . "' => " . '$basePath . \'' . str_replace('/var/www/ciims/protected', '', $name) . "',\n";
		    }
		}

		$path = realpath('/var/www/ciims/protected/controllers');

		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object) {
		    if (strpos($name, '.php') !== false && strpos($name, 'gii') == false)
		    {
		        $id = str_replace('.php', '', substr( $name, strrpos( $name, '/' )+1 ));
		        if ($this->starts_with_upper($id))
		        	$data .=  "    '" . $id . "' => " . '$basePath . \'' . str_replace('/var/www/ciims/protected', '', $name) . "',\n";
		    }
		}

		$path = realpath('/var/www/ciims/protected/components');

		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object) {
		    if (strpos($name, '.php') !== false && strpos($name, 'gii') == false)
		    {
		        $id = str_replace('.php', '', substr( $name, strrpos( $name, '/' )+1 ));
		        if ($this->starts_with_upper($id))
		        	$data .=  "    '" . $id . "' =>" . '$basePath . \'' . str_replace('/var/www/ciims/protected', '', $name) . "',\n";
		    }
		}

		$data .=  ");\n";

		$handle = fopen(dirname(__FILE__) . '/../config/classmap.php', 'w+');
		fwrite($handle, $data);
		fclose($handle);
		return;
	}

	private function starts_with_upper($str)
	{
	    $chr = mb_substr ($str, 0, 1, "UTF-8");
	    return mb_strtolower($chr, "UTF-8") != $chr;
	}
}
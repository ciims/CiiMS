<?php

// Set the scan directory
$directory = __DIR__ . DS . '..' . DS . 'modules';
$cachedConfig = __DIR__.DS.'..'.DS.'runtime'.DS.'modules.config.php';

// Attempt to load the cached file if it exists
if (file_exists($cachedConfig))
	return require($cachedConfig);
else
{
	// Otherwise generate one, and return it
	$response = array();

	// Find all the modules currently installed, and preload them
	foreach (new IteratorIterator(new DirectoryIterator($directory)) as $filename)
	{
		// Don't import dot files
		if (!$filename->isDot() && strpos($filename->getFileName(), ".") === false)
		{
			$path = $filename->getPathname();

			if (file_exists($path.DS.'config'.DS.'main.php'))
				$response[$filename->getFilename()] = require($path.DS.'config'.DS.'main.php');
			else
				array_push($response, $filename->getFilename());
		}
	}

	$encoded = serialize($response);
	file_put_contents($cachedConfig, '<?php return unserialize(\''.$encoded.'\');');

	// return the response
	return $response;
}

<?php

Yii::import('ext.cii.commands.CiiConsoleCommand');
class CiiSetupCommand extends CiiConsoleCommand
{
    public function actionSetHostname($hostname)
    {
        $validator = new CUrlValidator;
        $isValid = $validator->validateValue($hostname);
        try {
        Yii::import('application.models.Configuration');        
            if ($isValid != false)
            {
                $config = new Configuration;
                $config->attributes = array(
                    'key' => 'hostname',
                    'value' => $hostname
                );
        
                if ($config->save())
                    return $this->log('Base URL has been set');
                else
                    return $this->log('Unable to save Base URL');            
            }
            else
                return $this->log("$hostname is not a valid URL");
        } catch (Exception $e) {
            return $this->log("Base URL is already set. CiiMS will automatically manage this for you from now on");
        }
    }

	/**
	 * Generates a new encryption key
	 * @param $overrideConfig 	boolean 	Whether or not to generate the config file
	 *							If set to true, protected/config/params.php will be generated/updated
	 *							With the newly generated hash.
	 */
	public function actionGenerateHash($overrideConfig = false)
	{
		$hash = mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5(time() . md5(time())))) . hash("sha512", time()) . time()), 0, 120);
		
		if ($overrideConfig)
		{
			// Params File Template
			$paramsTemplate = array(
				'yiiPath' => NULL,
				'encryptionKey' => NULL,
				'debug' => 0,
				'trace' => 0
			);

			$params = $paramsTemplate;

			$paramsFile = __DIR__ . '/../config/params.php';

			// If the params file already exists, import it
			if (file_exists($paramsFile))
				$params = CMap::mergeArray($paramsTemplate, require $paramsFile);

			if (empty($params))
				$params = $paramsTemplate;

			$params['encryptionKey'] = $hash;

			$fh = fopen($paramsFile, 'w+');
			fwrite($fh, "<?php return array(\n");
			foreach ($params as $key=>$value)
			{
				if (is_int($value))
					fwrite($fh, "    '$key' => " . (int)$value . ",\n");
				elseif(is_bool($value))
				{
					if ($value)
						fwrite($fh, "    '$key' => true,\n");
					else
						fwrite($fh, "    '$key' => false,\n");
				}
				else
						fwrite($fh, "    '$key' => '$value',\n");

			}
			fwrite($fh, ");");
			fclose($fh);

			$this->log("An encryption key has been added to protected/config/params.php.");
		}
		else
		{
			$this->log("Please add the following to your protected/config/main.php file's params section:");
			$this->log("'encryptionKey' => '$hash'");
		}

		return $hash;
	}

	/**
	 * Generates a new admin user if one does not already exist
	 * @param $username 	string 	The email address of the user
	 * @param $password 	string 	The password for this new user
	 */
	public function actionGenerateFirstUser($username, $password)
	{
		if (Yii::app()->params['encryptionKey'] == NULL)
			Yii::app()->params['encryptionKey'] = $this->generateHash(true);

		Yii::import('application.models.Users');

		$count = Users::model()->count();

		if ($count != 0)
			return $this->log('Admin user already exists, aborting generation');

		$connection = Yii::app()->db;
		$connection->createCommand('INSERT INTO users (id, email, password, firstName, lastName, displayName, user_role, status, created, updated) VALUES (NULL, :email, :password, NULL, NULL, "administrator", 9, 1, UTC_TIMESTAMP(), UTC_TIMESTAMP())')
                   ->bindParam(':email',        $username)
                   ->bindParam(':password',     $this->getEncryptedPassword($username, $password, Yii::app()->params['encryptionKey']))
                   ->execute();

        return $this->log("A new admin user has been created");
    }

	/**
     * Generates the appropriate hash for the user
     * @param $email 	string The user's email address
     * @param $password string The user's password
     * @param $hash 	string The provided password
     */
	private function encryptHash($email, $password, $_dbsalt)
    {
        return mb_strimwidth(hash("sha512", hash("sha512", hash("whirlpool", md5($password . md5($email)))) . hash("sha512", md5($password . md5($_dbsalt))) . $_dbsalt), 0, 64);   
    }

    /**
     * Generates an encrypted password for the suer
     * @param $email 	string The user's email address
     * @param $password string The user's password
     * @param $hash 	string The provided password
     */
    public function getEncryptedPassword($email, $password, $hash)
    {
        $hash = $this->encryptHash($email, $password, $hash);
        return password_hash($hash, PASSWORD_BCRYPT, array('cost' => 13));
    }
}

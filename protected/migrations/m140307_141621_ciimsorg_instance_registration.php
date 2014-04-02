<?php

class m140307_141621_ciimsorg_instance_registration extends CDbMigration
{
	public function safeUp()
	{
	    $curl = curl_init();
        $connection = $this->getDbConnection();

        // Retrieve the current URL either from the database (if a submission has been set) or from the installer's hostname file
        Yii::import('application.models.Configuration');
        $url = $connection->createCommand('SELECT `value` FROM configuration WHERE `key` = \'hostname\';')->queryScalar();

        if ($url == false)
        {
            $hostnameFile = Yii::getPathOfAlias('application.runtime') . DIRECTORY_SEPARATOR . 'hostname';
            if (file_exists($hostnameFile))
            {
                $url = new StdClass;
                $url->value = preg_replace("/\s+/", "", @file_get_contents($hostnameFile));
            }
            else
            {
                echo 'CiiMS\' base url is not set within the configuration. Please see the UPGRADING guide before attempting to migrate your application';
                return false;
            }
        }

        // Submit the data
        $data = array(
            'name' => 'New CiiMS Site',
            'url' => $url->value
        );

        curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS => CJSON::encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen(CJSON::encode($data))
                ),
                CURLOPT_URL => 'https://www.ciims.org/customize/default/registration',
                CURLOPT_CAINFO => Yii::getPathOfAlias('application.config.certs') . DIRECTORY_SEPARATOR . 'GeoTrustGlobalCA.cer'
        ));

        $response = CJSON::decode(curl_exec($curl));
        
        if ($response['status'] == 200)
        {
            $instance_id = Cii::get($response['response'], 'instance_id');
            $token = Cii::get($response['response'], 'token');

            $connection->createCommand('INSERT INTO `configuration` (`key`, `value`, `created`, `updated`) VALUES (\'instance_id\', :instance_id, UTC_TIMESTAMP(), UTC_TIMESTAMP()), (\'token\', :token, UTC_TIMESTAMP(), UTC_TIMESTAMP())')->bindParam(':instance_id', $instance_id)->bindParam(':token', $token)->execute();

            // Manually register the pre-bundled default theme so that it can recieve updates
            unset($curl);
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Auth-ID: ' . $instance_id,
                    'X-Auth-Token: ' . $token
                ),
                CURLOPT_URL => 'https://www.ciims.org/customize/default/addAddon/id/FfELXWAihCRHv0rYbykt',
                CURLOPT_CAINFO => Yii::getPathOfAlias('application.config.certs') . DIRECTORY_SEPARATOR . 'GeoTrustGlobalCA.cer'
            ));

            $response2 = CJSON::decode(curl_exec($curl));

            // If the API returns anything BUT a http 500 error, assume the theme was registered
            if ($response2['status'] != 500)
                return true;
            else
            {
                echo "500 Error returned by Theme registration";
                print_r($response2);
                return false;
            }
        }

        print_r($response);
        return false;
    }

	public function safeDown()
	{
		echo "m140307_141621_ciimsorg_instance_registration does not support migration down.\n";
		return false;
	}
}

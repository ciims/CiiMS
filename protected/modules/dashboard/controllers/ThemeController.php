<?php

class ThemeController extends CiiDashboardAddonController implements CiiDashboardAddonInterface
{
    /**
     * Determines if a theme is up to date
     * @param  string $id The card ID
     * @return JSON
     */
    public function actionIsUpdateAvailable($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing ID'));

        // Retrieve the value from cache if it is set
        $response = Yii::app()->cache->get($id . '_updatecheck');

        // Otherwise, retrieve it from the origin server
        if ($response === false)
        {
            if (!$this->isInstalled($id))
                throw new CHttpException(400, Yii::t('Dashboard.main', 'Theme is not installed.'));

            $this->_returnResponse = true;
            $details  = $this->actionDetails($id);
            $this->_returnResponse = false;
            
            $theme = $this->getThemeDetails($id);

            $response = array(
                'status' => 200,
                'message' => NULL,
                'response' => array(
                    'update' => $theme['addonVersion'] != $details['response']['version'],
                    'currentVersion' => $theme['addonVersion'],
                    'latestVersion' => $details['response']['version']
            ));

            // Cache the value for 4 hours
            Yii::app()->cache->set($id . '_updatecheck', $response, 14400);
        }

        // Return the response
        return parent::renderResponse($response);
    }

    /**
     * Performs a hard update of the card by downloading the package then overwriting the existing config
     * @param  string $id The card ID
     * @return JSON
     */
    public function actionUpgrade($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing ID'));

        $this->_returnResponse = true;

        // Perform a forced install
        $response = $this->actionInstall($id, true);

        // Then return the actual response as JSON
        $this->_returnResponse = false;
        return parent::renderResponse($response);
    }

    /**
     * Installs a theme from CiiMS.org
     * @param string $id    The Theme ID
     * @return JSON
     */
    public function actionInstall($id=NULL, $force = false)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing ID'));

        // Abort the installation if the addon is already installed
        if ($this->isInstalled($id) && !$force)
            throw new CHttpException(403, Yii::t('Dashboard.main', 'That addon is already installed'));

        // Set the file path
        $filePath = Yii::getPathOfAlias('webroot.themes') . DIRECTORY_SEPARATOR . $id;
        $standardPath;

        // Register the addon with this instance
        $this->_returnResponse = true;
        $this->actionRegister($id);
        $details  = $this->actionDetails($id);
        $this->_returnResponse = false;

        // Downloads the ZIP package to the "cards" directory
        $this->downloadPackage($id, $details['response']['file'], Yii::getPathOfAlias('webroot.themes'));
        $zip = new ZipArchive;

        // If we can open the file
        if ($zip->open($filePath . '.zip') === true)
        {
            // And we were able to extract it
            if ($zip->extractTo($filePath))
            {
                $zip->close();

                // Delete the ZIP file
                unlink($filePath . '.zip');

                // Load the JSON
                $json = CJSON::decode(file_get_contents($filePath . DIRECTORY_SEPARATOR . 'theme.json'));

                // Add some information to the JSON file
                $json['uuid'] = $id;
                $json['addonVersion'] = $details['response']['version'];

                // And write it to the filesystem
                file_put_contents($filePath . DIRECTORY_SEPARATOR . 'theme.json', CJSON::encode($json));

                // Fetch the folder name and set some variables
                $name = str_replace('webroot.themes.', '', $json['folder']);
                $standardPath = Yii::getPathOfAlias('webroot.themes') . DIRECTORY_SEPARATOR . $name;

                // If a theme is already installed that has that name, rename it to "$filePath-old"
                if (file_exists($standardPath) && is_dir($standardPath))
                    rename($standardPath, $filePath . "-old");

                // Rename the folder to the correct path so that CiiMS and reference the messages correctly
                rename($filePath, $standardPath);

                if ($force)
                    $this->_returnResponse = true;
                else
                    $this->_returnResponse = false;

                // Bust the cache
                Yii::app()->cache->delete('settings_themes');
                Yii::app()->cache->delete($id . '_updatecheck');

                // Remove the old directory if it exists
                if (file_exists($filePath . "-old") && is_dir($filePath . "-old"))
                {
                    $config = new Configuration;
                    $config->fullDelete($filePath . "-old", 'theme');
                }

                return parent::renderResponse(array(
                    'status' => 200, 
                    'message' => NULL, 
                    'response' => array(
                        'details' => $details['response'], 
                        'json' => $json
                )));
            }
        }

        Yii::app()->cache->delete('settings_themes');

         // If anything went wrong, do a full deletion cleanup
        if (!$force)
        {
            $config = new Configuration;
            $config->fullDelete($standardPath, 'theme');
        }

        if (file_exists($filePath . "-old") && is_dir($filePath . "-old"))
            rename($filePath . "-old", $standardPath);

        unlink($filePath . '.zip');
    
        // And throw a JSON error for the client to catch and deal with
        throw new CHttpException(500, Yii::t('Dashboard.main', 'Failed to download and install archive'));
    }

    /**
     * Deletes a theme from the system
     * @param string $id    The Theme ID
     * @return boolean
     */
    public function actionUninstall($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing ID'));

        if (!$this->isInstalled($id))
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Theme is not installed!'));

        // Retrieve the theme details
        $theme = $this->getThemeDetails($id);

        $name = str_replace('webroot.themes.', '', $theme['folder']);

        // Refuse to remove the default theme if it was selected
        if ($name == "default")
            throw new CHttpException(403, Yii::t('Dashboard.main', 'The default theme cannot be uninstalled.'));

        // Wipe the cache
        $path = Yii::getPathOfAlias($theme['folder']);

        Yii::app()->cache->delete('settings_themes');

        // Configuration has a recursive deletion method we'll want to use
        $config = new Configuration;

        // Hard delete the method
        return $config->fullDelete($path, 'theme');
    }

    /**
     * Lists all the Cards that have been installed by scanning the themes dir
     */
    public function actionInstalled()
    {
        $files = array('status' => 200, 'message' => NULL, 'response' => array());
        $directories = glob(Yii::getPathOfAlias('webroot.themes') . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);
        foreach($directories as $dir)
        {
            $theme = str_replace(Yii::getPathOfAlias('webroot.themes') . DIRECTORY_SEPARATOR, '', $dir);
            $json = CJSON::decode(file_get_contents($dir . DIRECTORY_SEPARATOR . 'theme.json'));
            $uuid = (isset($json['uuid']) && $json['uuid'] != "") ? $json['uuid'] : $theme;

            $files['response'][$uuid] = array(
                'path' => $dir,
                'name' => $theme,
                'uuid' => (isset($json['uuid']) && $json['uuid'] != "") ? $json['uuid'] : false,
            );
        }

        return parent::renderResponse($files);
    }

    /**
     * Retrieves all the cards that are NOT currently installed but are associated to this instance
     */
    public function actionUninstalled()
    {
        $this->_returnResponse = true;
        $uninstalled = array('status' => 200, 'message' => NULL, 'response' => array());

        $installed = $this->actionInstalled();
        $registered = $this->actionRegistered();

        foreach ($registered['response'] as $theme)
        {
            // We don't care about the cards that don't have a UUID or that were self installed
            // We only want to track ones installed from CiiMS.org
            $installedKeys = array_keys($installed['response']);
            if (!in_array($theme['uuid'], $installedKeys))
                $uninstalled['response'][] = $theme;
        }

        $this->_returnResponse = false;
        return parent::renderResponse($uninstalled);
    }

    /**
     * Determines if a theme is installed by referencing the list of installed themes
     * @param  string   $id     The ID of the Theme we want to look up
     * @return boolean          If the Theme is installed or not
     */
    public function isInstalled($id=NULL) 
    {
        $this->_returnResponse = true;
        
        // Retrieve all the currently installed items
        $installed = $this->actionInstalled();
        $installedKeys = array_keys($installed['response']);
        
        $this->_returnResponse = false;

        if (in_array($id, $installedKeys))
            return true;
        return false;
    }
    
    /**
     * Provides details about a specific theme installed from CiiMS.org
     * @param string $id
     * @return JSON
     */
    private function getThemeDetails($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Dashboard.main', 'Missing ID'));

        if (!$this->isInstalled($id))
                throw new CHttpException(400, Yii::t('Dashboard.main', 'Theme is not installed.'));

        $this->_returnResponse = true;
        $installed = $this->actionInstalled();
        $this->_returnResponse = false;
        
        return CJSON::decode(file_get_contents($installed['response'][$id]['path'] . DIRECTORY_SEPARATOR . "theme.json"));
    }
}

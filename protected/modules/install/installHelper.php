<?php
/**
 * This class exists to help the bootstrap installer automate a few processes such as form validation and
 * field verification. It should only be used and called during the pre-bootstrap phases.
 * This also _significantly_ declutters the pre-bootstrap installer of unnecessary PHP Code, which means it
 * remains most HTML and jQuery
 */
class InstallHelper
{
    /**
     * Checks to see if Yii exists at the provided path
     * @param string $path  Path to Yii Framework
     */
    public function pathExists($path = "")
    {
        // Use forward slashes always, should fix Windows Install issue
        $path = str_replace('\\', '/', $path);
        if ($path[strlen($path)-1] != '/')
            $path .= '/';
        
        // Check if yii.php exists
        if (file_exists($path . '/yii.php'))
        {
            $this->setPath($path);
            $this->exitWithResponse(array('pathExists' => true));
        }
        
        $this->exitWithResponse(array('pathExists' => false));
    }
    
    /**
     * Initiates the Yii download
     * @param array $data   The data we need to know to initiate the download
     */
    public function initYiiDownload(array $data = array())
    {
        try {
            // Replace pathspec
            $data['runtime'] = str_replace('\\', '/', $data['runtime']);
            if ($data['runtime'][strlen($data['runtime'])-1] != '/')
                $data['runtime'] .= '/';
            
            // Create a progress file
            file_put_contents($data['runtime'] . 'progress.txt', '0');
            
            // Global variable for progress
            global $progress;
            $GLOBALS['progress'] = $data['runtime'] . 'progress.txt';
            
            // Set the target file
            $targetFile = fopen($data['runtime'] . 'yii.zip', 'w' );
            
            // Initiate the CURL request
            $ch = curl_init( $data['remote'] );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt( $ch, CURLOPT_FILE, $targetFile );
            curl_exec( $ch );
            
            // Extract the file
            $zip = new ZipArchive;
            $res = $zip->open($data['runtime'] . 'yii.zip');
            if ($res === true)
            {
                // Extract the directory
                $zip->extractTo($data['runtime']);
                $zip->close();
                
                // Remove the file
                unlink($data['runtime'] . 'yii.zip');
                
                // Set the path and prompt for a reload
                $this->setPath($data['runtime'] .  $data['version'] . '/framework/');
                $this->exitwithResponse(array('completed' => true));
            }
            
            $this->exitwithResponse(array('completed' => false, 'status' => 1));
        } 
        catch (Exception $e)
        {
            $this->exitwithResponse(array('completed' => false, 'status' => 2));
        }
    }
    
    /**
     * Sets the YiiPath in Session so the bootstrapper can take over
     * @param string $path      The path to Yii Framework
     */
    private function setPath($path)
    {
        session_start();
        $_SESSION['config']['params']['yiiPath'] = $path;
        session_write_close();
        return;
    }

    private function setLanguage()
    {
        $language = 'en_US';
        session_start();
        // If the language is set via POST, accept it
        if (isset($_POST['_lang']))
            $language = $_SESSION['_lang'] = $_POST['_lang'];
        else if (isset($_SESSION['_lang']))
            $language = $_SESSION['_lang'];
        else
            $language = $_SESSION['_lang'] = Yii::app()->getRequest()->getPreferredLanguage();

        $_SESSION['_lang'] = $language;
        session_write_close();
    }
    
    /**
     * Returns a json_encoded response then exits the script
     * @param array $response   The data we want to return
     */
    private function exitWithResponse(array $response = array())
    {
        header('Content-type: application/json');
        echo json_encode($response);
        exit();
    }
}
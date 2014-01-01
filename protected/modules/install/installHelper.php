<?php
/**
 * This class exists to help the bootstrap installer automate a few processes such as form validation and
 * field verification. It should only be used and called during the pre-bootstrap phases.
 * This also _significantly_ declutters the pre-bootstrap installer of unnecessary PHP Code, which means it
 * remains most HTML and jQuery
 *
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @package CiiMS https://www.github.com/charlesportwoodii/CiiMS
 * @license MIT License
 * @copyright 2011-2014 Charles R. Portwood II
 */
class InstallHelper
{

    // The current State
    public $stage = 0;

    public $config;
    /**
     * Constructor
     * Sets the stage when we first load up, then tries to call a method if it exists
     */
    public function __construct($ciimsConfig)
    {
        // Sets the stage
        $this->stage = max((isset($ciimsConfig['params']['stage']) ? $ciimsConfig['params']['stage'] : 0), isset($_GET['stage']) ? $_GET['stage'] : 0);
        $this->stage = isset($e) && !empty($e) ? 10 : $this->stage;
        if ($this->stage == 10)
            header("HTTP/1.0 409 Conflict");

        $this->config = $ciimsConfig;

        // Attempts to call a request if a method is called
        if (isset($_POST['_ajax']) && isset($_POST['_method']))
            if (method_exists($this, $_POST['_method']))
                $this->$_POST['_method']($_POST['data']);
    }
    
    /**
     * Initiates the Yii download
     * @param array $data   The data we need to know to initiate the download
     */
    public function initYiiDownload(array $data = array())
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        
        try {
            // Replace pathspec
            $data['runtime'] = __DIR__ . '/../../runtime/';
            
            // Create a progress file
            file_put_contents($data['runtime'] . 'progress.txt', '0');
            
            // Global variable for progress
            global $progress;
            $GLOBALS['progress'] = $data['runtime'] . 'progress.txt';
            
            // Set the target file
            $targetFile = fopen($data['runtime'] . 'yii.zip', 'w' );
            
            // Initiate the CURL request
            $ch = curl_init($data['remote'] );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false );
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FILE, $targetFile);
            curl_exec($ch);
            
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
                $this->exitwithResponse(array('completed' => true));
            }
            
            $this->exitwithResponse(array('completed' => false, 'status' => 1));
        } 
        catch (Exception $e) {
            $this->exitwithResponse(array('completed' => false, 'status' => 2));
        }
    }

    /**
     * Loads the stage viewfile
     */
    public function getView()
    {
        if (file_exists(__DIR__ . "/views/install/{$this->stage}.php"))
            include __DIR__ . "/views/install/{$this->stage}.php";
        else
            require __DIR__ . "/views/install/error.php";
        return;
    }

    /**
     * Loads styles
     */ 
    public function getStyles()
    {
        return require __DIR__ . "/views/install/styles.php";
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

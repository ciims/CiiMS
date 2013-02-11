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
     * Constructor for class
     * Setups the default content-type to json
     */
    public function __construct()
    {
        header('Content-type: application/json');
    }
    
    /**
     * Checks to see if Yii exists at the provided path
     * @param string $path  Path to Yii Framework
     */
    public function pathExists($path = "")
    {
        // Use forward slashes always, should fix Windows Install issue
        $path = str_replace('\\', '/', $path);
        
        // Check if yii.php exists
        if (file_exists($path . '/yii.php'))
        {
            $this->setPath($path);
            $this->exitWithResponse(array('pathExists' => true));
        }
        
        $this->exitWithResponse(array('pathExists' => false));
    }
    
    /**
     * Sets the YiiPath in Session so the bootstrapper can take over
     * @param string $path      The path to Yii Framework
     */
    public function setPath($path)
    {
        $_SESSION['params']['yiiPath'] = $path;
        return;
    }
    
    /**
     * Returns a json_encoded response then exits the script
     * @param array $response   The data we want to return
     */
    private function exitWithResponse(array $response = array())
    {
        echo json_encode($response);
        exit();
    }
}
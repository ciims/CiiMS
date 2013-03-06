<?php
error_reporting(-1);
class DefaultController extends CController
{
    /**
     * @var string $layout
     * The layout we want to use for the installer
     */
    public $layout = 'main';
    
    /**
     * @var int $stage
     * The defualt stage we want to start on for this instance
     */
    public $stage = 4;
    
    /**
     * @var array $breadcrumbs
     * An array of breadcrumbs that we can index against
     */
    public $breadcrumbs = array(
        4 => 'Connect to Database',
        5 => 'Migrate Database',
        6 => 'Create Admin User',
        7 => 'Finalize Configuration',
        10 => 'Error',
    );

    /**
     * BeforeAction, sets the stage 
     * @param CAction $action
     * @see CController::beforeAction($action);
     */
    public function beforeAction($action)
    {
        // Determine what the stage should be
        $this->stage = max($this->stage, max(Yii::app()->params['stage'], Cii::get(Yii::app()->session['stage'], 0)));

        return parent::beforeAction($action);   
    }
    
    /**
     * Error Action
     * The installer shouldn't error, if this happens, flat out die and blame the developer
     */
    public function actionError()
    {
        $this->stage = 10;
        $error = array();
        if (!empty(Yii::app()->errorHandler->error))
            $error=Yii::app()->errorHandler->error;
        
        $this->render('error', array('error' => $error));
    }
    
    /**
     * Initial action the user arrives to.
     * Handles setting up the database connection
     */
    public function actionIndex()
    {
        // Set the stage to 4
        $this->stage = Yii::app()->session['stage'] = 4;
        $model = new DatabaseForm;
        
        // Assign previously set credentials
        if (Cii::get(Yii::app()->session['dsn']) != "")
            $model->attributes = Yii::app()->session['dsn'];
        
        // If a post request was sent
        if (Cii::get($_POST, 'DatabaseForm'))
        {
            $model->attributes = $_POST['DatabaseForm'];
            
            if ($model->validateConnection())
            {
                Yii::app()->session['dsn'] = $model->attributes;
                $this->redirect($this->createUrl('/migrate'));
            }
            else
            {
                Yii::app()->user->setFlash('error', '<strong>Warning!</strong> ' . $model->getError('dsn'));
            }
        }
        $this->render('index', array('model'=>$model));
    }
    
    /**
     * Handles the database migrations
     * This is the whole point/benefit of wrapping the installer in Yii. We can run CDbMigrations
     * directly from the web app itself, which means the installer is _must_ cleaner
     */
    public function actionMigrate()
    {
        // Don't let the user get to this action if they haven't setup a DSN yet.
        if (Yii::app()->session['dsn'] == "")
            $this->redirect($this->createUrl('/'));
        
        // Set the stage to 5
        $this->stage = Yii::app()->session['stage'] = 5;
        
        $this->render('migrate');
    }
	
    /**
     * This action enables us to create an admin user for CiiMS
     */
    public function actionCreateAdmin()
    {
        $this->stage = Yii::app()->session['stage'] = 6;
        
        $model = new UserForm;
        
        if (Cii::get($_POST, 'UserForm') != NULL)
        {
            $model->attributes = Cii::get($_POST, 'UserForm', array());
            if ($model->save())
                $this->redirect($this->createUrl('/admin'));
            
            $errors = $model->getErrors();
            $firstError = array_values($errors);
            Yii::app()->user->setFlash('error', '<strong>Warning!</strong> ' . $firstError[0][0]);
        }
        
        $this->render('createadmin', array('model' => $model));
    }
    
    /**
     * This action finalizes the setup by writing the config file out
     */
    public function actionAdmin()
    {
        $this->stage = Yii::app()->session['stage'] = 7;
        $this->generateConfigFile();
        
        $this->render('admin');
    }
    
    /**
     * Ajax comment to run CDbMigrations
     */
    public function actionRunMigrations()
    {
        header('Content-Type: application/json');
        
        $response = $this->runMigrationTool(Yii::app()->session['dsn']);
        
        $data = array('migrated' => false, 'details' => $response);
        
        if (strpos($response, 'Migrated up successfully.') || strpos($response, 'Your system is up-to-date.'))
            $data = array('migrated' => true, 'details' => $response);
        
        echo CJavaScript::jsonEncode($data);
        Yii::app()->end();
    }
    
    /**
     * Generates a configuration file inside our config directory
     * Writes to /config/main.php
     */
    private function generateConfigFile()
    {
        // Load the configuration file
        $defaultConfig = include(dirname(__FILE__) . '/../../../config/main.default.php');
        
        // Set our config values
        $defaultConfig['name']                                  = Yii::app()->session['siteName'];
        $defaultConfig['components']['db']['connectionString']  = Yii::app()->session['dsn']['dsn'];
        $defaultConfig['components']['db']['username']          = Yii::app()->session['dsn']['username'];
        $defaultConfig['components']['db']['password']          = Yii::app()->session['dsn']['password'];
        
        $defaultConfig['params']['yiiPath']                     = $_SESSION['config']['params']['yiiPath'];
        $defaultConfig['params']['encryptionKey']               = Yii::app()->session['encryptionKey'];
        
        // Write the configuration file out
        $fileString = "";
        $this->buildArray($defaultConfig, 0, $fileString);
        $fh = fopen(dirname(__FILE__) . '/../../../config/main.php', 'w');
        fwrite($fh, '<?php return ' . $fileString);
        fclose($fh);
    }
    
    /**
     * Recursively builds an a readable PHP array for us to read from
     * @param array $array      The array we want to transform into a string
     * @param int $level        How deep we are
     * @param &string $d        The string we will manipualate (should be an empty string)
     */
    private function buildArray($array, $level = 0, &$d)
    {
        $d.= "array(\n";
        foreach ($array as $k=>$v)
        {
            $spaces = $level * 4;
            for ($i = 0; $i < $spaces; $i++)
                $d.=' ';
            if (is_array($k))
                $this->buildArray($k, $level+1, $d);
            else if (is_array($v))
            {
                $d.= "'" . $k ."' => ";
                $this->buildArray($v, $level+1, $d);
            }
            else if (is_int($k))
                $d.="'" . $v . "',\n";
            else if (is_bool($v))
                $d.= "'" . $k . "' => " . ($v ? 'true' : 'false') .",\n";
            else
                $d.= "'" . $k . "' => '" . $v ."',\n";
        }
        $d.= ")";
        if ($level == 0)
            $d.= ';';
        else
            $d.= ",\n";
    }
	/**
	 * Runs the migration tool, effectivly installing the database an all appliciable default settings
	 */
	private function runMigrationTool(array $dsn)
	{
	    $runner=new CConsoleCommandRunner();
		$runner->commands=array(
		    'migrate' => array(
		        'class' => 'application.commands.CiiMigrateCommand',
		        'dsn' => $dsn,
		        'interactive' => 0,
		    ),
		    'db'=>array(
                'class'=>'CDbConnection',
                'connectionString' => "mysql:host={$dsn['host']};dbname={$dsn['dbname']}",
                'emulatePrepare' => true,
                'username' => $dsn['username'],
                'password' => $dsn['password'],
                'charset' => 'utf8',
            ),
		);
		
		ob_start();
		$runner->run(array(
		    'yiic',
		    'migrate'
		));
        
		return htmlentities(ob_get_clean(), null, Yii::app()->charset);
	}
}
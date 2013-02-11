<?php

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
        7 => 'Verify Permissions',
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
        if (Cii::get($_POST, 'DatabaseForm') != NULL)
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
	 * Runs the migration tool, effectivly installing the database an all appliciable default settings
	 */
	private function runMigrationTool()
	{
	    $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
	    $runner = new CConsoleCommandRunner();
	    $runner->addCommands($commandPath);
	    $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
	    $runner->addCommands($commandPath);
	    $args = array('yiic', 'migrate', '--interactive=0');
	    ob_start();
	    $runner->run($args);
	    echo htmlentities(ob_get_clean(), null, Yii::app()->charset);
	}
}
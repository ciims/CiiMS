<?php
/**
 *
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @package CiiMS https://www.github.com/charlesportwoodii/CiiMS
 * @license MIT License
 * @copyright 2011-2014 Charles R. Portwood II
 *
 * @notice  This file is part of CiiMS, and likely will not function without the necessary CiiMS classes
 */
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
                Yii::app()->user->setFlash('error', Yii::t('Install.main', '{{warning}} {{error}}', array(
                    '{{warning}}' => CHtml::tag('strong', array(), Yii::t('Install.main', 'Warning!')),
                    '{{error}}' => $model->getError('dsn')
                )));
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
        ignore_user_abort(true);
        set_time_limit(0);
        
        // Don't let the user get to this action if they haven't setup a DSN yet.
        if (Yii::app()->session['dsn'] == "")
            $this->redirect($this->createUrl('/'));
        
        $this->writeFile(Yii::getPathOfAlias('application.runtime').DS.'hostname', (Yii::app()->getRequest()->getIsSecureConnection() ? 'https://' : 'http://') . Yii::app()->getRequest()->serverName);

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
            Yii::app()->user->setFlash('error',  Yii::t('Install.main', '{{warning}} {{error}}', array(
                '{{warning}}' => CHtml::tag('strong', array(), Yii::t('Install.main', 'Warning!')),
                '{{error}}' => $firstError[0][0]
            )));
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
        // Set our config values
        $name = Yii::app()->session['siteName'];
        $dsn  = Yii::app()->session['dsn']['dsn'];
        $user = Yii::app()->session['dsn']['username'];
        $pass = Yii::app()->session['dsn']['password'];
        
        $key  = Yii::app()->session['encryptionKey'];

        $config = "<?php return array(
        'name' => '{$name}',
        'components' => array(
            'db' => array(
                'class' => 'CDbConnection',
                'connectionString' => '{$dsn}',
                'emulatePrepare' => true,
                'username' => '{$user}',
                'password' => '{$pass}',
                'charset' => 'utf8',
                'schemaCachingDuration' => '3600',
                'enableProfiling' => true,
            ),
            'cache' => array(
                'class' => 'CFileCache',
            ),
        ),
        'params' => array(
            'encryptionKey' => '{$key}',
        )
    );";
        
        // Write the configuration file out
        $this->writeFile(Yii::getPathOfAlias('application.config').DS.'main.php', $config);
    }

    /**
     * Writes data to a file
     * @param  string $filePath The path alias
     * @param  mixed  $data     The data we want to write
     */
    private function writeFile($filePath, $data)
    {
        $fh = fopen($filePath, 'w+');
        fwrite($fh, $data);
        fclose($fh);
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
<?php

class ContentController extends CiiDashboardController
{
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow authenticated admins to perform any action
                'users'=>array('@'),
                'expression'=>'Yii::app()->user->role>=7'
            ),
            array('deny',   // Prevent Editors from deleting content
                'actions' => array('delete', 'deleteMany'),
                'expression' => 'Yii::app()->user->role==7'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

	/**
	 * Default management page
     * Display all items in a CListView for easy editing
	 */
	public function actionIndex()
	{
        $preview = NULL;

		$model=new Content('search');
        $model->unsetAttributes();  // clear any default values
        if(Cii::get($_GET, 'Content') !== NULL)
            $model->attributes=$_GET['Content'];

        // Only show posts that belong to that user if they are not an editor or an admin
        if (($role =Yii::app()->user->role))
        {
            if ($role != 7 && $role != 9)
                $model->author_id = Yii::app()->user->id;
        }

        if (Cii::get($_GET, 'id') !== NULL)
            $preview = $this->loadModel($_GET['id']);

        $model->pageSize = 20;

        if (!isset(Yii::app()->session['admin_perspective']))
            Yii::app()->session['admin_perspective'] = 1;
        
        if (Cii::get($_GET, 'perspective') !== NULL)
        {
            if (in_array((int)Cii::get($_GET, 'perspective'), array(1, 2)))
                Yii::app()->session['admin_perspective'] = Cii::get($_GET, 'perspective');
        }

        if (Yii::app()->session['admin_perspective'] == 2)
            $model->pageSize = 15;

        $viewFile = 'index_' . Yii::app()->session['admin_perspective'];
        $this->render($viewFile, array(
            'model' => $model,
            'preview' => $preview
        ));
	}

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        // we only allow deletion via POST request
        // and we delete /everything/
        $command = Yii::app()->db
                      ->createCommand("DELETE FROM content WHERE id = :id")
                      ->bindParam(":id", $id, PDO::PARAM_STR)
                      ->execute();

        Yii::app()->user->setFlash('success', 'Post has been deleted');
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }
    
    /**
     * Public function to delete many records from the content table
     * TODO, add verification notice on this
     */
    public function actionDeleteMany()
    {
        $key = key($_POST);
        if (count($_POST[$key]) == 0)
            throw new CHttpException(500, 'No records were supplied to delete');
        
        foreach ($_POST[$key] as $id)
        {
            $command = Yii::app()->db
                      ->createCommand("DELETE FROM content WHERE id = :id")
                      ->bindParam(":id", $id, PDO::PARAM_STR)
                      ->execute();
        }
        
        Yii::app()->user->setFlash('success', 'Post has been deleted');
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
     * 
     * @return Content $model
	 */
	public function loadModel($id)
	{
		$model=Content::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='Content-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
    
    /**
     * Retrieves view files for a particular path
     * @param  string $theme  The theme to reference
     * @param  string $type   The view type to lookup
     * @return array $files   An array of files
     */
    private function getFiles($theme='default', $type='views')
    {
    	$folder = $type;

    	if ($type == 'view')
    		$folder = 'content';

    	$returnFiles = array();

    	if (!file_exists(YiiBase::getPathOfAlias('webroot.themes.' . $theme)))
    		$theme = 'default';

        $files = Yii::app()->cache->get($theme.'-available-' . $type);

        if ($files == NULL)
        {
            $fileHelper = new CFileHelper;
            $files = $fileHelper->findFiles(YiiBase::getPathOfAlias('webroot.themes.' . $theme .'.views.' . $folder), array('fileTypes'=>array('php'), 'level'=>0));
            Yii::app()->cache->set($theme.'-available-' . $type, $files);
        }

        foreach ($files as $file)
        {
            $f = str_replace('content', '', str_replace('/', '', str_replace('.php', '', substr( $file, strrpos( $file, '/' ) + 1 ))));
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            	$f = trim(substr($f, strrpos($f, '\\') + 1));

            if (!in_array($f, array('all', 'password', '_post')))
                $returnFiles[$f] = $f;
        }
        
        return $returnFiles;
    }

    /**
     * Retrieves the available view files under the current theme
     * @return array    A list of files by name
     */
    private function getViewFiles($theme='default')
    {
    	return $this->getFiles($theme, 'views');
    }
    
    /**
     * Retrieves the available layouts under the current theme
     * @return array    A list of files by name
     */
    private function getLayouts($theme='default')
    {
        return $this->getFiles($theme, 'layouts');
    }
}
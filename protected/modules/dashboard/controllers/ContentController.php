<?php

class ContentController extends CiiDashboardController
{
	/**
	 * Default management page
     * Display all items in a CListView for easy editing
	 */
	public function actionIndex()
	{
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = 10;	
		
		$criteria=new CDbCriteria;
        $criteria->order = 'created DESC';
        $criteria->limit = $pageSize;
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)")
		         ->addCondition('type_id >= 2')
		         ->addCondition('password = ""')
		         ->addCondition('status = 1');
		
		$itemCount = Content::model()->count($criteria);
		$pages=new CPagination($itemCount);
		$pages->pageSize=$pageSize;
		
		$criteria->offset = $criteria->limit*($pages->getCurrentPage());
		$data = Content::model()->findAll($criteria);
		$pages->applyLimit($criteria);
		

        if (!isset(Yii::app()->session['admin_perspective']))
            Yii::app()->session['admin_perspective'] = 1;

		$viewFile = 'index_' . Yii::app()->session['admin_perspective'];

		$this->render($viewFile, array('data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
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
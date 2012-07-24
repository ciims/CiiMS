<?php

class DefaultController extends ACiiController
{
	
	public function actionIndex()
	{
		$files = Yii::app()->cache->get('admin-card-files');
		if ($files == NULL)
		{
			$this->pageTitle = str_replace(ucwords($this->id), '', $this->pageTitle) . 'Dashboard';
			$fileHelper = new CFileHelper;
			$files = $fileHelper->findFiles(dirname(__FILE__).'/../views/default/cards', array('fileTypes'=>array('php'), 'level'=>0));
			Yii::app()->cache->set('admin-card-files', $files);
		}
		$this->render('index', array('files'=>$files));
	}
}

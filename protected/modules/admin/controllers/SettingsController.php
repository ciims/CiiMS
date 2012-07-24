<?php

class SettingsController extends ACiiController
{

	
	public function beforeAction($action)
	{
		$this->menu = array(
			array('label'=>'Configuration Options'),
			array('label'=>'New Settings', 'url'=>Yii::app()->createUrl('admin/settings/save')),
		);
		return parent::beforeAction($action);
		
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionSave($id=NULL)
	{
		if ($id == NULL)
			$model=new Configuration;
		else
			$model=$this->loadModel($id);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Configuration']))
		{
			$model->attributes=$_POST['Configuration'];
			if($model->save())
				$this->redirect(array('save','id'=>$model->key));
		}

		$this->render('save',array(
			'model'=>$model,
			'id'=>$id
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
			$this->loadModel($id)->delete();

			Yii::app()->user->setFlash('success', 'Setting has been deleted.');
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Configuration('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Configuration']))
			$model->attributes=$_GET['Configuration'];

		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Configuration::model()->findByAttributes(array('key'=>$id));
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='configuration-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

<?php

class CategoriesController extends ACiiController
{
	public function beforeAction($action)
	{
		return parent::beforeAction($action);
		
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionSave($id=NULL)
	{
		if ($id == NULL)
			$model = new Categories;
		else
			$model=$this->loadModel($id);
		
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Categories']))
		{
			$model->attributes=$_POST['Categories'];
			if($model->save())
			{
				Yii::app()->user->setFlash('success', 'Category has been updated');
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}
			Yii::app()->user->setFlash('error', 'There was an error in your submission, please verify you data before trying again.');
		}
		
		$this->render('save',array(
			'model'=>$model,
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

		Yii::app()->user->setFlash('success', 'Category has been deleted.');
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Categories('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Categories']))
			$model->attributes=$_GET['Categories'];

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
		$model=Categories::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='categories-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

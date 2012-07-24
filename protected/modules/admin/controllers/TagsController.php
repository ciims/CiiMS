<?php

class TagsController extends ACiiController
{

	public function beforeAction($action)
	{
		$this->menu = array(
			array('label'=>'Content', 'url'=>Yii::app()->createUrl('admin/content')),
			array('label'=>'Categories', 'url'=>Yii::app()->createUrl('admin/categories')),
			array('label'=>'Comments', 'url'=>Yii::app()->createUrl('admin/comments')),
			array('label'=>'Tags', 'url'=>Yii::app()->createUrl('admin/tags')),
			array('label'=>'', 'url'=>array('#'))
		);
		return parent::beforeAction($action);
		
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
		
		Yii::app()->user->setFlash('success', 'Tag has been deleted');
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Tags('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tags']))
			$model->attributes=$_GET['Tags'];

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
		$model=Tags::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='tags-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

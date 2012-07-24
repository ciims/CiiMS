<?php

class CommentsController extends ACiiController
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
		$comment = $this->loadModel($id);
		$c = Content::model()->findByPk($comment->content_id);			
		$comment->delete();
		$c->comment_count = $c->comment_count - 1;
		$c->save();
		
		Yii::app()->user->setFlash('success', 'Comment has been deleted.');
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	public function actionApprove($id)
	{
		$model=$this->loadModel($id);
		
		// XOR
		$model->approved ^= 1;
		$model->save();
		
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Comments('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Comments']))
			$model->attributes=$_GET['Comments'];

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
		$model=Comments::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='comments-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

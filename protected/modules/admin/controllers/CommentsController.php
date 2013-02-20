<?php

class CommentsController extends ACiiController
{

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id=NULL)
	{
		// we only allow deletion via POST request
		$comment = $this->loadModel($id);
		$c = Content::model()->findByPk($comment->content_id);			
		$comment->delete();
		$c->comment_count = $c->comment_count - 1;
		$c->save();
		
		Yii::app()->user->setFlash('success', 'Comment has been deleted.');
	}

	public function actionApprove($id=NULL)
	{
		$model=$this->loadModel($id);
		
		if ($model->approved == -1)
			$model->approved = 1;
		else
			$model->approved ^= 1;
		$model->save();
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

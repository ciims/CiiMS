<?php

class CategoriesController extends CiiSettingsController
{
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

		if(Cii::get($_POST, 'Categories') !== NULL)
		{
			$model->attributes = Cii::get($_POST, 'Categories', array());
            $model->id = Cii::get($_POST['Categories'], 'id', NULL);
			if($model->save())
			{
				Yii::app()->user->setFlash('success', 'Category has been updated');
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}
			Yii::app()->user->setFlash('error', 'There was an error in your submission, please verify you data before trying again.');
		}
		
		$this->render('save',array('model'=>$model));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
	    if ($id === 1)
            throw new CHttpException(400, 'Cannot delete parent category');
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

		$model->pageSize = 25;
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
}
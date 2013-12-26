<?php

class CategoriesController extends CiiSettingsController
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
                            'expression'=>'Yii::app()->user->role==6||Yii::app()->user->role==9'
                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
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
			$model = Categories::model()->findByPk($id);

		if(Cii::get($_POST, 'Categories') !== NULL)
		{
			$model->attributes = Cii::get($_POST, 'Categories', array());
            $model->description = Cii::get(Cii::get($_POST, 'Categories', array()), 'description', NULL);
			if($model->save())
			{
				Yii::app()->user->setFlash('success',  Yii::t('Dashboard.main', 'Category has been updated'));
				$this->redirect(Yii::app()->createUrl('/dashboard/categories'));
			}

			Yii::app()->user->setFlash('error',  Yii::t('Dashboard.main', 'There was an error in your submission, please verify you data before trying again.'));
		}
		
		$this->render('save', array('model'=>$model));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
	    if ($id === 1)
            throw new CHttpException(400,  Yii::t('Dashboard.main', 'Cannot delete parent category'));

		// we only allow deletion via POST request
		Categories::model()->findByPk($id)->delete();

		Yii::app()->user->setFlash('success',  Yii::t('Dashboard.main', 'Category has been deleted.'));

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
}

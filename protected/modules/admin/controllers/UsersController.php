<?php

class UsersController extends ACiiController
{
	
    public function actions()
    {
        return array_merge(parent::actions(), array(
            'toggle' => array(
            'class'=>'bootstrap.actions.TbToggleAction',
            'modelName' => 'Users',
            )
        ));
    }
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Users']))
		{
			if ($_POST['Users']['password'] != '')
				$_POST['Users']['password'] = Users::model()->encryptHash($_POST['Users']['email'], $_POST['Users']['password'], Yii::app()->params['encryptionKey']);
			else
				unset($_POST['Users']['password']);
				
			$model->attributes=$_POST['Users'];
			if($model->save()) 
			{
				Yii::app()->user->setFlash('success', 'User has been updated.');
				$this->redirect(array('index','id'=>$model->id));
			}
		}

		$this->render('update',array(
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
		$m = $this->loadModel($id);
		$m->status = 0;
		$m->delete();

		Yii::app()->user->setFlash('success', 'User has been deleted');
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
            // Prevent deleting root and self
            if ($id != 1 && $id != Yii::app()->user->id)
            {
                $command = Yii::app()->db
                          ->createCommand("DELETE FROM users WHERE id = :id")
                          ->bindParam(":id", $id, PDO::PARAM_STR)
                          ->execute();
            }
        }
        
        Yii::app()->user->setFlash('success', 'Post has been deleted');
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }
    
    /**
     * Removes the metadata attribute from a user
     */
    public function actionRemoveMeta()
    {
        $id = Cii::get($_POST, 'key');
        $user = Cii::get($_POST, 'user_id');
        
        $model = UserMetadata::model()->findByAttributes(array('user_id' => $user, 'key' => $id));
        Cii::debug($model->attributes);
        if ($model == NULL)
            return false;
        
        return $model->delete();
    }
    
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Users('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$model->attributes=$_GET['Users'];

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
		$model=Users::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}

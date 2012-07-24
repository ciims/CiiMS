<?php

class ContentController extends ACiiController
{

	public function beforeAction($action)
	{
		$this->menu = array(
			array('label'=>'Content Options'),
			array('label'=>'New Post', 'url'=>Yii::app()->createUrl('admin/content/save'))
		);
		return parent::beforeAction($action);
		
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionSave($id=NULL)
	{
		$version = 0;
		
		if ($id == NULL)
		{
			$model = new Content;
			$version = 0;
		}
		else
		{
			$model=$this->loadModel($id);
			if ($model == NULL)
				throw new CHttpException(400,'We were unable to retrieve a post with that id. Please do not repeat this request again.');
			$version = sizeof(Content::model()->findAllByAttributes(array('id' => $id)));
		}

		if(isset($_POST['Content']))
		{
			$model2 = new Content;
			$model2->attributes=$_POST['Content'];
			// For some reason this isn't setting with the other data
			$model2->extract = $_POST['Content']['extract'];
			$model2->id = $id;
			$model2->vid = $model->vid+1;
			
			if($model2->save()) 
			{
				Yii::app()->user->setFlash('success', 'Content has been updated');
				$this->redirect(array('save','id'=>$model2->id));
			}
			else
			{
				Yii::app()->user->setFlash('error', 'There was an error saving your content. Please try again');
			}
		}

		$this->render('save',array(
			'model'=>$model,
			'id'=>$id,
			'version'=>$version
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
		// and we delete /everything/
		$command = Yii::app()->db->createCommand("DELETE FROM content WHERE id = :id");
		$command->bindParam(":id", $id, PDO::PARAM_STR);
		$command->execute();

		Yii::app()->user->setFlash('success', 'Post has been deleted');
		
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}
	
	/**
	 * Handles file uploading for the controller
	 */
	public function actionUpload()
	{
		if (Yii::app()->request->isPostRequest)
		{
			Yii::import("ext.EAjaxUpload.qqFileUploader");
			$path = '/';
			if ($_GET['title'] == 'blog-image')
				$path = '/blog-images/';
	        $folder=Yii::app()->getBasePath() .'/../uploads' . $path;// folder for uploaded files
	        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');//array("jpg","jpeg","gif","exe","mov" and etc...
	        $sizeLimit = 10 * 1024 * 1024;// maximum file size in bytes
	        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	        $result = $uploader->handleUpload($folder);
			
			if ($result['success'] = true)
			{
				$meta = ContentMetadata::model()->findbyAttributes(array('content_id'=>$_GET['id'], 'key'=>$_GET['title']));
				if ($meta == NULL)
					$meta = new ContentMetadata;
				$meta->content_id = $_GET['id'];
				$meta->key = $_GET['title'];
				$meta->value = '/uploads' . $path . $result['filename'];
				$meta->save();
			}
	        $return = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
 
        echo $return;
		}		
	}

	/**
	 * Displays a CMarkDownParser preview of the content to be displayed
	 */
	public function actionPreview()
	{
		$md = new CMarkdownParser();
		$this->renderPartial('preview', array('md'=>$md, 'data'=>$_POST));
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionMetaDelete($id, $key)
	{
		// we only allow deletion via POST request
		// and we delete /everything/
		ContentMetadata::model()->findByAttributes(array('content_id'=>$id, 'key'=>$key))->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Content('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Content']))
			$model->attributes=$_GET['Content'];

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
}

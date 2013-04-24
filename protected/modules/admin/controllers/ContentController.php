<?php

class ContentController extends ACiiController
{
	/**
	 * Handles the creation and editing of Content models.
     * If no id is provided, a new model will be created. Otherwise attempt to edit
     * @param int $id   The ContentId of the model we want to manipulate
	 */
	public function actionSave($id=NULL)
	{
	    // ContentVersionID
		$version = 0;
        $theme = Cii::get(Configuration::model()->findByAttributes(array('key'=>'theme')), 'value', 'default');
        $viewFiles = $this->getViewFiles($theme);
        $layouts   = $this->getLayouts($theme);
        
        // Editor Preferences
		$preferMarkdown = Configuration::model()->findByAttributes(array('key' => 'preferMarkdown'));
        if ($preferMarkdown == NULL)
            $preferMarkdown = false;
        else
            $preferMarkdown = (bool)$preferMarkdown->value;
        
        // Determine what we're doing, new model or existing one
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
            
            // Determine the version number based upon the count of existing rows
            // We do this manually to make sure we have the correct data
			$version = Content::model()->countByAttributes(array('id' => $id));
		}

		if(isset($_POST['Content']))
		{
			$model2 = new Content;
			$model2->attributes=$_POST['Content'];
            if ($_POST['Content']['password'] != "")
            	$model2->password = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(Yii::app()->params['encryptionKey']), $_POST['Content']['password'], MCRYPT_MODE_CBC, md5(md5(Yii::app()->params['encryptionKey']))));

			// For some reason this isn't setting with the other data
			$model2->extract = $_POST['Content']['extract'];
			$model2->id = $id;
			$model2->vid = $model->vid+1;
			$model2->viewFile = $_POST['Content']['view'];
            $model2->layoutFile = $_POST['Content']['layout'];

			if($model2->save()) 
			{
				Yii::app()->user->setFlash('success', 'Content has been updated');
				$this->redirect(array('save','id'=>$model2->id));
			}
			else
			{
				$model->attributes = $model2->attributes;
				$model->vid = $model2->vid-1;
				Yii::app()->user->setFlash('error', 'There was an error saving your content. Please try again');
			}
		}

		$attachmentCriteria = new CDbCriteria(array(
		    'condition' => "content_id = {$id} AND (t.key LIKE 'upload-%' OR t.key = 'blog-image')",
		    'order'     => 't.key ASC',
		    'group'     => 't.value'
		));
        
		$attachments = $id != NULL ? ContentMetadata::model()->findAll($attachmentCriteria) : NULL;
		
		$this->render('save',array(
			'model'          =>  $model,
			'id'             =>  $id,
			'version'        =>  $version,
			'preferMarkdown' =>  $preferMarkdown,
			'attachments' 	 =>  $attachments,
			'views'          =>  $viewFiles,
			'layouts'        =>  $layouts 
		));
	}

	/**
	 * Action for handling comments for a given post
	 */
	public function actionComments($id)
	{
	    if ($id == NULL)
            throw new CHttpException(400, 'Content ID is required before proceeding');
            
	    $content = Content::model()->findByPk($id);
        if ($content === NULL)
            throw new CHttpException(400, 'Comments for this content do not exists');
            
		$comments = $content->comments;
		$md = new CMarkdownParser();
		$this->render('comments', array('comments' => $comments, 'content' => $content, 'md' => $md));
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
		$command = Yii::app()->db
		              ->createCommand("DELETE FROM content WHERE id = :id")
		              ->bindParam(":id", $id, PDO::PARAM_STR)
		              ->execute();

		Yii::app()->user->setFlash('success', 'Post has been deleted');
		
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
            $command = Yii::app()->db
                      ->createCommand("DELETE FROM content WHERE id = :id")
                      ->bindParam(":id", $id, PDO::PARAM_STR)
                      ->execute();
        }
        
        Yii::app()->user->setFlash('success', 'Post has been deleted');
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

	/**
	 * Public action to add a tag to the particular model
	 * @return bool		If the insert was successful or not
	 */
	public function actionAddTag()
	{
		$id = Cii::get($_POST, 'id', NULL);
		$model = Content::model()->findByPk($id);
		if ($model == NULL)
			throw new CHttpException(400, 'Your request is invalid');
		
		return $model->addTag(Cii::get($_POST, 'keyword'));
	}
	
	/**
	 * Public action to add a tag to the particular model
	 * @return bool		If the insert was successful or not
	 */
	public function actionRemoveTag()
	{
		$id = Cii::get($_POST, 'id', NULL);
		$model = Content::model()->findByPk($id);
		if ($model == NULL)
			throw new CHttpException(400, 'Your request is invalid');
		
		return $model->removeTag(Cii::get($_POST, 'keyword'));
	}
	
    /**
     * Removes an image from a given post
     */
    public function actionRemoveImage()
    {
        $id     = Cii::get($_POST, 'id');
        $key    = Cii::get($_POST, 'key');
        
        // Only proceed if we have valid date
        if ($id == NULL || $key == NULL)
            throw new CHttpException(403, 'Insufficient data provided. Invalid request');
        
        $model = ContentMetadata::model()->findByAttributes(array('content_id' => $id, 'key' => $key));
        if ($model === NULL)
            throw new CHttpException(403, 'Cannot delete attribute that does not exist');
        
        return $model->delete();
    }
    
    /**
     * Promotes an image to blog-image
     */
    public function actionPromoteImage()
    {
        $id          = Cii::get($_POST, 'id');
        $key         = Cii::get($_POST, 'key');
        $promotedKey = 'blog-image';
        // Only proceed if we have valid date
        if ($id == NULL || $key == NULL)
            return false;
        
        $model = ContentMetadata::model()->findByAttributes(array('content_id' => $id, 'key' => $key));
        
        // If the current model is already blog-image, return true (consider it a successful promotion, even though we didn't do anything)
        if ($model->key == $promotedKey)
            return true;
        
        $model2 = ContentMetadata::model()->findByAttributes(array('content_id' => $id, 'key' => $promotedKey));
        if ($model2 === NULL)
        {
            $model2 = new ContentMetadata;
            $model2->content_id = $id;
            $model2->key = $promotedKey;
        }
        
        $model2->value = $model->value;
        
        Cii::debug($model2->attributes);
        if (!$model2->save())
            throw new CHttpException(403, 'Unable to promote image');
        
        return true;
    }
	/**
	 * Handles file uploading for the controller
	 */
	public function actionUpload($id)
	{
		if (Yii::app()->request->isPostRequest)
		{
			Yii::import("ext.EAjaxUpload.qqFileUploader");
			$path = '/';
	        $folder=Yii::app()->getBasePath() .'/../uploads' . $path;// folder for uploaded files
	        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');//array("jpg","jpeg","gif","exe","mov" and etc...
	        $sizeLimit = 10 * 1024 * 1024;// maximum file size in bytes
	        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
	        $result = $uploader->handleUpload($folder);
			
			if ($result['success'] = true)
			{
				$meta = ContentMetadata::model()->findbyAttributes(array('content_id' => $id, 'key' => $result['filename']));
				if ($meta == NULL)
					$meta = new ContentMetadata;
				$meta->content_id = $id;
				$meta->key = $result['filename'];
				$meta->value = '/uploads' . $path . $result['filename'];
				$meta->save();
				$result['filepath'] = '/uploads/' . $result['filename'];
			}
	        $return = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
 
        echo $return;
		}	
		Yii::app()->end();	
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
	 * If deletion is successful, the browser will be redirected
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
     * Sets the dashboard perspective from the list view to the card view
     */
    public function actionPerspective()
    {
        $perspective = Cii::get($_GET, 'id', 1);
        $perspective = in_array($perspective, array(1,2)) ? $perspective : 1;
        Yii::app()->session['admin_perspective'] = $perspective;
        $this->redirect($this->createUrl('/admin/content'));
    }
    
	/**
	 * Default management page
     * Display all items in a CListView for easy editing
	 */
	public function actionIndex()
	{
		$model=new Content('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Content']))
			$model->attributes=$_GET['Content'];

        if (!isset(Yii::app()->session['admin_perspective']))
            Yii::app()->session['admin_perspective'] = 1;
        
        if(Yii::app()->session['admin_perspective'] == 2)
            $model->pageSize = 20;
        $this->setLayout('contentWrapper');
        $viewFile = 'index_' . Yii::app()->session['admin_perspective'];
		$this->render($viewFile, array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
     * 
     * @return Content $model
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
    
    /**
     * Retrieves the available view files under the current theme
     * @return array    A list of files by name
     */
    private function getViewFiles($theme='default')
    {
        $files = Yii::app()->cache->get($theme.'-available-views');
        if ($files == NULL)
        {
            $fileHelper = new CFileHelper;
            $files = $fileHelper->findFiles(dirname(__FILE__).'/../../../../themes/' . $theme . '/views/content', array('fileTypes'=>array('php'), 'level'=>0));
            Yii::app()->cache->set($theme.'-available-view', $files);
        }
        $returnFiles = array();
        foreach ($files as $file)
        {
            $f = str_replace('.php', '', substr( $file, strrpos( $file, '/' )+1 ));
            if (!in_array($f, array('all', 'password')))
                $returnFiles[$f] = $f;
        }
        
        return $returnFiles;
    }
    
    /**
     * Retrieves the available layouts under the current theme
     * @return array    A list of files by name
     */
    private function getLayouts($theme='default')
    {
        $files = Yii::app()->cache->get($theme.'-available-layouts');
        if ($files == NULL)
        {
            $fileHelper = new CFileHelper;
            $files = $fileHelper->findFiles(dirname(__FILE__).'/../../../../themes/' . $theme . '/views/layouts', array('fileTypes'=>array('php'), 'level'=>0));
            Yii::app()->cache->set($theme.'-available-layouts', $files);
        }
        $returnFiles = array();
        foreach ($files as $file)
        {
            $f = str_replace('.php', '', substr( $file, strrpos( $file, '/' )+1 ));
            if (!in_array($f, array('main', 'default')))
                $returnFiles[$f] = $f;
        }
        
        return $returnFiles;
    }
}

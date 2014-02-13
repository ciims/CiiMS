<?php

class ContentController extends CiiDashboardController
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
                'expression'=>'Yii::app()->user->role>=7'
            ),
            array('deny',   // Prevent Editors from deleting content
                'actions' => array('delete', 'deleteMany'),
                'expression' => 'Yii::app()->user->role==7||Yii::app()->user->role==5'
            ),
            array('allow',   
                'actions' => array('index', 'save'),
                'expression' => 'Yii::app()->user->role==5'
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

  /**
   * Default management page
   * Display all items in a CListView for easy editing
   */
  public function actionIndex()
  {
        $preview = NULL;

        $model=new Content('search');
        $model->unsetAttributes();  // clear any default values
        if(Cii::get($_GET, 'Content') !== NULL)
            $model->attributes=$_GET['Content'];

        // Only show posts that belong to that user if they are not an editor or an admin
        if (($role =Yii::app()->user->role))
        {
            if ($role != 7 && $role != 9)
                $model->author_id = Yii::app()->user->id;
        }

        if (Cii::get($_GET, 'id') !== NULL)
            $preview = Content::model()->findByPk(Cii::get($_GET, 'id'));

        $model->pageSize = 20;

        $this->render('index', array(
            'model' => $model,
            'preview' => $preview
        ));
    }

    /**
     * Handles the creation and editing of Content models.
     * If no id is provided, a new model will be created. Otherwise attempt to edit
     * @param int $id   The ContentId of the model we want to manipulate
     */
    public function actionSave($id=NULL)
    {
        $version   = 0;
        $theme     = Cii::getConfig('theme', 'default');
        $viewFiles = $this->getViewFiles($theme);
        $layouts   = $this->getLayouts($theme);
          
        // Editor Preferences
        $preferMarkdown = Cii::getConfig('preferMarkdown',false);

        if ($preferMarkdown == NULL)
            $preferMarkdown = false;
        else
            $preferMarkdown = (bool)$preferMarkdown;
          
        // Determine what we're doing, new model or existing one
        if ($id == NULL)
        {
            $model = new Content;
            $model->savePrototype();
            $this->redirect($this->createUrl('/dashboard/content/save/id/' . $model->id));
        }
        else
        {
            $model = Content::model()->findByPk($id);
            if ($model == NULL)
                throw new CHttpException(400,  Yii::t('Dashboard.main', 'We were unable to retrieve a post with that id. Please do not repeat this request again.'));
              
            // Determine the version number based upon the count of existing rows
            // We do this manually to make sure we have the correct data
            $version = Content::model()->countByAttributes(array('id' => $id));
        }


        $role = Yii::app()->user->role;

        if ($role != 7 && $role != 9)
        {
            if ($model->author_id != Yii::app()->user->id)
                throw new CHttpException(401, Yii::t('Dashboard.main', 'You are not authorized to perform this action.'));
        }

        if(Cii::get($_POST, 'Content') !== NULL)
        {
            $model2 = new Content;
            $model2->attributes = Cii::get($_POST, 'Content', array());
            if (Cii::get($_POST['Content'],'password', "") != "")
                $model2->password = Cii::encrypt($_POST['Content']['password']);
            else
                $model2->password = "";

            // For some reason this isn't setting with the other data
            $model2->extract    = $_POST['Content']['extract'];
            $model2->id         = $id;
            $model2->vid        = $model->vid+1;
            $model2->viewFile   = Cii::get($_POST['Content'], 'view', 'blog');
            $model2->layoutFile = Cii::get($_POST['Content'], 'layout', 'blog');
            $model2->created    = $_POST['Content']['created'];
            $model2->commentable= Cii::get($_POST['Content'], 'commentable', 1);
            $model2->type_id    = Cii::get($_POST['Content'], 'type_id', 2);

            $model2->published = gmdate('Y-m-d H:i:s', strtotime(Cii::get($_POST['Content'], 'published', NULL) . $_POST['timezone']));

            if ($model->author_id != Yii::app()->user->id)
                $model2->author_id = $model->author_id;

            // Prevent editors and collaborators from publishing acticles
            if ($role == 5 || $role == 7)
                if ($model2->status == 1)
                    $model2->status = 2;


            if($model2->save()) 
            {
                Yii::app()->user->setFlash('success',  Yii::t('Dashboard.main', 'Content has been updated.'));

                // TODO: This should eventually be an Ajax Request as part of an APIController rather than being baked into this.
                if (Yii::app()->request->isAjaxRequest)
                {
                    echo CJSON::encode($model2->attributes);
                    return true;
                }

                $this->redirect(array('save','id'=>$model2->id));
            }
            else
            {
                foreach ($model2->attributes as $k=>$v)
                    $model->$k = $v;

                $model->vid = $model2->vid-1;
                $model->addErrors($model2->getErrors());

                Yii::app()->user->setFlash('error',  Yii::t('Dashboard.main', 'There was an error saving your content. Please try again.'));
            }
        }

        $this->render('save',array(
            'model'          =>  $model,
            'id'             =>  $id,
            'version'        =>  $version,
            'preferMarkdown' =>  $preferMarkdown,
            'views'          =>  $viewFiles,
            'layouts'        =>  $layouts,
            'canPublish'     => (Yii::app()->user->role != 7 && Yii::app()->user->role != 5)
        ));
    }

    /**
     * Handles file uploading for the controller
     *
     * If successful, this will throw a 200 HTTP status code, otherwise it will throw a 400 http status code indicating the error to DropZone
     * @param int $id       The id of the content
     */
    public function actionUpload($id, $promote = 0)
    {
        if (Yii::app()->request->isPostRequest)
        {
            $result = new CiiFileUpload($id, $promote);
            echo $result->uploadFile();
        }

        Yii::app()->end();  
    }

    /**
     * Public action to add a tag to the particular model
     * @return bool     If the insert was successful or not
     */
    public function actionAddTag()
    {
        $id = Cii::get($_POST, 'id', NULL);
        $model = Content::model()->findByPk($id);
        if ($model == NULL)
            throw new CHttpException(400,  Yii::t('Dashboard.main', 'Your request is invalid'));
        
        return $model->addTag(Cii::get($_POST, 'keyword'));
    }
    
    /**
     * Public action to add a tag to the particular model
     * @return bool     If the insert was successful or not
     */
    public function actionRemoveTag()
    {
        $id = Cii::get($_POST, 'id', NULL);
        $model = Content::model()->findByPk($id);
        if ($model == NULL)
            throw new CHttpException(400,  Yii::t('Dashboard.main', 'Your request is invalid'));
        
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
            throw new CHttpException(403,  Yii::t('Dashboard.main', 'Insufficient data provided. Invalid request'));
        
        $model = ContentMetadata::model()->findByAttributes(array('content_id' => $id, 'key' => $key));
        if ($model === NULL)
            throw new CHttpException(403,  Yii::t('Dashboard.main', 'Cannot delete attribute that does not exist'));
        
        return $model->delete();
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the previous
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        Yii::app()->db->createCommand('DELETE FROM content WHERE id = :id')->bindParam(':id', $id)->execute();

        Yii::app()->user->setFlash('success',  Yii::t('Dashboard.main', 'Post has been deleted'));
        
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
            throw new CHttpException(500,  Yii::t('Dashboard.main', 'No records were supplied to delete'));
        
        foreach ($_POST[$key] as $id)
            Content::model()->findByPk($id)->delete();
        
        Yii::app()->user->setFlash('success',  Yii::t('Dashboard.main', 'Post has been deleted'));
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Retrieves the available view files under the current theme
     * @return array    A list of files by name
     */
    private function getViewFiles($theme='default')
    {
        return $this->getFiles($theme, 'views.content');
    }
    
    /**
     * Retrieves the available layouts under the current theme
     * @return array    A list of files by name
     */
    private function getLayouts($theme='default')
    {
        return $this->getFiles($theme, 'views.layouts');
    }
    
    /**
     * Retrieves view files for a particular path
     * @param  string $theme  The theme to reference
     * @param  string $type   The view type to lookup
     * @return array $files   An array of files
     */
    private function getFiles($theme='default', $type='views')
    {
        $folder = $type;

        if ($type == 'view')
            $folder = 'content';

        $returnFiles = array();

        if (!file_exists(YiiBase::getPathOfAlias('webroot.themes.' . $theme)))
            $theme = 'default';

        $files = Yii::app()->cache->get($theme.'-available-' . $type);

        if ($files === false)
        {
            $fileHelper = new CFileHelper;
            $files = $fileHelper->findFiles(Yii::getPathOfAlias('webroot.themes.' . $theme .'.' . $folder), array('fileTypes'=>array('php'), 'level'=>0));
            Yii::app()->cache->set($theme.'-available-' . $type, $files);
        }

        foreach ($files as $file)
        {
            $f = str_replace('content', '', str_replace('/', '', str_replace('.php', '', substr( $file, strrpos( $file, '/' ) + 1 ))));
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
              $f = trim(substr($f, strrpos($f, '\\') + 1));

            if (!in_array($f, array('all', 'password', '_post')))
                $returnFiles[$f] = $f;
        }
        
        return $returnFiles;
    }
}

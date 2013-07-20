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
                'expression' => 'Yii::app()->user->role==7'
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

        if (!isset(Yii::app()->session['admin_perspective']))
            Yii::app()->session['admin_perspective'] = 1;
        
        if (Cii::get($_GET, 'perspective') !== NULL)
        {
            if (in_array((int)Cii::get($_GET, 'perspective'), array(1, 2)))
                Yii::app()->session['admin_perspective'] = Cii::get($_GET, 'perspective');
        }

        if (Yii::app()->session['admin_perspective'] == 2)
            $model->pageSize = 15;

        $viewFile = 'index_' . Yii::app()->session['admin_perspective'];
        $this->render($viewFile, array(
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
            $version = 0;
        }
        else
        {
            $model = Content::model()->findByPk($id);
              
            if ($model == NULL)
                throw new CHttpException(400,'We were unable to retrieve a post with that id. Please do not repeat this request again.');
              
            // Determine the version number based upon the count of existing rows
            // We do this manually to make sure we have the correct data
            $version = Content::model()->countByAttributes(array('id' => $id));
        }

        if(Cii::get($_POST, 'Content'))
        {
            $model2 = new Content;
            $model2->attributes = Cii::get($_POST, 'Content', array());
            if ($_POST['Content']['password'] != "")
                $model2->password = Cii::encrypt($_POST['Content']['password']);

            // For some reason this isn't setting with the other data
            $model2->extract    = $_POST['Content']['extract'];
            $model2->id         = $id;
            $model2->vid        = $model->vid+1;
            $model2->viewFile   = $_POST['Content']['view'];
            $model2->layoutFile = $_POST['Content']['layout'];
            $model2->created    = $_POST['Content']['created'];

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
            'attachments'    =>  $attachments,
            'views'          =>  $viewFiles,
            'layouts'        =>  $layouts 
        ));
    }

    /**
     * Handles file uploading for the controller
     */
    public function actionUpload($id)
    {
        if (Yii::app()->request->isPostRequest)
        {
            $path = '/';
            $folder = $this->getUploadPath();

            $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
            $sizeLimit = 10 * 1024 * 1024;

            $uploader = new CiiFileUploader($allowedExtensions, $sizeLimit);

            $result = $uploader->handleUpload($folder);
            
            if (Cii::get($result,'success', false) == true)
            {
                $meta = ContentMetadata::model()->findbyAttributes(array('content_id' => $id, 'key' => $result['filename']));

                if ($meta == NULL)
                    $meta = new ContentMetadata;

                $meta->content_id = $id;
                $meta->key = $result['filename'];
                $meta->value = '/uploads' . $path . $result['filename'];
                $meta->save();
                $result['filepath'] = '/uploads/' . $result['filename'];
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
            }
            else
            {
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                throw new CHttpException(400, $result['error']);
            }

            
 
        }  

        Yii::app()->end();  
    }

    private function getUploadPath($path="/")
    {
        return Yii::app()->getBasePath() .'/../uploads' . $path;
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

        if ($files == NULL)
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

    /**
     * Retrieves the available view files under the current theme
     * @return array    A list of files by name
     */
    private function getViewFiles($theme='default')
    {
        return $this->getFiles($theme, 'views');
    }
    
    /**
     * Retrieves the available layouts under the current theme
     * @return array    A list of files by name
     */
    private function getLayouts($theme='default')
    {
        return $this->getFiles($theme, 'views.layouts');
    }
}
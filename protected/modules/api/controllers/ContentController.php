<?php

class ContentController extends ApiController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'tag')
            ),
            array('allow', 
                'actions' => array('indexPost', 'indexDelete', 'tagPost', 'tagDelete', 'drafts', 'my', 'myDrafts'),
                'expression' => '$user!=NULL&&'
            ),
            array('deny')
        );
    }

    /**
     * [GET] [/content]
     * Retrieves an article by the requested id, or retrieves all articles based upon permissions
     * @param int $id   The Content id
     */
    public function actionIndex($id=NULL)
    {
        if($id===NULL)
            return $this->getAllContent();
        else
            return $this->getContent($id); 
    }

    /**
     * [POST] [/content/<id>] [/content]
     * Creates or modifies an existing entry
     * @param int $id   The Content id
     */
    public function actionIndexPost($id=NULL)
    {
        if ($id===NULL)
            return $this->createNewPost();
        else
            return $this->updatePost($id);
    }

    /**
     * [DELETE] [/content/<id>]
     * Deletes an article
     * @param int $id   The Content id
     */
    public function actionIndexDelete($id=NULL)
    {
        if (!in_array($this->user->role->id, array(8,9)))
            throw new CHttpException(403, Yii::t('Api.content', 'You do not have permission to delete entries.'));

        return  Yii::app()->db->createCommand('DELETE FROM content WHERE id = :id')->bindParam(':id', $id)->execute();
    }

    /**
     * [GET] [/content/tag/<id>]
     * Retrieves tags for a given entry
     */
    public function actionTag($id=NULL)
    {
        $model = $this->getModel($id);
        return $model->getTags();
    }

    /**
     * [POST} [/content/tag/<id>]
     * Creates a new tag for a given entry
     */
    public function actionTagPost($id=NULL)
    {
        $model = $this->getModel($id);

        if (!($this->user->id == $model->author->id || $this->user->isEditor()))
            throw new CHttpException(403, Yii::t('Api.content', 'You do not have permission to modify tags.'));

        if ($model->addTag(Cii::get($_POST, 'tag')))
            return $model->getTags();

        return $this->returnError(400, NULL, $model->getErrors());

    }

    /**
     * [DELETE] [/content/tag/<id>]
     * Deletes a tag for a given entry
     */
    public function actionTagDelete($id=NULL, $tag=NULL)
    {
        $model = $this->getModel($id);
        if (!($this->user->id == $model->author->id || $this->user->isEditor()))
            throw new CHttpException(403, Yii::t('Api.content', 'You do not have permission to modify tags.'));

        if ($model->removeTag(Cii::get($_POST, 'tag')))
            return $model->getTags();

        return $this->returnError(400, NULL, $model->getErrors());
    }

    /**
     * [GET] [/content/drafts]
     * Retrieves All Drafts if an admin
     */
    public function actionDrafts()
    {
        $model = new Content('Search');
        $model->unsetAttributes();  // clear any default values
        unset($_GET['password']);
        unset($_GET['like_count']);
        if(!empty($_GET))
            $model->attributes=$_GET;

        // A list of attributes that we want to hide
        $attributes = array('password', 'like_count');

        $response = array();
        foreach ($model->search()->getData() as $content)
        {
            if (!$content->isPublished())
                $response[] = $content->getApiAttributes($attributes);
        }

        return $response;
    }

    /**
     * Returns all articles non drafts for the authenticated user
     */
    public function actionMy()
    {
        $model = new Content('Search');
        $model->unsetAttributes();  // clear any default values
        unset($_GET['password']);
        unset($_GET['like_count']);
        if(!empty($_GET))
            $model->attributes=$_GET;

        // A list of attributes that we want to hide
         $attributes = array('password', 'like_count');

        $model->author_id = $this->user->id;

        $response = array();
        foreach ($model->search()->getData() as $content)
        {
            if($model->author->id == $this->user->id)
                $response[] = $content->getApiAttributes($attributes);
        }

        return $response;
    }

    /**
     * Returns all drafts for a particular user
     */
    public function actionMyDrafts()
    {
        $model = new Content('Search');
        $model->unsetAttributes();  // clear any default values
        unset($_GET['password']);
        unset($_GET['like_count']);
        if(!empty($_GET))
            $model->attributes=$_GET;

        // A list of attributes that we want to hide
        $attributes = array('password', 'like_count');

        $model->author_id = $this->user->id;
        
        $response = array();
        foreach ($model->search()->getData() as $content)
        {
            if (!$content->isPublished() && $model->author->id == $this->user->id)
                $response[] = $content->getApiAttributes($attributes);
        }

        return $response;
    }

    /**
     * Creates a new entry
     */
    private function createNewPost()
    {
        if (!in_array($this->user->role->id, array(5,8,9)))
            throw new CHttpException(403, Yii::t('Api.content', 'You do not have permission to create new entries.'));

        $model = new Content;
        $model->savePrototype();
        $model->attributes = $_POST;
        $model->author_id = $this->user->id;

        if ($model->save(false))
            return $model->getApiAttributes(array('password', 'like_count'));

        return $this->returnError(400, NULL, $model->getErrors());
    }
    
    /**
     * Updates an existing entry
     * @param int $id   The Content id
     */
    private function updatePost($id)
    {
        $model = $this->getModel($id);

        if (!in_array($this->user->role->id, array(7,8,9)) || $model->author->id != $this->user->id)
            throw new CHttpException(403, Yii::t('Api.content', 'You do not have permission to create new entries.'));

        if ($this->user->role->id == 5)
            $model->author_id = $this->user->id;

        $vid = $model->vid;
        $model->attributes = $_POST;
        $model->vid = $vid++;

        if ($model->save())
            return $model->getApiAttributes(array('password', 'like_count'));
        
        return $this->returnError(400, NULL, $model->getErrors());
    }

    /**
     * Retrieves an entry by the given id
     * @param int $id   The Content id
     */
    private function getContent($id)
    {
        $model = $this->getModel($id);

        // Add restrictions if the item is not published
        if (!$model->isPublished())
        {
            // If the user is the author or an admin
            if ($this->user === NULL)
                throw new CHttpException(403, Yii::t('Api.content', 'You must be authenticated to access this action.'));

            if (!($this->user->id == $model->author->id || $this->user->role >= 7))
                throw new CHttpException(403, Yii::t('Api.content', 'You must be authenticated to access this action.'));
        }

        return $model->getApiAttributes(array('password', 'like_count'));
    }

    /**
     * Retrieves the model
     * @param  int    $id The content ID
     * @return Content
     */
    private function getModel($id=NULL)
    {
        if ($id===NULL)
            throw new CHttpException(400, Yii::t('Api.content', 'Missing id'));
        $model = Content::model()->findByPk($id);
        if ($model===NULL)
            throw new CHttpException(404, Yii::t('Api.content', 'An entry with the id of {{id}} was not found', array('{{id}}' => $id)));

        return $model;
    }

    /**
     * Retrieves all articles that are published and not password protected
     */
    private function getAllContent()
    {
        $model = new Content('Search');
        $model->unsetAttributes();  // clear any default values
        unset($_GET['password']);
        unset($_GET['like_count']);
        if(!empty($_GET))
            $model->attributes=$_GET;

        // A list of attributes that we want to hide
        $attributes = array('password', 'like_count');

        $model->status = 1;
        $response = array();
        foreach ($model->search()->getData() as $content)
        {
            if ($content->isPublished() && ($content->password == "" || Cii::decrypt($content->password) == ""))
                $response[] = $content->getApiAttributes($attributes);
        }

        return $response;
    }
}

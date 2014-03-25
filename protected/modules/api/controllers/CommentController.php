<?php

class CommentController extends ApiController
{
	/**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {   
        return array(
            array('allow',
                'actions' => array('comments', 'countPost')
            ),
        	array('allow',
        		'actions' => array('indexPost', 'flagPost'),
        		'expression' => '$user!=NULL'
        	),
            array('allow',
                'actions' => array('indexDelete'),
                'expression' => '$user!=NULL&&($user->isSiteManager()||$user->isAdmin())'
            ),
            array('deny') 
        );  
    }

    /**
     * If Disqus comments are enabled, disable the entire API
     * @param  CAction $action   The action we are using
     * @return CAction
     */
    public function beforeAction($action)
    {
    	if (Cii::getConfig('useDisqusComments'))
    		throw new CHttpException(403, Yii::t('Api.comment', 'The comment API is not available while Disqus comments are enabled.'));

        if (Cii::getConfig('useDiscourceComments'))
            throw new CHttpException(403, Yii::t('Api.comment', 'The comment API is not available while Discourse comments are enabled.'));

    	return parent::beforeAction($action);
    }

    /**
     * [GET] [/comment/comments/count]
     * Retrives comments counts for a set of ID's
     * @return array
     */
    public function actionCountPost()
    {
        if (!isset($_POST['ids']))
            throw new CHttpException(400, Yii::t('Api.comment', 'Missing ids attribute'));

        $response = array();

        $criteria = new CDbCriteria;
        $criteria->addInCondition('content_id', Cii::get($_POST, 'ids', array()));
        $criteria->select = 'content_id, count(id) as count';
        $criteria->group = 'content_id';
        $results = Comments::model()->findAll($criteria);

        foreach ($results as $result)
            $response[$result->content_id] = $result->count;
        
        return $response;
    }

    /**
     * [GET] [/comment/comments/id/<id>]
     * Retrives comments for a given content id
     * @param  int  $id   The Content id
     * @return array
     */
    public function actionComments($id=NULL)
    {
    	if ($id === NULL)
    		throw new CHttpException(400, Yii::t('Api.comment', 'Missing id'));

    	$comments = Comments::model()->findAllByAttributes(array('content_id' => $id, 'approved' => 1), array('order' => 'created DESC'));

    	if ($comments === NULL)
    		throw new CHttpException(400, Yii::t('Api.comment', 'Could not find comments for that content piece.'));

    	$response = array();
    	foreach ($comments as $comment)
    		$response[] = $comment->getApiAttributes();

    	return $response;
    }

    /**
     * [POST] [/comment/index]
     * Creates a new comment or updates an existing comment
     * @param  int  $id   The Comment id
     * @return array
     */
    public function actionIndexPost($id=NULL)
    {
    	if ($id===NULL)
    		return $this->createComment();
    	else
    		return $this->updateComment($id);
    }

    /**
     * Creates a new comment
     * TODO: Figure out how to fix the email issues
     * @param  int  $id   The Comment id
     * @return array
     */
    private function createComment()
    {
    	$model = new Comments;
    	$model->attributes = $_POST;

    	$model->approved = Cii::getConfig('autoApproveComments', 0);
        $model->user_id = $this->user->id;
        $model->parent_id = 0;

    	if ($model->save())
            return $model->getApiAttributes();

    	return $this->returnError(400, NULL, $model->getErrors());
    }

    /**
     * Updates an existing comment
     * @param  int  $id   The Comment id
     * @return array
     */
    private function updateComment($id=NULL)
    {
    	$model = $this->getCommentModel($id);

    	// Make sure the user has permission to edit this comment
    	if ($this->user->user_role != 6 && $this->user->user_role != 9)
    	{
    		if ($model->user_id != $this->user->id)
    			throw new CHttpException(403, Yii::t('Api.comment', 'You do not have permission to edit this comment'));
    	}

    	$model->attributes = $_POST;
        $model->user_id = $this->user->id;
        $model->parent_id = 0;

    	if ($model->save())
            return $model->getApiAttributes();

    	return $this->returnError(400, NULL, $model->getErrors());
    }

    /**
     * [DELETE] [/comment/index]
     * Deletes a comment
     * @param  int  $id   The Comment id
     * @return array
     */
    public function actionIndexDelete($id=NULL)
    {
    	$model = $this->getCommentModel($id);

    	// Make sure the user has permission to edit this comment
    	if ($this->user->user_role != 6 && $this->user->user_role != 9)
    	{
    		if ($model->user_id != $this->user->id)
    			throw new CHttpException(403, Yii::t('Api.comment', 'You do not have permission to edit this comment'));
    	}

    	return $model->delete();
    }

    /**
     * [POST] [/comment/index/flag/id/<id>]
     * Flags or unflags a comment. Unflagging also approves it
     * @param  int  $id   The Comment id
     * @return bool
     */
    public function actionFlagPost($id=NULL)
    {
    	$model = $this->getCommentModel($id);
        $flagee = Users::model()->findByPk($model->user_id);

        // Damage the reputation of both the person who flagged this comment and the user who is being flagged
    	$flagee->setReputation(-5);
        $this->user->setReputation(-3);

    	return $this->returnError(400, NULL, $model->getErrors());
    }

    /**
     * Retrieves the Comment model
     * @param  int    $id The comment model
     * @return Comment 
     */
    private function getCommentModel($id=NULL)
    {
    	if ($id === NULL)
    		throw new CHttpException(400, Yii::t('Api.comment', 'Missing id'));

    	$model = Comments::model()->findByPk($id);
    	if ($model === NULL)
    		throw new CHttpException(404, Yii::t('Api.comment', 'Comment #{{id}} was not found.', array('{{id}}' => $id)));

    	return $model;
    }
}
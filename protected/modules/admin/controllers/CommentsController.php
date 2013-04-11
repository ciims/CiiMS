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
		$comment = Comments::model()->findByPk($id);
        if ($comment === NULL)
            throw new CHttpException(400, 'Cannot find comment');
        
        $comment->approved = -2;
		if ($comment->save())
		    Yii::app()->user->setFlash('success', 'Comment has been deleted.');
        else
            Yii::app()->user->setFlash('warning', 'Unable to delete comment');
        
        $this->redirect($this->createUrl('/admin/content/comments/id/' . $comment->content_id));
	}
    
    /**
     * Flags or approves a particular comment
     * @param int $id   The id of the comment
     * 
     */
	public function actionApprove($id=NULL)
	{
		$comment = Comments::model()->findByPk($id);
		if ($model === NULL)
            throw new CHttpException(400, 'Unable to load comment');
        
		if ($model->approved == -1)
			$model->approved = 1;
		else
			$model->approved ^= 1;
		
        if ($model->save())
            Yii::app()->user->setFlash('success', 'Comment has been altered');
        else
            Yii::app()->user->setFlash('warning', 'Unable to un/approve comment');
        
        $this->redirect($this->createUrl('/admin/content/comments/id/' . $model->content->id));
	}
    
    /**
     * Allows the user to comment on a particular article
     * @param int $id       The id of the article we want to comment on.
     */
    public function actionComment($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, 'Content not found');
        
        $content = Content::model()->findByPk($id);
        if ($content === NULL)
            throw new CHttpException(400, 'Content does not exist');
        
        $md = new CMarkdownParser();
        
        $comment = new Comments();
        $comment->content_id = $id;
        $comment->user_id = Yii::app()->user->id;
        $comment->comment = Cii::get($_POST, 'comment', "");
        $comment->parent_id = 0;
        $comment->approved = 1;
        
        if ($comment->comment == "")
            throw new CHttpException(400, 'Comment cannot be empty');
        
        if ($comment->save())
            $this->renderPartial('comment', array('md' => $md, 'comment' => $comment));
        else
            throw new CHttpException(400, 'There were errors saving your post.');
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

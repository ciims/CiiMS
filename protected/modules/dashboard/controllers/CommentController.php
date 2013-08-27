<?php

class CommentController extends CiiDashboardController
{
    /**
     * Retrieves a renderPartial view of all comments for a particular post
     * 
     * @param  int $id      The id of the content
     * @return viewfile     Returns a renderPartial view for ThreadedComments
     */
    public function actionGetComments($id = NULL)
    {
        $this->layout = false;

        if ($id == NULL)
            throw new CHttpException(400, Yii::t('ciims.controllers.Comments', 'Unable to retrieve comments for the requested post.'));

        $comments = Comments::model()->findAllByAttributes(array('content_id' => $id));
        
        return Comments::model()->thread(array_reverse($comments), true);
    }

    /**
	 * Provides functionality to make a comment
	 */
	public function actionComment()
	{
		if (Yii::app()->request->isAjaxRequest && Cii::get($_POST, 'Comments'))
		{
			$comment = new Comments();
			$comment->attributes = array(
				'user_id'	=>	Yii::app()->user->id,
				'content_id'=>	$_POST['Comments']['content_id'],
				'comment'	=>	$_POST['Comments']['comment'],
				'parent_id'	=>	Cii::get($_POST['Comments'], 'parent_id', 0),
				'approved'	=>	Cii::getConfig('autoApproveComments', 1) == null ? 1 : Cii::getConfig('autoApproveComments', 1),
			);
			
			if ($comment->save())
			{
				// Pass the values as "now" for the comment view"
				$comment->created = $comment->updated = Yii::t('Dashboard.main', 'now');;

				// Set the attributed id to make life easier...
				header("X-Attribute-Id: {$comment->id}");
				$this->renderPartial('/content/comments', array(
					'count'=>$content->comment_count, 
					'comment'=>$comment,
					'depth' => 0,
					'md' => new CMarkdownParser
				));
			}
			else
				throw new CHttpException(400, Yii::t('Dashboard.main', 'There was an error saving your comment.'));
		}
	}

	/**
	 * Deletes a comment from the system. As of CiiMS 1.8 CiiMS now performs a hard delete and actually restructures 
	 * the parent->child tree nodes in the database.
	 * 
	 * @param  int    $id The ID of the comment
	 * @return boolean    If the update was successful
	 */
	public function actionDelete($id=NULL)
	{
		if ($id === NULL)
			throw new CHttpException(400, Yii::t('Dashboard.main', 'No comment with that id exists.'));

		$comment = Comments::model()->findByPk($id);
		$parent = $comment->parent_id;
		if ($comment->delete())
		{
			return Yii::app()->db->createCommand('UPDATE comments SET parent_id = :parent_id WHERE parent_id = :id')
					  ->bindParam(':id', $id)
					  ->bindParam(':parent_id', $parent)
					  ->execute();
		}

		throw new CHttpException(500, Yii::t('Dashboard.main', 'An error occured while trying to delete a comment.'));
	}

	/**
	 * Changes whether or not the comment should be approved or not
	 *
	 * With approval I'm not too worried about the parent->child tree being messed up, as the nodes still exist in the tree
	 * and are simply ignored.
	 * 
	 * @param  int     $id  The id of the comment
	 * @return boolean      If the comment was modified or not
	 */
	public function actionApprove($id=NULL)
	{
		if ($id === NULL)
			throw new CHttpException(400, Yii::t('Dashboard.main', 'No comment with that id exists.'));

		$comment = Comments::model()->findByPk($id);
		if ($comment->approved != 1)
			$comment->approved = 1;
		else
			$comment->approved = -1;

		return $comment->save();
	}
}
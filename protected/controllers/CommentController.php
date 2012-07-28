<?php
class CommentController extends CiiController
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl'
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow authenticated users to perform any action
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionComment()
	{
		if (Yii::app()->request->isAjaxRequest && isset($_POST))
		{
			$comment = new Comments();
			$comment->attributes = array(
				'user_id'=>Yii::app()->user->id,
				'content_id'=>$_POST['Comments']['content_id'],
				'comment'=>$_POST['Comments']['comment'],
				'parent_id'=>0,
				'approved'=>1
			);
			
			if ($comment->save())
			{
				$content = Content::model()->findByPk($_POST['Comments']['content_id']);
				$content->comment_count++;
				$content->save();
				$this->renderPartial('comment', array('count'=>$_POST['count'], 'comment'=>$comment));
			}
		}
	}
	
	public function actionFlag($id=NULL)
	{
		if (Yii::app()->request->isPostRequest)
		{
			$comment = Comments::model()->findByPk($id);
			if ($comment == NULL)
				throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
			
			$comment->approved = '-1';
			if($comment->save())
				return 1;
			else
				throw new CHttpException(400, 'Something went wrong');
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
}

?>

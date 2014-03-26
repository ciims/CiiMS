<?php

class ProfileController extends CiiSiteController
{

	/**
	 * The layout to use for this controller
	 * @var string
	 */
	public $layout = '//layouts/main';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array('accessControl'));
	}

    /**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // Allow all users to any section
				'actions' => array('index', 'badges'),
				'users'=>array('*'),
			),
			array('allow',  // deny all users
				'actions' => array('edit'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Provides functionality to view a given profile
	 * @param  int 	  $id          The ID belonging to the user
	 * @param  string $displayName The user's display name. This isn't super necessary, it just is better for SEO
	 */
	public function actionIndex($id=NULL, $displayName=NULL)
	{
		// If an ID isn't provided, throw an error
		if ($id === NULL)
			throw new CHttpException(404, Yii::t('ciims.controllers.Profile', "Oops! That user doesn't exist on our network!"));

		// For SEO, if the display name isn't in the url, reroute it
		if ($id !== NULL && $displayName === NULL)
		{
			$model = Users::model()->findByPk($id);
			if ($model === NULL || $model->status == 0)
				throw new CHttpException(404, Yii::t('ciims.controllers.Profile', "Oops! That user doesn't exist on our network!"));
			else
				$this->redirect('/profile/' . $model->id . '/' . preg_replace('/[^\da-z]/i', '', $model->displayName));
		}

		$model = Users::model()->findByPk($id);

		// Don't allow null signings or invalidated users to pollute our site
		if($model->status == 0)
			throw new CHttpException(404, Yii::t('ciims.controllers.Profile', "Oops! That user doesn't exist on our network!"));

		$this->pageTitle = $model->displayName . ' | ' . Cii::getConfig('name', Yii::app()->name);
		$postsCriteria = Content::model()->getBaseCriteria()
									     ->addCondition('type_id=2')
									     ->addCondition('password=""')
									     ->addCondition('author_id=:id');
	    $postsCriteria->params = array(
	    	':id' => $id
	    );

		$contentCount =  Content::model()->count($postsCriteria);
		$this->render('index', array('model' => $model, 'contentCount' => $contentCount));
	}

	/**
	 * Provides functionality for a user to edit their profile
	 */
	public function actionEdit()
	{
		$model = Users::model()->findByPk(Yii::app()->user->id);

		if (Cii::get($_POST, 'Users', NULL) !== NULL)
		{
			$cost = Cii::getBcryptCost();

			if ($_POST['Users']['password'] != '')
				$_POST['Users']['password'] = password_hash(Users::model()->encryptHash($_POST['Users']['email'], $_POST['Users']['password'], Yii::app()->params['encryptionKey']), PASSWORD_BCRYPT, array('cost' => $cost));
			else
				unset($_POST['Users']['password']);

			unset($_POST['Users']['status']);
			unset($_POST['Users']['user_role']);

			$model->attributes = Cii::get($_POST, 'Users', array());
			$model->about = Cii::get(Cii::get($_POST, 'Users', array()), 'about', NULL);

			if ($model->save())
			{
				Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Profile', 'Your profile has been updated!'));
				$this->redirect($this->createUrl('/profile/'. $model->id));
			}
			else
				Yii::app()->user->setFlash('warning', Yii::t('ciims.controllers.Profile', 'There were errors saving your profile. Please correct them before trying to save again.'));
		}

		$this->render('edit', array('model' => $model));
	}

	/**
	 * Provides functionality for a user to show their badges and awards that they have earned
	 */
	public function actionBadges()
	{
		$model = Users::model()->findByPk(Yii::app()->user->id);
		
		$this->render('badges', array('model' => $model));
	}
}
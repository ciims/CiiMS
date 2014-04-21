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
				'actions' => array('index'),
				'users'=>array('*'),
			),
			array('allow',  // deny all users
				'actions' => array('edit', 'resend'),
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

		$this->pageTitle = Yii::t('ciims.controllers.Profile', 'User {{user}} - CiiMS | {{sitename}}', array('{{user}}' => $model->name, '{{sitename}}' => Cii::getConfig('name', Yii::app()->name)));
		$this->render('index', array('model' => $model, 'md' => new CMarkdownParser));
	}

	/**
	 * Provides functionality for a user to edit their profile
	 */
	public function actionEdit()
	{
		$model = new ProfileForm;
        $model->load(Yii::app()->user->id);

		if (Cii::get($_POST, 'ProfileForm', NULL) !== NULL)
		{
            $model->attributes = $_POST['ProfileForm'];
            $model->password_repeat = $_POST['ProfileForm']['password_repeat'];
            $model->about = $_POST['ProfileForm']['about'];

			if ($model->save())
			{
				Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Profile', 'Your profile has been updated!'));
				$this->redirect($this->createUrl('profile/index', array(
                    'id' => $model->id,
                    'displayName' => $model->displayName
                )));
			}
			else
				Yii::app()->user->setFlash('error', Yii::t('ciims.controllers.Profile', 'There were errors saving your profile. Please correct them before trying to save again.'));
		}

		$this->render('edit', array('model' => $model));
	}

    /**
     * Send a new verification email to the user
     */
    public function actionResend()
    {
        $model = new ProfileForm;
        $model->load(Yii::app()->user->id);

        // If we don't have one on file, then someone the user got to a page they shouldn't have gotten to
        // Seamlessly redirect them back
        if ($model->getNewEmail() == NULL)
            $this->redirect(Yii::app()->user->returnUrl);

        if ($model->sendVerificationEmail())
            Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Profile', 'A new verification email has been resent to {{user}}. Please check your email address.', array(
                '{{user}}' => $model->getNewEmail()
            )));
        else
            Yii::app()->user->setFlash('error', Yii::t('ciims.controllers.Profile', 'There was an error resending the verification email. Please try again later.'));

        $this->redirect(Yii::app()->user->returnUrl);
    }
}

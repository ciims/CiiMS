<?php

class UsersController extends CiiSettingsController
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
                            'expression'=>'Yii::app()->user->role==6||Yii::app()->user->role==9'
                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if (Cii::get($_POST, 'Users', NULL) !== NULL)
		{
			$cost = Cii::getBcryptCost();

			if ($_POST['Users']['password'] != '')
				$_POST['Users']['password'] = password_hash(Users::model()->encryptHash($_POST['Users']['email'], $_POST['Users']['password'], Yii::app()->params['encryptionKey']), PASSWORD_BCRYPT, array('cost' => $cost));
			else
				unset($_POST['Users']['password']);
				
			$model->attributes=$_POST['Users'];
			$model->about = Cii::get($_POST['Users'], 'about', NULL);
			
			// Handle saving and updating of Metadata via CDbCommand
			// By wrapping this in a transaction, we can make sure all metadata is saved AND that the operation goes quickly
			if (Cii::get($_POST, 'UserMetadata') !== NULL)
			{
				$connection = Yii::app()->db;
				$transaction = $connection->beginTransaction();
				$rollback = false;
				$message = NULL;

				foreach (Cii::get($_POST, 'UserMetadata') as $k=>$v)
				{
					// Allow items to be added
					if (strpos($k, '__new') !== false)
					{
						// Prevent new API keys from being generated
						$k = str_replace('api_key', '', str_replace(' ', '_', str_replace('__new', '', $k)));
						$command = $connection->createCommand('INSERT INTO user_metadata (`key`, `value`, user_id, created, updated) VALUES (:key, :value, :id, UTC_TIMESTAMP(), UTC_TIMESTAMP())');
						$command->bindParam(':value', $v);
					}
					else if ($v == "" && $k)
					{
						$command = $connection->createCommand('DELETE FROM user_metadata WHERE `key` = :key AND user_id = :id');
					}
					else
					{
						// And updated
						$command = $connection->createCommand('UPDATE user_metadata SET `value` = :value, updated = UTC_TIMESTAMP() WHERE `key` = :key AND user_id = :id');
						$command->bindParam(':value', $v);
					}

					$k = (string)$k;
					$command->bindParam(':key', $k);
					$command->bindParam(':id', $id);
					try {
						$command->execute();
					} catch (Exception $e) {
						$transaction->rollBack();
						$message = $e->getMessage();
						$rollback = true;
						break;
					}
				}

				// Allow metadata to be saved independently of the actual model
				if (!$rollback)
					$transaction->commit();
				else
					Yii::app()->user->setFlash('error', $message);

			}

			if($model->save()) 
			{
				Yii::app()->user->setFlash('success', Yii::t('Dashboard.main', 'User has been updated.'));
				$this->redirect(array('update','id'=>$model->id));
			}
			else 
				$transaction->rollBack();
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Internal API endpoint for inviting new users to join the site
	 *
	 **/
	public function actionCreate()
	{
		$validator=new CEmailValidator;
        if (!$validator->validateValue(Cii::get($_POST, 'email', NULL)))
			throw new CHttpException(400, Yii::t('Dashboard.main', 'The email address you provided is invalid.'));

		if (Users::model()->countByAttributes(array('email' => Cii::get($_POST, 'email', NULL))))
			throw new CHttpException(400, Yii::t('Dashboard.main', 'A user with that email address already exists.'));

		$user = new Users;
		$user->attributes = array(
			'status' => Users::PENDING_INVITATION,
			'email' => Cii::get($_POST, 'email', NULL),
			'user_role' => 5,
			'about' => '',
			'password' => '',
			'displayName' => '',
			'firstName' => '',
			'lastName' => '',
		);

		$user->created = new CDbExpression('UTC_TIMESTAMP()');
		$user->updated =  new CDbExpression('UTC_TIMESTAMP()');

		// Save the user, and ignore all validation
		if ($user->save(false))
		{
			$hash = mb_strimwidth(hash("sha256", md5(time() . md5(hash("sha512", time())))), 0, 16);
			$meta = new UserMetadata;
			$meta->user_id = $user->id;
			$meta->key = 'activationKey';
			$meta->value = $hash;
			$meta->save();

			// Send an invitation email
			
			$this->sendEmail($user, Yii::t('Dashboard.email', "You've Been Invited..."), '//email/invite', array('user' => $user, 'hash' => $hash), true, true);
			// End the request
			return $this->renderPartial('/users/userList', array('data' => $user));
		}

		throw new CHttpException(400, Yii::t('Dashboard.main', 'An unexpected error occured fulfilling your request.'));
	}
    
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Users('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$model->attributes=$_GET['Users'];

		$model->pageSize = 25;

		// Retrive users who have been sent an invitation
		$criteria = new CDbCriteria;
		$criteria->addCondition('status = :status');
		$criteria->params = array(':status' => Users::PENDING_INVITATION);
		$invitees = new CActiveDataProvider('Users', array(
		    'criteria' => $criteria
		));

		$this->render('index',array(
			'model'=>$model,
			'invitees' => $invitees
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 * @param integer $id
	 */
	public function loadModel($id)
	{
		$model=Users::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('Dashboard.main', 'The requested page does not exist.'));
		return $model;
	}
}

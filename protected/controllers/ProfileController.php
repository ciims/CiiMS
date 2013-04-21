<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This controller provides basic functionality for a user to view and edit their personal profile
 *
 * PHP version 5
 *
 * MIT LICENSE Copyright (c) 2012-2013 Charles R. Portwood II
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to 
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom 
 * the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   CategoryName
 * @package    CiiMS Content Management System
 * @author     Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2013
 * @license    http://opensource.org/licenses/MIT  MIT LICENSE
 * @link       https://github.com/charlesportwoodii/CiiMS
 */
class ProfileController extends CiiController
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
		return array_merge(
			parent::filters(), 
			array(
				'accessControl'
			)
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
			throw new CHttpException(404, 'Oops! That user doesn\'t exist on our network!');

		// For SEO, if the display name isn't in the url, reroute it
		if ($id !== NULL && $displayName === NULL)
		{
			$model = Users::model()->findByPk($id);
			if ($model === NULL)
				throw new CHttpException(404, 'Oops! That user doesn\'t exist on our network!');
			else
				$this->redirect('/profile/' . $model->id . '/' . preg_replace('/[^\da-z]/i', '', $model->displayName));
		}

		$model = Users::model()->findByPk($id);

		$this->pageTitle = $model->displayName . ' | ' . Yii::app()->name;
		$contentCount =  Yii::app()->db->createCommand('SELECT content.id
													   FROM content WHERE vid = (
                                                        SELECT MAX(vid) 
                                                        FROM content AS content2 
                                                        WHERE content2.id = content.id
                                                      ) 
                                                      AND type_id = 2 AND status = 1 
                                                      AND password=""
                                                      AND content.author_id = :author_id
                                                      ORDER BY content.created DESC LIMIT 5')
                                 ->bindParam(':author_id', $id)
                                 ->queryAll();
		$this->render('index', array('model' => $model, 'contentCount' => count($contentCount)));
	}

	/**
	 * Provides functionality for a user to edit their profile
	 */
	public function actionEdit()
	{
		$model = Users::model()->findByPk(Yii::app()->user->id);

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
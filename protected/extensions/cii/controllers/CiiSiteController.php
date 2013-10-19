<?php

/**
 * This is a separate controller designed to handle items that are specific to application.controllers
 */
class CiiSiteController extends CiiController
{
	/**
	 * Before Action
	 * Checks the theme to determine if bootstrap is required. If it is, preload it
	 *
	 * This effectively elinminates bootstrap from every part of CiiMS except the DefaultTheme, and should improve performance.
	 * However we may have to rewrite a bunch of stuff in the theme layer.
	 * 
	 * @param  CAction $action    The action we are executing
	 * @see CiiController::beforeAction($action)
	 */
	public function beforeAction($action)
	{
		Yii::import('webroot.themes.' . $this->getTheme() . '.Theme');

		$theme = new Theme();

		if ($theme->useBootstrap)
			Yii::app()->getComponent("bootstrap");

		return parent::beforeAction($action);
	}
}
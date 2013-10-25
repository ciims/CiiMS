<?php

class CategoryController extends ApiController
{
	/**
	 * [actionIndex description]
	 * @return array $categories
	 */
	public function actionIndex()
	{
		if (Yii::app()->request->getRequestType() == 'POST')
		{
		}

		$categories = Categories::model()->findAll();

		$response = array();
		foreach ($categories as $category)
			$response[] = array('id' => $category->id, 'parent_id' => $category->parent_id, 'name' => $category->name, 'slug' => $category->slug);

		return $response;
	}
}
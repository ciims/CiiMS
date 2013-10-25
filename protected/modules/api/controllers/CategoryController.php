<?php

class CategoryController extends ApiController
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
                        'actions' => array('index')
                    ),
                    array('allow',
                        'actions' => array('indexPost', 'indexDelete'),
                        'expression' => '$user!=NULL&&($user->user_role==6||$user->user_role==9)'
                    ),
                    array('deny') 
            );  
    }

	/**
	 * [GET] [/categories]
	 * @return array    List of categories
	 */
	public function actionIndex($id=NULL)
	{
        if ($id !== NULL)
        {
            $category = Categories::model()->findByPk($id);
            if ($category == NULL)
                throw new CHttpException(404, Yii::t('Api.main', 'A category with {{id}} was not found.', array('{{id}}' => $id)));

            return array('id' => $category->id, 'parent_id' => $category->parent_id, 'name' => $category->name, 'slug' => $category->slug);
		}

        
        $categories = Categories::model()->findAll();
		$response = array();
		foreach ($categories as $category)
			$response[] = array('id' => $category->id, 'parent_id' => $category->parent_id, 'name' => $category->name, 'slug' => $category->slug);

		return $response;
	}

    /**
     * [POST] [/category]
     * @return array    Category
     */
    public function actionIndexPost()
    {
        $category = new Categories;
        $category->parent_id = 1;
        $category->attributes = $_POST;
        
        if ($category->save())
            return array('id' => $category->id, 'parent_id' => $category->parent_id, 'name' => $category->name, 'slug' => $category->slug);
        else
            return $category->getErrors();
    }    
}

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
            array('allow',
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
	 * [GET] [/category/<id>]
	 * @return array    List of categories
	 */
	public function actionIndex($id=NULL)
	{
        if ($id !== NULL)
        {
            $category = Categories::model()->findByPk($id);
            if ($category == NULL)
                throw new CHttpException(404, Yii::t('Api.category', 'A category with the id of {{id}} was not found.', array('{{id}}' => $id)));

            return $category->getAPIAttributes();
		}
        
        $categories = Categories::model()->findAll();
		$response = array();

		foreach ($categories as $category)
			$response[] = $category->getAPIAttributes();

		return $response;
	}

    /**
     * [POST] [/category/<id>]
     * @return array    Category
     */
    public function actionIndexPost($id=NULL)
    {
        if ($id === NULL)
        {
            $category = new Categories;
            $category->parent_id = 1;
        }
        else
        {
            $category = Categories::model()->findByPk($id); 
            if ($category == NULL)
                throw new CHttpException(404, Yii::t('Api.category', 'A category with the id of {{id}} was not found.', array('{{id}}' => $id)));
        }
        
        $category->attributes = $_POST;
        
        if ($category->save())
            return $category->getAPIAttributes();
        else
            return $category->getErrors();
    }

    /**
     * [DELETE] [/category/<id>]
     * @return boolean
     */
    public function actionIndexDelete($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, Yii::t('Api.category', 'A category id must be specified to delete.'));
        
        $category = Categories::model()->findByPk($id); 
        if ($category == NULL)
            throw new CHttpException(404, Yii::t('Api.category', 'A category with the id of {{id}} was not found.', array('{{id}}' => $id)));

        if ($category->id == 1)
            throw new CHttpException(400, Yii::t('Api.category', 'The root category cannot be deleted.'));

        return $category->delete();
    }
}

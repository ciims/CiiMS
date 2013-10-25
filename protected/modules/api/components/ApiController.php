<?php

class ApiController extends CController
{
	/**
	 * The current action
	 * @var CAction
	 */
	private $_action;

	/**
	 * The default message to return to the user
	 * @var mixed
	 */
	public $message = NULL;

	/**
	 * The default HTTP Status code to supply back to the user
	 * @var integer
	 */
	public $status = 200;

	/**
	 * Prevents caching of responses, preloads accessControl filter
	 */
	public function filters()
    {
        return array(
            array(
                'CHttpCacheFilter',
                'cacheControl'=>'public, no-store, no-cache, must-revalidate',
            ),
            'accessControl'
        );
    }

    /**
     * This is the same as CController::runAction($action), except it returns data rather than echoing it.
     * @param  CAction $action
     * @see CController::runAction($action);
     */
	public function runAction($action)
	{
	    $priorAction=$this->_action;
	    $this->_action=$action;
	    if($this->beforeAction($action))
	    {
	    	$response = $action->runWithParams($this->getActionParams());
	        if($response===false)
	            $this->invalidActionParams($action);
	        else
	            $this->afterAction($action);
	    }

	    $this->_action=$priorAction;
	    $this->renderOutput($response);
	}

	/**
	 * Outputs the data as JSON
	 * @param  array  $response the response data
	 */
	public function renderOutput($response = array())
	{
		header('Content-Type: application/json');
		echo CJSON::encode(array(
			'status' => $this->status,
			'message' => $this->message,
			'response' => $response
		));
	}

	/**
	 * Default Error Handler. Yii automatically magics the response when renderOutput is called. This just updates the necessary components for us
	 */
	public function actionError()
    {
	    if($error=Yii::app()->errorHandler->error)
	    {
            $this->status = $error['code'];
            $this->message = $error['message'];
	    }
    }
}
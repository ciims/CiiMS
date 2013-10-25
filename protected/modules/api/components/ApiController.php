<?php

/**
 * This is the base class for which all API controllers will extend from. This class provides serveral piece of functionality that will inherited
 * by all child clases, the biggest being pre-filtering and the outputting of JSON encoded text for each response.
 *
 * All actions that are run from this parent class should @return a value rather than running $this->render(). Exceptions are handled normally
 */
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
     * BeforeAction, validates that there is a valid response body
     * @param  CAction $action    The action we want to run
     * @return [type]         [description]
     */
    public function beforeAction($action)
    {
		// If content was sent as application/x-www-form-urlencoded, use it. Otherwise, assume raw JSON was sent and convert it into
		// the $_POST variable for ease of use
		if (Yii::app()->request->rawBody != "" && empty($_POST)) 
		{
			// IF the rawBody is malformed, throw an HTTP 500 error
			json_decode(Yii::app()->request->rawBody);
 			if (json_last_error() != JSON_ERROR_NONE)
 			{
 				header('HTTP/1.1 500 Internal Server Error');
 				$this->status = 500;
 				$this->message = Yii::t('Api.main', 'Request payload not properly formed JSON.');
 				return null;
 			}

			$_POST = CJSON::decode(Yii::app()->request->rawBody);
		}

		return true;
    }

    /**
     * This is the same as CController::runAction($action), except it returns data rather than echoing it.
     * @param  CAction $action
     * @see CController::runAction($action);
     */
	public function runAction($action)
	{
		$response = null;
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
	 * Default handler
	 * @return null
	 */
	public function actionIndex()
	{
		return null;
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
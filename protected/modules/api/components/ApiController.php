<?php

/**
 * This is the base class for which all API controllers will extend from. This class provides serveral piece of functionality that will inherited
 * by all child clases, the biggest being pre-filtering and the outputting of JSON encoded text for each response.
 *
 * All actions that are run from this parent class should @return a value rather than running $this->render(). Exceptions are handled normally
 */
class ApiController extends CiiController
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
	 * The HTTP_X_AUTH_TOKEN if supplied
	 * @var string
	 */
	public $xauthtoken = null;

	/**
 	 * The HTTP_X_AUTH_EMAIL if supplied
	 * @var string
	 */
	public $xauthemail = null;

	/**
	 * The User object if XAUTH has validated.
	 * @var User
	 */
	public $user = null;

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
     * Overrides accesscontrol
     * @param CFilterChain $filterChain
     */
    public function filterAccessControl($filterChain)
    {
    	// Retrieve the AUTH Token and Email if they were set 
    	$this->xauthtoken = Cii::get($_SERVER, 'HTTP_X_AUTH_TOKEN', NULL);
    	$this->xauthemail = Cii::get($_SERVER, 'HTTP_X_AUTH_EMAIL', NULL);

    	// Determine the user associated with it, if any
    	if ($this->xauthemail != NULL)
    	{
    		// If a user exists with that email address 
    		$user = Users::model()->findByAttributes(array('email' => $this->xauthemail));
    		if ($user == NULL)
    			break;

    		if ($user->status!=Users::ACTIVE)
    			throw new CHttpException(403, Yii::t('Api.main', 'Only active users can access the API.'));

    		$q = new CDbCriteria();
			$q->addCondition('t.key LIKE :key');
			$q->addCondition('value = :value');
			$q->addCondition('user_id = :user_id');
			$q->params = array(':user_id' => $user->id, ':value' => $this->xauthtoken, ':key' => 'api_key%');
    		$meta = UserMetadata::model()->find($q);

    		// And they have an active XAuthToken, set $this->user = the User object
    		if ($meta != NULL)
    			$this->user = $user;
    	}  	

        $filter=new ApiAccessControlFilter;
        $filter->user = $this->user;
        $filter->setRules($this->accessRules());
        $filter->filter($filterChain);
    }

    /**
     * Method overload allows clearer separation of controller actions in relation to REQUEST_TYPE
     *
     * GET actions will be routed to action$actionID
     * Other actions will be routed to action$actionIDREQUEST_TYPE
     * @param $actionID string  The string name of the action that we want to run
     * @return CInlineAction
     * @see CController::createAction($actionID)
     */
    public function createAction($actionID)
    {
        if($actionID==='')
            $actionID=$this->defaultAction;

        if (Yii::app()->request->getRequestType() != 'GET' && $actionID != 'error')
            $actionID .= Yii::app()->request->getRequestType();

        if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')) // we have actions method
            return new ApiInlineAction($this,$actionID);
        else
        {
            $action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
            if($action!==null && !method_exists($action,'run'))
                throw new CException(Yii::t('yii', 'Action class {class} must implement the "run" method.', array('{class}'=>get_class($action))));
            return $action;
        }
    }

    /**
     * BeforeAction, validates that there is a valid response body
     * @param  CAction $action    The action we want to run
     */
    public function beforeAction($action)
    {
        if ((Cii::getConfig('enableAPI') != true || Cii::get(Cii::getCiiConfig(), 'allow_api', true) == false) && $this->id != "event" && $this->id != "comment")
        {
            header('HTTP/1.1 403 Access Denied');
            $this->status = 403;
            $this->message = Yii::t('Api.main', 'The CiiMS API is not enabled.');
            return null;
        }

		// If content was sent as application/x-www-form-urlencoded, use it. Otherwise, assume raw JSON was sent and convert it into
		// the $_POST variable for ease of use
		if (Yii::app()->request->rawBody != "" && empty($_POST)) 
		{
			// IF the rawBody is malformed, throw an HTTP 500 error. Use json_encode so that we can get json_last_error
			$_POST = json_decode(Yii::app()->request->rawBody);
 			if (json_last_error() != JSON_ERROR_NONE)
 			{
 				header('HTTP/1.1 400 Bad Request');
 				$this->status = 400;
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
    public function renderOutput($response = array(), $status=NULL, $message=NULL)
    {
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: x-auth-token, x-auth-email");
        header('Access-Control-Allow-Methods: PUT, PATCH, DELETE, POST, GET, OPTIONS');
        
        echo CJSON::encode(array(
            'status' => $status != NULL ? $status : $this->status,
            'message' => $message != NULL ? $message : $this->message,
            'response' => $response
        ));
        Yii::app()->end();
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

    /**
     * Performs an error dump with the given status code
     * @param  int    $status    The HTTP Status Code
     * @param  string $message   The error message
     * @param  array  $response  The error response
     * @return array
     */
    public function returnError($status, $message = NULL, $response)
    {
    	header('HTTP/1.1 '. $status);
        $this->status = $status;

        if ($message === NULL)
       		$this->message = Yii::t('Api.main', 'Failed to set model attributes.');
       	else
       		$this->message = $message;

        return $response;
    }
}
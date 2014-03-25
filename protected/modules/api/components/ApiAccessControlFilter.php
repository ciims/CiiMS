<?php

class ApiAccessControlFilter extends CAccessControlFilter
{
    public $user = null;

    private $_rules = array();

    /**
     * Performs the pre-action filtering.
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     * @return boolean whether the filtering process should continue and the action
     * should be executed.
     */
    protected function preFilter($filterChain)
    {
        $app=Yii::app();
        $request=$app->getRequest();
        $user=$this->user;
        $verb=$request->getRequestType();
        $ip=$request->getUserHostAddress();

        foreach($this->getRules() as $rule)
        {
            if(($allow=$rule->isUserAllowed($user,$filterChain->controller,$filterChain->action,$ip,$verb))>0) // allowed
                break;
            elseif($allow<0) // denied
            {
                if(isset($rule->deniedCallback))
                    call_user_func($rule->deniedCallback, $rule);
                else
                    $this->accessDenied($user,$this->resolveErrorMessage($rule));
                return false;
            }
        }

        return true;
    }
    
    public function getRules()
    {
        return $this->_rules;
    }

    /**
     * @param array $rules list of access rules.
     */
    public function setRules($rules)
    {
        foreach($rules as $rule)
        {
            if(is_array($rule) && isset($rule[0]))
            {
                $r=new ApiAccessRule;
                $r->allow=$rule[0]==='allow';
                foreach(array_slice($rule,1) as $name=>$value)
                {
                    if($name==='expression' || $name==='roles' || $name==='message' || $name==='deniedCallback')
                        $r->$name=$value;
                    else
                        $r->$name=array_map('strtolower',$value);
                }
                $this->_rules[]=$r;
            }
        }
    }

    /**
     * Denies the access of the user.
     * This method is invoked when access check fails.
     * @param IWebUser $user the current user
     * @param string $message the error message to be displayed
     */
    protected function accessDenied($user,$message=NULL)
    {  
        http_response_code(403);
        Yii::app()->controller->renderOutput(array(), 403, $message);
    }
}

class ApiAccessRule extends CAccessRule
{
    /**
     * Checks whether the Web user is allowed to perform the specified action.
     * @param CWebUser $user the user object
     * @param CController $controller the controller currently being executed
     * @param CAction $action the action to be performed
     * @param string $ip the request IP address
     * @param string $verb the request verb (GET, POST, etc.)
     * @return integer 1 if the user is allowed, -1 if the user is denied, 0 if the rule does not apply to the user
     */
    public function isUserAllowed($user,$controller,$action,$ip,$verb)
    {
        if($this->isActionMatched($action)
            && $this->isIpMatched($ip)
            && $this->isVerbMatched($verb)
            && $this->isControllerMatched($controller)
            && $this->isExpressionMatched($user))
            return $this->allow ? 1 : -1;
        else
            return 0;
    }
}

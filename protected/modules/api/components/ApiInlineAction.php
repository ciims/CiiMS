<?php

class ApiInlineAction extends CInlineAction
{
    /**
     * This method has been overloaded so that it returns a response rather than a boolean value
     *
     * Executes a method of an object with the supplied named parameters.
     * This method is internally used.
     * @param mixed $object the object whose method is to be executed
     * @param ReflectionMethod $method the method reflection
     * @param array $params the named parameters
     * @return boolean whether the named parameters are valid
     * @since 1.1.7
     */
    protected function runWithParamsInternal($object, $method, $params)
    {
        $ps=array();
        foreach($method->getParameters() as $i=>$param)
        {
            $name=$param->getName();
            if(isset($params[$name]))
            {
                if($param->isArray())
                    $ps[]=is_array($params[$name]) ? $params[$name] : array($params[$name]);
                elseif(!is_array($params[$name]))
                    $ps[]=$params[$name];
                else
                    return false;
            }
            elseif($param->isDefaultValueAvailable())
                $ps[]=$param->getDefaultValue();
            else
                return false;
        }

        return $method->invokeArgs($object,$ps);
    }

}

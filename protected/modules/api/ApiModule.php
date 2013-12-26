<?php
/**
 * CiiMS API Module
 *
 * This is a stand alone module for CiiMS that adds a JSON REST API functionality to CiiMS
 */
class ApiModule extends CWebModule
{
	public function init()
	{        
		// import the module-level models and components
		$this->setImport(array(
			'api.models.*',
            'api.components.*',
		));

        // Disable any attempts to render a layout
        $this->layout = false;

        // Disable logging for the API
        Yii::app()->log->routes[0]->enabled = false; 

        // Update components
		Yii::app()->setComponents(array(
            'errorHandler' => array(
            	'errorAction'  => 'api/default/error',
        	),
            'messages' => array(
                'class' => 'ext.cii.components.CiiPHPMessageSource',
                'basePath' => Yii::getPathOfAlias('application.modules.api')
            )
        ));
	}
}

<?php

class ApiModule extends CWebModule
{
	public function init()
	{        
		// import the module-level models and components
		$this->setImport(array(
			'api.models.*',
            'api.components.*',
		));

        // Disable logging for the API
        Yii::app()->log->routes[0]->enabled = false;

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

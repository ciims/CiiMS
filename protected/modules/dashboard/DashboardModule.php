<?php

class DashboardModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
        $this->layoutPath = Yii::getPathOfAlias('dashboard.views.layouts');
        
		// import the module-level models and components
		$this->setImport(array(
			'dashboard.models.*',
			'dashboard.components.*',
		));

		 $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG);
		 Yii::app()->setComponents(array(
            'errorHandler' => array(
            	'errorAction'  => 'dashboard/default/error',
        	),
            'clientScript'=>array(
                 'class' => 'ext.minify.EClientScript',
                'combineScriptFiles'    => false,   // Script Combination Kills the Dashboard
                'combineCssFiles'       => true,
                'optimizeCssFiles'      => false,   // CSS Combination kills the dashboard too...
                'optimizeScriptFiles'   => true,
                'compressHTML'          => true,
                'packages'=>array(
                    'jquery'=>array(
                        'baseUrl'=>$asset . '/js/',
                        'js'=>array('jquery-2.0.0.min.js')
                    )
                )
            ),
        ));
	}
}

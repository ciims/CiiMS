<?php

class HybridauthModule extends CWebModule {

	public $baseUrl;
	public $providers;
	private $_assetsUrl;
	

	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		// import the module-level models and components
		$this->setImport(array(
			'hybridauth.models.*',
			'hybridauth.components.*',
		));

		Yii::app()->setComponents(array(
            'messages' => array(
                'class' => 'ext.cii.components.CiiPHPMessageSource',
                'basePath' => Yii::getPathOfAlias('application.modules.hybridauth')
            )
        ));
	}

	public function beforeControllerAction($controller, $action) {
		if (parent::beforeControllerAction($controller, $action)) {
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	/** 
	 * Convert configuration to an array for Hybrid_Auth, rather than object properties as supplied by Yii
	 * @return array
	 */
	public function getConfig() {
		return array(
			'baseUrl' => Yii::app()->getBaseUrl(true),
			'base_url' => Yii::app()->getBaseUrl(true) . '/hybridauth/callback', // URL for Hybrid_Auth callback
			'providers' => CMap::mergeArray($this->providers, Cii::getHybridAuthProviders()),
		);
	}

	/**
	 * Get the Hybrid_Auth adapter that is supplied once someone has authenticated.
	 * @return Hybrid_Provider_Adapter adapter or null if they are not logged in, or are logged in locally.
	 */
	public function getAdapter() {
		return Yii::app()->session['hybridauth-adapter'];
	}

	/** 
	 * Put the Hybrid_Provider_Adapter into the session so we don't need to generate it from the provider on each 
	 * request.
	 * @param Hybrid_Provider_Adapter $adapter 
	 */
	public function setAdapter(Hybrid_Provider_Adapter $adapter) {
		Yii::app()->session['hybridauth-adapter'] = $adapter;
	}



}

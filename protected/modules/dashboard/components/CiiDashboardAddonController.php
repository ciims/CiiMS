<?php

class CiiDashboardAddonController extends CiiDashboardController
{
    /**
     * Sets the layout file to null
     * @var string $layout
     */
    public $layout = NULL;

    /**
     * Override of BeforeAction to disable log routing
     * @param CAction $action   The Action
     * @see CiiDashboardController::beforeAction()
     */
    public function beforeAction($action)
    {
        Yii::app()->log->routes[0]->enabled = false;
        return parent::beforeAction($action);
    }

    /**
     * Retrieves the current type based upon the class name
     * @return string The Type
     */
    private function getType()
    {
        return strtolower(Cii::singularize(str_replace('Controller', '', get_class($this))));
    }

    public function actionRegister()
    {

    }

    public function actionUnregister()
    {

    }

    /**
     * Provides functionality to perform a JSON search
     * @return JSON
     */
    public function actionSearch()
    {
        // Set the data up
        $data = array(
            'type' => $this->getType(),
            'text' =>  Cii::get($_POST, 'text')
        );
        
        $response = $this->curlRequest('/customize/default/search', $data);
        echo CJSON::encode($response['response']);
        Yii::app()->end();        
    }

    /**
     * CURL wrapper for controllers that extend CiiDashboardAddonController
     * Ensures that the X-Auth details are set and that the correct cert is loaded.
     *
     * @param  string        $endpoint  The API endpoint we want to hit
     * @param  array|boolean $data      The POST data we want to send with the request, if any
     * @return array
     */
    protected function curlRequest($endpoint, $data = false)
    {
        // Make a curl request to ciims.org to search for soime cards.
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Auth-ID: ' . Cii::getConfig('instance_id'),
                'X-Auth-Token: ' . Cii::getConfig('token')
            ),
            CURLOPT_URL => 'https://www.ciims.org/' . $endpoint,
            CURLOPT_CAINFO => Yii::getPathOfAlias('application.config.certs') . DIRECTORY_SEPARATOR . 'GeoTrustGlobalCA.cer'
        ));

        // Set the POST attributes if the data is set 
        if ($data !== false)
        {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, CJSON::encode($data));
        }

        $response = CJSON::decode(curl_exec($curl));
        curl_close($curl);

        return $response;

    }
}

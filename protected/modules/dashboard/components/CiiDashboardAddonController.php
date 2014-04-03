<?php

class CiiDashboardAddonController extends CiiDashboardController
{
    /**
     * Sets the layout file to null
     * @var string $layout
     */
    public $layout = NULL;

    /**
     * Whether or not the respon should be returned
     * @var boolean $_returnResponse
     */
    protected $_returnResponse = false;

    /**
     * Overload of init to handle JSON response errors
     */
    public function init()
    {
        parent::init();
        Yii::app()->errorHandler->errorAction = '/dashboard/' . $this->getType() . '/error';
    }

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
        return strtolower(CiiInflector::singularize(str_replace('Controller', '', get_class($this))));
    }
   
    /**
     * Returns a JSON error appropriate for this controller
     * @return JSON
     */ 
    public function actionError()
    {
        if (Yii::app()->user->isGuest)
           return $this->redirect($this->createUrl('/login?next=' . Yii::app()->request->requestUri));

        if ($error = Yii::app()->errorHandler->error)
        {
            header('Content-Type: application/json');
            echo CJSON::encode(array('status' => $error['code'], 'message' => $error['message'], 'response' => NULL));
            Yii::app()->end();
        }
        else
            $this->redirect($this->createUrl('/error/403'));
    }

    /**
     * Handles processing of the response for this controller
     * @param array $response   The response from the API
     * @return JSON
     */
    protected function renderResponse($response)
    {
        if ($this->_returnResponse)
            return $response;

        header('Content-Type: application/json');
        if ($response['status'] == 200)
            echo CJSON::encode($response);
        else
            throw new CHttpException($response['status'], $response['message']);
        Yii::app()->end();
    }

    /**
     * Registers a card with this instance
     * @param string $id    The UUID of the Card
     */
    public function actionRegister($id=NULL)
    {
        $response = $this->curlRequest('default/addAddon/id/' . $id, array(null));
        return $this->renderResponse($response);
    }

    /**
     * Unregisters a card with this instance
     * @param string $id    The UUID of the Card
     */
    public function actionUnregister($id=NULL)
    {
        $response = $this->curlRequest('default/removeAddon/id/' . $id, array(null));
        return $this->renderResponse($response);
    }

    /**
     * Lists all Addons of this type registered to this instance
     */
    public function actionRegistered()
    {
        $response = $this->curlRequest('default/' . CiiInflector::pluralize($this->getType()));
        return $this->renderResponse($response);
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
        
        $response = $this->curlRequest('default/search', $data);
        return $this->renderResponse($response);
    }
    
    /**
     * Retrives the details for a particular card
     * @param string   $id    The Addon ID
     * @return JSON
     */
    public function actionDetails($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, 'Missing ID');

        $response = $this->curlRequest('default/addon/id/' . $id);

        return $this->renderResponse($response);
    }

    /**
     * Displays a menu for installing a new addon
     * @param string   $id    The Addon ID
     */
    public function actionDetailsView($id=NULL)
    {
        if ($id == NULL)
            throw new CHttpException(400, 'Missing ID');

        $this->_returnResponse = true;
        $details = $this->actionDetails($id);

        echo $this->renderPartial('application.modules.dashboard.views.settings.addoninstall', array(
            'details' => $details['response'], 
            'id' => $id,
            'md' => new CMarkdownParser,
            'type' => $this->getType(),
        ));

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
            CURLOPT_URL => 'https://www.ciims.org/customize/' . $endpoint,
            CURLOPT_CAINFO => Yii::getPathOfAlias('application.config.certs') . DIRECTORY_SEPARATOR . 'GeoTrustGlobalCA.cer'
        ));

        // Set the POST attributes if the data is set 
        if ($data != false)
        {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, CJSON::encode($data));
        }

        $response = CJSON::decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }
    
    /**
     * Retrieves a file from our CDN provider and returns it
     * WARNING: This has the potential to return a _very_ large response object that can exceed PHP's MAX_MEMORY settings
     *
     * @param string $file  The Path to the package ZIP file
     * @param string $id
     * @return ZIP Package
     */
    protected function downloadPackage($id, $file, $path)
    {
        // We're downloading a file - prevent the client aborting the process from screwing up the download
        // and ensure that we have sufficient time to complete it
        ignore_user_abort(true);
        set_time_limit(0);

        $fp = fopen($path . DIRECTORY_SEPARATOR . $id . '.zip', 'w+');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $file,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_CAINFO => Yii::getPathOfAlias('application.config.certs') . DIRECTORY_SEPARATOR . 'BaltimoreCyberTrustRootCA.crt'
        ));

        curl_exec($curl);
        curl_close($curl);
        fclose($fp);

        return;
    }
}

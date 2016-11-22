<?php
namespace AppBundle\Service;
use AppBundle\Service\CurlRequestService;

class XplentyService{

    /**
     * XplentyService constructor.
     * @param $settings
     */
    function __construct($settings)
    {
        $this->settings = $settings;
        $this->apiKey = $settings['apiKey'];
        $this->baseURL = $settings['baseURL'];
        $this->xplentyAccountId = $settings['xplentyAccountId'];
    }

    /**
     * List all packages for account
     * @return mixed
     */
    public function listPackages(){
        $data = array('flow_type' => 'all');
        $results = $this->get( $this->xplentyAccountId . '/api/packages?include=flow', $data);
        return $results;
    }

    /**
     * Gets informations about a package
     * @param $id
     * @return mixed
     */
    public function getPackageInformation($id){
        $results = $this->get($this->xplentyAccountId . "/api/packages/" . $id . "?include=flow");
        return $results;
    }

    /**
     * Lists the available jobs
     * @return mixed
     */
    public function listJobs(){
        $results = $this->get($this->xplentyAccountId . "/api/jobs");
        return $results;
    }

    /**
     * List account connections
     * @return mixed
     */
    public function listConnections($type = 'all'){
        $params = array('type' =>  $type, 'sort' => 'name', 'direction' => 'ASC');
        $results = $this->get( $this->xplentyAccountId . '/api/connections', $params);
        return $results;
    }

    /**
     * Gets informations about a package
     * @param $type
     * @param $id
     * @return mixed
     */
    public function getConnectionInformation($type, $id){
        $results = $this->get($this->xplentyAccountId . "/api/connections/" . $type . "/" . $id . "?include=flow");
        return $results;
    }

    /**
     * Creates an account connection
     * @param $name
     * @param $token
     * @param $refresh_token
     * @param $type
     * @return mixed
     */
    public function createConnection($name, $token, $refresh_token, $username, $type){
        $data = array('name' => $name, 'password' => $token, 'refresh_token' => $refresh_token, 'username' => $username);
        $results = $this->post($this->xplentyAccountId . "/api/connections/" . $type, $data);
        return $results;
    }

    /**
     * Create package
     * @return mixed
     */
    public function createPackage($basePackage){
        $data = array('name' => 'ARM App automated package', 'description' => 'Created via API by the ARM app',
            'source_package_id' => $basePackage
        //    ,'variables' => array ('IndriAccountId' => 'someID', 'AdwordsCustomer' => '000-000-000-000')
        );
        $results = $this->post($this->xplentyAccountId . '/api/packages', $data);
        return $results;
    }

    /**
     * Update a package
     * @param $id
     * @param $data
     * @return mixed
     *
     */
    public function updatePackage($id, $data){
        $results = $this->put($this->xplentyAccountId . '/api/packages/' . $id, $data);
        return $results;
    }

    /**
     * Actually performs posts to the xplenty API
     * @param $url
     * @param $body
     * @return mixed
     * @throws \Exception
     */
    public function get($url, $body = false){
        $curlRequestService = new CurlRequestService($this->apiKey);
        $headerOptions = array ('Content-Type: application/json', 'Accept: application/vnd.xplenty+json; version=2');

        try {
            $results = $curlRequestService->fetchUrl($this->baseURL . $url, $headerOptions, json_encode($body));
        }catch(\Exception $e){
            throw $e;
        }


        return $results;
    }

    /**
     * Actually performs posts to the xplenty API
     * @param $url
     * @param $body
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function post($url, $body){
        $curlRequestService = new CurlRequestService($this->apiKey);
        $headerOptions = array ('Content-Type: application/json', 'Accept: application/vnd.xplenty+json; version=2');

        try {
            $results = $curlRequestService->PostToUrl($this->baseURL . $url, $headerOptions, json_encode($body));
            if($results == NULL){
                throw new \Exception('Got NULL result from API');
            }
        }catch(\Exception $e){
            throw $e;
        }

        return $results;
    }

    /**
     * Actually performs puts to the xplenty API
     * @param $url
     * @param $body
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function put($url, $body){
        $curlRequestService = new CurlRequestService($this->apiKey);
        $headerOptions = array ('Content-Type: application/json', 'Accept: application/vnd.xplenty+json; version=2');

        try {
            $results = $curlRequestService->PutToUrl($this->baseURL . $url, $headerOptions, json_encode($body));
            if($results == NULL){
                throw new \Exception('Got NULL result from API');
            }
        }catch(\Exception $e){
            throw $e;
        }

        return $results;
    }

}
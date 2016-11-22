<?php
namespace AppBundle\Service;

use AppBundle\Service\SQSService;

class LoggerService {


    /**
     * Constructor for the logger service
     * @param $app
     */
    public function ___construct(){
        parent::__construct();
        $this->logGeneratorCode = $this->app['logGeneratorCode'];
    }

    /**
     * Creates the log array
     * @param $type
     * @param $code
     * @param $moduleFunction
     * @param $message
     * @param $meta_data
     * @param string $tags
     * @param string $playbook_id
     * @return mixed
     */
    public function createLog($type, $code, $moduleFunction, $message, $meta_data, $indri_account_id = '', $indri_customer_id = ''
        , $tags = '', $playbook_id = ''){
        $log = array();
        $log['etype'] = $type;
        $log['created'] = new \DateTime();
        $log['generator'] = $this->logGeneratorCode;
        $log['code'] = $code;
        $log['module_function'] = $moduleFunction;
        $log['message'] = $message;
        $log['meta_data'] = $meta_data;
        $log['indri_account_id'] = $indri_account_id;
        $log['indri_customer_id'] = $indri_customer_id;
        $log['tags'] = $tags;
        $log['playbook_id'] = $playbook_id;
        $status = $this->sendLog($log);
        return $status;
    }

    /**
     * Actually sends the log to the SQS service
     * @param $log
     * @return mixed
     */
    private function sendLog($log){
        $sqsService = new SQSService($this->app);
        $status = $sqsService->enqueue($log);
        return $status;
    }


    /**
     * Converts a Stdclass object into an array
     * @param $incomingObject
     * @return mixed
     */
    public function convertIntoArray($incomingObject){
        return json_decode(json_encode($incomingObject), true);
    }

}
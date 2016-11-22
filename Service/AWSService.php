<?php
namespace AppBundle\Service;
use Aws\Sdk;

class AWSService{

    const awsRegion = 'us-east-1';

    /**
     * AWSService constructor.
     * @param $credentials
     */
    public function __construct($credentials){
        $this->credentials = $credentials;
        $this->aws = $this->initializeAWS();
    }

    /**
     * Initialize AWS
     * @return Sdk
     */
    protected function initializeAWS(){
        $sdk = new Sdk([
            'region'   => self::awsRegion,
            'version'  => 'latest',
            'credentials' => [
                'key'    => $this->credentials['key'],
                'secret' => $this->credentials['secret'],
            ],
        ]);
        return $sdk;
    }
}
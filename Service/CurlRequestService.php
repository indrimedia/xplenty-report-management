<?php
namespace AppBundle\Service;

class CurlRequestService{

    /**
     * CurlRequestService constructor.
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $url
     * @param array $headerOptions
     * @param boolean $jsondecode
     * @return mixed
     */
    public function fetchUrl($url, $headerOptions, $data = false, $jsondecode = TRUE){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOptions);
        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        try {
            $preresult = curl_exec($ch);
        }catch(\Exception $e){
            throw $e;
        }
        if($jsondecode) {
            $result = json_decode($preresult);
        }else{
            $result = $preresult;
        }
        return $result;
    }

    /**
     * @param string $url
     * @param array $headerOptions
     * @return mixed
     */
    public function PostToUrl($url, $headerOptions, $body = '{}'){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOptions);

        try {
            $result = json_decode(curl_exec($ch));
        }catch(\Exception $e){
            throw $e;
        }
        return $result;
    }

    /**
     * @param string $url
     * @param array $headerOptions
     * @return mixed
     */
    public function PutToUrl($url, $headerOptions, $body = '{}'){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOptions);

        try {
            $result = json_decode(curl_exec($ch));
        }catch(\Exception $e){
            throw $e;
        }
        return $result;
    }
}
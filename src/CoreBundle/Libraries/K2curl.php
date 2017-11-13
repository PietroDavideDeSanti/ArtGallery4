<?php
namespace CoreBundle\Libraries;

use CoreBundle\Protocol\cUrlOutput;

class K2curl {
    
    private $endpoint;
    private $method;
    private $params = array();
    private $params_type = 0;
    private $headers = array();    
    private $debug = false;
    private $ssl_verify = true;
    private $auth = false;
    private $userpwd;

    public function getSSLVerify() {
        return $this->ssl_verify;
    }    
    
    public function getDebug() {
        return $this->debug;
    }

    public function getEndpoint() {
        return $this->endpoint;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getParams($key=null) {
        if(!empty($key)) {
            return $this->params[$key];
        }
        return $this->params;
    }

    public function getParamsType() {
        return $this->params_type;
    }
    
    public function getHeaders($key=null) {
        if(!empty($key)) {
            return $this->headers[$key];
        }
        return $this->headers;
    }
    
    public function setSSLVerify($boolean) {
        $this->ssl_verify = $boolean;
    }     
    
    public function setDebug($boolean) {
        $this->debug = $boolean;
    }

    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setParams($key, $value) {
        $this->params[$key] = $value;
    }

    public function setHeaders($key, $value) {
        $this->headers[$key] = $value;
    }

    public function setParamsType($params_type) {
        $this->params_type = $params_type;
    }
    
    public function addHeaders($headers) {
        foreach ($headers as $key => $value) {
            $this->setHeaders($key, $value);
        }        
    }
    
    public function addParams($params) {
        foreach ($params as $key => $value) {
            $this->setParams($key, $value);
        }
    }
    
    public function getAuth() {
        return $this->auth;
    }

    public function setAuth($auth) {
        $this->auth = $auth;
    }

    public function getUserpwd() {
        return $this->userpwd;
    }

    public function setUserpwd($userpwd) {
        $this->userpwd = $userpwd;
    }

            
    private function buildHeaders() {
        $headers_formated = array();
        $headers = $this->getHeaders();
        foreach ($headers as $key => $value) {
            array_push($headers_formated, $key.': '.$value);
        }
        return  $headers_formated;        
    }
    
    private function buildParams() {
        if(!empty($this->getParams())) {
            return json_encode($this->getParams());
        } else return null;
    }

    /**
     * @return cUrlOutput
     */
    public function sendRequest() {
        $headers = $this->buildHeaders();
        $params = $this->buildParams();
        #init curl
        $ch = curl_init($this->getEndpoint());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->getMethod());

        if(!empty($params)) {
            if($this->getParamsType() == 1){
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->getParams(), '', '&'));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
            }
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //debug option
        if($this->getDebug()){
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        }
        
        if($this->auth){
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);      
            curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);      
        }
        
        // Eseguo la cUrl:
        //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);

        // Per il debug
//        $error = 'Error: '.curl_errno($ch).' , '.curl_error($ch);
//        dump($data);
////        $info = json_decode($info);
//        dump($info);
//        dump($error);
//        exit();

        // Chiusura della cUrl:
        curl_close($ch);

        $output = new cUrlOutput();
        $output->data = $data;
        $output->info = $info;


        return $output;
    }    
    
}
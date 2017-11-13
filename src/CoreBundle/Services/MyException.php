<?php

namespace CoreBundle\Services;
//libraries
use Symfony\Component\HttpKernel\Exception\HttpException as HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;


class MyException
{
    // http status code -- OBBLIGATORI
    CONST SUCCESS_STATUS              =  200;
    CONST CREATE_SUCCESS_STATUS       =  201;

    CONST MOVED_PERMANENTLY_STATUS    =  301;

    CONST BAD_REQUEST_STATUS          =  400;
    CONST UNAUTHORIZED_STATUS         =  401;
    CONST FORBIDDEN_STATUS            =  403;
    CONST NOT_FOUND_STATUS            =  404;
    CONST METHOD_NOT_ALLOWED_STATUS   =  405;
    CONST CONFLICT                    =  409;
    CONST GONE                        =  410;

    CONST ERROR_SERVER_STATUS         =  500;
    CONST NOT_IMPLEMENTED             =  501;
    CONST SERVICE_UNAVAILABLE         =  503;


// ERRORI GESTITI DAL CORE
    CONST GENERIC_ERROR                             = "00";
    CONST JSON_MALFORMED                            = "01";
    CONST QUERYSTRING_MALFORMED                     = "02";
    CONST VIOLATION_CONSTRAINT                      = "03";
    CONST INSERT_ERROR                              = "04";
    
// ALTRI TIPI DI ERRORE -- CUSTOM
    CONST INVALID_CONSUMER                          = "WW01"; //CONTROLLER WS
    CONST DOCUMENT_NOT_FOUND                        = "WW02";
    CONST DOCUMENT_NOT_CONSUMER                     = "WW03";
    CONST INVALID_TOKEN                             = "WW04";
    CONST EXPIRED_TOKEN                             = "WW05";
    CONST DATI_NOT_FOUND                            = "LJ01"; //CONTROLLER LIQUIDAZIONE_AUTOMATICA
    CONST CURL_NOT_RESPONSE                         = "LJ02";
    CONST INVALID_USER                              = "LJ03";
    CONST USER_NOT_FOUND                            = "LJ04";
    CONST ERROR_SEND_WEB_LETTER                     = "LJ05"; //CONTROLLER LETTERE_WEB
    CONST PROPOSTA_WEB_LETTER_NOT_FOUND             = "LJ06";
    CONST PROPOSTA_NOT_IN_DELIBERED_STATUS          = "LJ07";
    CONST ERROR_SAVE_ESTIMATE                       = "VP01"; //CONTROLLER PREREGISTRATIONTOKEN
    CONST ERROR_SEND_EMAIL                          = "VP02"; 
    CONST DOCUMENT_EMPTY                            = "VD01"; //CONTROLLER DOCUMENT BUNDLE VENDITADIRETTA 
    CONST PROPOSAL_DOCUMENT_NOT_EXIST               = "VD02"; 
    CONST FINANZ_DOCUMENT_NOT_EXIST                 = "VD03"; 
    CONST DOCUMENT_NOT_EXIST                        = "VD04"; 
    CONST REGISTRY_DATA_NOT_EXIST                   = "VD06"; 
    CONST MINIMAL_REGISTRY_DATA_NOT_EXIST           = "VD07"; 
    CONST EXTENDED_REGISTRY_DATA_NOT_EXIST          = "VD08"; 
    CONST PAYMENT_DATA_NOT_EXIST                    = "VD09"; 
    CONST FLAG_NOT_EXIST                            = "TT01"; //CONTROLLER TRASPARENZA BUNDLE TRASPARENZA
    CONST ERROR_SAVE_REGISTRY                       = "VR01"; //CONTROLLER REGISTRATION
    CONST ERROR_SAVE_GUARANTOR                      = "VR02";
    CONST ERROR_UPDATE_PROPOSTA                     = "VR03";
    CONST ERROR_INSERT_DBLISTA                      = "VR04";
    CONST ERROR_INSERT_DBHISTORY                    = "VR05";
    CONST DBLISTA_NOT_FOUND                         = "CC01"; //CONTROLLER CWF BUNDLE CWF
    CONST PROPOSTA_NOT_FOUND                        = "CC02"; 
    
    
    
    //private $logger;
    //private $translator;
    
    public function __construct () {
        //$this->logger = $logger;
        //$this->translator = $translator;
    }

/* gestione di tutte le eccezioni */    
    public function errorHttpHandler($e) {
        $data = new \stdClass();
        $data->errors = array();
        $data->errors[] = $this->errorProtocol($e->getMessage());

        //$this->logger->error($e->getMessage());
        return new JsonResponse($data, $e->getStatusCode());
    }    
        
    private function errorProtocol($strError) {

        $arr_tmp = explode("|", $strError);
        $arr["code"] = isset($arr_tmp[0]) ? $arr_tmp[0] : null;
        $arr["fieldName"] = isset($arr_tmp[1]) ? $arr_tmp[1] : null;
        $arr["description"] = isset($arr_tmp[2]) ? $arr_tmp[2] : null;
        return $arr;
    }

/* fine gestione di tutte le eccezioni */
    
/* gestione delle eccezioni del solo componente validator */    

    public function errorValidatorHandler($e) {

        $data = new \stdClass();
        $data->errors = array();
        $strMessage = $e->getMessage();

        $colError = explode("§", $strMessage);

        for($i=1; $i<count($colError); $i=$i+2) {
            $arr = array();
            $arr["code"] = MyException::VIOLATION_CONSTRAINT;
            $arr["fieldName"] = $this->getFieldNameException($colError[$i-1]);
            $arr["description"] = $colError[$i];
            $data->errors[] = $arr;
        }

        return new JsonResponse($data, MyException::BAD_REQUEST_STATUS);
    }

    private function getFieldNameException($strError) {
        $arr = explode(").", $strError);
        return trim(str_replace(":", "", $arr[1]));
    }    

/* fine gestione delle eccezioni del solo componente validator */    
    
}

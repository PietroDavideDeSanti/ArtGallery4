<?php

namespace CoreBundle\Controller;

// Componenti di Symfony:
use CoreBundle\Protocol\Response;
use CoreBundle\Protocol\ServerVars;
use CoreBundle\Services\MySerializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Eccezioni
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidatorException;

//Protocol
use CoreBundle\Protocol\EndpointConfiguration;
use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\ParamVars;
use CoreBundle\Protocol\UserVars;
use CoreBundle\Protocol\FileVars;
use CoreBundle\Protocol\ProviderData;

// Services:
use CoreBundle\Services\MyException;
use CoreBundle\Libraries\K2curl;

// Serializzatore
use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


class K2Controller extends Controller
{
    function __construct() {

    }

    function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        if($this->isPortalDown()){
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                Header( "HTTP/1.1 403 Portal Temporarily Down" );
                $response = array (
                    "Location" => "/php/r_login.php"
                );
                die(json_encode($response));
            } else {
                // Altrimenti reindirizza alla view "portal down"
                Header( "HTTP/1.1 403 Portal Temporarily Down" );
                Header( "Location: /php/r_login.php");
                die();
            }
        }

    }

    protected function validateRequest(Request $request, EndpointConfiguration $configuration) {
        $globalVars = new GlobalVars();

        $globalVars->server = $this->setServerParams($request);

        $globalVars->channel = $this->setChannelParams($request);

        $configuration = $this->customizeEndpointConfigurationByChannel($configuration, $globalVars);

        //include il controllo HTTP_ERROR_CODE 400
        $globalVars->params = $this->setInputParams($request, $configuration);
        $globalVars->session = $this->get('Parameters')->getSessionParams($request);

        if($configuration->files) {
            $globalVars->file = $this->setFileParams($request);
        }

        if($configuration->login) {
            //include il controllo HTTP_ERROR_CODE 401
            $globalVars->user = $this->setUserParams($configuration, $globalVars);
            if(!empty($configuration->aclcode)) {
                //include il controllo HTTP_ERROR_CODE 403
                $this->get("acl_handler")->validateAction($configuration->aclcode, $globalVars->user);
            }
        }

        return $globalVars;
    }


    /**
     * @param Request $request
     * @return GlobalVars
     */
    private function setServerParams (Request $request) {
        $server = new ServerVars();
        $server->clientIp = $request->getClientIp();
        //... se ci sono altre proprietà estendere la classe e gestirne il recupero

        return $server;
    }

    private function setChannelParams(Request $request){
        $arrHeader = $this->get('Parameters')->getHeaderParams($request);
        if(isset($arrHeader["Channel"]) ) {
            $jsonHeader = json_encode($arrHeader);
            $objHeader = $this->get("my_serializer")->deserialize($jsonHeader, "CoreBundle\Request\Headers\Channel", "json");
            $arr_error = $this->get('validator')->validate($objHeader);
            if (count($arr_error) > 0) {
                throw new ValidatorException($arr_error->__toString(), 400);
            }
            return $objHeader->getChannel();
        } else {
            return $this->getParameter("default_channel");;
        }
    }

    private function customizeEndpointConfigurationByChannel(EndpointConfiguration $endpointConfiguration, GlobalVars $globalVars){
        $findChannel = false;

        // Recupero channel:
        $channelCode = $globalVars->channel;

        // Recupero le caratteristiche del channel dai parameter.yml:
        $arrChannel = $this->getParameter("channel");
        foreach($arrChannel as $channel){
            if(strtoupper($channel["codice"]) == strtoupper($channelCode)) {
                $findChannel = true;
                $endpointConfiguration->sso = $channel["sso"];
                break;
            }
        }

        if(!$findChannel){
            throw new HttpException(MyException::UNAUTHORIZED_STATUS, "||Channel not valid");
        }

        return $endpointConfiguration;
    }

    /**
     * @param Request $request
     * @param EndpointConfiguration $config
     * @return ParamVars
     */
    private function setInputParams(Request $request, EndpointConfiguration $config) {
        try {
            $return = new ParamVars();

            // Q U E R Y S T R I N G
            //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
            if ($config->querystring) {
                if ($config->encodingUrl) {
                    $realUrl = base64_decode($this->get('Parameters')->getQuerystringParams($request));
                } else {
                    $realUrl = $this->get('Parameters')->getQuerystringParams($request);
                }
                $jsonQuerystring = json_encode($realUrl);
                $objQuerystring = $this->get("my_serializer")->deserialize($jsonQuerystring, $config->querystring, "json");
                $arr_error = $this->get('validator')->validate($objQuerystring);
                if (count($arr_error) > 0) {
                    throw new ValidatorException($arr_error->__toString(), 400);
                }
                $return->data = array_merge($return->data, json_decode($jsonQuerystring, true));
            }

            // F O R M - D A T A
            //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
            if ($config->post) {
                $arrPost = $this->get('Parameters')->getPostParams($request);
                $jsonPost = json_encode($arrPost);
                $objPost = $this->get("my_serializer")->deserialize($jsonPost, $config->post, "json");
                $arr_error = $this->get('validator')->validate($objPost);
                if (count($arr_error) > 0) {
                    throw new ValidatorException($arr_error->__toString(), 400);
                }
                $return->data = array_merge($return->data, json_decode($jsonPost, true));
            }

            // R A W B O D Y
            //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
            if ($config->rawbody) {
                $jsonRawbody = $this->get('Parameters')->getBodyParams($request);
                //verifica se i parametri passati sono un json formalmente corretto
                if(!$this->isJSON($jsonRawbody)){
                    throw new HttpException(MyException::BAD_REQUEST_STATUS, MyException::JSON_MALFORMED.'||JSON formalmente errato');
                }
                //deserializzo json della request in un oggetto
//                $objRawbody = $this->get("my_serializer")->deserialize($jsonRawbody, $config->rawbody, "json");
                $objRawbody = $this->get("my_serializer")->deserialize($jsonRawbody, $config->rawbody, "json");
                $arr_error = $this->get('validator')->validate($objRawbody);
                if (count($arr_error) > 0) {
                    throw new ValidatorException($arr_error->__toString(), 400);
                }
                $return->data = array_merge($return->data, json_decode($jsonRawbody, true));
            }

            // H E A D E R
            //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //
            if ($config->headers) {
                $arrHeader = $this->get('Parameters')->getHeaderParams($request);
                $jsonHeader = json_encode($arrHeader);
                $objHeader = $this->get("my_serializer")->deserialize($jsonHeader, $config->headers, "json");
                $arr_error = $this->get('validator')->validate($objHeader);
                if (count($arr_error) > 0) {
                    throw new ValidatorException($arr_error->__toString(), 400);
                }
                $return->data = array_merge($return->data, json_decode($jsonHeader, true));
            }


            // L O G I N
            //  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  //

            if  (($config->login) && ($config->sso)) {
                $arrHeader = $this->get('Parameters')->getHeaderParams($request);
                $jsonHeader = json_encode($arrHeader);
                $objHeader = $this->get("my_serializer")->deserialize($jsonHeader, "CoreBundle\Request\Headers\Authorization", "json");
                $arr_error = $this->get('validator')->validate($objHeader);
                if (count($arr_error) > 0) {
                    throw new ValidatorException($arr_error->__toString(), 400);
                }

                $return->data = array_merge($return->data, json_decode($jsonHeader, true));

            }


            if ($config->session) {
                $objSession = $this->get('Parameters')->getSessionParams($request);
                $jsonSession = json_encode($objSession);
                $objSession = $this->get("my_serializer")->deserialize($jsonSession, $config->session, "json");
                $arr_error = $this->get('validator')->validate($objSession);
                if (count($arr_error) > 0) {
                    throw new ValidatorException($arr_error->__toString(), 400);
                }

            }

            return $return;

        } catch (ValidatorException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return FileVars
     */
    private function setFileParams(Request $request) {
        $return = new FileVars();
        $return->files = $this->get('Parameters')->getFileParams($request);
        return $return;
    }

    /**
     * @param EndpointConfiguration $config
     * @param GlobalVars $globalVars
     * @return UserVars
     * @internal param ParamVars $paramVars
     */
    private function setUserParams(EndpointConfiguration $config, GlobalVars $globalVars)
    {

        if($config->sso) {

            $globalVars->user = $this->get("sso_user_data_handler")->setUserDataProvider($globalVars);

            $globalVars->user->details = $this->get("sso_user_data_handler")->setUserDataConsumer($globalVars);

        } else {
            //TODO non funziona
            $globalVars->user = $this->get("session_user_data_handler")->setUserDataProvider($globalVars);

            $globalVars->user->details = $this->get("session_user_data_handler")->setUserDataConsumer($globalVars);

        }
        return $globalVars->user;

    }

    /**
     * @param EndpointConfiguration $config
     * @param GlobalVars $globalVars
     * @return Response
     */
    protected function initResponse(EndpointConfiguration $config, GlobalVars $globalVars) {
        $response = new Response();
        if(count($config->context) > 0) {
            $arrElementContext = $this->get("button_handler")->getAllButtons($config->context, $globalVars->user->profile);
            // Gestione della response
            $output = array(
                "ElementContext.elementDetail",
                "ElementDetail.name",
                "ElementDetail.image",
                "ElementDetail.description",
                "ElementDetail.elementId",
                "Element.action"
            );

            $buttons = $this->get("my_serializer")->setJsonResponse($arrElementContext, $output);
            $response->button = $this->get("button_handler")->addPropertyDisableButtons($buttons);
        }

        return $response;
    }

    /**
     * Funzione per la verifica del 403 (Forbidden)
     * @param integer $elementId
     * @param array $arrProfile
     * @return bool
     */
    private function validateAction($elementId, $arrProfile) {
        $check  = false;
        foreach ($arrProfile as $profileId) {
            $check = $this->get('ACL')->isEnabledByProfile($elementId, $profileId);
            if($check) {
                return true;
            }
        }
        return false;
    }

//    /**
//     * Funzione per la verifica del 403 (Forbidden)
//     * @param integer $elementId
//     * @param integer $userId
//     * @return bool|int
//     */
//    protected function checkPermissionByUser($elementId, $userId) {
//        return $this->get('ACL')->isEnabledByUser($elementId, $userId);
//    }

    /**
     * Funzione per verificare la sintassi di un JSON:
     */
    private function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }


    private function isPortalDown(){
//        $portalDown = $this->get("Desired")->isPortalDown();
//        if( $portalDown['par_vcval'] === "1" ){
//            return true;
//        } else {
            return false;
//        }
    }

}

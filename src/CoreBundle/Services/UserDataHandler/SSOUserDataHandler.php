<?php

namespace CoreBundle\Services\UserDataHandler;

use CoreBundle\Interfaces\UserDataHandlerInterface;
use CoreBundle\Libraries\K2curl;
use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\ProviderData;
use CoreBundle\Protocol\UserVars;
use CoreBundle\Services\MyException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SSOUserDataHandler implements UserDataHandlerInterface
{

    /**
     * @var EntityManager
     */
    private $em;

    private $wsHost;

    /**
     * SSOUserDataHandler constructor.
     * @param EntityManager $em
     * @param $wsHost
     */
    public function __construct(EntityManager $em, $wsHost)
    {
        $this->em = $em;
        $this->wsHost = $wsHost;
    }

    /**
     * @param GlobalVars $globalVars
     * @return GlobalVars
     */
    public function setUserDataProvider(GlobalVars $globalVars){

        $paramVars = $globalVars->params;
        $objSession = $globalVars->session;
        $userVars = $globalVars->user;

        //verifico che esista una sessione per il token
        $tokenData = $objSession->get("tokenData");
        if(!$tokenData){
            // Se non ci sono dati IN ASSOLUTO in sessione e' un errore.
            // Al massimo, se non ci sono "providerData" in sessione, invio la cUrl (vedi IF...ELSE qui sotto)
            throw new HttpException(MyException::UNAUTHORIZED_STATUS, "|Authorization|Accesso non autorizzato");
        }

        // Verifico che il token non sia scaduto;
        $now = new \DateTime();
        if ($tokenData->expires < $now->getTimestamp()) {
            throw new HttpException(MyException::GONE, "|Authorization|Token scaduto");
        }

        // Provo a recuperare i dati utente dalla sessione:
        $tmpUserVars = $objSession->get("userVars");
        // Se non esistono allora li devo richiedere al provider
        if(!empty($tmpUserVars)) {
            // E assegno i dati recuperabili ad una variabile:
            $userVars->id = $tmpUserVars->id;
            $userVars->username = $tmpUserVars->username;
            $userVars->role = $tmpUserVars->role;
            $userVars->name = $tmpUserVars->name;
            $userVars->surname = $tmpUserVars->surname;
            $userVars->email = $tmpUserVars->email;
            $userVars->fiscal = $tmpUserVars->fiscal;
            $userVars->profile = $tmpUserVars->profile;

        } else {
            $userVars = $this->getUserDataProvider($paramVars->data["Authorization"], $paramVars->data["Channel"]);
            // Scrivo i dati in sessione:
            $objSession->set( "userVars", $userVars);
        }

        return $userVars;

    }

    /**
     * @param GlobalVars $globalVars
     * @return array
     */
    public function setUserDataConsumer(GlobalVars $globalVars){

        $objSession = $globalVars->session;

        $details = $objSession->get("userVarsDetails");
        if(is_null($details)) {
            //recupero il customer id dal codice fiscale
            $colRegistry = $this->em->getRepository("portalBundle:Registry")->findBy(array("codiceFiscale" => $globalVars->user->fiscal));
            foreach($colRegistry as $registry){
                $customer = $registry->getCustomer()->last();
                if($customer) {
                    $details["customer"] = $customer->getId();
                    //basta il primo
                    break;
                }
            }
            $objSession->set("userVarsDetails", $details);
        }


        return $details;
    }

    /**
     * @param string $token
     * @param $channel
     * @return UserVars
     */
    private function getUserDataProvider($token, $channel){

        // Recupero i dati per la cUrl dai parameter.yml:
        $host = $this->wsHost;

        $endpoint = "/api/wslogin/user";

        $curl = new K2curl();
        $curl->setMethod('GET');
        $curl->setEndpoint($host . $endpoint);
        $curl->addHeaders(
            array(
                "Channel" => $channel,
                "accept" => "application/json",
                "Authorization" => "Bearer ". $token
            )
        );

        // Eseguo la cURL verso il web service:
        $result = $curl->sendRequest();


        // Verifico che la chiamata sia andata a buon fine:
        $httpCode = $result->info["http_code"];

        if( $httpCode != 200 ){
            $result = json_decode($result->data);
            $errors = $result->errors;

            $errorMsg = "";
            foreach($errors as $error){
                $errorMsg .= "||" . $error->description;
            }

            throw new HttpException($httpCode, $errorMsg);
        }

        $data = $result->data;
        $data = json_decode($data);
        $data = $data->data;

        $userVars = new UserVars();

        $userVars->id = $data->id;
        $userVars->username = $data->username;
        $userVars->role = $data->role;
        $userVars->name = $data->name;
        $userVars->surname = $data->surname;
        $userVars->email = $data->email;
        $userVars->fiscal = $data->fiscal;
        $userVars->profile = $data->profile;

        return $userVars;

    }

}
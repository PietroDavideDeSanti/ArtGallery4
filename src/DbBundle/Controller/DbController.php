<?php 

namespace DbBundle\Controller;

use CoreBundle\Controller\K2Controller;

use DbBundle\Model\DbModel;

// Route Libraries:
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Exception Libraries:
use Symfony\Component\HttpKernel\Exception\HttpException;

// Response Libraries:
use Symfony\Component\HttpFoundation\JsonResponse;

// Protocol::
use CoreBundle\Protocol\EndpointConfiguration;
use CoreBundle\Protocol\ResponseProtocol;

class DbController extends K2Controller{

    /**
     * porta alla form di inserimento tabella
     * @Route("/leggiDb", name="db_leggidb")
     * @Method("POST");
     */
    public function leggiDb(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/leggiDb";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new DbModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("DbBundle:Default:viewCercaDb.html.twig");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * cercala tabella nel db
     * @Route("/cercaDb", name="db_cercadb")
     * @Method("POST");
     */
    public function cercaDb(Request $request) {
        try{
        
            $config = new EndpointConfiguration();
            $config->post = "DbBundle\Request\Post\\" . ucfirst(__FUNCTION__);
            //$config->rawbody = "DbBundle\Request\Rawbody\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/cercaDb";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new DbModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("DbBundle:Default:viewTabellaDb.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * prova servizio
     * @Route("/provaServizio", name="db_provaservizio")
     * @Method("GET");
     */
    public function provaServizio(Request $request) {
        try{


            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/provaServizio";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new DbModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return new Response("servizio ");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }


    /**
     * prova servizio
     * @Route("/provaQuery", name="db_provaquery")
     * @Method("GET");
     */
    public function provaQuery(Request $request) {
        try{


            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/provaQuery";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new DbModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);

            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *

            // Lancio il render della view:
            return new Response("niente");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * endpoint per provare postman
     * @Route("/provaPostman", name="db_provapostman")
     * @Method("GET");
     */
    public function provaPostman(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/provaPostman";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new DbModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Restituisco il JSON:
            return new JsonResponse($response, 200);

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * prova di postman
     * @Route("/provaPostman1", name="db_provapostman1")
     * @Method("POST");
     */
    public function provaPostman1(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->rawbody = "DbBundle\Request\Rawbody\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/provaPostman1";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new DbModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Restituisco il JSON:
            return new JsonResponse($response, 200);

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    #####
}

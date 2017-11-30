<?php 

namespace UserBundle\Controller;

use CoreBundle\Controller\K2Controller;

use UserBundle\Model\UserModel;

// Route Libraries:
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

// Exception Libraries:
use Symfony\Component\HttpKernel\Exception\HttpException;

// Response Libraries:
use Symfony\Component\HttpFoundation\JsonResponse;

// Protocol::
use CoreBundle\Protocol\EndpointConfiguration;
use CoreBundle\Protocol\ResponseProtocol;
use Symfony\Component\HttpFoundation\Response;


class UserController extends K2Controller{

    /**
     * processa i dati del login
     * @Route("/loginProcessaDati", name="user_login")
     * @Method("POST");
     */
    public function login(Request $request) {
        try{


            $config = new EndpointConfiguration();
            $config->post = "UserBundle\Request\Post\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/loginProcessaDati";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *


                // Lancio il render della view:
            return $this->render('GalleryBundle:Default:home.html.twig',array('twig'=>$response));





        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * indirizza alla form per recupero della password
     * @Route("/recuperaPass", name="user_recovery")
     * @Method("GET");
     */
    public function recovery(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/recuperaPass";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("UserBundle:Default:formRecoveryPass.html.twig");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * cerca nel db una corrispondenza con l'username, se la trova restituisce la password
     * @Route("/recuperaPassword", name="user_recoverypass")
     * @Method("POST");
     */
    public function recoveryPass(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->post = "UserBundle\Request\Post\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/recuperaPassword";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *


            return $this->render("UserBundle:Default:viewPass.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * indirizza verso la form di registrazione
     * @Route("/registration", name="user_registration")
     * @Method("POST");
     */
    public function registration(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/registration";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("UserBundle:Default:formRegistration.html.twig");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * prende i dati dalla form e registra l'utente nel db
     * @Route("/processaDatiReg", name="user_processadatireg")
     * @Method("POST");
     */
    public function processaDatiReg(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->post = "UserBundle\Request\Post\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/processaDatiReg";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            //se arrivo in questo punto ho passato la validazione

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *

            // Lancio il render della view:
            return $this->render("UserBundle:Default:viewUtenteInserito.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * porta alla login da amministartore
     * @Route("/adminAccess", name="user_adminaccess")
     * @Method("GET");
     */
    public function adminAccess(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/adminAccess";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("UserBundle:Default:adminLogin.html.twig");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * login admin
     * @Route("/loginProcessaDatiAdmin", name="user_loginadmin")
     * @Method("POST");
     */
    public function loginAdmin(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->post = "UserBundle\Request\Post\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/loginProcessaDatiAdmin";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:home.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * prova ajax
     * @Route("/endPointAjax", name="user_provaajax")
     * @Method("POST");
     */
    public function provaAjax(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->rawbody = "UserBundle\Request\Rawbody\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/endPonitAjax";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Restituisco il JSON:
            return new JsonResponse($response, 200);


        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * prova validazione raw
     * @Route("/provaRawBody", name="user_provavalidazioneraw")
     * @Method("POST");
     */
    public function provaValidazioneRaw(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->rawbody = "UserBundle\Request\Rawbody\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/provaRawBody";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Restituisco il JSON:
            return new JsonResponse($response, 200);

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * prova di una chiamata ajax
     * @Route("/endPointAjax1", name="user_provaajax1")
     * @Method("POST");
     */
    public function provaAjax1(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->rawbody = "UserBundle\Request\Rawbody\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/endPointAjax1";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new UserModel($em, $container);
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

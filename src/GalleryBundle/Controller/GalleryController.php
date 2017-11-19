<?php 

namespace GalleryBundle\Controller;

use CoreBundle\Controller\K2Controller;

use GalleryBundle\Model\GalleryModel;

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

class GalleryController extends K2Controller{

    /**
     * porta alla form di inserimento dell' opera
     * @Route("/inserimentoOpera", name="gallery_inserisciopera")
     * @Method("POST");
     */
    public function inserisciOpera(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/inserimentoOpera";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);



            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:formInserimentoOpera.html.twig");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * inzirizza alla home
     * @Route("/tornaHome", name="gallery_tornahome")
     * @Method("POST");
     */
    public function tornaHome(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/tornaHome";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *


            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:home.html.twig",array('twig'=>$response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * vengo presi i dati dalla form, vengono validati e viene aggiunta un opera nel db
     * @Route("/processaDatiOpera", name="gallery_processadatiopera")
     * @Method("POST");
     */
    public function processaDatiOpera(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->post = "GalleryBundle\Request\Post\\" . ucfirst(__FUNCTION__);
            $config->login = false;
            $config->aclcode = "/processaDatiOpera";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);



            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
           

            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:viewOperaInserita.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * porta alla lista di tutte le opere inserite dell'utente
     * @Route("/listaOpereInserite", name="gallery_cercaopereinserite")
     * @Method("POST");
     */
    public function cercaOpereInserite(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/listaOpereInserite";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:viewlistaOpere.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * visualizza il dettaglio dell'autore
     * @Route("/dettagliAutore", name="gallery_dettaglioautore")
     * @Method("POST");
     */
    public function dettaglioAutore(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/dettagliAutore";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:viewAutore.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * cancella un opera inserita
     * @Route("/eliminaOpera", name="gallery_eliminaopera")
     * @Method("POST");
     */
    public function eliminaOpera(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/eliminaOpera";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:viewlistaOpere.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * porta alla lista degli autori delle opere inserite dall'utente
     * @Route("/listaAutori", name="gallery_listaautori")
     * @Method("POST");
     */
    public function listaAutori(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/listaAutori";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
           
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:viewlistaAutori.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * cerca le opere dell'autore cliccato
     * @Route("/cercaOpereAutore", name="gallery_cercaopereautore")
     * @Method("POST");
     */
    public function cercaOpereAutore(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/cercaOpereAutore";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("GalleryBundle:Default:viewlistaOpere.html.twig", array("twig" => $response));

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    /**
     * fa il log out: cancella lo user dalla sessione e torna al login
     * @Route("/logOut", name="gallery_logout")
     * @Method("POST");
     */
    public function logOut(Request $request) {
        try{

            $config = new EndpointConfiguration();
            $config->login = false;
            $config->aclcode = "/logOut";
            $config->context = array(
            );

            // Inizializzo in globalVars tutti i dati da passare al Model (+ gestione degli error code 400 - 401 - 403):
            $globalVars = $this->validateRequest($request, $config);

            // Inizializzo la risposta:
            $response = $this->initResponse($config, $globalVars);

            // Model *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            $em = $this->getDoctrine()->getManager();
            $container = $this->container;
            $model = new GalleryModel($em, $container);
            $response = $model->{__FUNCTION__}($globalVars, $response);
            // *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
            
            // Lancio il render della view:
            return $this->render("UserBundle:Default:login.html.twig");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    #####

}

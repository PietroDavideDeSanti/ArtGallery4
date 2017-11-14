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
     * manda alla finestra di login
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
            var_dump($response->data);
            die();
            // Lancio il render della view:
            return new Response("Hello World");

        } catch (HttpException $e) {
            return $this->get("MyException")->errorHttpHandler($e);
        }
    }

    #####

}

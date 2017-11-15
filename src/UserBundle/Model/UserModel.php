<?php 

namespace UserBundle\Model;

use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\Response;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Dependency Injection:
use Doctrine\Common\Persistence\ObjectManager as EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

use DbBundle\Entity\Utente;
use DbBundle\Entity\Profilo;

class UserModel {

    private $em;
    private $container;

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    function __construct(EntityManager $em, ContainerInterface $container) {
        $this->em = $em;
        $this->container = $container;
    }

    public function login (GlobalVars $globalVars, Response $response){
        try{

            // params è un oggetto con campo data(array) che contiene i parametri della richiesta POST/GET (in questo caso username e password)
            $params=$globalVars->params;

            $repository= $this->em->getRepository("DbBundle:Utente");

            $user=$repository->selectUserWhere($params->data["username"],$params->data["password"]);



            $response->data = $user;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function recovery (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function recoveryPass (GlobalVars $globalVars, Response $response){
        try{

            $username=$globalVars->params->data["username"];

            $repository= $this->em->getRepository("DbBundle:Utente");

            $utente=$repository->selectUserFromUname($username);


            $response->data = $utente;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function registration (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function processaDatiReg (GlobalVars $globalVars, Response $response){
        try{

            //creo gli oggetti


            $utente=new Utente();
            $utente->setNome($globalVars->params->data["nome"]);
            $utente->setCognome($globalVars->params->data["cognome"]);
            $utente->setUsername($globalVars->params->data["username"]);
            $utente->setPassword($globalVars->params->data["password"]);

            //creo un profilo
            $profilo=new Profilo();
            $profilo->setNomeProfilo("user");
            $profilo->addProfiloUtente($utente);

            //$utente->addProfilo($profilo);

            // chiamo la repository di utente

            $repository= $this->em->getRepository("DbBundle:Utente");
            $repositoryProfilo= $this->em->getRepository("DbBundle:Profilo");

            $idProf=$repositoryProfilo->insertProfilo($this->em,$profilo);
            $id=$repository->insertUtente($this->em,$utente);



            var_dump($idProf);
            die();

            $response->data = $id;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    #####

}

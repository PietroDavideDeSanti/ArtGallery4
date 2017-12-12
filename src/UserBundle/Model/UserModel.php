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


            if($user) {
               $globalVars->session->set("user",$user);
            }



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

           //cerco il profilo user
            
            $prof=$this->em->getRepository("DbBundle:Profilo")->findOneBy(array('nomeProfilo'=>'user'));
            
            if(!$prof){
                //creo un profilo
                $prof=new Profilo();
                $prof->setNomeProfilo("user");
                $this->em->persist($prof);
            }
           
          
            $utente=new Utente();
            $utente->setNome($globalVars->params->data["nome"]);
            $utente->setCognome($globalVars->params->data["cognome"]);
            $utente->setUsername($globalVars->params->data["username"]);
            $utente->setPassword($globalVars->params->data["password"]);
            $utente->addProfilo($prof);
          
            // chiamo la repository di utente

            $repository= $this->em->getRepository("DbBundle:Utente");

            $id=$repository->insertUtente($this->em,$utente);

            $response->data = $id;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function adminAccess (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function loginAdmin (GlobalVars $globalVars, Response $response){
        try{
            

            // admin è un booleano che vale true se l'utente è un admin

            //verifico se l'utente è nel db
            $repository= $this->em->getRepository("DbBundle:Utente");
            $user=$repository->selectUserWhere($globalVars->params->data["username"],$globalVars->params->data["password"]);

            if($user){

                $idUtente=$user->getId();

                //verifico se l'utente è un amministratore
                $admin=$this->em->getRepository("DbBundle:Utente")->isAdmin($this->em,$idUtente);

                if($admin){

                    //metto in sessione l'admin (inserisco una stringa qualunque per dire che è stato effettuato un accesso come admin)
                    $globalVars->session->set("admin",$user);
                }

            }




            $response->data = $user;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function provaAjax (GlobalVars $globalVars, Response $response){
        try{

            dump($globalVars->params->data["primoCampo"]);

            $response->data = $globalVars->params->data["primoCampo"];
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function provaValidazioneRaw (GlobalVars $globalVars, Response $response){
        try{

            $response->data = "tutto OK";
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function provaAjax1 (GlobalVars $globalVars, Response $response){
        try{


            $arr=[];

            $c1=$globalVars->params->data["primoCampo"];
            $c2=$globalVars->params->data["secondoCampo"];

            $arr[]=$c1;
            $arr[]=$c2;


            $response->data = $arr;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function pageElements (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function pageElements_element (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function formElement (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    #####

}

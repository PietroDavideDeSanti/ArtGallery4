<?php 

namespace DbBundle\Model;

use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\Response;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Dependency Injection:
use Doctrine\Common\Persistence\ObjectManager as EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DbModel {

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

    public function leggiDb (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function cercaDb (GlobalVars $globalVars, Response $response){
        try{
            
            //verifico se la tabella esiste
            $tabella=$globalVars->params->data["tabella"];
            
            $connection = $this->em->getConnection();
        
            $statement = $connection->prepare("SHOW TABLES FROM test");
            $statement->execute();
            
            $results = $statement->fetchAll();
            $bool=false;
            foreach($results as $arr){
                if($arr["Tables_in_test"]==$tabella)
                    $bool=true;
            }
            //se ho trovato la tabella
            if($bool){
            
                $connection = $this->em->getConnection();
        
                $statement = $connection->prepare("SELECT * FROM $tabella");
                $statement->execute();
            
                $results = $statement->fetchAll();
            }
            else{
                $results=[];
            }
            
            //-------------------------------
            

            $response->data = $results;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function provaServizio (GlobalVars $globalVars, Response $response){
        try{

//            $naming=$this->container->get("uv.naming");
//            $naming->setName("Marzullo");

            $naming1=$this->container->get("uv.service1");
            //dump($naming1);
            die();

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    #####

}

<?php 

namespace UserBundle\Model;

use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\Response;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Dependency Injection:
use Doctrine\Common\Persistence\ObjectManager as EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


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

    #####

}

<?php 

namespace GalleryBundle\Model;

use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\Response;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Dependency Injection:
use Doctrine\Common\Persistence\ObjectManager as EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DbBundle\Entity\Opera;
use DbBundle\Entity\Autore;


class GalleryModel {

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

    public function inserisciOpera (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function tornaHome (GlobalVars $globalVars, Response $response){
        try{

            $response->data = '';
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function processaDatiOpera (GlobalVars $globalVars, Response $response){
        try{

            $idUtente=$globalVars->session->get("user")->getId();

            $user = $this->em->getRepository("DbBundle:Utente")->find($idUtente);

            //controllo se autore esiste sul db
            $nomeAutore=$globalVars->params->data["nome"];
            $etaAutore=$globalVars->params->data["eta"];

            $autore=$this->em->getRepository("DbBundle:Autore")->findOneBy(array('nome'=>$nomeAutore,'eta'=>$etaAutore));

            //se l' autore non esiste
            if(!$autore){
                // creo l'autore e faccio la persist
                $autore=new Autore();
                $autore->setNome($globalVars->params->data["nome"]);
                $autore->setEta($globalVars->params->data["eta"]);
                $this->em->persist($autore);

            }

            //inizializzo opera
            $opera=new Opera();
            $opera->setTitolo($globalVars->params->data["titolo"]);
            $opera->setTecnica($globalVars->params->data["tecnica"]);
            $opera->setDimensioni($globalVars->params->data["dimensioni"]);
            $opera->setData(new \DateTime($globalVars->params->data["data"]));
            //setto idUtente dell'utente che ha inserito l'opera
            $opera->setUtenteId($user);
            //---------
            $opera->setAutoreId($autore);

            //faccio la persist di opera

            $repository= $this->em->getRepository("DbBundle:Opera");
            $idOpera=$repository->insertOpera($this->em,$opera);

            //$repositoryAut= $this->em->getRepository("DbBundle:Autore");

            $response->data = $idOpera;
            return $response;

        } catch (DBALException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    #####

}

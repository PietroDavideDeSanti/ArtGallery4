<?php

namespace CoreBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserActionListener implements EventSubscriber // EventSubscriber e' un'INTERFACCIA, anche se il nome non termina con "Interface"
{

    private $session;

    public function __construct($session){
        $this->session = $session;
    }

    public function getSubscribedEvents(){
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args){
        // Verifico se negli header e' presente l'accessToken:
//        if( array_key_exists("accessToken", apache_request_headers()) ) {
//            $entity = $args->getEntity();
//            $token = apache_request_headers()["accessToken"];
//            // Recupero il token:
//            $token = str_replace("Bearer", "", $token);
//            // Tramite il token, recupero in sessione lo userId:
//            $sessionData = $this->session->get($token);
//            $userId = $sessionData->getId();
//            // Scrivo nella entity la userAction:
//            $entity->setUserAction($userId);
//        }
    }

    public function preUpdate(LifecycleEventArgs $args){
//        // Verifico se negli header e' presente l'accessToken:
//        if( array_key_exists("accessToken", apache_request_headers()) ) {
//            $entity = $args->getEntity();
//            $token = apache_request_headers()["accessToken"];
//            // Recupero il token:
//            $token = str_replace("Bearer", "", $token);
//            // Tramite il token, recupero in sessione lo userId:
//            $sessionData = $this->session->get($token);
//            $userId = $sessionData->getId();
//            // Scrivo nella entity la userAction:
//            $entity->setUserAction($userId);
//
//            // Istruzioni in piu' per dire a doctrine che e' stato eseguito l'update e deve recuperare i dati aggiornati
//            $em = $args->getEntityManager();
//            $meta = $em->getClassMetadata(get_class($entity));
//            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
//            // <-- fine istruzioni
//        }
    }

}
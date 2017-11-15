<?php 

namespace DbBundle\Repository;

use CoreBundle\Libraries\AbstractRepository;
use CoreBundle\Utils\status as status;
use DbBundle\Entity\Utente;

/**
* UtenteRepository
*
*/
class UtenteRepository extends AbstractRepository {


    public function selectUserWhere($username,$password){


        $utente= $this->findOneBy(array('username'=>$username,'password'=>$password));


        return $utente;

    }

    public function selectUserFromUname($username){
        $utente= $this->findOneBy(array('username'=>$username));

        return $utente;


    }

    public function insertUtente($em,$utente){

        $em->persist($utente);


        $em->flush();

        return $utente->getId();

    }
}

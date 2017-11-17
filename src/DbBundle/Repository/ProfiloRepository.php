<?php 

namespace DbBundle\Repository;

use CoreBundle\Libraries\AbstractRepository;
use CoreBundle\Utils\status as status;
use DbBundle\Entity\Profilo;

/**
* ProfiloRepository
*
*/
class ProfiloRepository extends AbstractRepository {


    public function insertProfilo($em,$profilo){
        $em->persist($profilo);
        $em->flush();
        return $profilo->getId();

    }


    public function getProfilo($nomeProfilo){

        $results=$this->findOneBy(array('nomeProfilo'=>$nomeProfilo));

        
        return $results;

    }
}

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
        //oggetto profilo
        //$results= $this->findOneBy(array('id'=>6));
        // nomeProfilo

        $results=$this->findOneBy(array('nomeProfilo'=>$nomeProfilo));

        /*
        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT * 
                                           FROM profilo
                                           WHERE nomeProfilo='user' " );

        $statement->execute();
        $results = $statement->fetch();
        */

        
        return $results;

    }
}

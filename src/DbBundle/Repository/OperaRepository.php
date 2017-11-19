<?php 

namespace DbBundle\Repository;

use CoreBundle\Libraries\AbstractRepository;
use CoreBundle\Utils\status as status;
use DbBundle\Entity\Opera;

/**
* OperaRepository
*
*/
class OperaRepository extends AbstractRepository {
    public function get_all_by_autore_id($id, $sort=array(), $limit=null, $offset=null) {
        return parent::findBy( array('autoreId' => $id, 'status' => 'A'), $sort, $limit, $offset);
    }



    public function insertOpera($em,$opera){

        $em->persist($opera);

        $em->flush();
        return $opera->getId();

    }
    
    
    public function getAuthors($em,$idUtente){
        
        $connection = $em->getConnection();
        
        $statement = $connection->prepare("SELECT distinct  autore_id,nome,eta
                                            FROM(SELECT *
                                                 FROM opera
		                                 WHERE utente_id=$idUtente) op join autore a on op.autore_id=a.id");
  
        $statement->execute();
            // result contiene tutte le opere inserite dall'utente con id = $idUtente
        $results = $statement->fetchAll();
            
       
        return $results;
        
    }
}

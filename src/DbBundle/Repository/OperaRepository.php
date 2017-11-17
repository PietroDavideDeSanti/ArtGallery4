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
}

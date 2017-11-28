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

    public function selectAllOperas(){

        //array delle entity coinvolte
        $arrEntities = array(
            'DbBundle:Opera',

        );

        // Legami tra le entity (INNER JOIN...):
        $arrJoins = array(

        );

        // JOIN conditions (...ON...)
        $arrJoinsCondition = array(

        );

        // WHERE conditions (...WHERE...)
        $arrParams = array(

        );

    return parent::get_all_active($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition);

    }

    public function selectOperasAuthor(){

        //array delle entity coinvolte
        $arrEntities = array(
            'DbBundle:Opera',

        );

        // Legami tra le entity (INNER JOIN...):
        $arrJoins = array(

        );

        // JOIN conditions (...ON...)
        $arrJoinsCondition = array(

        );

        // WHERE conditions (...WHERE...)
        $arrParams = array(
            'Opera.autoreId = 8'
        );

        return parent::get_all_active($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition);


    }


    public function selectOperasAuthorTecnic(){

        //array delle entity coinvolte
        $arrEntities = array(
            'DbBundle:Opera',

        );

        // Legami tra le entity (INNER JOIN...):
        $arrJoins = array(

        );

        // JOIN conditions (...ON...)
        $arrJoinsCondition = array(

        );

        // WHERE conditions (...WHERE...)
        $arrParams = array(
            "Opera.autoreId = 8",
            "Opera.tecnica = 'erterte'",
        );

        return parent::get_all_active($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition);


    }


    public function operaJoinAutore(){
        //array delle entity coinvolte
        $arrEntities = array(
            'DbBundle:Opera',
            'DbBundle:Autore',
        );

        // Legami tra le entity (INNER JOIN...):
        $arrJoins = array(
            AbstractRepository::$inner.' Opera.autoreId'=>'Autore',
        );

        // JOIN conditions (...ON...)
        $arrJoinsCondition = array(
            AbstractRepository::$inner.' Opera.autoreId'=>'Autore.id = 8',
        );

        // WHERE conditions (...WHERE...)
        $arrParams = array(

            "Opera.tecnica = 'erterte'",

        );

        return parent::get_one_active($arrEntities, $arrJoins, $arrParams, $arrJoinsCondition);


    }

    public function addOpera($entity){

        return parent::insert_entity($entity);

    }
}

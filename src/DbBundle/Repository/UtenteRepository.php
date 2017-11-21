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

    public function isAdmin($em,$idUtente){

        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT *
                                           FROM utente_profilo up join profilo p on up.profilo_id=p.id
                                           WHERE utente_id=$idUtente");

        $statement->execute();
        // result contiene tutte le opere inserite dall'utente con id = $idUtente
        $results = $statement->fetchAll();

        $isAdmin=false;

        foreach($results as $arr){
            //completa
            if($arr["nomeProfilo"]=="admin"){
                $isAdmin=true;
            }

        }



        return $isAdmin;
    }
}

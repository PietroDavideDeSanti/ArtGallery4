<?php
/**
 * Created by PhpStorm.
 * User: K22017
 * Date: 24/11/2017
 * Time: 10:00
 */
namespace DbBundle\Services;
class Naming
{

    private $name;

    public function setName($name){
        $this->name=$name;
    }

    public function getName(){
        return $this->name;
    }

    public function stampaStringa(){
        echo "stringa da stampare! ";
    }

    public function ritornaStringa(){
        return "casa";
    }
}
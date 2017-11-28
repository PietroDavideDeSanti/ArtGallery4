<?php
/**
 * Created by PhpStorm.
 * User: K22017
 * Date: 24/11/2017
 * Time: 10:00
 */
namespace DbBundle\Services;
class Naming1
{
    private $service;
    private $stringa;

    public function __construct($service,$stringa){
        $this->service=$service;
        $this->stringa=$stringa;


    }


    public function stampaStringa(){
        echo "seconda stringa da stampare! ".$this->stringa;
    }

    public function stampa(){

        $this->service->stampaStringa();
    }

    public function provaStampa($st){


    }
}
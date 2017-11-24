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

    public function __construct($service){
        $this->service=$service;

    }


    public function stampaStringa(){
        echo "seconda stringa da stampare!";
    }

    public function stampa(){

        $this->service->stampaStringa();
    }
}
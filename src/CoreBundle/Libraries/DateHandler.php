<?php

namespace CoreBundle\Libraries;

class dateHandler {
    
    public static function ConvertiData($data) {
        // $data e' in formato '19901212' ('AAAAMMGG')
        if(strlen($data) == 8){
           return substr($data,0,4).'-'.substr($data,4,2).'-'.substr($data,6,2);
        } 
        
        if(strlen($data) == 6){
        return substr($data,0,4).'-'.substr($data,4,2).'-01';
        }
        // $dataConvertita e' in formato 'AAAA-MM-GG'
        
        else return $data;
    }

    public static function ConvertiDataItaliana($data){
        // $data e' in formato '16/07/2017' ('gg/mm/aaaa')
        $data = trim($data);
        return substr($data,6,4).'-'.substr($data,3,2).'-'.substr($data,0,2);
    }
    
    public static function DividiPerCento($data) {
        
        return $data/100;
    }
}


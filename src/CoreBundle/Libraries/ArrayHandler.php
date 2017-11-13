<?php

namespace CoreBundle\Libraries;


class ArrayHandler
{

    public function getFlatDataRecursive($array, $prefix = '') {
        $result = array();
        foreach($array as $key=>$value) {
            if(is_array($value)) {
                $result = $result + $this->getFlatDataRecursive($value, $prefix . $key . "_");
            }
            else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    public function changeKey( $array, $old_key, $new_key ) {

        if( ! array_key_exists( $old_key, $array ) )
            return $array;

        $keys = array_keys( $array );
        $keys[ array_search( $old_key, $keys ) ] = $new_key;

        return array_combine( $keys, $array );
    }

    public function arrayInsertAfter($key, array &$array, $newKey, $newValue){
        if(array_key_exists($key, $array)){
            $new = array();
            foreach($array as $k => $value){
                $new[$k] = $value;
                if($k == $key){
                    $new[$newKey] = $newValue;
                }
            }
            return $new;
        } else {
            return $array;
        }
    }

}
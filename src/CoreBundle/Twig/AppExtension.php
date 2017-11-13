<?php
/**
 * Created by PhpStorm.
 * User: Michele Melillo
 * Date: 23/06/2017
 * Time: 13:43
 */

namespace CoreBundle\Twig;


class AppExtension extends \Twig_Extension{
    private $url_symfony = "/php/k2/sf/k2/web/";

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('NoCache', array($this, 'NoCache'))
        );
    }

    public function NoCache($var){
        $url = $_SERVER['DOCUMENT_ROOT'].$this->url_symfony.$var;
        if(file_exists($url)){
            return $var."?v=".filemtime($url);
        } else {
            return $var."?v=".random_int(0, 1000);
        }
    }

}
<?php
namespace CoreBundle\Protocol;

class EndpointConfiguration {
    public $headers = null;
    public $rawbody = null;
    public $post = null;
    public $querystring = null;
    public $encodingUrl = null;
    public $files = null;
    public $sso = null;
    public $session = null;

    public $login = null;
    public $aclcode = null;
    public $context = array();

}
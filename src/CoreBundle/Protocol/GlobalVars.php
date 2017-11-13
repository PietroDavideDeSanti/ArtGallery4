<?php

namespace CoreBundle\Protocol;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GlobalVars {

    /**
     * @var ChannelVars
     */
    public $channel = null;

    /**
     * @var ParamVars
     */
    public $params;

    /**
     * @var UserVars
     */
    public $user;

    /**
     * @var LoginVars
     */
    public $login;

    /**
     * @var FileVars
     */
    public $file;

    /**
     * @var ServerVars
     */
    public $server;

    /**
     * @var SessionInterface
     */
    public $session;


    function __construct() {
        $this->channel = new ChannelVars();
        $this->params = new ParamVars();
        $this->user = new UserVars();
        $this->login = new LoginVars();
        $this->file = new FileVars();
        $this->server = new ServerVars();
    }


}



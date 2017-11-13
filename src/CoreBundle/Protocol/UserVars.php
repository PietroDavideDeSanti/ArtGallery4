<?php

namespace CoreBundle\Protocol;


class UserVars {
    /**
     * user_id
     * @var integer
     */
    public $id;

    /**
     * array di profile_id
     * @var integer[]
     */
    public $profile = null;

    /**
     * @var string
     */
    public $username = null;

    /**
     * @var string
     */
    public $fiscal = null;

    /**
     * Ruolo dell'utente
     * @var integer
     */
    public $role = null;

    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $surname = null;

    /**
     * @var string
     */
    public $email = null;

    /**
     * Array di key->value
     * @var mixed
     */
    public $details = null;
}

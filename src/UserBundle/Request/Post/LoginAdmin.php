<?php

namespace UserBundle\Request\Post;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author K2
 * Per la documentazione sugli Assert, vedere qui:
 * http://symfony.com/doc/current/validation.html
 * 
 */
class LoginAdmin {

     /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $password;
    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }



}

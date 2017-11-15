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
class RecoveryPass {

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }



}

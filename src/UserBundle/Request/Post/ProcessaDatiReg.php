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
class ProcessaDatiReg {

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $nome;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $cognome;

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

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * @param mixed $cognome
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;
    }

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

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }





}

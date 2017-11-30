<?php

namespace UserBundle\Request\Rawbody;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author K2
 * Per la documentazione sugli Assert, vedere qui:
 * http://symfony.com/doc/current/validation.html
 * 
 */
class ProvaAjax1 {


    /**
     * @Assert\NotBlank()
     */
    protected $primoCampo;


    /**
     * @Assert\NotBlank()
     */
    protected $secondoCampo;

    /**
     * @return mixed
     */
    public function getPrimoCampo()
    {
        return $this->primoCampo;
    }

    /**
     * @param mixed $primoCampo
     */
    public function setPrimoCampo($primoCampo)
    {
        $this->primoCampo = $primoCampo;
    }

    /**
     * @return mixed
     */
    public function getSecondoCampo()
    {
        return $this->secondoCampo;
    }

    /**
     * @param mixed $secondoCampo
     */
    public function setSecondoCampo($secondoCampo)
    {
        $this->secondoCampo = $secondoCampo;
    }





}

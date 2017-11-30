<?php

namespace DbBundle\Request\Rawbody;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author K2
 * Per la documentazione sugli Assert, vedere qui:
 * http://symfony.com/doc/current/validation.html
 * 
 */
class CercaDb {
    
    
     /**
     * @Assert\NotBlank()
     */
    protected $tabella;

    /**
     * @return mixed
     */
    public function getTabella()
    {
        return $this->tabella;
    }

    /**
     * @param mixed $tabella
     */
    public function setTabella($tabella)
    {
        $this->tabella = $tabella;
    }


    







}

<?php

namespace DbBundle\Request\Post;

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
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $tabella;
    
    
    function getTabella() {
        return $this->tabella;
    }

    function setTabella($tabella) {
        $this->tabella = $tabella;
    }






}

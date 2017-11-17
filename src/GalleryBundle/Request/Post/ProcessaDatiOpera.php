<?php

namespace GalleryBundle\Request\Post;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author K2
 * Per la documentazione sugli Assert, vedere qui:
 * http://symfony.com/doc/current/validation.html
 * 
 */
class ProcessaDatiOpera {


    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $titolo;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $tecnica;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $dimensioni;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $data;

    // dati relativi all'autore

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $nome;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $eta;

    /**
     * @return mixed
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * @param mixed $titolo
     */
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;
    }

    /**
     * @return mixed
     */
    public function getTecnica()
    {
        return $this->tecnica;
    }

    /**
     * @param mixed $tecnica
     */
    public function setTecnica($tecnica)
    {
        $this->tecnica = $tecnica;
    }

    /**
     * @return mixed
     */
    public function getDimensioni()
    {
        return $this->dimensioni;
    }

    /**
     * @param mixed $dimensioni
     */
    public function setDimensioni($dimensioni)
    {
        $this->dimensioni = $dimensioni;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

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
    public function getEta()
    {
        return $this->eta;
    }

    /**
     * @param mixed $eta
     */
    public function setEta($eta)
    {
        $this->eta = $eta;
    }








}

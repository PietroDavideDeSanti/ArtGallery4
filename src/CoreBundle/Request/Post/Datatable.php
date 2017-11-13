<?php

namespace CoreBundle\Request\Post;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of datatable
 *
 * @author K2
 */
class Datatable {
    
    /**
     * @Assert\NotBlank(message = "§Il valore del campo draw non può essere vuoto§")
     */
    protected $draw;

    /**
     * @Assert\NotBlank(message = "§Il valore del campo columns non può essere vuoto§")
     */
    protected $columns;
    

    protected $order;
    
    /**
     * @Assert\NotBlank(message = "§Il valore del campo start non può essere vuoto§")
     */
    protected $start;
    
    /**
     * @Assert\NotBlank(message = "§Il valore del campo length non può essere vuoto§")
     */
    protected $length;
    
    /**
     * @Assert\NotBlank(message = "§Il valore del campo search non può essere vuoto§")
     */
    protected $search;
    
    protected $custom;
    
    public function getDraw() {
        return $this->draw;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getStart() {
        return $this->start;
    }

    public function getLength() {
        return $this->length;
    }

    public function getSearch() {
        return $this->search;
    }

    public function setDraw($draw) {
        $this->draw = $draw;
    }

    public function setColumns($columns) {
        $this->columns = $columns;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

    public function setStart($start) {
        $this->start = $start;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    public function setSearch($search) {
        $this->search = $search;
    }

    public function getCustom() {
        return $this->custom;
    }

    public function setCustom($custom) {
        $this->custom = $custom;
    }



}

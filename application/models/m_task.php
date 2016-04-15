<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of m_task
 *
 * @author adriannatabio
 */
class M_Task extends CI_Model {

    
    
    protected $table = 'tasks';

    function __construct() {
        parent::__construct();
    }

    public function add($data) {
        return $this->db->insert($this->table, $data);
    }

    public function get() {
        
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of m_chart
 *
 * @author Adr
 */
class M_supply extends CI_Model 
{
    
    protected $table;


    public function __construct() {
        parent::__construct();
        $this->table = 'inventory_product';
    }

    public function insert($data) {
        $data['fk_class_id'] = 3; // 3 -> Supplies
        $saved = $this->db->insert('inventory_product', $data);
        return $saved ? $this->db->insert_id() : FALSE;
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function update($data, $id) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function all() {
        return $this->db->select('a.description, a.id, fk_unit_id, unit.description AS unit_description')->from($this->table.' AS a')->join('inventory_unit as unit', 'unit.id = a.fk_unit_id', 'left')->where('fk_class_id', 3)->get()->result_array();
    }

}

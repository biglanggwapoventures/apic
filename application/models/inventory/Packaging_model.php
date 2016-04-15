<?php

class Packaging_model extends CI_Controller {

    protected $table = 'pm_inventory_unit';

    public function __construct() {
        parent::__construct();
    }

    public function all() {
        return $this->db->get($this->table)->result();
    }

    public function create($data) {
        return $this->db->insert($this->table, $data) ? $this->db->insert_id() : FALSE;
    }
    
    public function update($id, $data){
        return $this->db->update($this->table, $data, ['id' => $id]);
    }
}

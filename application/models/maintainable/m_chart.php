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
class M_chart extends CI_Model 
{

    public function __construct() {
        parent::__construct();
        $this->description = NULL;
        $this->added_by = NULL;
    }

    public function insert($description, $added_by) {
        $saved = $this->db->insert('maintainable_coa', [
            'description' => $description,
            'added_by' => $added_by
        ]);
        return $saved ? $this->db->insert_id() : FALSE;
    }

    public function delete($id) {
        return $this->db->update('maintainable_coa', ['is_deleted' => 1], ['id' => $id]);
    }

    public function update($id, $description) {
        return $this->db->update('maintainable_coa', ['description' => $description], ['id' => $id]);
    }

    public function all($show_deleted = FALSE) {
        if($show_deleted === FALSE){
            $this->db->where(['is_deleted' => 0]);
        }
        return $this->db->select('description, id')->from('maintainable_coa')->order_by('description')->get()->result_array();
    }
    
    public function is_valid($id){
        $this->db->select('id')->from('maintainable_coa')->where('is_deleted', 0);
        if(is_array($id)){
            $this->db->where_in('id', $id);
            return $this->db->get()->num_rows() == count($id);
        }
        return $this->db->where('id', $id)->get()->num_rows() > 0;
    }

}

<?php

class M_Class extends CI_Model {

    function add($description = FALSE) {
        if ($this->db->insert('inventory_class', array('Description' => $description))) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    function update($ID = FALSE, $new_description = FALSE) {
        $this->db->where('id', $ID);
        return $ID ? $this->db->update('inventory_class', array('description' => $new_description)) : FALSE;
    }

    function get($search_token = FALSE, $filter = array(), $limit = 999, $offset = 0) {
        if ($search_token) {
            $this->db->like('description', $search_token, 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $result = $this->db->limit($limit, $offset)->get('inventory_class')->result_array();
        return $result;
    }

    function delete($ID) {
        if ($ID) {
            $this->db->where('id', $ID);
            return $this->db->delete('inventory_class');
        }
        return FALSE;
    }

    function raw() {
        return 1;
    }

    function finished() {
        return 2;
    }

}

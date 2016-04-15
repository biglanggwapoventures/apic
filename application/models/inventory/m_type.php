<?php

class M_Type extends CI_Model {

    function add($description = FALSE) {
        if ($this->db->insert('inventory_type', array('Description' => $description))) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    function update($ID = FALSE, $new_description = FALSE) {
        $this->db->where('id', $ID);
        return $ID ? $this->db->update('inventory_type', array('description' => $new_description)) : FALSE;
    }

    function get($search_token = FALSE, $filter = array(), $limit = 999, $offset = 0) {
        if ($search_token) {
            $this->db->like('description', $search_token, 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $result = $this->db->limit($limit, $offset)->get('inventory_type')->result_array();
        return $result;
    }

    function delete($ID) {
        if ($ID) {
            $this->db->where('id', $ID);
            return $this->db->delete('inventory_type');
        }
        return FALSE;
    }

}

<?php

class M_Supplier extends CI_Model {

    const TABLE_NAME_GENERAL = 'purchase_supplier';

    public function add($data) {
        return $this->db->insert(self::TABLE_NAME_GENERAL, $data) ? $this->db->insert_id() : FALSE;
    }

    public function update($field_name, $field_value, $primary_key) {
        $this->db->where('id', $primary_key);
        return $this->db->update(self::TABLE_NAME_GENERAL, array($field_name => $field_value));
    }

    public function get($id = FALSE) {
        if ($id) {
            return $this->db->get_where(self::TABLE_NAME_GENERAL, array('id' => $id))->row_array();
        }
        return $this->db->get(self::TABLE_NAME_GENERAL)->result_array();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete(self::TABLE_NAME_GENERAL);
    }

    public function is_valid($id) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if (is_array($id)) {
            $id = array_unique($id);
            $this->db->where_in('id', $id);
            $count = $this->db->get()->num_rows();
            return $count === count($id);
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

}

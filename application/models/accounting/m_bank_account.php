<?php

class M_Bank_Account extends CI_Model {

    const TABLE_NAME_GENERAL = "accounting_bank_account";

    public function add($data) {
        return $this->db->insert(self::TABLE_NAME_GENERAL, $data) ? $this->db->insert_id() : FALSE;
    }

    public function get($id = FALSE) {
        if ($id) {
            $this->db->where('id', $id);
        }
        return $this->db->get(self::TABLE_NAME_GENERAL)->result_array();
    }

    public function update($id, $field_name, $new_value) {
        $this->db->where('id', $id);
        return $this->db->update(self::TABLE_NAME_GENERAL, array($field_name => $new_value));
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete(self::TABLE_NAME_GENERAL);
    }

    public function is_valid($id) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if (is_array($id)) {
            $this->db->where_in('id', $id);
            $count = $this->db->get()->num_rows();
            return $count === count(array_unique($id));
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

}

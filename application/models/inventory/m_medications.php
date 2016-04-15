<?php

class M_Medications extends CI_Model {

    private $_table = 'inventory_medications';

    public function insert($product_code, $description) {
        $inserted = $this->db->insert($this->_table, [
            'product_code' => $product_code,
            'description' => $description
        ]);
        return $inserted ? $this->db->insert_id() : FALSE;
    }

    public function update($id, $code, $description) {
        return $this->db->where(['id' => $id])->update($this->_table, [
                    'code' => $code,
                    'description' => $description
        ]);
    }

    public function all() {
        return $this->db->order_by('description', 'ASC')->get($this->_table)->result_array();
    }

    public function get($id) {
        return $this->db->where(['id' => $id])->get($this->_table)->row_array();
    }

    public function delete($id) {
        return $this->db->where(['id' => $id])->delete($this->_table);
    }

}

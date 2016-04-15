<?php

class M_supplier extends CI_Model {

    protected $table;
    public $fields = NULL;

    public function __construct() {
        parent::__construct();
        $this->table = 'maintainable_suppliers';
    }

    public function insert($value_array) {
        $saved = $this->db->insert($this->table, $value_array);
        return $saved ? $this->db->insert_id() : FALSE;
    }

    public function delete($id) {
        return $this->db->update($this->table, ['is_deleted' => 1], ['id' => $id]);
    }

    public function update($value_array, $id) {
        return $this->db->update($this->table, $value_array, ['id' => $id]);
    }

    public function get_assigned_supplies($supplier_id) {
        return $this->db->select('a.id, a.fk_inventory_product_id, p.id as p_id, p.code, p.description, u.description AS unit_description')
                ->from('maintainable_supplier_supplies as a')
                ->where(['fk_maintainable_supplier_id' => $supplier_id])
                ->join('inventory_product as p', 'p.id = a.fk_inventory_product_id', 'left')
                ->join('inventory_unit AS u', 'u.id = p.fk_unit_id')
                ->get()->result_array();
    }

    public function save_assigned_materials($supplier_id, $array) {
        //check if data is empty;
        if (empty($array)) {
            //data is empty, delete all
            return $this->db->delete('maintainable_supplier_supplies', ['fk_maintainable_supplier_id' => $supplier_id]);
        }
        $data = [];
        foreach ($array as $item) {
            if (array_key_exists('id', $item)) {
                $data['old'][] = $item;
                $data['ids'][] = $item['id'];
            } else {
                $data['new'][] = $item;
            }
        }
        $this->db->trans_begin();
        //check if there are old data 
        if (array_key_exists('old', $data)) {
            //there are old data, update them
            $this->db->update_batch('maintainable_supplier_supplies', $data['old'], 'id');
            //there might be non-existing old data, remove them
            $this->db->where(['fk_maintainable_supplier_id' => $supplier_id])->where_not_in('id', $data['ids'])->delete('maintainable_supplier_supplies');
        } else {
            //there are no old data, delete all old data
            $this->db->delete('maintainable_supplier_supplies', ['fk_maintainable_supplier_id' => $supplier_id]);
        }
        //check if there are new data
        if (array_key_exists('new', $data)) {
            //there are new data, insert them
            $this->db->insert_batch('maintainable_supplier_supplies', $data['new']);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function all($show_deleted = FALSE) {
        if($this->fields !== NULL){
            $this->db->select(implode($this->fields, ','));
        }
        if ($show_deleted === FALSE) {
            $this->db->where(['is_deleted' => 0]);
        }
        return $this->db->from($this->table)->order_by('name', 'ASC')->get()->result_array();
    }

    public function get($id, $show_deleted = FALSE) {
        if ($show_deleted === FALSE) {
            $this->db->where(['is_deleted' => 0]);
        }
        return $this->db->select('*')->from($this->table)->where('id', $id)->get()->row_array();
    }

    public function is_unique($field, $value) {
        $data = $this->db->select('id')->from($this->table)->where([$field => $value, 'is_deleted' => 0])->get();
        if ($data->num_rows() > 0) {
            return FALSE;
        }
        return TRUE;
    }

    public function is_valid($id = array()) {
        $this->db->select('id')->from($this->table);
        if (is_array($id)) {
            $this->db->where_in('id', $id);
            return count($id) === $this->db->get()->num_rows();
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

}

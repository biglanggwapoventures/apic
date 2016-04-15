<?php

class Medication_model extends CI_Model {

    protected $table = 'inventory_product';
    protected $class_id = 4;

    public function all() {
        return $this->db->select('p.id, p.code, p.description, p.status, p.fk_unit_id, u.description unit_description')
                        ->join('inventory_unit u', 'u.id = fk_unit_id')
                        ->from($this->table . ' p')
                        ->where('fk_class_id', $this->class_id)
                        ->get()
                        ->result();
    }

    public function create($data) {
        $data['fk_class_id'] = $this->class_id;
        $result = $this->db->insert($this->table, $data);
        return $result ? $this->db->insert_id() : FALSE;
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id, 'fk_class_id' => $this->class_id]);
    }

    public function is_unique($attr, $value, $id = FALSE) {
        if (is_numeric($id)) {
            $this->db->where('id !=', $id);
        }
        return $this->db->select($attr)
                        ->from($this->table)
                        ->where([
                            'fk_class_id' => $this->class_id,
                            $attr => $value
                        ])
                        ->get()->num_rows() === 0;
    }

}

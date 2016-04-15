<?php

class M_print_check extends CI_Model
{
	protected $table = 'accounting_printed_checks';

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function all($show_deleted = FALSE)
    {
        $this->db->select('id, bank_account, payee, DATE_FORMAT(created_at, "%m/%d/%Y") AS `created_at`,  DATE_FORMAT(created_at, "%m/%d/%Y") AS `check_date`, amount, check_number', FALSE);
        $this->db->where('deleted_at IS NULL', FALSE, FALSE)->order_by('id', 'DESC');
        return $this->db->limit(100)->get($this->table)->result_array();
    }

    public function get($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    public function delete($id)
    {
        return $this->db->update($this->table, ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    }

    public function is_approved($id)
    {
        $this->db->select('id')->from($this->table)->where('approved_by IS NOT NULL', FALSE, FALSE)->where('id', $id);
        return $this->db->get()->num_rows() === 1;
    }

    public function is_valid($id)
    {
        $this->db->select('id')->from($this->table);
        if (is_array($id)) {
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
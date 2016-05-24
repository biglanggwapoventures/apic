<?php

class M_dummy_check extends CI_Model
{

    protected $table = 'accounting_dummy_checks';

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function all($show_deleted = FALSE, $offset=0, $where=array())
    {
        $this->db->order_by('id', 'DESC');
        if ($show_deleted === FALSE) {
            $this->db->where('deleted_at IS NULL', FALSE, FALSE);
        }
        $this->db->select('dc.id, dc.payee, DATE_FORMAT(dc.date, "%m/%d/%Y") AS `formatted_date`,  dc.date, dc.check_amount, account.Username as created_by', FALSE);
        $this->db->from($this->table.' as dc');
        $this->db->join('account', 'account.id=dc.created_by');
        $this->db->limit(100, $offset);
        if(isset($where['dc.payee'])){
            $this->db->like('dc.payee', $where['dc.payee']);
            unset($where['dc.payee']);
        }
        if(!empty($where)){
            $this->db->where($where);
        }
        return $this->db->get()->result_array();
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

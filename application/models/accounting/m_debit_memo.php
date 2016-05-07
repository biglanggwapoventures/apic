<?php

class M_debit_memo extends CI_Model
{

    protected $table = 'accounting_debit_memo';

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
        $this->db->order_by('id', 'DESC');
        if ($show_deleted === FALSE) {
            $this->db->where('deleted_at IS NULL', FALSE, FALSE);
        }
        $this->db->select('dc.id, DATE_FORMAT(dc.date, "%m/%d/%Y") AS `formatted_date`, dc.remarks, dc.date, dc.amount, customer.company_name, account.Username as created_by, accounts.Username as approved_by, dc.created_at as created_at', FALSE);
        $this->db->from($this->table.' as dc');
        $this->db->join('account', 'account.id=dc.created_by');
        $this->db->join('account as accounts', 'accounts.id=dc.approved_by', 'left');
        $this->db->join('sales_customer as customer', 'customer.id = dc.fk_sales_customer_id');
        return $this->db->get()->result_array();
    }

    public function get($id)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->select('dc.id, DATE_FORMAT(dc.date, "%m/%d/%Y") AS `formatted_date`, dc.remarks, dc.date, dc.amount, customer.company_name, account.Username as created_by, accounts.Username as approved_by, dc.created_at as created_at', FALSE);
        $this->db->from($this->table.' as dc');
        $this->db->join('account', 'account.id=dc.created_by');
        $this->db->join('account as accounts', 'accounts.id=dc.approved_by', 'left');
        $this->db->join('sales_customer as customer', 'customer.id = dc.fk_sales_customer_id');
        return $this->db->get_where($this->table, ['dc.id' => $id])->row_array();
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

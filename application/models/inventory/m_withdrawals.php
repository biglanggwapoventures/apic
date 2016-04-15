<?php

class M_withdrawals extends CI_Model 
{

    protected $table = 'inventory_stock_withdrawal';
    protected $details_table = 'inventory_stock_withdrawal_detail';

    public function create($data)
    {
        $this->load->helper('pmarray');
        $this->db->trans_begin();

        $this->db->insert($this->table, $data['sa']);
        $id = $this->db->insert_id();

        array_walk($data['details'], 'insert_prop', ['name' => 'withdrawal_id', 'value' => $id]);

        $this->db->insert_batch($this->details_table, $data['details']);

        if ($this->db->trans_status() === TRUE) 
        {
            $this->db->trans_commit(); 
            return $id; 
        } 
        else 
        {
            $this->db->trans_rollback();
            return FALSE;
        }
    }   

    public function update($id, $data)
    {
        $this->db->trans_begin();

        $this->db->update($this->table, $data['sa'], ['id' => $id]);

        $details = ['new' => [], 'existing' => [], 'existing_ids' => []];

        foreach ($data['details'] AS $row) 
        {
            if (isset($row['id'])) 
            {
                $details['existing'][] = $row;
                $details['existing_ids'][] = $row['id'];
            }
            else 
            {
                $row['withdrawal_id'] = $id;
                $details['new'][] = $row;
            }
        }

        if(empty($details['existing_ids']))
        {
            $this->db->delete($this->details_table, ['withdrawal_id' => $id]);
        }
        else
        {
            $this->db->where_not_in('id', $details['existing_ids'])->delete($this->details_table, ['withdrawal_id' => $id]);
        }

        //update existing
        if (!empty($details['existing'])) {
            $this->db->update_batch($this->details_table, $details['existing'], 'id');
        }

        //insert new
        if (!empty($details['new'])) {
            $this->db->insert_batch($this->details_table, $details['new']);
        }

        if ($this->db->trans_status() === TRUE) 
        {
            $this->db->trans_commit();
            return TRUE;
        } 
        else 
        {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function all()
    {
        $this->db->select('adj.id, adj.date, adj.approved_by, a.username')->from($this->table.' AS adj');  
        $this->db->join('account AS a', 'a.id = adj.created_by');
        return $this->db->order_by('adj.id', 'DESC')->get()->result_array();
    }

    public function get($id)
    {
        $data['sw'] = $this->db->select('id, date, approved_by, remarks')->from($this->table)->where('id', $id)->get()->row_array();
        $data['details'] = $this->db->select('id, product_id, quantity, unit_price')->from($this->details_table)->where('withdrawal_id', $id)->get()->result_array();
        return $data;
    }

    public function is_valid($id, $options = FALSE) 
    {
        $this->db->select('id')->from($this->table);
        if($options !== FALSE)
        {
            $this->db->where($options);
        }
        if (is_array($id)) 
        {
            $ids = array_unique($id);
            $this->db->where_in('id', $ids);
            return count($ids) === $this->db->get()->num_rows();
        } 
        else 
        {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count === 1;
        }
    }

    public function has_valid_details($id, $detail_ids)
    {
        return $this->db->select('id')->from($this->details_table)->where('withdrawal_id', $id)->get()->num_rows() === count($detail_ids);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}

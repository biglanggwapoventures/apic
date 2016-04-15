<?php

class M_gatepass extends CI_Model
{
	protected $table = 'gatepass';

	public function create($data)
	{
		$this->load->helper('pmarray');
		$this->db->trans_begin();

		$this->db->insert($this->table, $data['gp']);
		$id = $this->db->insert_id();

		if(isset($data['pls']))
		{
			$pls = [];
			foreach($data['pls'] as $pl)
			{
				$pls[] = ['pl_id' => $pl, 'gatepass_id' => $id];
			}
			
			$this->db->insert_batch('gatepass_packing_lists', $pls);
		}
		else if(isset($data['items']))
		{
			array_walk($data['items'], 'insert_prop', ['name' => 'gatepass_id', 'value' => $id]);
			$this->db->insert_batch('gatepass_items', $data['items']);
		}

		if ($this->db->trans_status() === FALSE) 
		{
            $this->db->trans_rollback();
            return FALSE;
        } 
        else 
        {
            $this->db->trans_commit();
            return TRUE;
        }
	}

	public function update($id, $data)
	{
		$this->load->helper('pmarray');
		$this->db->trans_begin();

		$this->db->update($this->table, $data['gp'], ['id' => $id]);

		$this->db->delete('gatepass_packing_lists', ['gatepass_id' => $id]);
		$this->db->delete('gatepass_items', ['gatepass_id' => $id]);

		if(isset($data['pls']))
		{
			$pls = [];
			foreach($data['pls'] as $pl)
			{
				$pls[] = ['pl_id' => $pl, 'gatepass_id' => $id];
			}
			$this->db->insert_batch('gatepass_packing_lists', $pls);
		}
		else if(isset($data['items']))
		{
			array_walk($data['items'], 'insert_prop', ['name' => 'gatepass_id', 'value' => $id]);
			$this->db->insert_batch('gatepass_items', $data['items']);
		}

		if ($this->db->trans_status() === FALSE) 
		{
            $this->db->trans_rollback();
            return FALSE;
        } 
        else 
        {
            $this->db->trans_commit();
            return TRUE;
        }
	}

	public function all($show_deleted = FALSE)
	{
		$this->db->order_by('id', 'DESC');
		$this->db->select('gp.id, type, issued_to, customer.company_name AS customer, DATE_FORMAT(created_at, "%m/%d/%Y %h:%i:%s %p") AS `formatted_date`, account.username AS created_by', FALSE);
		$this->db->from($this->table.' AS gp');
		$this->db->join('sales_customer AS customer', 'customer.id = gp.customer_id', 'left');
		$this->db->join('account', 'account.id = gp.created_by');
		$this->db->order_by('id', 'DESC');
		$this->db->limit(100);
		return $this->db->get()->result_array();
	}

	public function get($id)
	{
		$this->db->select('gp.*, customer.company_name AS customer, customer.id AS customer_id, trucking');
		$this->db->from($this->table.' AS gp')->join('sales_customer AS customer', 'customer.id = gp.customer_id', 'left');
		$this->db->where('gp.id', $id);
		$data = $this->db->get()->row_array();
		if($data['type'] === 'pl')
		{
			$data['pls'] = $this->db->get_where('gatepass_packing_lists', ['gatepass_id' => $id])->result_array();
		}
		else
		{
			$data['items'] = $this->db->get_where('gatepass_items', ['gatepass_id' => $id])->result_array();
		}
		return $data;
	}

	public function delete($id)
	{
		return $this->db->delete($this->table, ['id' => $id]);
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

	public function get_available($customer_id)
	{
		$this->db->select('delivery.id')->from('sales_delivery AS delivery');
		$this->db->join('sales_order AS s_order', 's_order.id = delivery.fk_sales_order_id');
		$this->db->join('gatepass_packing_lists AS gp', 'gp.pl_id = delivery.id', 'left');
		$this->db->where('gp.gatepass_id IS NULL', FALSE, FALSE);
		$this->db->where(['s_order.fk_sales_customer_id' => $customer_id, 'delivery.status' => M_Status::STATUS_DELIVERED]);
		$this->db->order_by('delivery.id','DESC');
		return array_column($this->db->get()->result_array(), 'id');
	}

	public function get_type($id)
	{
		$result = $this->db->select('type')->from($this->table)->where('id', $id)->get()->row_array();
		if($result)
		{
			return $result['type'];
		}
		return FALSE;
	}

	public function get_items($id)
	{
		$gatepass = $this->get($id);
		if($gatepass['type'] === 'pl')
		{
			$this->db->select('pl_d.fk_sales_delivery_id AS pl_id, p.description, pl_d.this_delivery, u.description AS unit_description');
			$this->db->from('sales_delivery_detail AS pl_d');
			$this->db->join('sales_order_detail AS o_d', 'o_d.id = pl_d.fk_sales_order_detail_id');
			$this->db->join('inventory_product AS p', 'p.id = o_d.fk_inventory_product_id');
			$this->db->join('inventory_unit AS u', 'u.id = p.fk_unit_id');
			$this->db->where_in('pl_d.fk_sales_delivery_id', array_column($gatepass['pls'], 'pl_id'));
			$gatepass['items'] = $this->db->get()->result_array();
			unset($gatepass['pls']);
			
		}
		return $gatepass;
	}
}
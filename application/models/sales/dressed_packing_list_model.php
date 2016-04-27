<?php

class dressed_packing_list_model extends CI_Model
{
	protected $table = 'sales_delivery';

	
	function create($packing_list, $details)
	{
		$this->db->trans_start();

		$this->db->insert($this->table, $packing_list);

		$id = $this->db->insert_id();

		foreach($details AS &$row){
			$row['fk_sales_delivery_id'] = $id;
		}

		$this->db->insert_batch('sales_delivery_detail', $details);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function update($id, $packing_list, $details)
	{
		$this->db->trans_start();

		$this->db->update($this->table, $packing_list, ['id' => $id]);

		$temp = ['new' => [], 'updated' => [], 'updated_ids' => []];

		foreach($details AS &$row){
			if(isset($row['id'])){
				$temp['updated'][] = $row;
				$temp['updated_ids'][] = $row['id'];
			}else{
				$row['fk_sales_delivery_id'] = $id;
				$temp['new'][] = $row;
			}
		}

		unset($details);

		if(empty($temp['updated_ids'])){
			$this->db->delete('sales_delivery_detail', ['fk_sales_delivery_id' => $id]);
		}else{
			$this->db->where('fk_sales_delivery_id', $id)->where_not_in('id', $temp['updated_ids'])->delete('sales_delivery_detail');
			$this->db->update_batch('sales_delivery_detail', $temp['updated'], 'id');
		}

		if(!empty($temp['new'])){
			$this->db->insert_batch('sales_delivery_detail', $temp['new']);
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function get($id)
	{
		$packing_list = $this->db->select('so.po_number, customer.company_name, customer.address, customer.customer_code, truck.plate_number, truck.driver, assistant.name AS assistant, pl.*, agent.name AS sales_agent')
			->from('sales_delivery AS pl')
			->join('sales_order AS so', 'so.id = pl.fk_sales_order_id')
			->join('sales_customer AS customer', 'customer.id = so.fk_sales_customer_id')
			->join('sales_trucking AS truck', 'truck.id = pl.fk_sales_trucking_id', 'left')
			->join('trucking_assistants AS assistant', 'assistant.id = pl.fk_trucking_assistant_id', 'left')
			->join('sales_agent AS agent', 'agent.id = so.fk_sales_agent_id', 'left')
			->get_where($this->table, ['pl.id' => $id, 'pl.type' => 'd'])->row_array();

		if(!$packing_list){
			return NULL;
		}

		$packing_list['details'] = $this->db->get_where('sales_delivery_detail', ['fk_sales_delivery_id' => $id])->result_array();
		return $packing_list;
	}

	function exists($id)
	{
		return $this->db->select('id')->get_where($this->table, ['id' => $id, 'type' => 'd'])->num_rows() > 0;
	}

	function get_so_number($id)
	{
		$result = $this->db->select('fk_sales_order_id')->get_where($this->table, ['id' => $id, 'type' => 'd'])->row_array();
		return $result ? $result['fk_sales_order_id'] : NULL;
	}
	
}
<?php

class Sales_order_model extends CI_Model
{
	protected $table = 'sales_order';

	function create($sales_order, $order_line)
	{
		$this->db->trans_start();

		$this->db->insert($this->table, $sales_order);

		$id = $this->db->insert_id();

		foreach($order_line AS &$line){
			$line['fk_sales_order_id'] = $id;
		}

		$this->db->insert_batch('sales_order_detail', $order_line);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	 
	function update($id, $sales_order, $order_line)
	{
		$this->db->trans_start();

		$this->db->update($this->table, $sales_order, ['id' => $id]);

		$temp = ['new' => [], 'updated' => [], 'updated_ids' => []];

		foreach($order_line AS &$row){
			if(isset($row['id'])){
				$temp['updated'][] = $row;
				$temp['updated_ids'][] = $row['id'];
			}else{
				$row['fk_sales_order_id'] = $id;
				$temp['new'][] = $row;
			}
		}

		unset($order_line);

		if(empty($temp['updated_ids'])){
			$this->db->delete('sales_order_detail', ['fk_sales_order_id' => $id]);
		}else{
			$this->db->where('fk_sales_order_id', $id)->where_not_in('id', $temp['updated_ids'])->delete('sales_order_detail');
			$this->db->update_batch('sales_order_detail', $temp['updated'], 'id');
		}

		if(!empty($temp['new'])){
			$this->db->insert_batch('sales_order_detail', $temp['new']);
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function set_agent($id, $agent_id)
	{
		return $this->db->update($this->table, ['fk_sales_agent_id' => $agent_id], ['id' => $id]);
	}
}
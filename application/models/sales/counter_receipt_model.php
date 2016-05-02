<?php

class Counter_receipt_model extends CI_Model
{
	protected $table = 'sales_counter_receipt';

	function create($data)
	{
		$this->db->trans_start();

		$this->db->insert($this->table, $data['cr']);

		$id = $this->db->insert_id();

		foreach($data['details'] AS &$row){
			$row['fk_sales_counter_receipt_id'] = $id;
		}

		$this->db->insert_batch('sales_counter_receipt_detail', $data['details']);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function update($id, $data)
	{
		$this->db->trans_start();

		$this->db->update($this->table, $data['cr'], ['id' => $id]);

		$this->sync('sales_counter_receipt_detail', $data['details'], 'id', 'fk_sales_counter_receipt_id', $id);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function get($id)
	{
		$data = $this->db->get_where($this->table, ['id' => $id])->row_array();

		if($data){
			$data['details'] = $this->db->get_where('sales_counter_receipt_detail', ['fk_sales_counter_receipt_id' => $id])->result_array();
			return $data;
		}

		return NULL;
	}

	function all($page = 1, $params = FALSE)
	{
		$limit = 100;
        $offset = ($page <= 1 ? 0 : ($page-1)*$limit);

		$this->db->select('cr.id, cr.date, customer.company_name AS customer, (SUM((delivery_detail.this_delivery * order_detail.unit_price) - (delivery_detail.this_delivery * order_detail.discount)) - IFNULL(delivery.credit_memo_amount, 0)) AS amount, created_by.Username AS created_by, approved_by.Username AS approved_by', FALSE)
			->from($this->table.' AS cr')
			->join('sales_counter_receipt_detail AS cr_detail', 'cr_detail.fk_sales_counter_receipt_id = cr.id')
			->join('sales_delivery AS delivery', 'delivery.id = cr_detail.fk_sales_delivery_id')
			->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
			->join('sales_order_detail AS order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
			->join('sales_order AS sales', 'sales.id = order_detail.fk_sales_order_id')
			->join('sales_customer AS customer', 'customer.id = sales.fk_sales_customer_id')
			->join('account AS created_by', 'created_by.id = cr.created_by')
			->join('account AS approved_by', 'approved_by.id = cr.approved_by', 'left');

		if($params !== FALSE){
            $this->db->where($params);
        }

		return $this->db->group_by('cr.id')
			->order_by('cr.id', 'DESC')
			->limit($limit, $offset)
			->get()
			->result_array();
	}

	function exists($id)
	{
		return $this->db->select('id')->get_where($this->table, ['id' => $id])->num_rows() > 0;
	}


	function sync($table, $value_array, $table_pk_column_name, $table_fk_column_name, $table_fk_column_value)
	{
		$temp = ['new' => [], 'updated' => [], 'updated_ids' => []];

		foreach($value_array AS &$row){
			if(isset($row[$table_pk_column_name])){
				$temp['updated'][] = $row;
				$temp['updated_ids'][] = $row[$table_pk_column_name];
			}else{
				$row[$table_fk_column_name] = $table_fk_column_value;
				$temp['new'][] = $row;
			}
		}

		unset($value_array);

		if(empty($temp['updated_ids'])){
			$this->db->delete($table, [$table_pk_column_name => $table_fk_column_value]);
		}else{
			$this->db->where($table_fk_column_name, $table_fk_column_value)
				->where_not_in($table_pk_column_name, $temp['updated_ids'])
				->delete($table);
			$this->db->update_batch($table, $temp['updated'], $table_pk_column_name);
		}

		if(!empty($temp['new'])){
			$this->db->insert_batch($table, $temp['new']);
		}
	}

	function is_approved($id)
	{
		return $this->db->select('id')->where('approved_by IS NOT NULL')->get_where($this->table, ['id' => $id])->num_rows() > 0;
	}

	function delete($id)
	{
		return $this->db->delete($this->table, ['id' => $id]);
	}
}
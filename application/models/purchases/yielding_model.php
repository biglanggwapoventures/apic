<?php

class Yielding_model extends CI_Model
{

	protected $table = 'yieldings';

	function get($rr_id)
	{
		$data['yielding'] = $this->db->get_where($this->table, ['fk_purchase_receiving_id' => $rr_id])
			->row_array();

		if($data['yielding']){

			$sources = $this->db->get_where('yieldings_from', ['fk_yielding_id' => $data['yielding']['id']])
				->result_array();
			$results = $this->db->where_in('fk_yieldings_from_id', array_column($sources, 'id'))
				->get('yieldings_to')
				->result_array();

			$data['source'] = array_column($sources, NULL, 'id');

			foreach($results AS $row){
				if(isset($data['source'][$row['fk_yieldings_from_id']])){
					$data['source'][$row['fk_yieldings_from_id']]['result'][] = $row; 
				}
			}

			return $data;	 
		}

		return NULL;

	}

	function exists($rr_id)
	{
		return $this->db->get_where($this->table, ['fk_purchase_receiving_id' => $rr_id])->num_rows() > 0;
	}

	function create($data)
	{

		$this->db->trans_start();

		$this->db->insert($this->table, $data['yielding']);

		$id = $this->db->insert_id();

		foreach($data['source'] AS &$row){

			$results = $row['result'];
			unset($row['result']);

			$row['fk_yielding_id'] = $id;

			$this->db->insert('yieldings_from', $row);

			$source_id = $this->db->insert_id();

			foreach($results AS &$result){
				$result['fk_yieldings_from_id'] = $source_id;
			}

			$this->db->insert_batch('yieldings_to', $results);
		}

		$this->db->trans_complete();

		return $this->db->trans_status();

	}

	function update($rr_id, $data)
	{
		$this->db->trans_start();

		$this->db->update($this->table, $data['yielding'], ['fk_purchase_receiving_id' => $rr_id]);

		// $this->db->where_not_in('yieldings_from', array_column($data['source'], 'id'))
		// 	->where('')

		foreach($data['source'] AS &$row){

			$results  = $row['result'];
			unset($row['result']);

			if(!isset($row['id'])){
			
				$this->db->insert('yieldings_from', $row);

				foreach($results AS &$result){
					$result['fk_yielding_from_id'] = $this->db->insert_id();
				}

				$this->db->insert_batch('yieldings_to', $results);

			}else{

				$id = $row['id'];
				unset($row['id']);

				$this->db->update('yieldings_from', $row, ['id' => $id]);

				$this->sync('yieldings_to', $results, 'id', 'fk_yieldings_from_id', $id);

			}
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
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
			$this->db->delete($table, [$table_fk_column_value => $table_fk_column_value]);
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
}
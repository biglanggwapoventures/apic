<?php

class Yielding_model extends CI_Model
{

	protected $table = 'yieldings';

	function create($data)
	{
		$this->db->trans_start();

		$this->db->insert($this->table, $data['yielding']);

		$yielding_id = $this->db->insert_id();

		$data['source']['fk_yielding_id'] = $yielding_id;

		$this->db->insert('yieldings_from', $data['source']);

		$source_id =  $this->db->insert_id();

		foreach($data['results'] AS &$row){
			$row['fk_yieldings_from_id'] = $source_id;
		}

		$this->db->insert_batch('yieldings_to', $data['results']);
 
		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function update($id, $data)
	{
		$this->db->trans_start();

		$this->db->update($this->table, $data['yielding'], ['id' => $id]);

		$source_id = $data['source']['id'];
		unset($data['source']['id']);

		$this->db->update('yieldings_from', $data['source'], ['id' => $source_id]);

		$this->sync('yieldings_to', $data['results'], 'id', 'fk_yieldings_from_id', $source_id);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function all($page = 1, $params = FALSE)
	{
		$limit = 100;
        $offset =  $page <= 1 ? 0 : ( $page - 1 ) * $limit ;
		$this->db->select('
			yield.id, 
			yield.fk_purchase_receiving_id, 
			DATE(yield.created_at) AS created_at, 
			yield_source.yield_type, 
			CASE WHEN yield_type = "dtc" THEN "Dressed to Cut-ups" ELSE "Live to Dressed" END AS yield_type_description,
			created_by.Username AS created_by', FALSE)
			->from($this->table.' AS yield')
			->join('yieldings_from AS yield_source', 'yield_source.fk_yielding_id = yield.id')
			->join('account AS created_by', 'created_by.ID = yield.created_by')
			// ->where('fk_purchase_receiving_id IS NULL')
			->group_by('yield.id');

		if($params !== FALSE){
			$this->db->where($params);
		}

		$this->db->limit($limit, $offset)
			->order_by('yield.id', 'DESC');

		return $this->db->get()
			->result_array();
	}

	public function get($id)
	{
		$data['yielding'] = $this->db->get_where($this->table, ['id' => $id])->row_array();
		if($data['yielding']){
			$data['source'] = $this->db->get_where('yieldings_from', ['fk_yielding_id' => $id])->row_array();
			$data['result'] = $this->db->get_where('yieldings_to', ['fk_yieldings_from_id' => $data['source']['id']])->result_array();
			return $data;
		}
		return NULL;
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
			$this->db->delete($table, [$table_fk_column_name => $table_fk_column_value]);
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
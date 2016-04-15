<?php

class M_inventory extends CI_Model
{
	protected $table = 'running_inventory';

	public function create_records($data)
	{
		$this->db->insert_batch($this->table, $data);
	}



}
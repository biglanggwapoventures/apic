<?php

class M_payables extends CI_Model
{
	public function generate()
	{
		$this->load->model('maintainable/m_supplier', 'supplier');
		$this->supplier->fields = ['id', 'name'];
		$suppliers = array_column($this->supplier->all(), 'name', 'id');

		$data = [];

		function insert(&$array, $customer, $period, $amount)
		{
			if(isset($array[$customer][$period]))
			{
				$array[$customer][$period] += $amount;
			}
			else
			{
				$array[$customer][$period] = $amount;
			}
		}

		// get indisbursed
		$this->db->select('rr.id AS id, rr.date, rr.fk_maintainable_supplier_id AS supplier_id, po.type AS po_type');
		$this->db->from('purchase_receiving AS rr');
		$this->db->join('purchase_order AS po', 'po.id = rr.fk_purchase_order_id','left');
		$this->db->join('purchase_disbursement_detail AS dsb_detail', 'dsb_detail.fk_purchase_receiving_id = rr.id', 'left');
		$this->db->join('purchase_disbursement AS dsb', 'dsb.id = dsb_detail.fk_purchase_disbursement_id', 'left');
		$this->db->where('(dsb.status IS NULL OR dsb.status !='. M_Status::STATUS_APPROVED.') AND rr.status = '. M_Status::STATUS_RECEIVED.' AND po.`type` = \'lcl\'', FALSE, FALSE);
		$undisbursed = $this->db->get()->result_array();

		if(empty($undisbursed))
		{
			return [];
		}

		// get total amount of rr and its id
		$this->db->select('rr_detail.fk_purchase_receiving_id AS id, SUM((order_detail.unit_price * rr_detail.this_receive) - rr_detail.discount) AS amount', FALSE);
		$this->db->from('purchase_receiving_detail AS rr_detail');
		$this->db->join('purchase_order_detail AS order_detail', 'order_detail.id = rr_detail.fk_purchase_order_detail_id');
		$this->db->where_in('rr_detail.fk_purchase_receiving_id', array_column($undisbursed, 'id'));
		$this->db->group_by('rr_detail.fk_purchase_receiving_id');
		$amounts = array_column($this->db->get()->result_array(), 'amount', 'id');

		// distribute amount to rrs
		$now = date_create();
		foreach($undisbursed AS $row)	
		{
			$row['amount'] = $amounts[$row['id']];
			$rr_date = date_create($row['date']);
			$diff = date_diff($now, $rr_date)->format('%a');
			if($diff <= 30)
			{
				insert($data, $suppliers[$row['supplier_id']], '30', $row['amount']);
			}
			else if($diff > 30 && $diff <= 60)
			{
				insert($data, $suppliers[$row['supplier_id']], '60', $row['amount']);
			}
			else if($diff > 60 && $diff <= 90)
			{
				insert($data, $suppliers[$row['supplier_id']], '90', $row['amount']);
			}
			else
			{
				insert($data, $suppliers[$row['supplier_id']], '90+', $row['amount']);
			}
		}

		return $data;

	}
}
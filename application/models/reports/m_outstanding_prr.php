<?php

class M_outstanding_prr extends CI_Model
{

	public function generate($supplier_id)
	{
		// get all disbursed rr to be used in NOT IN clause
		$this->db->select('dsb_detail.fk_purchase_receiving_id AS id')->from('purchase_disbursement AS dsb');
		$this->db->join('purchase_disbursement_detail AS dsb_detail', 'dsb_detail.fk_purchase_disbursement_id = dsb.id');
		$this->db->where(['dsb.fk_maintainable_supplier_id' => $supplier_id, 'dsb.status' => M_Status::STATUS_APPROVED]);
		$disbursed = array_column($this->db->get()->result_array(), 'id');

		// retrieve undisbursed using NOT IN clause
		$this->db->select('id, date, fk_purchase_order_id AS po_no')->from('purchase_receiving')->where(['fk_maintainable_supplier_id' => $supplier_id, 'status' => M_Status::STATUS_RECEIVED]);
		if(!empty($disbursed))
		{
			$this->db->where_not_in('id', $disbursed);
		}
		$undisbursed = $this->db->order_by('id', 'DESC')->get()->result_array();

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
		foreach($undisbursed AS &$row)
		{
			$row['amount'] = $amounts[$row['id']];
		}

		return $undisbursed;
	}

}
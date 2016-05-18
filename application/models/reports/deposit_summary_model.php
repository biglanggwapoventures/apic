<?php

class Deposit_summary_model extends CI_Model
{
	function generate($date, $bank_account = FALSE)
	{	
		$this->db->select("
			customer.company_name AS customer,
			SUM(IF(receipt_detail.payment_method = 'Cash', receipt_detail.amount, 0)) AS cash_amount,
			check_trans.check_amount,
			receipt_detail.payment_method,
			check_trans.check_number,
			check_trans.check_date,
			receipt.pay_from AS depositor_bank
		", FALSE)
			->from('sales_receipt AS receipt')
			->join('sales_customer AS customer', 'customer.id = receipt.fk_sales_customer_id')
			->join('sales_receipt_detail AS receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id')
			->join('sales_receipt_check_transaction AS check_trans', 'check_trans.fk_sales_receipt_detail_id = receipt_detail.id', 'left')
			->where("receipt.deposit_date = '{$date}'");

			if($bank_account){
				$this->db->where("receipt.pay_to = '{$bank_account}'");
			}

			$this->db->group_by('receipt.id');

		return $this->db->get()->result_array();
	}
}
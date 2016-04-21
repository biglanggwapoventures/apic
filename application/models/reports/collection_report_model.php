<?php

class Collection_report_model extends CI_Model
{
	public function generate($customer = FALSE, $agent = FALSE, $start_date = FALSE, $end_date = FALSE)
	{
		$this->db->select('receipt.id, receipt_detail.fk_sales_delivery_id, receipt_detail.amount, customer.company_name AS customer_name, DATEDIFF(CURDATE(), delivery.date) AS days_collected', FALSE)
			->from('sales_receipt_detail AS receipt_detail')
			->join('sales_receipt AS receipt', 'receipt.id = receipt_detail.fk_sales_receipt_id')
			->join('sales_customer AS customer', 'customer.id = receipt.fk_sales_customer_id')
			->join('sales_delivery AS delivery', 'delivery.id = receipt_detail.fk_sales_delivery_id')
			->join('sales_order AS s_order', 's_order.id = delivery.fk_sales_order_id')
			->where('receipt_detail.amount > 0');

		if($customer){
			$this->db->where('receipt.fk_sales_customer_id', $customer);
		}
		if($agent){
			$this->db->where('s_order.fk_sales_agent_id', $agent);
		}
		if($start_date){
			$this->db->where('receipt.`date` >=', $start_date);
		}
		if($end_date){
			$this->db->where('receipt.`date` <=', $end_date);
		}
		return $this->db->get()->result_array();
	}
}
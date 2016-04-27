<?php

class Customer_ledger_model extends CI_Model
{
	function generate($customer_id, $start_date)
	{
		// GET TOTAL AMOUNT OF PACKING LISTS BEFORE THE START DATE SPECIFIED
		$payable = $this->db->select('SUM((order_detail.unit_price * delivery_detail.this_delivery) - (order_detail.discount * delivery_detail.this_delivery)) AS total_amount', FALSE)
			->from('sales_delivery AS delivery')
			->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
			->join('sales_order_detail AS order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
			->join('sales_order', 'sales_order.id = order_detail.fk_sales_order_id')
			->where([
				'sales_order.fk_sales_customer_id' => $customer_id,
				'DATE(delivery.`date`) <' => $start_date,
				'delivery.`status`' => M_Status::STATUS_DELIVERED
			])
			->get()
			->row_array();

		// GET TOTAL AMOUNT OF PAYMENTS MADE BEFORE THE START DATE SPECIFIED
		$paid = $this->db->select('SUM( CASE WHEN DATEDIFF(receipt.deposit_date, CURDATE()) <= 0 THEN receipt_detail.amount ELSE 0 END )  AS total_amount', FALSE)
			->from('sales_receipt AS receipt')
			->join('sales_receipt_detail AS receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id')
			->join('sales_delivery AS delivery', 'delivery.id = receipt_detail.fk_sales_delivery_id')
			->join('sales_order', 'sales_order.id = delivery.fk_sales_order_id')
			->where([
				'receipt.`status`' => M_Status::STATUS_FINALIZED,
				'delivery.`status`' => M_Status::STATUS_DELIVERED,
				'receipt.`date` <' => $start_date,
				'sales_order.fk_sales_customer_id' => $customer_id
			])
			->get()
			->row_array();

		// GET TOTAL AMOUNT CREDIT MEMOS BEFORE THE START DATE SPECIFIED
		$credit_memo = $this->db->select('SUM(IFNULL(delivery.credit_memo_amount, 0)) AS total_amount', FALSE)
			->from('cm')
			->join('sales_delivery AS delivery', 'delivery.id = cm.delivery_id ')
			->join('sales_order', 'sales_order.id = delivery.fk_sales_order_id')
			->where([
				'cm.`date` <' =>  $start_date,
				'delivery.status' => M_Status::STATUS_DELIVERED,
				'sales_order.fk_sales_customer_id' => $customer_id
			])
			->get()
			->row_array();

		// SET BALANCE TO 0
		$balance = 0;

		if($payable){
			$balance += $payable['total_amount'];
		}
		if($paid){
			$balance -= $paid['total_amount'];
		}

		if($credit_memo){
			$balance -= $credit_memo['total_amount'];
		}

		$receipts = [];
		$credit_memos = [];

		// GET ALL PACKING LIST ON AND AFTER THE START DATE SPECIFIED
		$deliveries =  $this->db->select('"PL" AS description, delivery.id, DATE(delivery.date) AS date, SUM((order_detail.unit_price * delivery_detail.this_delivery) - (order_detail.discount * delivery_detail.this_delivery)) AS amount', FALSE)
			->from('sales_delivery AS delivery')
			->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
			->join('sales_order_detail AS order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
			->join('sales_order', 'sales_order.id = delivery.fk_sales_order_id')
			->where([
				'sales_order.fk_sales_customer_id' => $customer_id,
				'DATE(delivery.`date`) >=' => $start_date,
				'delivery.`status`' => M_Status::STATUS_DELIVERED
			])
			->group_by('delivery.id')
			->get()
			->result_array();

		$credit_memos = $this->db->select('"CM" AS description, cm.delivery_id AS id, cm.`date`, IFNULL(delivery.credit_memo_amount, 0)  AS amount', FALSE)
			->from('cm')
			->join('sales_delivery AS delivery', ' delivery.id = cm.delivery_id')
			->join('sales_order', 'sales_order.id = delivery.fk_sales_order_id')
			->where([
				'cm.`date` >=' =>  $start_date,
				'delivery.status' => M_Status::STATUS_DELIVERED,
				'sales_order.fk_sales_customer_id' => $customer_id,
				'delivery.credit_memo_amount >' => 0
			])
			->get()
			->result_array();

		$receipts = $this->db->select('"SR" AS description, receipt.id, receipt.`date`, receipt_detail.amount, receipt.deposit_date, DATEDIFF(receipt.deposit_date, CURDATE()) AS pdc', FALSE)
				->from('sales_receipt AS receipt')
				->join('sales_receipt_detail AS receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id')
				->join('sales_delivery AS delivery', 'delivery.id = receipt_detail.fk_sales_delivery_id')
				->join('sales_order', 'sales_order.id = delivery.fk_sales_order_id')
				->where([
					'receipt.`status`' => M_Status::STATUS_FINALIZED,
					'delivery.`status`' => M_Status::STATUS_DELIVERED,
					'receipt.`date` >=' => $start_date,
					'sales_order.fk_sales_customer_id' => $customer_id,
					'receipt_detail.amount > ' => 0
				])
				->get()->result_array();


		$ledger = array_merge(array_merge($receipts, $deliveries), $credit_memos);


		function date_compare($a, $b)
		{
		    return strtotime($a['date']) - strtotime($b['date']);
		}    
		usort($ledger, 'date_compare');

		return compact('balance', 'ledger');

	}
}
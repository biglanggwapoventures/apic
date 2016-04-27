<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of m_ageing
 *
 * @author Adr
 */
class M_Receivable extends CI_Model {

    public $customer;
    protected $query = "SELECT fk_sales_customer_id, customer.company_name,
            SUM(CASE WHEN (total.amount - IFNULL(receivable.amount, 0) - IFNULL(credit_memo_amount, 0)) > 0 THEN (total.amount - IFNULL(receivable.amount, 0)  - IFNULL(credit_memo_amount, 0)) ELSE 0 END) AS balance,
            CASE
                WHEN DATEDIFF(CURDATE(), delivery.`date`) <= 30 THEN 30
                WHEN DATEDIFF(CURDATE(), delivery.`date`) <= 60 && DATEDIFF(CURDATE(), delivery.`date`) >= 31 THEN 60
                WHEN DATEDIFF(CURDATE(), delivery.`date`) <= 90 && DATEDIFF(CURDATE(), delivery.`date`) >= 61 THEN 90
                ELSE '90+' END AS `age`
            FROM  pm_sales_delivery AS delivery
        JOIN pm_sales_order AS `order` ON `order`.id = delivery.fk_sales_order_id
        JOIN pm_sales_customer AS customer ON customer.id = `order`.fk_sales_customer_id
        JOIN
	(
            SELECT inner_delivery_detail.fk_sales_delivery_id AS delivery_id,
                SUM((inner_order_detail.unit_price * inner_delivery_detail.this_delivery) - (inner_order_detail.discount * inner_delivery_detail.this_delivery)) AS amount
            FROM pm_sales_delivery_detail AS inner_delivery_detail
            JOIN pm_sales_order_detail AS inner_order_detail ON inner_order_detail.id = inner_delivery_detail.fk_sales_order_detail_id
            GROUP BY inner_delivery_detail.fk_sales_delivery_id
	) AS total ON total.delivery_id = delivery.id
        LEFT JOIN
	(
            SELECT receipt_detail.fk_sales_delivery_id AS delivery_id, 
                   SUM(CASE WHEN receipt_detail.payment_method = 'Cash' THEN receipt_detail.amount WHEN receipt_detail.payment_method = 'Check' && DATEDIFF(check_payment.deposit_date, CURDATE()) <= 0 THEN check_payment.amount ELSE 0 END) amount
            FROM pm_sales_receipt AS receipt 
            JOIN pm_sales_receipt_detail AS receipt_detail 	ON receipt_detail.fk_sales_receipt_id = receipt.id
            JOIN pm_sales_delivery AS delivery ON delivery.id =  receipt_detail.fk_sales_delivery_id
            LEFT JOIN pm_sales_receipt_check_transaction AS check_payment ON check_payment.fk_sales_receipt_detail_id = receipt_detail.id
            WHERE receipt.status = 9
            GROUP BY receipt_detail.fk_sales_delivery_id
        ) AS receivable ON receivable.delivery_id = delivery.id
        GROUP BY fk_sales_customer_id, age
        HAVING balance > 0
        ORDER BY company_name ASC";

    function __construct() {
        parent::__construct();
    }

    public function generate() {
        // echo "<pre>";
        // print_r();
        // echo "</pre>"
        $data = [];
        $result = $this->db->query($this->query)->result_array();
        foreach($result as $row){
            $data[$row['company_name']][$row['age']] = $row['balance'];
        }
        // $this->generate_report();
        return $data;
    }

    public function generate_report()
    {

        $age = [];

        // get all delivered amounts from all customers
        $this->db->select('delivery.id AS packing_list_id, customer.company_name AS packing_list_customer, DATE(delivery.date) AS packing_list_date, (SUM((order_detail.unit_price * delivery_detail.this_delivery) - (order_detail.discount * delivery_detail.this_delivery))-IFNULL(delivery.credit_memo_amount, 0)) AS packing_list_amount', FALSE)
            ->from('sales_delivery AS delivery')
            ->join('sales_order AS s_order', 's_order.id = delivery.fk_sales_order_id')
            ->join('sales_customer AS customer', 'customer.id = s_order.fk_sales_customer_id')
            ->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
            ->join('sales_order_detail AS order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
            ->where('delivery.status = '.M_Status::STATUS_DELIVERED)
            ->group_by('delivery.id');

        $deliveries = $this->db->get()->result_array();

        // get all paid
        $this->db->select('SUM(CASE WHEN DATEDIFF(receipt.deposit_date, CURDATE()) <= 0 THEN receipt_detail.amount ELSE 0 END) AS packing_list_paid_amount, receipt_detail.fk_sales_delivery_id AS packing_list_id', FALSE)
            ->from('sales_receipt_detail AS receipt_detail')
            // ->join('sales_receipt_check_transaction AS check_transaction', 'check_transaction.fk_sales_receipt_detail_id = receipt_detail.id', 'left')
            ->join('sales_receipt AS receipt', 'receipt.id = receipt_detail.fk_sales_receipt_id')
            ->where('receipt.approved_by IS NOT NULL')
            ->where_in('receipt_detail.fk_sales_delivery_id', array_column($deliveries, 'packing_list_id'))
            ->group_by('receipt_detail.fk_sales_delivery_id');

        $payments = array_column($this->db->get()->result_array(), NULL, 'packing_list_id');

        function insert(&$array, $customer, $period, $amount)
        {
            if(isset($array[$customer][$period])){
                $array[$customer][$period] += $amount;
            }else{
                $array[$customer][$period] = $amount;
            }
        }

        foreach($deliveries AS $row){

            $payment = isset($payments[$row['packing_list_id']]) ? $payments[$row['packing_list_id']]['packing_list_paid_amount'] : 0;

            $balance = $row['packing_list_amount'] - $payment;

            if($balance <= 0){
                if($balance < 0){
                    insert($age, $row['packing_list_customer'], 'overpayment', abs($balance));
                }
                continue;
            }

            $packing_list_date = date_create($row['packing_list_date']);
            $packing_list_age = $packing_list_date->diff(date_create())->format('%a');

            if($packing_list_age <= 30){
                insert($age, $row['packing_list_customer'], '30', $balance);
            }else if($packing_list_age >= 31 && $packing_list_age <= 60){
               insert($age, $row['packing_list_customer'], '60', $balance);
            }else if($packing_list_age >= 61 && $packing_list_age <= 90){ 
                insert($age, $row['packing_list_customer'], '90', $balance);
            }else{ 
                insert($age, $row['packing_list_customer'], '90+', $balance);
            }

        }

        return $age;


    }   

}

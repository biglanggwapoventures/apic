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
        GROUP BY age,fk_sales_customer_id
        HAVING balance > 0
        ORDER BY company_name ASC";

    function __construct() {
        parent::__construct();
    }

    public function generate() {
        $data = [];
        $result = $this->db->query($this->query)->result_array();
        foreach($result as $row){
            $data[$row['company_name']][$row['age']] = $row['balance'];
        }
        return $data;
    }

}

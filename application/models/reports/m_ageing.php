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
class M_Ageing extends CI_Model {

    const RECVBL_CURRENT = 0;
    const RECVBL_PAST_DUE_1_TO_30 = 1;
    const RECVBL_PAST_DUE_31_TO_60 = 31;
    const RECVBL_PAST_DUE_61_PLUS = 61;
    
    public $customer;

    function __construct() {
        parent::__construct();
        $this->start_date = '';
        $this->end_date = '';
    }

    public function generate() {
        /*
         * 1. GET TOTAL AMOUNT FROM ALL APPROVED PACKING LIST PER CUSTOMER
         * 2. GET TOTAL PAID FROM RECEIPT PER CUSTOMER 
         */
        $this->db->select('customer.id as customer_id, '
                . 'SUM((delivery_detail.this_delivery * s_order_detail.unit_price) - (delivery_detail.this_delivery * s_order_detail.discount)) as total_amount', FALSE);
        $this->db->from('sales_delivery AS delivery');
        $this->db->join('sales_order AS s_order', 's_order.id = delivery.fk_sales_order_id');
        $this->db->join('sales_customer AS customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->db->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id');
        $this->db->join('sales_order_detail AS s_order_detail', 's_order_detail.id = delivery_detail.fk_sales_order_detail_id');
        
        $this->db->where('delivery.status', M_Status::STATUS_DELIVERED);
        $this->customer !== NULL ? $this->db->where('customer.id', $this->customer) : '';
        $this->db->group_by('customer.id');
        $total_amounts = $this->db->get()->result_array();
        
        
    }
    
    public function get_receivable(){
        $this->db->select('');
    }
    
    

}

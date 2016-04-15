<?php

class M_Customer_Ledger extends CI_Model {

    private $start_date;
    private $end_date;
    private $customer;

    function __construct() {
        parent::__construct();
        $this->start_date = '';
        $this->end_date = '';
        $this->customer = '';
    }

    public function date_filter($start_date, $end_date = '') {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function customer_filter($customer) {
        $this->customer = $customer;
    }

    public function generate($limit = 30, $offset = 0) {
        $this->db->select('s_delivery.id, DATE_FORMAT(s_delivery.date, "%M %d, %Y") as date, s_delivery.invoice_number,'
                . 'delivery_detail.this_delivery, FORMAT(delivery_detail.amount,2) as delivery_amount,'
                . 's_order.total_amount,'
                . 'FORMAT(order_detail.unit_price,2) as unit_price, order_detail.product_quantity, order_detail.amount as order_amount,'
                . 'product.description, product.code, unit.description as unit, '
                . 'FORMAT(s_receipt.amount,2) as receipt_amount, FORMAT(delivery_detail.amount-s_receipt.amount,2) as balance,'
                . 'customer.company_name, customer.customer_code', FALSE);
        $this->db->from('sales_delivery as s_delivery');
        $this->db->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = s_delivery.id')
                ->join('sales_order as s_order', 's_order.id = s_delivery.fk_sales_order_id')
                ->join('sales_order_detail as order_detail', 'order_detail.fk_sales_order_id = s_order.id')
                ->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id')
                ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id')
                ->join('sales_receipt_detail as s_receipt', 's_receipt.fk_sales_delivery_id = s_delivery.id')
                ->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->start_date ? $this->db->where('s_delivery.date >=', $this->start_date) : '';
        $this->end_date ? $this->db->where('s_delivery.date <=', $this->end_date) : '';
        $this->customer ? $this->db->where('s_order.fk_sales_customer_id', $this->customer) : '';
        return $this->db->limit($limit, $offset)->order_by('s_delivery.date', 'DESC')->get()->result_array();
    }

}

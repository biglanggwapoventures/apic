<?php

class M_Inventory_Report extends CI_Model {

    private $start_date;
    private $end_date;
    private $product;

    function __construct() {
        parent::__construct();
        $this->start_date = '';
        $this->end_date = '';
        $this->product = '';
    }

    public function set_date_filter($start_date, $end_date = '') {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function set_product_filter($product) {
        $this->product = $product;
    }

    /*
     * 1. Get stock-in from receiving
     * 2. Get stock-out from gatepass
     * 3. Merge them
     * 4. Sort date desc
     */

    public function generate($limit = 30, $offset = 0) {
        /* get stock-ins */
        $this->db->select('DATE_FORMAT(receiving.date, "%M %d, %Y") as date, CONCAT(product.description, " (", product.code, ")") as product,'
                        . 'receiving_detail.this_receive as stock_in, category.description as product_category', FALSE)
                ->from('purchase_receiving as receiving')
                ->join('purchase_receiving_detail as receiving_detail', 'receiving_detail.fk_purchase_receiving_id = receiving.id')
                ->join('purchase_order_detail as order_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id')
                ->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id')
                ->join('inventory_category as category', 'category.id = product.fk_category_id')
                ->where('receiving.status', M_Status::STATUS_RECEIVED);
        $this->start_date ? $this->db->where('receiving.date >=', $this->start_date) : '';
        $this->end_date ? $this->db->where('receiving.date <=', $this->end_date) : '';
        $this->product ? $this->db->where('order_detail.fk_inventory_product_id', $this->product) : '';
        $stock_ins = $this->db->get()->result_array();
        $inventory[] = $stock_ins ? $stock_ins : array();

        /* get stock outs */
        $this->db->select('DATE_FORMAT(SUBSTRING_INDEX(delivery.exit_datetime," ", 1), "%M %d, %Y") as date, delivery_detail.quantity as stock_out,'
                        . 'CONCAT(product.description, " (", product.code, ")") as product,category.description as product_category', FALSE)
                ->from('inventory_delivery_log as delivery')
                ->join('inventory_delivery_log_detail as delivery_detail', 'delivery_detail.fk_delivery_log_id = delivery.id')
                ->join('inventory_product as product', 'product.id = delivery_detail.fk_inventory_product_id')
                ->join('inventory_category as category', 'category.id = product.fk_category_id');
        $this->start_date ? $this->db->where('SUBSTRING_INDEX(delivery.exit_datetime," ", 1) >=', $this->start_date, FALSE) : '';
        $this->end_date ? $this->db->where('SUBSTRING_INDEX(delivery.exit_datetime," ", 1) <=', $this->end_date) : '';
        $this->product ? $this->db->where('delivery_detail.fk_inventory_product_id', $this->product) : '';
        $stock_outs = $this->db->get()->result_array();
        $inventory[] = $stock_outs ? $stock_outs : array();

        /* merge */
        $inventory = array_merge($inventory[0], $inventory[1]);

        /* sort by date */

        function date_compare($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);
            return $t1 - $t2;
        }

        usort($inventory, 'date_compare');

        return $inventory;
    }

}

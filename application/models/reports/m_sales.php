<?php

class M_Sales extends CI_Model {

    const ROW_LIMIT = 100;

    public $start_date_filter = FALSE;
    public $end_date_filter = FALSE;
    public $customer_filter = FALSE;
    public $product_filter = FALSE;
    public $offset = FALSE;

    function __construct() {
        parent::__construct();
    }

    public function generate() {
        $this->db->select('delivery.id as pl, s_order.id, s_order.po_number, DATE_FORMAT(delivery.date, "%M %d, %Y") as date, '
                . 'FORMAT(order_detail.unit_price,2) as unit_price, '
                . 'CONCAT(delivery_detail.this_delivery, " ",unit.description) as product_quantity,'
                . 'FORMAT(order_detail.discount,2) as discount,'
                . 'FORMAT((order_detail.unit_price * delivery_detail.this_delivery) - (order_detail.discount * delivery_detail.this_delivery), 2) as amount,'
                . 'product.description as product,'
                . 'customer.company_name', FALSE);
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_order as s_order', 's_order.id = delivery.fk_sales_order_id')
                ->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
                ->join('sales_order_detail as order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
                ->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id')
                ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id')
                ->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->start_date_filter ? $this->db->where('DATE(delivery.date) >= DATE("' . $this->start_date_filter . '")', FALSE, FALSE) : '';
        $this->end_date_filter ? $this->db->where('DATE(delivery.date) <= DATE("' . $this->end_date_filter . '")', FALSE, FALSE) : '';
        $this->customer_filter ? $this->db->where('s_order.fk_sales_customer_id', $this->customer_filter) : '';
        $this->product_filter ? $this->db->where('order_detail.fk_inventory_product_id', $this->product_filter) : '';
        $this->db->where('delivery_detail.this_delivery >', 0);
        $this->offset && $this->offset - 1 > 0 ? $this->db->limit(self::ROW_LIMIT, self::ROW_LIMIT * ($this->offset - 1)) : $this->db->limit(self::ROW_LIMIT, 0);
        $this->db->where('delivery.status', M_Status::STATUS_DELIVERED);
        $this->db->where_in('unit.id', [4,7,28]);
        return $this->db->order_by('delivery.id', 'DESC')->get()->result_array();
    }

    public function get_total_units() {
        $this->db->select('unit.description as unit_description, SUM(unit.quantity*delivery_detail.this_delivery) AS q, FORMAT(SUM(delivery_detail.this_delivery),2) as quantity', FALSE);
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_order as s_order', 's_order.id = delivery.fk_sales_order_id')
                ->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
                ->join('sales_order_detail as order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
                ->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id')
                ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id')
                ->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->start_date_filter ? $this->db->where('DATE(delivery.date) >= DATE("' . $this->start_date_filter . '")', FALSE, FALSE) : '';
        $this->end_date_filter ? $this->db->where('DATE(delivery.date) <= DATE("' . $this->end_date_filter . '")', FALSE, FALSE) : '';
        $this->customer_filter ? $this->db->where('s_order.fk_sales_customer_id', $this->customer_filter) : '';
        $this->product_filter ? $this->db->where('order_detail.fk_inventory_product_id', $this->product_filter) : '';
        $this->db->where('delivery.status', M_Status::STATUS_DELIVERED);
        $this->db->where_in('unit.id', [4,7,28]);
        return $this->db->group_by('unit.id')->get()->result_array();
    }

    public function get_total_amount() {
        $this->db->select('FORMAT(SUM((order_detail.unit_price * delivery_detail.this_delivery) - (order_detail.discount * delivery_detail.this_delivery)), 2) as total_amount', FALSE);
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_order as s_order', 's_order.id = delivery.fk_sales_order_id')
                ->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
                ->join('sales_order_detail as order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
                ->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id')
                ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id')
                ->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->start_date_filter ? $this->db->where('DATE(delivery.date) >= DATE("' . $this->start_date_filter . '")', FALSE, FALSE) : '';
        $this->end_date_filter ? $this->db->where('DATE(delivery.date) <= DATE("' . $this->end_date_filter . '")', FALSE, FALSE) : '';
        $this->customer_filter ? $this->db->where('s_order.fk_sales_customer_id', $this->customer_filter) : '';
        $this->product_filter ? $this->db->where('order_detail.fk_inventory_product_id', $this->product_filter) : '';
        $this->db->where('delivery.status', M_Status::STATUS_DELIVERED);
        $this->db->where_in('unit.id', [4,7,28]);
        return $this->db->get()->row_array();
    }

}

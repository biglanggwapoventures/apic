<?php

class M_Sales_Order extends CI_Model {

    public function get_next_row_id($order_id, $mode) {
        if(!strcmp($mode, "next")){
            $query = "SELECT id FROM pm_sales_order WHERE id > {$order_id} ORDER BY id ASC LIMIT 1";
        }else{
            $query = "SELECT id FROM pm_sales_order WHERE id < {$order_id} ORDER BY id DESC LIMIT 1";
        }
        return $this->db->query($query)->result_array();
    }

    public function get_min_id(){
        return $this->db->query("SELECT MIN(id) as id FROM pm_sales_order")->result_array();
    }

    public function get_max_id(){
        return $this->db->query("SELECT MAX(id) as id FROM pm_sales_order")->result_array();
    }

    public function master_list($arr = array()) {
        $this->load->library('subquery');
        $this->db->select('s_order.id, s_order.po_number, DATE_FORMAT(s_order.date, "%M %d, %Y") as date, customer.company_name, '
                . 'FORMAT(IFNULL(SUM((order_detail.unit_price * order_detail.product_quantity)-(order_detail.product_quantity*order_detail.discount)), 0), 2) as total_amount, '
                . 's_order.status', FALSE);
        $this->db->from('sales_order as s_order');
        $this->db->join('sales_order_detail as order_detail', 'order_detail.fk_sales_order_id = s_order.id', 'left');
        $this->db->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->filter_functions($arr);
        $this->db->group_by('s_order.id')->order_by('s_order.id', 'DESC');
        return $this->db->get()->result_array();
    }

    private function filter_functions($arr = array()) {
        $arr['so'] ? $this->db->where('s_order.id', $arr['so']) : NULL;
        $arr['po'] ? $this->db->where('s_order.po_number', $arr['po']) : NULL;
        $arr['date'] ? $this->db->where('s_order.date', $arr['date']) : NULL;
        $arr['customer'] ? $this->db->where('s_order.fk_sales_customer_id', $arr['customer']) : NULL;
        $arr['page'] && $arr['page'] - 1 > 0 ? $this->db->limit(100, 100 * ($arr['page'] - 1)) : $this->db->limit(100, 0);
    }

    public function get($search_token = array(), $filter = array(), $limit = 999, $offset = 0) {
        $this->load->model('inventory/m_product');
        $this->db->select('s_order.*, CONCAT("[", customer.customer_code, "] ", customer.company_name) AS customer', FALSE);
        $this->db->from('sales_order as s_order');
        $this->db->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        if ($search_token) {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $this->db->order_by('s_order.id', 'DESC');
        $orders = $this->db->limit($limit, $offset)->get()->result_array();
        foreach ($orders as &$o) {
            $o['misc_charges'] = json_decode($o['misc_charges'], TRUE);
            $formatted_details = array();
            $this->db->select('order_detail.*');
            $this->db->select('product.description, product.code');
            $this->db->select('unit.description as unit_description');
            $this->db->from('pm_sales_order_detail as order_detail');
            $this->db->where('order_detail.fk_sales_order_id', $o['id']);
            $this->db->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id');
            $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id', 'left');
            $details = $this->db->get()->result_array();
            if (!$details) {
                $formatted_details = array(
                    'fk_inventory_product_id' => array(''),
                    'fk_sales_agent_id' => array(''),
                    'product_quantity' => array(''),
                    'quantity_delivered' => array(0),
                    'unit_description' => array('units'),
                    'unit_price' => array(0),
                    'discount' => array(0),
                    'amount' => array(0),
                    'remarks' => array(''),
                    'unit_description' => array('units'),
                    'total_units' => array(0)
                );
            }
            for ($x = 0; $x < count($details); $x++) {
                $formatted_details['id'][$x] = $details[$x]['id'];
                $formatted_details['fk_inventory_product_id'][$x] = $details[$x]['fk_inventory_product_id'];
                $formatted_details['product_description'][$x] = "[{$details[$x]['code']}] {$details[$x]['description']}";
                $formatted_details['prod_desc'][$x] = $details[$x]['description'];
                $formatted_details['product_quantity'][$x] = $details[$x]['product_quantity'];
                $formatted_details['quantity_delivered'][$x] = $details[$x]['quantity_delivered'];
                $formatted_details['unit_description'][$x] = $details[$x]['unit_description'];
                $formatted_details['unit_price'][$x] = $details[$x]['unit_price'];
                $formatted_details['discount'][$x] = $details[$x]['discount'];
                $formatted_details['remarks'][$x] = $details[$x]['remarks'];
                $formatted_details['total_units'][$x] = $details[$x]['total_units'];
            }
            $o['details'] = $formatted_details;
        }
        return $orders;
    }

    public function fetch_order_details($order_id, $exclude_served_orders = FALSE) {
        $data = [];
        $this->load->library('subquery');
        $this->db->select('details.id, details.fk_inventory_product_id,  details.product_quantity,  details.discount, details.unit_price, details.total_units, '
                        . 'CONCAT("[",product.code,"] ", product.description) AS product_description, '
                        . 'unit.description as unit_description, '
                        . 'IFNULL(delivery.total_qty_delivered, 0) as quantity_delivered, IFNULL(delivery.total_units_delivered, 0) as units_delivered', FALSE)
                ->from('sales_order_detail as details')
                ->join('inventory_product as product', 'product.id = details.fk_inventory_product_id')
                ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id');   

        $sub = $this->subquery->start_subquery('join', 'left', 'delivery.order_detail_id = details.id');
        $sub->select('SUM(delivery_detail.delivered_units) as total_units_delivered, SUM(delivery_detail.this_delivery) as total_qty_delivered, delivery_detail.fk_sales_order_detail_id as order_detail_id', FALSE);
        $sub->from('sales_delivery as s_delivery');
        $sub->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = s_delivery.id');
        $sub->where([
            's_delivery.status' => M_Status::STATUS_DELIVERED,
            's_delivery.fk_sales_order_id' => $order_id
        ]);
        $sub->group_by('delivery_detail.fk_sales_order_detail_id');
        $this->subquery->end_subquery('delivery');
        $this->db->where('details.fk_sales_order_id', $order_id);

        if($exclude_served_orders){
             $this->db->where('details.product_quantity > IFNULL(delivery.total_qty_delivered, 0)', FALSE, FALSE);
        }
        
        $data['items_ordered'] = $this->db->get()->result_array();

        return $data;
    }

    public function add($data) {
        $details = $data['details'];
        unset($data['details'], $data['add_ons']);
        if ($this->db->insert('sales_order', $data)) {
            $order_id = $this->db->insert_id();
            foreach ($details as &$d) {
                $d['fk_sales_order_id'] = $order_id;
            }

            $this->db->insert_batch('sales_order_detail', $details);
            
            return TRUE;
        }
        return FALSE;
    }

    public function update($order_id, $data) {
        $active_ids = array();
        $active_details = array();
        $new_details = array();
        $details = $data['details'];
        unset($data['details'], $data['add_ons']);
        $this->db->where('id', $order_id);
        if ($this->db->update('sales_order', $data)) {
            foreach ($details as &$d) {
                $d['fk_sales_order_id'] = $order_id;
                if (isset($d['id'])) {
                    $active_ids[] = $d['id'];
                    $active_details[] = $d;
                } else {
                    $new_details[] = $d;
                }
            }

            if (!empty($active_ids)) {
                $this->db->where_not_in('id', $active_ids);
            }
            $this->db->where('fk_sales_order_id', $order_id);
            $this->db->delete('sales_order_detail');

            if (!empty($active_details)) {
                $this->db->update_batch('sales_order_detail', $active_details, 'id');
            }
            if (!empty($new_details)) {
                $this->db->insert_batch('sales_order_detail', $new_details);
            }
           
            return TRUE;
        }
        return FALSE;
    }

    public function get_so_from($customer_id = FALSE, $status = FALSE) {
        $this->db->select('id')->from('sales_order')->where(array(
            'fk_sales_customer_id' => $customer_id,
            'status' => $status ? $status : M_Status::STATUS_APPROVED
        ));
        return $this->db->get()->result_array();
    }

    public function get_legit_prices($order_id) {
        $this->db->where('fk_sales_order_id', $order_id);
        $data = $this->db->select('id, unit_price')->from('sales_order_detail')->get()->result_array();
        if ($data) {
            $prices = array();
            foreach ($data as $d) {
                $prices[$d['id']] = $d['unit_price'];
            }
            return $prices;
        }
        return $data;
    }

    public function delete($id) {
        return $this->db->delete('sales_order', array('id' => $id));
    }

    public function get_ordered_products($order_id = FALSE, $order_details = FALSE)
    {
        if(!$order_id && !$order_details){
            return [];
        }
        $this->db->select('id, fk_inventory_product_id AS product_id')->from('sales_order_detail');
        if($order_id){
            $this->db->where('fk_sales_order_id', $order_id);
        }else{
            $this->db->where_in('id', $order_details);
        }
        return array_column($this->db->get()->result_array(), 'product_id', 'id');
    }

}

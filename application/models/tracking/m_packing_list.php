<?php

class M_Packing_list extends CI_Model {

    protected $table = 'tracking_packing_list';

    // function create($data)
    // {

    //     $this->db->trans_start();

    //     $this->db->insert('tracking_tariff', $data['tariff']);

    //     $id = $this->db->insert_id();

    //     if(!empty($data['tariff_details'])){
    //         foreach($data['tariff_details'] AS &$less){
    //             $less['fk_tariff_id'] = $id;
    //         }
    //         $this->db->insert_batch('tracking_tariff_details', $data['tariff_details']);
    //     }

    //     $this->db->trans_complete();

    //     return $this->db->trans_status() ? $id : FALSE;
    // }
    function create($packing, $order_line)
    {
        $this->db->trans_start();

        $this->db->insert($this->table, $packing);

        $id = $this->db->insert_id();

        foreach($order_line AS &$line){
            $line['fk_packing_list_id'] = $id;
        }

        $this->db->insert_batch('tracking_packing_list_details', $order_line);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

      public function get($id, $limit = 999, $offset = 0) {
        // $this->load->model('inventory/m_product');
        $this->db->select('tpl.*, tt.code AS customer, tt.option ,tl.name AS location', FALSE);
        $this->db->from('tracking_packing_list as tpl');
        $this->db->join('tracking_tariff as tt', 'tt.id = tpl.fk_tariff_id');
        $this->db->join('tracking_location as tl', 'tl.id = tt.fk_location_id', 'left');
        $this->db->where('tpl.id',$id);

        $this->db->order_by('tpl.id', 'DESC');
        $data = $this->db->get()->result_array();
        foreach ($data as &$o) {
            $formatted_details = array();
            $this->db->select('tpld.*');
            $this->db->select('ttd.rate, rate');
            $this->db->select('tl.name as location');

            $this->db->from('tracking_packing_list_details as tpld');
            $this->db->where('tpld.fk_packing_list_id', $o['id']);

            $this->db->join('tracking_tariff_details as ttd', 'ttd.id = tpld.fk_tariff_details_id');
            $this->db->join('tracking_location as tl', 'tl.id = ttd.fk_location_id', 'left');

            $less = $this->db->get()->result_array();
            if (!$less) {
                $formatted_details = array(
                    'fk_tariff_details_id' => array(''),
                    'fk_packing_list_id' => array(''),
                    'pcs' => array(0),
                    'amount' => array(0),
                    'rate' => array(0),
                    'location' => array('')
                );
            }
            for ($x = 0; $x < count($less); $x++) {
                $formatted_details['id'][$x] = $less[$x]['id'];
                $formatted_details['fk_tariff_details_id'][$x] = $less[$x]['fk_tariff_details_id'];
                $formatted_details['location'][$x] = "{$less[$x]['location']}";
                $formatted_details['location_desc'][$x] = $less[$x]['location'];
                $formatted_details['pcs'][$x] = $less[$x]['pcs'];
                $formatted_details['rate'][$x] = $less[$x]['rate'];
                $formatted_details['amount'][$x] = $less[$x]['amount'];
            }
            $o['less'] = $formatted_details;
        }
        return $data;
    }
    public function get_customer_products($customer_id) {
    $this->db->select('cpl.fk_inventory_product_id as product_id, '
                    . 'FORMAT(cpl.price, 2) as price, '
                    . 'FORMAT(cpl.discount, 2) as discount, '
                    . 'CONCAT("[", p.code, "] ", p.description) AS description, '
                    . 'u.description as prod_unit', FALSE)
            ->from('sales_customer_pricing AS cpl')
            ->join('inventory_product AS p', 'p.id = cpl.fk_inventory_product_id')
            ->join('inventory_unit AS u', 'u.id = p.fk_unit_id')
            ->where(['cpl.fk_sales_customer_id' => $customer_id, 'p.status' => 'a']);
    return $this->db->get()->result();
    }


    function update($id, $packing, $order_line)
    {
        $this->db->trans_start();

        $this->db->update($this->table, $packing, ['id' => $id]);

        $temp = ['new' => [], 'updated' => [], 'updated_ids' => []];

        foreach($order_line AS &$row){
            if(isset($row['id'])){
                $temp['updated'][] = $row;
                $temp['updated_ids'][] = $row['id'];
            }else{
                $row['fk_packing_list_id'] = $id;
                $temp['new'][] = $row;
            }
        }

        unset($order_line);

        if(empty($temp['updated_ids'])){
            $this->db->delete('tracking_packing_list_details', ['fk_packing_list_id' => $id]);
        }else{
            $this->db->where('fk_packing_list_id', $id)->where_not_in('id', $temp['updated_ids'])->delete('tracking_packing_list_details');
            $this->db->update_batch('tracking_packing_list_details', $temp['updated'], 'id');
        }

        if(!empty($temp['new'])){
            $this->db->insert_batch('tracking_packing_list_details', $temp['new']);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

function sync($table, $value_array, $table_pk_column_name, $table_fk_column_name, $table_fk_column_value)
    {
        $temp = ['new' => [], 'updated' => [], 'updated_ids' => []];

        foreach($value_array AS &$row){
            if(isset($row[$table_pk_column_name])){
                $temp['updated'][] = $row;
                $temp['updated_ids'][] = $row[$table_pk_column_name];
            }else{
                $row[$table_fk_column_name] = $table_fk_column_value;
                $temp['new'][] = $row;
            }
        }

        unset($value_array);

        if(empty($temp['updated_ids'])){
            $this->db->where([
                $table_fk_column_name => $table_fk_column_value
            ]);
            $this->db->delete($table);
        }else{
            $this->db->where([
                    $table_fk_column_name => $table_fk_column_value
                ])
                ->where_not_in($table_pk_column_name, $temp['updated_ids'])
                ->delete($table);

            $this->db->update_batch($table, $temp['updated'], $table_pk_column_name);
        }

        if(!empty($temp['new'])){
            $this->db->insert_batch($table, $temp['new']);
        }
    }
    function delete($ID) {
        if ($ID) {
            $this->db->where('id', $ID);
            return $this->db->delete('tracking_packing_list');
        }
        return FALSE;
    }

    function all($search=[] ,$wildcards = [])
    {
        $data = [];
        $this->db->select('DISTINCT pl.*, sc.company_name AS company, ttt.id AS trip_ticket , tt.code AS code', FALSE);
        $this->db->from('tracking_packing_list AS pl');
        $this->db->join('tracking_tariff AS tt', 'tt.id = pl.fk_tariff_id');
        $this->db->join('tracking_trip_ticket AS ttt', 'ttt.id = pl.fk_trip_ticket_id');
        $this->db->join('sales_customer AS sc', 'sc.id = pl.fk_sales_customer_id');

        if(!empty($search)){
           $this->db->where($search);
        }
        if(!empty($wildcards)){
            $this->db->like($wildcards);
        }

        $this->db->order_by('pl.id', 'DESC');

        $data = $this->db->get($this->table)->result_array();
        return $data;
    }

      public function get_tariff_detail($tariff_id) {
        $this->db->select('tt.*, tll.name AS location, ttd.id AS detail_id,ttd.rate');
        $this->db->from('tracking_tariff AS tt');
        $this->db->join('tracking_tariff_details AS ttd', 'tt.id = ttd.fk_tariff_id');
        $this->db->join('tracking_location AS tll', 'tll.id = ttd.fk_location_id','left');
        $this->db->where('tt.id', $tariff_id);
        $data = $this->db->get()->result();
        return $data;

    }

    public function get_tariff_value($id) {
        if($id){
            $this->db->select('tpl.fk_trip_ticket_id');
            $this->db->from('tracking_packing_list AS tpl');
            $this->db->where('tpl.id', $id);
        }
        return $this->db->get()->result_array();

    }
    public function find($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }


    function exists($id)
    {
        $this->db->where([
            'id' => $id
        ]);

        return $this->db->get('tracking_tariff')->num_rows() > 0;
    }


    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('code')->from($this->table)->where('code', $code)->get()->num_rows() === 0;
    }

    public function getTariff($id)
    {
        $this->db->select('DISTINCT tt.*, tl.name AS location',FALSE);
        $this->db->from('tracking_tariff AS tt');
        $this->db->join('tracking_location AS tl', 'tl.id = tt.fk_location_id');
        if($id){
            $this->db->where('tt.id', $id);
        }
        return $this->db->get('tracking_tariff')->result_array();
    }
    public function get_trip_ticket($id)
    {
        if($id){
            $this->db->select('tt.*');
            $this->db->from('tracking_trip_ticket AS tt');
            $this->db->where('fk_sales_customer_id', $id);
            $this->db->where('approved_by IS NOT NULL', null, false);
            $this->db->where('tt.id NOT IN (SELECT fk_trip_ticket_id FROM `pm_tracking_packing_list`)', NULL, FALSE);
        }
        return $this->db->get()->result_array();
    }

    public function getDetails($id)
    { 

        $data = $this->db->get_where('tracking_tariff', ['id' => $id])->row_array();

        if(!$data){
            return NULL;
        }
        $this->db->select('tt.*, tl.name AS location');
        $this->db->from('tracking_tariff AS tt');
        $this->db->join('tracking_location AS tl', 'tl.id = tt.fk_location_id');
        $this->db->where('tt.id', $id);
        $data['tariff'] = $this->db->get()->row_array();

        $this->db->select('DISTINCT ttd.*, tll.name AS location', FALSE);
        $this->db->from('tracking_tariff_details AS ttd');
        $this->db->join('tracking_location AS tll', 'tll.id = ttd.fk_location_id');
        $data['less'] = $this->db->get_where('tracking_tariff_details', ['ttd.fk_tariff_id' => $id])->result();

        return $data;
    }

   
}



// <?php

// class M_Sales_Order extends CI_Model {

//     public function get_next_row_id($order_id, $mode) {
//         if(!strcmp($mode, "next")){
//             $query = "SELECT id FROM pm_sales_order WHERE id > {$order_id} ORDER BY id ASC LIMIT 1";
//         }else{
//             $query = "SELECT id FROM pm_sales_order WHERE id < {$order_id} ORDER BY id DESC LIMIT 1";
//         }
//         return $this->db->query($query)->result_array();
//     }

//     public function get_min_id(){
//         return $this->db->query("SELECT MIN(id) as id FROM pm_sales_order")->result_array();
//     }

//     public function get_max_id(){
//         return $this->db->query("SELECT MAX(id) as id FROM pm_sales_order")->result_array();
//     }

//     public function master_list($arr = array()) {
//         $this->load->library('subquery');
//         $this->db->select('s_order.id, s_order.po_number, DATE_FORMAT(s_order.date, "%M %d, %Y") as date, customer.company_name, '
//                 . 'FORMAT(IFNULL(SUM((order_detail.unit_price * order_detail.product_quantity)-(order_detail.product_quantity*order_detail.discount)), 0), 2) as total_amount, '
//                 . 's_order.status', FALSE);
//         $this->db->from('sales_order as s_order');
//         $this->db->join('sales_order_detail as order_detail', 'order_detail.fk_sales_order_id = s_order.id', 'left');
//         $this->db->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
//         $this->filter_functions($arr);
//         $this->db->group_by('s_order.id')->order_by('s_order.id', 'DESC');
//         return $this->db->get()->result_array();
//     }

//     private function filter_functions($arr = array()) {
//         $arr['so'] ? $this->db->where('s_order.id', $arr['so']) : NULL;
//         $arr['po'] ? $this->db->where('s_order.po_number', $arr['po']) : NULL;
//         $arr['date'] ? $this->db->where('s_order.date', $arr['date']) : NULL;
//         $arr['customer'] ? $this->db->where('s_order.fk_sales_customer_id', $arr['customer']) : NULL;
//         $arr['page'] && $arr['page'] - 1 > 0 ? $this->db->limit(100, 100 * ($arr['page'] - 1)) : $this->db->limit(100, 0);
//     }

//     public function get($search_token = array(), $filter = array(), $limit = 999, $offset = 0) {
//         $this->load->model('inventory/m_product');
//         $this->db->select('s_order.*, CONCAT("[", customer.customer_code, "] ", customer.company_name) AS customer', FALSE);
//         $this->db->from('sales_order as s_order');
//         $this->db->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
//         if ($search_token) {
//             $this->db->like($search_token['category'], $search_token['token'], 'both');
//         }
//         if (!empty($filter)) {
//             $this->db->where($filter);
//         }
//         $this->db->order_by('s_order.id', 'DESC');
//         $orders = $this->db->limit($limit, $offset)->get()->result_array();
//         foreach ($orders as &$o) {
//             $o['misc_charges'] = json_decode($o['misc_charges'], TRUE);
//             $formatted_details = array();
//             $this->db->select('order_detail.*');
//             $this->db->select('product.description, product.code');
//             $this->db->select('unit.description as unit_description');
//             $this->db->from('pm_sales_order_detail as order_detail');
//             $this->db->where('order_detail.fk_sales_order_id', $o['id']);
//             $this->db->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id');
//             $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id', 'left');
//             $details = $this->db->get()->result_array();
//             if (!$details) {
//                 $formatted_details = array(
//                     'fk_inventory_product_id' => array(''),
//                     'fk_sales_agent_id' => array(''),
//                     'product_quantity' => array(''),
//                     'quantity_delivered' => array(0),
//                     'unit_description' => array('units'),
//                     'unit_price' => array(0),
//                     'discount' => array(0),
//                     'amount' => array(0),
//                     'remarks' => array(''),
//                     'unit_description' => array('units'),
//                     'total_units' => array(0)
//                 );
//             }
//             for ($x = 0; $x < count($details); $x++) {
//                 $formatted_details['id'][$x] = $details[$x]['id'];
//                 $formatted_details['fk_inventory_product_id'][$x] = $details[$x]['fk_inventory_product_id'];
//                 $formatted_details['product_description'][$x] = "[{$details[$x]['code']}] {$details[$x]['description']}";
//                 $formatted_details['prod_desc'][$x] = $details[$x]['description'];
//                 $formatted_details['product_quantity'][$x] = $details[$x]['product_quantity'];
//                 $formatted_details['quantity_delivered'][$x] = $details[$x]['quantity_delivered'];
//                 $formatted_details['unit_description'][$x] = $details[$x]['unit_description'];
//                 $formatted_details['unit_price'][$x] = $details[$x]['unit_price'];
//                 $formatted_details['discount'][$x] = $details[$x]['discount'];
//                 $formatted_details['remarks'][$x] = $details[$x]['remarks'];
//                 $formatted_details['total_units'][$x] = $details[$x]['total_units'];
//             }
//             $o['details'] = $formatted_details;
//         }
//         return $orders;
//     }

//     public function fetch_order_details($order_id, $exclude_served_orders = FALSE) {
//         $data = [];
//         $this->load->library('subquery');
//         $this->db->select('details.id, details.fk_inventory_product_id,  details.product_quantity,  details.discount, details.unit_price, details.total_units, '
//                         . 'CONCAT("[",product.code,"] ", product.description) AS product_description, '
//                         . 'unit.description as unit_description, '
//                         . 'IFNULL(delivery.total_qty_delivered, 0) as quantity_delivered, IFNULL(delivery.total_units_delivered, 0) as units_delivered', FALSE)
//                 ->from('sales_order_detail as details')
//                 ->join('inventory_product as product', 'product.id = details.fk_inventory_product_id')
//                 ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id');   

//         $sub = $this->subquery->start_subquery('join', 'left', 'delivery.order_detail_id = details.id');
//         $sub->select('SUM(delivery_detail.delivered_units) as total_units_delivered, SUM(delivery_detail.this_delivery) as total_qty_delivered, delivery_detail.fk_sales_order_detail_id as order_detail_id', FALSE);
//         $sub->from('sales_delivery as s_delivery');
//         $sub->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = s_delivery.id');
//         $sub->where([
//             's_delivery.status' => M_Status::STATUS_DELIVERED,
//             's_delivery.fk_sales_order_id' => $order_id
//         ]);
//         $sub->group_by('delivery_detail.fk_sales_order_detail_id');
//         $this->subquery->end_subquery('delivery');
//         $this->db->where('details.fk_sales_order_id', $order_id);

//         if($exclude_served_orders){
//              $this->db->where('details.product_quantity > IFNULL(delivery.total_qty_delivered, 0)', FALSE, FALSE);
//         }
        
//         $data['items_ordered'] = $this->db->get()->result_array();

//         return $data;
//     }

//     public function add($data) {
//         $details = $data['details'];
//         unset($data['details'], $data['add_ons']);
//         if ($this->db->insert('sales_order', $data)) {
//             $order_id = $this->db->insert_id();
//             foreach ($details as &$d) {
//                 $d['fk_sales_order_id'] = $order_id;
//             }

//             $this->db->insert_batch('sales_order_detail', $details);
            
//             return TRUE;
//         }
//         return FALSE;
//     }

//     public function update($order_id, $data) {
//         $active_ids = array();
//         $active_details = array();
//         $new_details = array();
//         $details = $data['details'];
//         unset($data['details'], $data['add_ons']);
//         $this->db->where('id', $order_id);
//         if ($this->db->update('sales_order', $data)) {
//             foreach ($details as &$d) {
//                 $d['fk_sales_order_id'] = $order_id;
//                 if (isset($d['id'])) {
//                     $active_ids[] = $d['id'];
//                     $active_details[] = $d;
//                 } else {
//                     $new_details[] = $d;
//                 }
//             }

//             if (!empty($active_ids)) {
//                 $this->db->where_not_in('id', $active_ids);
//             }
//             $this->db->where('fk_sales_order_id', $order_id);
//             $this->db->delete('sales_order_detail');

//             if (!empty($active_details)) {
//                 $this->db->update_batch('sales_order_detail', $active_details, 'id');
//             }
//             if (!empty($new_details)) {
//                 $this->db->insert_batch('sales_order_detail', $new_details);
//             }
           
//             return TRUE;
//         }
//         return FALSE;
//     }

//     public function get_so_from($customer_id = FALSE, $status = FALSE) {
//         $this->db->select('id')->from('sales_order')->where(array(
//             'fk_sales_customer_id' => $customer_id,
//             'status' => $status ? $status : M_Status::STATUS_APPROVED
//         ));
//         return $this->db->get()->result_array();
//     }

//     public function get_legit_prices($order_id) {
//         $this->db->where('fk_sales_order_id', $order_id);
//         $data = $this->db->select('id, unit_price')->from('sales_order_detail')->get()->result_array();
//         if ($data) {
//             $prices = array();
//             foreach ($data as $d) {
//                 $prices[$d['id']] = $d['unit_price'];
//             }
//             return $prices;
//         }
//         return $data;
//     }

//     public function delete($id) {
//         return $this->db->delete('sales_order', array('id' => $id));
//     }

//     public function get_ordered_products($order_id = FALSE, $order_details = FALSE)
//     {
//         if(!$order_id && !$order_details){
//             return [];
//         }
//         $this->db->select('id, fk_inventory_product_id AS product_id')->from('sales_order_detail');
//         if($order_id){
//             $this->db->where('fk_sales_order_id', $order_id);
//         }else{
//             $this->db->where_in('id', $order_details);
//         }
//         return array_column($this->db->get()->result_array(), 'product_id', 'id');
//     }

// }

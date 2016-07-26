<?php

class M_Packing_list extends CI_Model {

    protected $table = 'tracking_packing_list';
    public $trip_ticket;
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
        $data['packinglist_created'] = 1;
        $this->db->from('tracking_trip_ticket as tt');
        $this->db->where(['tracking_trip_ticket.id' => $packing['fk_trip_ticket_id']]);
        $this->db->update('tracking_trip_ticket', $data);

        return $this->db->trans_status();
    }

      public function get($id, $limit = 999, $offset = 0) {
        // $this->load->model('inventory/m_product');
        $this->db->select('tpl.*, tt.option, tl.name AS location');
        $this->db->select('customer.company_name AS customer, CASE tt.option WHEN 1 THEN "Departure" ELSE "Arrival" END AS trip_point, CASE tt.option WHEN 1 THEN "Arrival" ELSE "Departure" END AS service_point, tl.name AS trip_point_location, trip_t.id AS trip_ticket_id, trip_t.date AS trip_ticket_date,
            CASE trip_t.trip_type WHEN 1 THEN "Chick Van" WHEN 2 THEN "Harvester" ELSE "Dressed Chicken" END AS trip_type', FALSE);

        $this->db->from('tracking_packing_list as tpl');
        $this->db->join('tracking_trip_ticket as trip_t', 'trip_t.id = tpl.fk_trip_ticket_id');
        $this->db->join('tracking_tariff as tt', 'tt.id = tpl.fk_tariff_id');
        $this->db->join('sales_customer as customer', 'customer.id = tpl.fk_sales_customer_id');
        $this->db->join('tracking_location as tl', 'tl.id = tt.fk_location_id', 'left');
        $this->db->where('tpl.id',$id);

        $this->db->order_by('tpl.id', 'DESC');
        $data = $this->db->get()->result_array();

        foreach ($data as &$o) {
            $formatted_details = array();
            $this->db->select('tpld.*');
            $this->db->select('tl.name as location');

            $this->db->from('tracking_packing_list_details as tpld');
            $this->db->where('tpld.fk_packing_list_id', $o['id']);
            $this->db->join('tracking_location as tl', 'tl.id = tpld.fk_location_id', 'left');
            // $this->db->where('ttd.deleted_at', 0);
            $less = $this->db->get()->result_array();
            if (!$less) {
                $formatted_details = array(
                    'fk_location_id' => array(''),
                    'fk_packing_list_id' => array(''),
                    'pcs' => array(0),
                    'amount' => array(0),
                    'rate' => array(0),
                    'location' => array('')
                );
            }
            for ($x = 0; $x < count($less); $x++) {
                $formatted_details['id'][$x] = $less[$x]['id'];
                $formatted_details['fk_location_id'][$x] = $less[$x]['fk_location_id'];
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

    function getCustomer($search = []){
        $this->db->select('tt.*, sc.company_name AS company_name, sc.id AS id', FALSE);
        $this->db->from('tracking_trip_ticket as tt');
        $this->db->join('sales_customer as sc', 'sc.id = tt.fk_sales_customer_id');
        $this->db->where('tt.packinglist_created',0);
        $this->db->order_by('sc.company_name', 'ASC');
        return $this->db->get()->result_array();
    }

    function getCustomerNew($search = []){
        $this->db->select('tt.*, sc.company_name AS company_name, sc.id AS id', FALSE);
        $this->db->from('tracking_packing_list as tt');
        $this->db->join('sales_customer as sc', 'sc.id = tt.fk_sales_customer_id');
        $this->db->where($search);
        $this->db->order_by('sc.company_name', 'ASC');
        $res1 = $this->db->get()->result_array();
        // if(!empty($search)){
        //     $this->db->select('pl.*, sd.company_name AS company_name, sd.id AS id', FALSE);
        //     $this->db->from('tracking_packing_list as pl');
        //     $this->db->join('sales_customer as sd', 'sd.id = pl.fk_sales_customer_id');
        //     $this->db->where($search);
        //     $res2 = $this->db->get()->result_array();
        // }
        // $result = array_merge($res1 , $res2);

        return $res1;
    }

    function update($id, $packing, $order_line)
    {
        $this->db->trans_start();
        print_r($this->trip_ticket);
        $this->db->update($this->table, $packing, ['id' => $id]);
        $data['packinglist_created'] = 1;
        $this->db->from('tracking_trip_ticket as tt');
        $this->db->where(['tracking_trip_ticket.id' => $packing['fk_trip_ticket_id']]);
        $this->db->update('tracking_trip_ticket', $data);

        $this->db->from('tracking_trip_ticket as ttt');
        $this->db->where(['tracking_trip_ticket.id' => $this->trip_ticket]);
        $this->db->update('tracking_trip_ticket', ['packinglist_created' => 0]);


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
    public function get_trip_value($id)
    {
        if($id){
            $this->db->select('tt.*');
            $this->db->from('tracking_trip_ticket AS tt');
            $this->db->where('tt.id', $id);
            // $this->db->where('approved_by IS NOT NULL', null, false);
            // $this->db->where('tt.id NOT IN (SELECT fk_trip_ticket_id FROM `pm_tracking_packing_list`)', NULL, FALSE);
        }
        return $this->db->get()->result_array();
    }
      public function get_tariff_detail($tariff_id) {
        $this->db->select('tt.*, tll.name AS location, ttd.id AS detail_id,ttd.rate');
        $this->db->from('tracking_tariff AS tt');
        $this->db->join('tracking_tariff_details AS ttd', 'tt.id = ttd.fk_tariff_id');
        $this->db->join('tracking_location AS tll', 'tll.id = ttd.fk_location_id','left');
        $this->db->where('tt.id', $tariff_id);
        // $this->db->where('ttd.deleted_at', 0);
        $data = $this->db->get()->result();
        return $data;

    }

    public function reset($id){
        $data['packinglist_created'] = 0;
        $this->db->from('tracking_trip_ticket as tt');
        $this->db->where(['tracking_trip_ticket.id' => $id]);
        $this->db->update('tracking_trip_ticket', $data);
    }
    public function get_tariff_value($id) {
        if($id){
            $this->db->select('tpl.fk_trip_ticket_id');
            $this->db->from('tracking_packing_list AS tpl');
            $this->db->where('tpl.id', $id);
        }
        $query = $this->db->get()->result_array();

        $this->trip_ticket = $query[0]['fk_trip_ticket_id'];
        return $query;

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
            $this->db->where(['tt.id', $id]);
        }
        return $this->db->get('tracking_tariff')->result_array();
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


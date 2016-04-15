<?php

class M_Gatepass extends CI_Model {

    const TABLE_NAME_GENERAL = 'inventory_delivery_log';
    const TABLE_NAME_DETAIL = 'inventory_delivery_log_detail';

    public function get_by_id($id) {
        //get general info
        $this->db->select('log.*, truck.trucking_name, truck.plate_number, truck.driver, CONCAT(user.FirstName," ",user.LastName) as generated_by', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as log');
        $this->db->join('sales_trucking as truck', 'log.fk_sales_trucking_id = truck.id');
        $this->db->join('account as user', 'user.id = log.generated_by');
        $this->db->where('log.id', $id);
        $info['general'] = $this->db->get()->row_array();

        //get details
        $this->db->select('log_detail.quantity, product.description, product.code, unit.description as unit');
        $this->db->from(self::TABLE_NAME_DETAIL . ' as log_detail');
        $this->db->join('inventory_product as product', 'product.id = log_detail.fk_inventory_product_id');
        $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id');
        $this->db->where('log_detail.fk_delivery_log_id', $id);
        $info['details'] = $this->db->get()->result_array();

        return $info;
    }

    public function get($with_details = FALSE, $search_token = array(), $filter = array(), $limit = 15, $offset = 0) {
        $this->db->select('log.*, truck.trucking_name, truck.plate_number')->from(self::TABLE_NAME_GENERAL . ' as log');
        $this->db->join('sales_trucking as truck', 'log.fk_sales_trucking_id = truck.id');
        $this->db->join('account as user', 'user.id = log.generated_by');
        if ($search_token) {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $data = $this->db->order_by('log.exit_datetime', 'DESC')->limit($limit, $offset)->get()->result_array();

        return $data;
    }

    public function add($general, $details) {
        $this->db->trans_begin();

        $this->db->insert(self::TABLE_NAME_GENERAL, $general); //insert general info
        $id = $this->db->insert_id(); //get id from the insert   
        foreach ($details as &$item) { //add the id to details
            $item['fk_delivery_log_id'] = $id;
        }
        $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details); //insert details

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit(); //uncomment after debug
            return $id; //uncomment after debug
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

}

<?php

class M_Job_Order extends CI_Model {

    const TABLE_NAME_GENERAL = 'production_job_order';
    const TABLE_NAME_DETAIL = 'production_job_order_detail';
    const TABLE_NAME_MISC = 'production_job_order_misc_fees';

    const LIMIT_PER_PAGE = 50;

    function __construct() {
        parent::__construct();
    }

    public function master_list($params) {
        $this->db->select('jo.id, DATE_FORMAT(jo.datetime_started, "%m/%d/%Y %r") as date_started, jo.production_code, jo.approved_by', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as jo');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as detail', 'detail.fk_production_job_order_id = jo.id');
        $this->db->group_by('jo.id')->order_by('jo.id', 'DESC');
        $this->filter_functions($params);
        return $this->db->get()->result_array();
    }

    private function filter_functions($arr = array()) {
        $this->load->helper('pmdate');
        $date = explode(' - ', $arr['date']);
        if($arr['date'] && is_valid_date($date, 'm/d/Y')){
            $start_date = date('Y-m-d', strtotime($date[0]));
            $end_date = date('Y-m-d', strtotime($date[1]));
            $this->db->where('DATE(date_started) >=', "'{$start_date}'", FALSE, FALSE)->where('DATE(date_started) <=', "'{$end_date}'", FALSE, FALSE);
        }
        $arr['jo'] ? $this->db->where('jo.id', $arr['jo']) : NULL;
        $arr['production_code'] ? $this->db->where('jo.production_code', $arr['production_code']) : NULL;
        $arr['page'] && $arr['page'] - 1 > 0 ? $this->db->limit(self::LIMIT_PER_PAGE, self::LIMIT_PER_PAGE * ($arr['page'] - 1)) : $this->db->limit(self::LIMIT_PER_PAGE, 0);

    }

    public function get($id) {
        $data = [];
        $this->db->select('jo.id, DATE_FORMAT(jo.datetime_started, "%m/%d/%Y %r") as date_started, jo.production_code, jo.approved_by, jo.remarks', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as jo');
        $this->db->where('jo.id', $id);
        $data['jo'] = $this->db->get()->row_array();

        $this->db->select('detail.id, detail.fk_production_job_order_id, detail.sequence_number, detail.fk_inventory_product_id, detail.mix_number, detail.fk_sales_customer_id');
        $this->db->from(self::TABLE_NAME_DETAIL . ' as detail');
        $this->db->where('detail.fk_production_job_order_id', $id);
        $data['details'] = $this->db->get()->result_array();

        return $data;
    }
    
    public function delete($id){
        $this->db->where('id', $id);
        return $this->db->delete(self::TABLE_NAME_GENERAL);
    }

    public function create($data)
    {
        $this->load->helper('pmarray');
        $this->db->trans_start();

        $this->db->insert('production_job_order', $data['jo']);

        $id = $this->db->insert_id();

        array_walk($data['details'], 'insert_prop', ['name' => 'fk_production_job_order_id', 'value' => $id]);

        $this->db->insert_batch('production_job_order_detail', $data['details']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            return TRUE;
        }
        $this->db->trans_rollback();
        return FALSE;
    }

    public function update($id, $data)
    {
        $this->db->trans_start();

        $this->db->update('production_job_order', $data['jo'], ['id' => $id]);

        $details = [
            'existing_ids' => [],
            'new_details' => [],
            'existing_details' => []
        ];

        foreach($data['details'] AS $d)
        {
            if(isset($d['id']))
            {
                $details['existing_ids'][] = $d['id'];
                $details['existing_details'][] = $d; 
            }
            else
            {
                $temp = $d;
                $temp['fk_production_job_order_id'] = $id;
                $details['new_details'][] = $temp;
            }
        }

        if(empty($details['existing_ids']))
        {
            $this->db->delete('production_job_order_detail', ['fk_production_job_order_id' => $id]);
        }
        else
        {
            $this->db->where('fk_production_job_order_id', $id)->where_not_in('id', $details['existing_ids'])->delete('production_job_order_detail');
            $this->db->update_batch('production_job_order_detail', $details['existing_details'], 'id');
        }

        if(!empty($details['new_details']))
        {
            $this->db->insert_batch('production_job_order_detail', $details['new_details']);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) 
        {
            $this->db->trans_commit();
            return TRUE;
        }
        $this->db->trans_rollback();
        return FALSE;
    }

    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE)
        {
            $this->db->where('id !=', $id);
        }
        return $this->db->select('id')->from('production_job_order')->where('production_code', $code)->get()->num_rows() === 0;
    }

    public function has_valid_details($detail_ids, $jo_id)
    {
        $detail_ids = array_unique($detail_ids);
        $this->db->where('fk_production_job_order_id', $jo_id)->where_in('id', $detail_ids);
        return $this->db->select('id')->from('production_job_order_detail')->get()->num_rows() === $detail_ids;
    }

    public function exists($id)
    {
        return $this->db->select('id')->from('production_job_order')->where('id', $id)->get()->num_rows() === 1;
    }

    public function is_approved($id)
    {
        return $this->db->select('id')->from('production_job_order')->where(['id' =>$id, 'received' => 0])->where('`approved_by` IS NOT NULL', FALSE, FALSE)->get()->num_rows() === 1;
    }

    public function get_unreceived()
    {
        $this->db->select('ord.id, ord.production_code')->from('production_job_order AS ord')->join('production_receiving AS rec', 'rec.jo_no = ord.id', 'left');
        return $this->db->where('rec.id IS NULL', FALSE, FALSE)->get()->result_array();
    }

    public function get_details($jo_id)
    {
        $this->db->select('d.id, p.description, f.formulation_code, d.mix_number, u.description AS unit, customer.company_name AS customer');
        $this->db->from('production_job_order_detail AS d');
        $this->db->join('inventory_product AS p', 'p.id = d.fk_inventory_product_id');
        $this->db->join('production_formulation AS f', 'f.id = p.fk_production_formulation_id');
        $this->db->join('inventory_unit AS u', 'u.id = p.fk_unit_id');
        $this->db->join('sales_customer AS customer', 'customer.id = d.fk_sales_customer_id', 'left');
        $this->db->where('d.fk_production_job_order_id', $jo_id);
        return $this->db->get()->result_array();
    }

    public function get_consumed_materials($jo_id)
    {
        $this->db->select('SUM(pfd.quantity*mix_number) AS quantity, pfd.fk_inventory_product_id AS rm_id', FALSE);
        $this->db->from('production_job_order_detail AS jod');
        $this->db->join('inventory_product AS p', 'p.id = jod.fk_inventory_product_id');
        $this->db->join('production_formulation_detail AS pfd', 'pfd.fk_production_formulation_id = p.fk_production_formulation_id');
        $this->db->where('jod.fk_production_job_order_id', $jo_id);
        $this->db->group_by('pfd.fk_inventory_product_id');
        return array_column($this->db->get()->result_array(), 'quantity', 'rm_id');
    }


    public function perform_costing($products)
    {
        $this->db->select('');
    }

}

<?php

class M_Receiving extends CI_Model
{

    const LIMIT_PER_PAGE = 50;

    public function create($data)
    {
        $this->load->helper('pmarray');
        $this->db->trans_start();

        $this->db->insert('production_receiving', $data['receiving']);
        $id = $this->db->insert_id();
        array_walk($data['details'], 'insert_prop', ['name' => 'receiving_id', 'value' => $id]);
        $this->db->insert_batch('production_receiving_details', $data['details']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            return $id;
        }
        $this->db->trans_rollback();
        return FALSE;
    }

    public function update($id, $data)
    {

        $this->db->trans_start();

        $details = ['new' => [], 'existing' => []];

        $this->db->update('production_receiving', $data['receiving'], ['id' => $id]);

        foreach ($data['details'] AS $row) {
            if (isset($row['id'])) {
                $details['existing'][] = $row;
            } else {
                $row['receiving_id'] = $id;
                $details['new'][] = $row;
            }
        }

        if (!empty($details['new'])) {
            $this->db->insert_batch('production_receiving_details', $details['new']);
        }

        if (!empty($details['existing'])) {
            $this->db->update_batch('production_receiving_details', $details['existing'], 'id');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            return TRUE;
        }
        $this->db->trans_rollback();
        return FALSE;
    }

    public function all($params)
    {
        $this->db->select('r.id, DATE_FORMAT(r.datetime, "%m/%d/%Y %r") as datetime, jo.production_code, r.approved_by', FALSE);
        $this->db->from('production_receiving AS r');
        $this->db->join('production_job_order AS jo', 'jo.id = r.jo_no');
        $this->filter_functions($params);
        return $this->db->order_by('r.id', 'DESC')->get()->result_array();
    }

    public function get($id)
    {
        $this->db->select('r.id, DATE_FORMAT(r.datetime, "%m/%d/%Y %r") as datetime, jo.production_code, jo.id AS jo_no, r.remarks, r.approved_by', FALSE);
        $this->db->from('production_receiving AS r');
        $this->db->join('production_job_order AS jo', 'jo.id = r.jo_no');
        $this->db->where('r.id', $id);
        $data = $this->db->get()->row_array();

        $this->db->select('r.id, d.id AS jo_detail_id, p.description, f.formulation_code, d.mix_number, u.description AS unit, r.quantity');
        $this->db->from('production_job_order_detail AS d');
        $this->db->join('production_receiving_details AS r', 'r.jo_detail_id = d.id', 'left');
        $this->db->join('inventory_product AS p', 'p.id = d.fk_inventory_product_id');
        $this->db->join('production_formulation AS f', 'f.id = p.fk_production_formulation_id');
        $this->db->join('inventory_unit AS u', 'u.id = p.fk_unit_id');
        $this->db->where('d.fk_production_job_order_id', $data['jo_no']);
        $data['details'] = $this->db->get()->result_array();

        return $data;
    }

    private function filter_functions($arr = array())
    {
        $this->load->helper('pmdate');
        $date = explode(' - ', $arr['date']);
        if ($arr['date'] && is_valid_date($date, 'm/d/Y')) {
            $start_date = date('Y-m-d', strtotime($date[0]));
            $end_date = date('Y-m-d', strtotime($date[1]));
            $this->db->where('DATE(r.datetime) >=', "'{$start_date}'", FALSE, FALSE)->where('DATE(r.datetime) <=', "'{$end_date}'", FALSE, FALSE);
        }
        $arr['id'] ? $this->db->where('r.id', $arr['id']) : NULL;
        $arr['jo_no'] ? $this->db->where('r.jo_no', $arr['jo_no']) : NULL;
        $arr['page'] && $arr['page'] - 1 > 0 ? $this->db->limit(self::LIMIT_PER_PAGE, self::LIMIT_PER_PAGE * ($arr['page'] - 1)) : $this->db->limit(self::LIMIT_PER_PAGE, 0);
    }

    public function exists($id)
    {
        return $this->db->select('id')->from('production_receiving')->where('id', $id)->get()->num_rows() === 1;
    }

    public function delete($id)
    {
        return $this->db->delete('production_receiving', ['id' => $id]);
    }

    public function get_cost($receiving_detail_id)
    {
        $cost = 0;
        $this->load->model('production/m_formulation', 'formulation');
        $this->db->select('pjod.fk_inventory_product_id AS product_id, pjod.fk_production_job_order_id AS jo_id, pjod.mix_number');
        $this->db->from('production_receiving_details AS prd');
        $this->db->join('production_job_order_detail AS pjod', 'pjod.id = prd.jo_detail_id');
        $this->db->where('prd.id', $receiving_detail_id);
        $result = $this->db->get()->row_array();

        $raw_mats = $this->formulation->get_raw_mats([$result['product_id']]);
        // $data = ['finished_id' => $result['product_id'] ,'costing' => []] ;
        foreach($raw_mats AS $row){
            $rm_cost = $this->formulation->get_cost($row['rm_id'], $result['jo_id']);
            $cost += ($row['quantity'] * $rm_cost);         
            // $data['costing'][] = ['rm' => $row['rm_id'], 'cost' => $this->formulation->get_cost($row['rm_id'], $result['jo_id'])];
        }

        return $cost * $result['mix_number'];
        // return $data;
    }

}

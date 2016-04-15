<?php

class M_Formulation extends CI_Model {

    const TABLE_NAME_GENERAL = 'production_formulation';
    const TABLE_NAME_DETAIL = 'production_formulation_detail';

    public function __construct() {
        parent::__construct();
    }

    public function _create($general, $details) {
        $this->db->trans_start();

        /* INSERT GENERAL INFO */
        $this->db->insert(self::TABLE_NAME_GENERAL, $general);
        /* GET ID FROM GENERAL INFO */
        $insert_id = $this->db->insert_id();
        /* ADD THE ID TO DETAILS */
        foreach ($details as &$row) {
            $row['fk_production_formulation_id'] = $insert_id;
        }
        /* INSER DETAILS */
        $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);
        $this->db->trans_complete();

        return $this->db->trans_status() === TRUE;
    }

    public function _update($id, $general, $details) {
        $existing_detail_ids = array();
        $existing_details = array();
        $new_details = array();
        $this->db->trans_start();

        $this->db->where('id', $id);

        /* UPDATE GENERAL INFO */
        $this->db->update(self::TABLE_NAME_GENERAL, $general);

        /* SEPARATE NEW DETAILS FROM EXISTING DETAILS */
        foreach ($details as &$row) {
            if (array_key_exists('id', $row) && is_numeric($row['id'])) {
                $existing_details[] = $row;
                $existing_detail_ids[] = $row['id'];
            } else {
                $row['fk_production_formulation_id'] = $id;
                $new_details[] = $row;
            }
        }

        if (!empty($existing_detail_ids)) {
            $this->db->where('fk_production_formulation_id',$id);
            $this->db->where_not_in('id', $existing_detail_ids);
            $this->db->delete(self::TABLE_NAME_DETAIL);
        }

        /* UPDATE EXISTING DETAILS */
        if (!empty($existing_details)) {
            $this->db->update_batch(self::TABLE_NAME_DETAIL, $existing_details, 'id');
        }
        /* INSERT NEW DETAILS */
        if (!empty($new_details)) {
            $this->db->insert_batch(self::TABLE_NAME_DETAIL, $new_details);
        }
        $this->db->trans_complete();

        return $this->db->trans_status() === TRUE;
    }

    public function get($id = FALSE, $with_details = FALSE) {
        $this->db->select('formula.*')->from(self::TABLE_NAME_GENERAL . ' as formula');
        if (is_numeric($id)) {
            $this->db->where('formula.id', $id);
        }
        if(is_array($id))
        {
            $this->db->where($id);
        }
        $this->db->order_by('status', 'DESC')->order_by('formulation_code', 'ASC');
        $formulas = $this->db->get()->result_array();
        $details = array();
        $formula_ids = array();
        if ($formulas && $with_details === TRUE) {
            $formula_ids = array_map(function($var) {
                return $var['id'];
            }, $formulas);
        }
        if (!empty($formula_ids)) {
            $this->db->select('detail.id, detail.fk_production_formulation_id, detail.fk_inventory_product_id as raw_product_id, detail.quantity as raw_product_quantity, '
                            . 'product.description as raw_product_description, product.code as raw_product_code, unit.description as raw_product_unit', FALSE)
                    ->from(self::TABLE_NAME_DETAIL . ' as detail')
                    ->join('inventory_product as product', 'product.id = detail.fk_inventory_product_id')
                    ->join('inventory_unit as unit', 'unit.id = product.fk_unit_id')
                    ->where_in('detail.fk_production_formulation_id', $formula_ids);
            $details = $this->db->get()->result_array();
        }
        if (!empty($details)) {
            foreach ($formulas as &$formula) {
                $ID = $formula['id'];
                $formula['details'] = array_filter($details, function($var) USE ($ID) {
                    if ($var['fk_production_formulation_id'] == $ID) {
                        return $var;
                    }
                });
            }
        }
        return $formulas;
    }

    public function is_valid($id = array()) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if (is_array($id)) {
            $this->db->where_in('id', $id);
            return count($id) ===  $this->db->get()->num_rows();
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function get_average($raw_mats)
    {
        $this->db->select('product_id, AVG(unit_price) AS unit_price', FALSE)->from('running_inventory');
        $this->db->where_in('product_id', $raw_mats)->where('`in` IS NOT NULL', FALSE, FALSE);
        $this->db->where(['is_counted' => 1])->group_by('product_id');
        return $this->db->get()->result_array();
    }

    public function get_point_before_production($job_order_id)
    {
        $this->db->select('MIN(id) AS id', FALSE)->from('production_job_order_detail');
        $this->db->where('fk_production_job_order_id', $job_order_id);
        $result = $this->db->get()->row_array();

        $this->db->select('id')->from('running_inventory')->where('jo_order_detail_id', $result['id']);
        $id = $this->db->get()->row_array();
        return $id['id'];
    }

    public function get_cost($product_id, $jo_id = FALSE)
    {
        if($jo_id){
            $movement_limit = $this->get_point_before_production($jo_id);
        }
        $this->db->select('in,out,unit_price')->from('running_inventory');
        $this->db->where(['product_id' => $product_id, 'is_counted' => 1]);
        if(isset($movement_limit)){
            $this->db->where('id <', $movement_limit);
        }
        $result = $this->db->order_by('id', 'ASC')->get()->result_array();

        $overall_cost = 0;
        $overall_qty = 0;

        foreach($result AS $index => &$row){
            if($row['in'] > 0){
                $row['remaining'] = $row['in'];
                $overall_cost = (($overall_cost*$overall_qty) + ($row['in'] * $row['unit_price'])) / ($overall_qty + $row['in']);
                $overall_qty += $row['in'];
            }else if($row['out'] > 0){
                $row['cost'] = $row['out'] * $overall_cost;
                $temp_out = $row['out'];
                for($x = 0; $x<$index; $x++){
                    if($result[$x]['in'] > 0 && $result[$x]['remaining'] > 0){
                        $temp_out -= $result[$x]['remaining'];
                        if($temp_out < 0){
                            $result[$x]['remaining'] = abs($temp_out);
                            break;
                        }else if($temp_out === 0){
                            $result[$x]['remaining'] = 0;
                            break;
                        }else{
                            $result[$x]['remaining'] = 0;
                        }
                    }
                }
                $overall_qty -= $row['out'];
            }
        }
        return $overall_qty ? round($overall_cost, 2): 0;
    }

    public function get_fifo_cost($product_id)
    {
        $this->load->model('production/m_receiving', 'receiving');
        $this->db->select('*')->from('running_inventory');
        $this->db->where(['product_id' => $product_id, 'is_counted' => 1]);
        $this->db->or_having(['in >' => 0, 'out >' => 0]);
        $result = $this->db->order_by('id', 'ASC')->get()->result_array();

        $costs = [];
        $current_cost = 0;

        foreach($result AS $index => &$row){
            if($row['in'] > 0){
                $row['remaining'] = $row['in'];
                if($row['production_receiving_detail_id'] && $row['production_receiving_detail_id'] > 86){
                    $costs[] = [ 'qty' => $row['in'], 'orig_qty' => $row['in'], 'production_receiving_detail_id' =>  $row['production_receiving_detail_id']];
                }else{
                    $costs[] = [ 'qty' => $row['in'], 'orig_qty' => $row['in'], 'cost' => $row['unit_price']];
                }
            }else if($row['out'] > 0){
                $temp_out = $row['out'];
                foreach($costs AS &$c){
                    if($c['qty'] > 0){
                        $remaining = $c['qty'] - $temp_out;
                        if($remaining === 0){
                            $c['qty'] = 0;
                            break;
                        }else if($remaining < 0){
                            $c['qty'] = 0;
                            $temp_out = abs($remaining);
                        }else{
                            $c['qty'] = $remaining;
                            break;
                        }
                    }
                }
            }
        }
        
        foreach($costs AS $cost){
            if(floatval($cost['qty']) > 0){
               if(isset($cost['production_receiving_detail_id'])){
                    return $this->receiving->get_cost($cost['production_receiving_detail_id']) / $cost['orig_qty'];
               }
               return $cost['cost'];
            }
        }
        return 0;
    }

    public function get_raw_mats($pf_ids)
    {
        $this->db->select('p.id AS fp_id, pfd.fk_inventory_product_id AS rm_id, pfd.quantity', FALSE);
        $this->db->from('inventory_product AS p');
        $this->db->join('production_formulation_detail AS pfd', 'pfd.fk_production_formulation_id = p.fk_production_formulation_id');
        $this->db->where_in('p.id', $pf_ids);
        return $this->db->get()->result_array();
    }
    
    /////////

    public function all($params = FALSE, $like = FALSE)
    {
        $this->db->select('id, formulation_code, status');
        $this->db->from('production_formulation');
        if($params){
            $this->db->where($params);
        }
        if($like){
            $this->db->like($like);
        }
        $this->db->order_by('formulation_code');
        return $this->db->get()->result_array();
    }

    public function find($id)
    {
        $formulation = $this->db->get_where('production_formulation', ['id' => $id])->row_array();
        if(!$formulation){
            return NULL;
        }
        $this->db->select('pfd.id, pfd.quantity, p.description, p.id AS product_id, u.description AS unit');
        $this->db->from('production_formulation_detail AS pfd, inventory_product AS p, inventory_unit AS u');
        $this->db->where('pfd.fk_inventory_product_id = p.id AND p.fk_unit_id = u.id', FALSE, FALSE);
        $this->db->where('pfd.fk_production_formulation_id', $id);
        $details = $this->db->get()->result_array();
        array_walk($details, function(&$var){
            $var['cost'] = $this->get_cost($var['product_id']);
        });
        return [
            'formulation' => $formulation,
            'raw_mats' => $details
        ];
    }

    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('id')->from('production_formulation')->where('formulation_code', $code)->get()->num_rows() === 0;
    }

    public function is_active($id)
    {
        return $this->db->select('id')->from('production_formulation')->where(['status' => 1, 'id' => $id])->get()->num_rows() > 0;
    }

     public function create($data) {
        $this->db->trans_start();

        $this->db->insert('production_formulation', $data['formulation']);
        $id = $this->db->insert_id();
        foreach ($data['formula'] as &$row) {
            $row['fk_production_formulation_id'] = $id;
        }
        $this->db->insert_batch('production_formulation_detail', $data['formula']);
        $this->db->trans_complete();

        return $this->db->trans_status() === TRUE;
    }

    public function update($id, $data) {

        $existing = ['ids' => [], 'items' => []];
        $new = [];

        $this->db->trans_start();

        $this->db->update('production_formulation', $data['formulation'], ['id' => $id]);

        foreach ($data['formula'] as &$row) {
            if (isset($row['id'])) {
                $existing['items'][] = $row;
                $existing['ids'][] = $row['id'];
            } else {
                $row['fk_production_formulation_id'] = $id;
                $new[] = $row;
            }
        }

        if (!empty($existing['ids'])) {
            $this->db->where('fk_production_formulation_id',$id);
            $this->db->where_not_in('id', $existing['ids']);
            $this->db->delete('production_formulation_detail');
        }

        if (!empty($existing['items'])) {
            $this->db->update_batch('production_formulation_detail', $existing['items'], 'id');
        }

        if (!empty($new)) {
            $this->db->insert_batch('production_formulation_detail', $new);
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}

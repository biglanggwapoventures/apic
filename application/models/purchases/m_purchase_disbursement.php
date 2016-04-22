<?php

class M_Purchase_Disbursement extends CI_Model {

    //table names
    const TABLE_NAME_GENERAL = 'purchase_disbursement';
    const TABLE_NAME_DETAIL = 'purchase_disbursement_detail';
    const TABLE_NAME_CHECK_TRANSACTION = 'purchase_disbursement_check_transaction';

    public function get_next_row_id($disbursement_id, $mode, $type) {
        if(!strcmp($mode, "next")){
            $query = "SELECT id FROM pm_purchase_disbursement WHERE id > {$disbursement_id} AND disbursement_type='{$type}' ORDER BY id ASC LIMIT 1";
        }else{
            $query = "SELECT id FROM pm_purchase_disbursement WHERE id < {$disbursement_id} AND disbursement_type='{$type}' ORDER BY id DESC LIMIT 1";
        }
        return $this->db->query($query)->result_array();
    }

    public function get_min_id($type){
        return $this->db->query("SELECT MIN(id) as id FROM pm_purchase_disbursement WHERE disbursement_type='{$type}'")->result_array();
    }

    public function get_max_id($type){
        return $this->db->query("SELECT MAX(id) as id FROM pm_purchase_disbursement WHERE disbursement_type='{$type}'")->result_array();
    }

    public function add($general, $details, $payment) {
        $this->db->trans_begin();
        if ($general['status'] == M_Status::STATUS_APPROVED) {
            $general['is_locked'] = 1;
            $general['approved_by'] = $this->session->userdata('user_id');
        }
        $this->db->insert(self::TABLE_NAME_GENERAL, $general);
        $id = $this->db->insert_id();
        if (!empty($details)) {
            foreach ($details as &$detail) {
                $detail['fk_purchase_disbursement_id'] = $id;
            }
            $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);
        }
        $payment['fk_purchase_disbursement_id'] = $id;
        $this->db->insert('purchase_disbursement_payments', $payment);
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit(); //uncomment after debug
            return $id; //uncomment after debug
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function update($id, $general, $line, $payment) {
        $this->db->trans_begin();
        if ($general['status'] == M_Status::STATUS_APPROVED) {
            $general['is_locked'] = 1;
            $general['approved_by'] = $this->session->userdata('user_id');
        } else {
            $general['is_locked'] = 0;
        }

        //update general details
        $this->db->update('purchase_disbursement', $general, ['id' => $id]);
        $this->db->update('purchase_disbursement_payments', $payment, ['fk_purchase_disbursement_id' => $id]);

        $added_details = [];
        $ids = [];

        foreach ($line as $index => &$l) {
            if (array_key_exists('id', $l)) {
                $ids[] = $l['id'];
            } else {
                $l['fk_purchase_disbursement_id'] = $id;
                $added_details[] = $l;
                unset($line[$index]);
            }
        }

        if (!empty($ids)) {
            $this->db->where('fk_purchase_disbursement_id', $id);
            $this->db->where_not_in('id', $ids);
            $this->db->delete('purchase_disbursement_detail');
        }

        if (!empty($added_details)) {
            $this->db->insert_batch('purchase_disbursement_detail', $added_details);
        }

        if (!empty($line)) {
            $this->db->update_batch('purchase_disbursement_detail', $line, 'id');
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function master_list($type=['rr', 'advance'], $page = 1, $params = FALSE, $like = FALSE) {

        $limit = 100;
        $offset = ($page <= 1 ? 0 : ($page-1)*$limit);

        $this->db->select('dsb.is_locked, dsb.disbursement_type, dsb.payee, dsb.fk_purchase_order_id AS po_id, dsb.id, DATE_FORMAT(dsb.date, "%d-%b-%Y") AS `date`, dsb.remarks', FALSE);
        $this->db->select('CASE WHEN dsb.status = '.M_Status::STATUS_APPROVED.' THEN "Approved" ELSE "Pending" END AS `status`', FALSE);
        $this->db->from('purchase_disbursement AS dsb');
        if($type !== 'others'){
            $this->db->select('CASE WHEN dsb.disbursement_type = "rr" THEN "RR Payment" ELSE "Advance RR Payment" END AS `type`', FALSE);
            $this->db->select('CASE WHEN payment.check_number IS NOT NULL THEN payment.amount ELSE "" END AS check_amount', FALSE);
            $this->db->select('dsb.fk_maintainable_supplier_id AS supplier_id, payment.check_number, '
                    . 'CASE WHEN dsb.disbursement_type = "rr" '
                    . 'THEN SUM((ordr_dtl.unit_price * rcv_dtl.this_receive) - rcv_dtl.discount) '
                    . 'ELSE payment.amount END AS amount', FALSE);
            $this->db->where_in('disbursement_type', $type);
            $this->db->join('purchase_disbursement_detail AS dsb_dtl', 'dsb_dtl.fk_purchase_disbursement_id = dsb.id', 'left');
            $this->db->join('purchase_receiving AS rcv', 'rcv.id = dsb_dtl.fk_purchase_receiving_id', 'left');
            $this->db->join('purchase_receiving_detail AS rcv_dtl', 'rcv_dtl.fk_purchase_receiving_id = rcv.id', 'left');
            $this->db->join('purchase_order_detail AS ordr_dtl', 'ordr_dtl.id = rcv_dtl.fk_purchase_order_detail_id', 'left');
        }else{
            $this->db->select('SUM(liquidation.amount) AS total_amount, payment.check_number');
            $this->db->where('disbursement_type', 'others');  
            $this->db->join('purchase_disbursement_liquidations AS liquidation', 'liquidation.disbursement_id = dsb.id', 'left');
        }

        $this->db->join('purchase_disbursement_payments AS payment', 'payment.fk_purchase_disbursement_id = dsb.id', 'left');

        if($params){
            $this->db->where($params);
        }

        if($like){
            $this->db->like($like);
        }

        $this->db->order_by('dsb.id', 'DESC');
        $this->db->group_by('dsb.id');
        
        return $this->db->limit($limit, $offset)->get()->result_array();
    }

    public function get($disbursement_id, $type=['rr', 'advance']) {
        // get disbursement info
        $this->db->select('dsb.*, supplier.name AS supplier');
        $this->db->from('purchase_disbursement AS dsb');
        $this->db->join('maintainable_suppliers AS supplier', 'supplier.id = dsb.fk_maintainable_supplier_id', 'left');
        $this->db->where('dsb.id', $disbursement_id);
        $voucher = $this->db->get()->row_array();

        if($type==='others'){
            //get liquidation
            $this->db->select('lqd.*, account.description AS account');
            $this->db->from('purchase_disbursement_liquidations AS lqd');
            $this->db->join('maintainable_coa AS account', 'account.id = lqd.account_id');
            $this->db->where('lqd.disbursement_id', $disbursement_id);
            $voucher['liquidation'] = $this->db->get()->result_array();
        }else{
            // get disbursement line
            $this->db->select('dsb_dtl.*, rcv.fk_purchase_order_id, rcv.pr_number, rcv.date AS receiving_date, '
                    . 'SUM((rcv_dtl.this_receive*order_dtl.unit_price) - rcv_dtl.discount) AS amount, rcv.pr_number', FALSE);
            $this->db->from('purchase_disbursement_detail AS dsb_dtl');
            $this->db->join('purchase_receiving AS rcv', 'rcv.id = dsb_dtl.fk_purchase_receiving_id');
            $this->db->join('purchase_receiving_detail AS rcv_dtl', 'rcv_dtl.fk_purchase_receiving_id = rcv.id');
            $this->db->join('purchase_order_detail AS order_dtl', 'order_dtl.id = rcv_dtl.fk_purchase_order_detail_id');
            $this->db->where('dsb_dtl.fk_purchase_disbursement_id', $disbursement_id);
            $this->db->group_by('dsb_dtl.id');
            $voucher['line'] = $this->db->get()->result_array();
        }
        
        // get disbursement details
        $this->db->select('pmt.*, bank.account_number, bank.bank_name AS bank');
        $this->db->from('purchase_disbursement_payments AS pmt');
        $this->db->join('accounting_bank_account AS bank', 'bank.id = pmt.fk_accounting_bank_account_id', 'left');
        $this->db->where('pmt.fk_purchase_disbursement_id', $disbursement_id);
        $payment = array_merge($this->db->get()->row_array(), $voucher);
        
        return array_merge($voucher, $payment);
    }

    public function is_valid($id) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if (is_array($id)) {
            $this->db->where_in('id', $id);
            $count = $this->db->get()->num_rows();
            return $count === count($id);
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function is_locked($id) {
        return $this->db->get_where(self::TABLE_NAME_GENERAL, ['id' => $id, 'is_locked' => 1])->row_array() > 0;
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete(self::TABLE_NAME_GENERAL);
    }

    public function change_lock_state($id, $request_state) {
        $this->db->where('id', $id);
        return $this->db->update(self::TABLE_NAME_GENERAL, array(
                    'is_locked' => ($request_state ? 1 : 0)));
    }

    /*
     * @return bool
     * FALSE if purchase disbursement is not approved
     * TRUE, otherwise
     * 
     * @param id
     * ID of the purchase order
     */

    public function is_approved($id) {
        return $this->db->select('id')->from('purchase_disbursement')->where(['id' => $id, 'status' => M_Status::STATUS_APPROVED])->get()->row_array() > 0;
    }

    /*
     * @return bool
     * TRUE if check voucher is successfully created
     * FALSE, otherwise
     * 
     * @param data
     * validated data
     * 
     * @param creation_date
     * if not supplied, will use current system date
     */

    public function create_check_voucher($data, $creation_date = FALSE) {
        $this->db->trans_begin();
        $data['general']['date'] = $creation_date ? $creation_date : date('Y-m-d');
        $data['general']['disbursement_type'] = 'others';

        $this->db->insert('purchase_disbursement', $data['general']);
        
        $id = $this->db->insert_id();
        
        array_walk($data['liquidation'], function(&$var) USE($id){
            $var['disbursement_id'] = $id;
        });
        
        $this->db->insert_batch('purchase_disbursement_liquidations', $data['liquidation']);
        
        $data['payment']['fk_purchase_disbursement_id'] = $id;
        
        $this->db->insert('purchase_disbursement_payments', $data['payment']);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return $id;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }
    
    
    /*
     * @return bool
     * TRUE if check voucher is successfully updated
     * FALSE, otherwise
     * 
     * @param data
     * validated data
     */

    public function update_check_voucher($id, $data) {
        $this->db->trans_begin();

        $this->db->update('purchase_disbursement', $data['general'], ['id' => $id]);
        
        $liquidations = [];
        
        array_map(function($var) USE(&$liquidations, $id){
            if(array_key_exists('id', $var) === FALSE){
                $var['disbursement_id'] = $id;
                $liquidations['new'][] = $var;
            }else{
                $liquidations['old'][] = $var;
                $liquidations['old_ids'][] = $var['id'];
            }
        }, $data['liquidation']);
        
        if(array_key_exists('old', $liquidations)){
            $this->db->update_batch('purchase_disbursement_liquidations', $liquidations['old'], 'id');
            $this->db->where('disbursement_id', $id)->where_not_in('id', $liquidations['old_ids'])->delete('purchase_disbursement_liquidations');
        }else{
            $this->db->delete('purchase_disbursement_liquidations', ['disbursement_id' => $id]);
        }
        
        if(array_key_exists('new', $liquidations)){
            $this->db->insert_batch('purchase_disbursement_liquidations', $liquidations['new']);
        }
        
        $this->db->update('purchase_disbursement_payments', $data['payment'], ['fk_purchase_disbursement_id' => $id]);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function get_check_details($id)
    {
        return $this->db->get_where('purchase_disbursement_payments', ['fk_purchase_disbursement_id' => $id])->row_array();
    }

    public function payee($id)
    {
        $this->db->select('disbursement_type AS type, payee, fk_maintainable_supplier_id AS supplier_id');
        $result = $this->db->from(self::TABLE_NAME_GENERAL)->where('id', $id)->get()->row_array();
        if($result && $result['payee']){
            return $result['payee'];
        }else{
            $supplier = $this->db->select('name')->get_where('maintainable_suppliers', ['id' => $result['supplier_id']])->row_array();
            return $supplier['name'];
        }
        return NULL;
    }

}

<?php

class M_Purchase_Receiving extends CI_Model {

    const TABLE_NAME_GENERAL = 'purchase_receiving';
    const TABLE_NAME_DETAIL = 'purchase_receiving_detail';

    public function get_next_row_id($receiving_id, $mode) {
        if(!strcmp($mode, "next")){
            $query = "SELECT id FROM pm_purchase_receiving WHERE id > {$receiving_id} ORDER BY id ASC LIMIT 1";
        }else{
            $query = "SELECT id FROM pm_purchase_receiving WHERE id < {$receiving_id} ORDER BY id DESC LIMIT 1";
        }
        return $this->db->query($query)->result_array();
    }

    public function add($general, $details) {
        $this->db->trans_begin();
        if ($general['status'] == M_Status::STATUS_RECEIVED) {
            $general['is_locked'] = 1;
            $general['approved_by'] = $this->session->userdata('user_id');
        }

        $this->db->insert(self::TABLE_NAME_GENERAL, $general);
        $id = $this->db->insert_id();

        $order_details_ids = array_map(function($var){
            return $var['fk_purchase_order_detail_id'];
        }, $details);        

        foreach ($details as &$detail) {
            $detail['fk_purchase_receiving_id'] = $id;
        }
        
        $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);

        $this->set_unit_price($id);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return $id;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function update($id, $general, $details) {
        $this->db->trans_begin();
        if ($general['status'] == M_Status::STATUS_RECEIVED) {
            $general['is_locked'] = 1;
            $general['approved_by'] = $this->session->userdata('user_id');
        } else {
            $general['is_locked'] = 0;
        }
        $this->db->where('id', $id);
        $this->db->update(self::TABLE_NAME_GENERAL, $general);

        //segregate new and existing details
        $active_details = array();
        $new_details = array();
        foreach ($details as &$detail) {
            if (array_key_exists('id', $detail)) {
                $active_details[] = $detail;
            } else {
                $temp = $detail;
                $temp['fk_purchase_receiving_id'] = $id;
                $new_details[] = $temp;
            }
        }

        //delete non existing details
        $this->db->where('fk_purchase_receiving_id', $id);
        $this->db->where_not_in('id', !empty($active_details) ? array_map(function($var) {
                            return $var['id'];
                        }, $active_details) : array(0));
        $this->db->delete(self::TABLE_NAME_DETAIL);

        //update existing
        if (!empty($active_details)) {
            $this->db->update_batch(self::TABLE_NAME_DETAIL, $active_details, 'id');
        }
        //insert new
        if (!empty($new_details)) {
            $this->db->insert_batch(self::TABLE_NAME_DETAIL, $new_details);
        }

        $this->set_unit_price($id);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function set_unit_price($rr_id)
    {
        $this->db->select('po.id, po.type');
        $this->db->from('purchase_receiving AS pr, purchase_order AS po');
        $this->db->where('pr.fk_purchase_order_id = po.id', FALSE, FALSE)->where('pr.id', $rr_id);
        $po = $this->db->get()->row_array();

        if($po['type'] == 'imt'){
            $this->db->select('id')->from('purchase_receiving_detail')->where('fk_purchase_receiving_id', $rr_id);
            $rrd = $this->db->get()->row_array();
            
            $this->db->select('unit_price')->from('running_inventory')->where('purchase_receiving_detail_id', $rrd['id']);
            $unit_price = $this->db->get()->row_array();

            $this->db->update('purchase_order_detail', ['unit_price' => $unit_price['unit_price']], ['fk_purchase_order_id' => $po['id']]);
        }
    }

    public function get($with_details = FALSE, $search_token = array(), $filter = array(), $limit = 1000000, $offset = 0) {
        //get general details
        $this->db->select('receiving.*,  supplier.name as supplier,  receiving.date as date,'
                . 'FORMAT(SUM((order_detail.unit_price*receiving_detail.this_receive) - receiving_detail.discount),2) as total_amount', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as receiving');
        $this->db->join('maintainable_suppliers as supplier', 'supplier.id = receiving.fk_maintainable_supplier_id');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as receiving_detail', 'receiving_detail.fk_purchase_receiving_id = receiving.id');
        $this->db->join('purchase_order_detail as order_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id');
        $this->db->group_by('receiving.id')->order_by('id', 'DESC');
        if ($search_token) {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $data = $this->db->limit($limit, $offset)->get()->result_array();
        if (!$with_details) {
            return $data;
        }

        //gather all receiving ids
        $receiving_ids = $data ? array_map(function($var) {
                    return $var['id'];
                }, $data) : 0;

        //get details from gatehered receiving ids
        $this->db->select('receiving_detail.*, order_detail.quantity, order_detail.pieces, FORMAT(order_detail.unit_price, 2) as unit_price, order_detail.unit_price AS unit_price_unformatted,'
                . 'FORMAT(order_detail.unit_price * receiving_detail.this_receive, 2) as amount,'
                . 'product.description, product.code, unit.description as unit_description', FALSE);
        $this->db->from('purchase_receiving_detail as receiving_detail');
        $this->db->join('purchase_order_detail as order_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id');
        $this->db->join('purchase_receiving as receiving', 'receiving.id = receiving_detail.fk_purchase_receiving_id');
        $this->db->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id');
        $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id', 'left');
        $this->db->group_by('receiving_detail.id');
        $this->db->where_in('receiving_detail.fk_purchase_receiving_id', $receiving_ids);
        $details = $this->db->get()->result_array();

        //get received quantities per order detail
        $this->db->select('SUM(receiving_detail.this_receive) as delivered_quantity, SUM(receiving_detail.pieces_received) AS pieces_received, receiving_detail.fk_purchase_order_detail_id as order_detail_id');
        $this->db->from(self::TABLE_NAME_DETAIL . ' as receiving_detail');
        $this->db->join('purchase_order_detail as order_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id');
        $this->db->join(self::TABLE_NAME_GENERAL . ' as receiving', 'receiving.id = receiving_detail.fk_purchase_receiving_id');
        $this->db->group_by('receiving_detail.fk_purchase_order_detail_id');
        $this->db->where(array(
            'receiving.status' => M_Status::STATUS_RECEIVED
        ));
        $raw_unreceived = $this->db->get()->result_array();

        $unreceived = array_column($raw_unreceived, NULL, 'order_detail_id');
        // foreach ($raw_unreceived as $item) {
        //     $unreceived[$item['order_detail_id']] = $item['delivered_quantity'];
        // }

        foreach ($data as &$da) {
            foreach ($details as $key => &$value) {
                if ($da['id'] === $value['fk_purchase_receiving_id']) {
                    $value['delivered_quantity'] = isset($unreceived[$value['fk_purchase_order_detail_id']]) ? $unreceived[$value['fk_purchase_order_detail_id']]['delivered_quantity'] : 0;
                    $value['delivered_pieces'] = isset($unreceived[$value['fk_purchase_order_detail_id']]) ? $unreceived[$value['fk_purchase_order_detail_id']]['pieces_received'] : 0;
                    $da['details'][] = $value;
                    unset($details[$key]);
                }
            }
        }
        return $data;
    }

    public function is_valid($id, $po_id = FALSE, $supplier_id = FALSE, $received_only = FALSE) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if ($po_id) {
            $this->db->where('fk_purchase_order_id', $po_id);
        }
        if ($supplier_id) {
            $this->db->where('fk_maintainable_supplier_id', $supplier_id);
        }
        if ($received_only) {
            $this->db->where('status', M_Status::STATUS_RECEIVED);
        }
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
        $this->db->select('is_locked');
        $data = $this->db->get_where(self::TABLE_NAME_GENERAL, array('id' => $id))->row_array();
        return $data ? (int) $data['is_locked'] === 1 : FALSE;
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

    public function get_undisbursed($supplier_id = FALSE) {
        $this->db->select('receiving.id as id, receiving.fk_purchase_order_id,  STR_TO_DATE(receiving.date, "%Y-%m-%d") AS `date`, receiving.pr_number,'
                . 'FORMAT(SUM((receiving_detail.this_receive * order_detail.unit_price) - receiving_detail.discount), 2) as total_amount', FALSE);
        $this->db->from('purchase_receiving as receiving');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as receiving_detail', 'receiving.id = receiving_detail.fk_purchase_receiving_id');
        $this->db->join('purchase_order_detail as order_detail', 'receiving_detail.fk_purchase_order_detail_id = order_detail.id');
        $this->db->join('purchase_order AS po', 'po.id = order_detail.fk_purchase_order_id');
        $this->db->join('purchase_disbursement_detail as dsb_detail', 'dsb_detail.fk_purchase_receiving_id = receiving.id', 'left');
        $this->db->join('purchase_disbursement as dsb', 'dsb.id = dsb_detail.fk_purchase_disbursement_id', 'left');
        $this->db->where('receiving.fk_maintainable_supplier_id', $supplier_id);
        $this->db->where(['receiving.status' => M_Status::STATUS_RECEIVED, 'po.type !=' => 'imt']);
        $this->db->where('CASE WHEN dsb.status IS NOT NULL THEN dsb.status NOT IN (' . M_Status::STATUS_APPROVED . ',' . M_Status::STATUS_DEFAULT . ',' . M_Status::STATUS_ACTIVE
                . ') ELSE 1=1 END', FALSE, FALSE);
        $this->db->group_by('receiving.id');
        $this->db->order_by('receiving.id', 'DESC');
        return $this->db->get()->result_array();
    }
    ////////////////////////////////////Disbursement/////////////////////////////////////////
    function createPurchaseDisbursement($paymentType =false, $COACode = false, $COADisbursementAmount = false, $purchaseSupplierID = false, $purchaseOrderID = false, $paymentMethod = false, $date = false, $otherDetails = false, $remarks = false, $status = false, $isLocked = false, $isPrinted = false, $createdBy = false, $approvedBy = false ){
        
        $this->db->start_cache();
        $this->db->flush_cache();
        $data = array(
            "payment_type" =>  $paymentType,
            "coa_code" => $COACode,
            "coa_disbursed_amount" => $COADisbursementAmount,
            "fk_maintainable_supplier_id" => $purchaseSupplierID,
            "fk_purchase_order_id" => $purchaseOrderID,
            "payment_method" => $paymentMethod,
            "date" => $date,
            "other_details" => $otherDetails,
            "remarks" => $remarks,
            "status" => $status,
            "is_locked" => $isLocked,
            "is_printed" => $isPrinted,
            "created_by" => $createdBy,
            "approved_by" => $approvedBy
        );
        $this->db->insert("pm_purchase_disbursement", $data);
        $id = $this->db->insert_id();
        $this->db->flush_cache();
        $this->db->stop_cache();
        return $id;
    }
    function deletePurchaseDisbursement($purchaseDisbursementID){
        
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->where_in("pm_purchase_disbursement.id", $purchaseDisbursementID);
        $this->db->delete("pm_purchase_disbursement");
        $this->db->flush_cache();
        $this->db->where_in("pm_purchase_disbursement_check_transaction.fk_purchase_disbursement_id", $purchaseDisbursementID);
        $this->db->delete("pm_purchase_disbursement_check_transaction");
        $this->db->flush_cache();
        $this->db->where_in("pm_purchase_disbursement_detail.fk_purchase_disbursement_id", $purchaseDisbursementID);
        $this->db->delete("pm_purchase_disbursement_detail");
        $this->db->flush_cache();
        $this->db->stop_cache();
    }
    function createPurchaseDisbursementDetail($purchaseDisbursementID = false, $purchaseReceivingID = false){
        $this->db->start_cache();
        $this->db->flush_cache();
        $data = array(
            "fk_purchase_disbursement_id" =>  $purchaseDisbursementID,
            "fk_purchase_receiving_id" => $purchaseReceivingID
        );
        $this->db->insert("pm_purchase_disbursement_detail", $data);
        $id = $this->db->insert_id();
        $this->db->flush_cache();
        $this->db->stop_cache();
        return $id;
    }
    function retrievePurchaseDisbursementDetail($purchaseDisbursementID = false, $purchaseReceivingID = false){
        
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->select("*");
        $condition = array();
        ($purchaseDisbursementID) ? $condition["fk_purchase_disbursement_id"] = $purchaseDisbursementID : null;
        ($purchaseReceivingID && !is_array($purchaseReceivingID)) ? $condition["fk_purchase_receiving_id"] = $purchaseReceivingID : null;
        (is_array($purchaseReceivingID)) ? $this->db->where_in("fk_purchase_receiving_id", $purchaseReceivingID) : null;
        (is_array($purchaseDisbursementID)) ? $this->db->where_in("fk_purchase_disbursement_id", $purchaseDisbursementID) : null;
        (count($condition) > 0) ? $this->db->where($condition) : "";
        $result = $this->db->get("pm_purchase_disbursement_detail");
        if($result->num_rows() > 0){
            $this->db->flush_cache();
            $this->db->stop_cache();
            return $result->result_array();
        }else{
            $this->db->flush_cache();
            $this->db->stop_cache();
            return false;
        }
    }
    function createPurchaseDisbursementCheckTransaction($purchaseDisbursementID = false, $accountingBankAccountID = false, $checkNumber = false, $checkDate = false, $depositDate = false, $amount = false){
        $this->db->start_cache();
        $this->db->flush_cache();
        $data = array(
            "fk_purchase_disbursement_id" =>  $purchaseDisbursementID,
            "fk_accounting_bank_account_id" => $accountingBankAccountID,
            "check_number" => $checkNumber,
            "check_date" => $checkDate,
            "deposit_date" => $depositDate,
            "amount" => str_replace(',', '', $amount)
        );
        $this->db->insert("pm_purchase_disbursement_check_transaction", $data);
        $id = $this->db->insert_id();
        $this->db->flush_cache();
        $this->db->stop_cache();
        return $id;
    }
    function retrievePurchaseDisbursement($ID, $status = false){
        if(!$ID){
            return array();
        }
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->select("*");
        $this->db->select("pm_purchase_disbursement.id, pm_purchase_disbursement.status");
        $this->db->join("pm_maintainable_suppliers","pm_maintainable_suppliers.id = pm_purchase_disbursement.fk_maintainable_supplier_id","left");
        $this->db->join("pm_purchase_disbursement_detail","pm_purchase_disbursement_detail.fk_purchase_disbursement_id = pm_purchase_disbursement.id","left");
        $this->db->where_in("pm_purchase_disbursement.id", $ID);
        
        if($status) { $this->db->where("pm_purchase_disbursement.status", $status); echo $status;  }
        $result = $this->db->get("pm_purchase_disbursement");
        if($result->num_rows() > 0){
            $this->db->flush_cache();
            $this->db->stop_cache();
            return $result->result_array();
        }else{
            $this->db->flush_cache();
            $this->db->stop_cache();
            return array();
        }
        $this->db->flush_cache();
        $this->db->stop_cache();
    }
    function retrievePurchaseReceivingCheck($receivingIDs){
        if(!$receivingIDs){
            return array();
        }
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->select("*");
        $this->db->select("pm_purchase_disbursement_check_transaction.id");
        $this->db->join("pm_accounting_bank_account", "pm_accounting_bank_account.id = pm_purchase_disbursement_check_transaction.fk_accounting_bank_account_id","left");
        $this->db->join("pm_purchase_disbursement_detail","pm_purchase_disbursement_detail.fk_purchase_disbursement_id = pm_purchase_disbursement_check_transaction.fk_purchase_disbursement_id","left");
        $this->db->where_in("pm_purchase_disbursement_check_transaction.fk_purchase_disbursement_id", $receivingIDs);
        $result = $this->db->get("pm_purchase_disbursement_check_transaction");
        if($result->num_rows() > 0){
            $this->db->flush_cache();
            $this->db->stop_cache();
            return $result->result_array();
        }else{
            $this->db->flush_cache();
            $this->db->stop_cache();
            return array();
        }
        $this->db->flush_cache();
        $this->db->stop_cache();
    }
    
    /*
     * @return bool
     * FALSE if purchase order is not approved
     * TRUE, otherwise
     * 
     * @param id
     * ID of the purchase order
     */
    public function is_approved($id){
        $data = $this->db->select('status')->from('purchase_receiving')->where('id', $id)->get()->row_array();
        return $data['status'] == M_Status::STATUS_RECEIVED;
    }


    public function all($page = 1, $params = FALSE)
    {
        $limit = 100;
        $offset = ($page <= 1 ? 0 : ($page-1)*$limit);

        $this->db->select('rr.id, rr.pr_number AS dr_si, rr.fk_purchase_order_id AS po_id, rr.fk_maintainable_supplier_id AS supplier_id, DATE_FORMAT(STR_TO_DATE(`date`, "%Y-%m-%d"), "%d-%b-%Y") AS `date`, rr.is_locked', FALSE);
        $this->db->select('CASE WHEN status = '.M_Status::STATUS_RECEIVED.' THEN "Approved" ELSE "Pending" END AS `status`', FALSE);
        $this->db->select('SUM((rrd.this_receive * pod.unit_price) - rrd.discount) AS amount', FALSE);
        $this->db->from('purchase_receiving AS rr')->join('purchase_receiving_detail AS rrd', 'rrd.fk_purchase_receiving_id = rr.id');
        $this->db->join('purchase_order_detail AS pod', 'pod.id = rrd.fk_purchase_order_detail_id');

        if($params !== FALSE)
        {
            $this->db->where($params);
        }

        $this->db->limit($limit, $offset)->group_by('rr.id')->order_by('rr.id', 'DESC');
        return $this->db->get()->result_array();

    }
        

}

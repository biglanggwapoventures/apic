<?php

class M_Purchase_Order extends CI_Model {

    const TABLE_NAME_GENERAL = 'purchase_order';
    const TABLE_NAME_DETAIL = 'purchase_order_detail';


    public function all($page = 1, $params = FALSE)
    {
        $limit = 100;
        $offset = ($page <= 1 ? 0 : ($page-1)*$limit);

        $this->db->select('po.id, DATE_FORMAT(po.date, "%d-%b-%Y") AS `date`, po.fk_maintainable_supplier_id AS supplier_id', FALSE);
        $this->db->select('CASE WHEN po.status = '.M_Status::STATUS_APPROVED.' THEN "Approved" ELSE "Pending" END AS `status`', FALSE);
        $this->db->select('SUM(CASE WHEN po.`type` = "lcl" THEN pod.unit_price * pod.quantity ELSE pod.amount END) AS amount', FALSE);
        $this->db->select('CASE WHEN po.`type` = "lcl" THEN "Local" ELSE "Imported" END AS `type`', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL.' AS po');
        $this->db->join(self::TABLE_NAME_DETAIL.' AS pod', 'pod.fk_purchase_order_id = po.id');

        if($params !== FALSE)
        {
            $this->db->where($params);
        }

        $this->db->limit($limit, $offset)->group_by('po.id')->order_by('po.id', 'DESC');
        $data =  $this->db->get()->result_array();

        if(!$data){
            return [];
        }

        $this->db->select('po_id, SUM(amount) AS amount', FALSE);
        $this->db->from('purchase_order_other_fees')->where_in('po_id', array_column($data, 'id'));
        $this->db->group_by('po_id');
        $fees = array_column($this->db->get()->result_array(), 'amount', 'po_id');

        if(!$fees){
            return $data;
        }

        foreach($data AS &$row){
            if(isset($fees[$row['id']])){
                $row['amount'] += $fees[$row['id']];
            }
        }

        return $data;
    }

    public function add($general, $details, $others = FALSE) {
        $this->load->helper('pmarray');
        $this->db->trans_begin();
        if ($general['status'] == M_Status::STATUS_APPROVED) {
            $general['is_locked'] = 1;
            $general['approved_by'] = $this->session->userdata('user_id');
        }

        $this->db->insert(self::TABLE_NAME_GENERAL, $general);
        $id = $this->db->insert_id();

        array_walk($details, 'insert_prop', ['name' => 'fk_purchase_order_id', 'value' => $id]);
        $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);

        if($others!==FALSE)
        {
            if(isset($others['fees']))
            {
                array_walk($others['fees'], 'insert_prop', ['name' => 'po_id', 'value' => $id]);
                $this->db->insert_batch('purchase_order_other_fees', $others['fees']);
            }
            
            if(isset($others['checks']))
            {
                array_walk($others['checks'], 'insert_prop', ['name' => 'po_id', 'value' => $id]);
                $this->db->insert_batch('purchase_order_issued_checks', $others['checks']);
            }
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return $id;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function update($id, $general, $details, $others = FALSE) {
        $this->load->helper('pmarray');
        $this->db->trans_begin();
        if ($general['status'] ==  M_Status::STATUS_APPROVED) {
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
                $temp['fk_purchase_order_id'] = $id;
                $new_details[] = $temp;
            }
        }

        //delete non existing details
        $this->db->where('fk_purchase_order_id', $id);
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


        $this->db->delete('purchase_order_other_fees', ['po_id' => $id]);
        $this->db->delete('purchase_order_issued_checks', ['po_id' => $id]);
        if($others!==FALSE)
        {

            if(isset($others['fees']))
            {
                array_walk($others['fees'], 'insert_prop', ['name' => 'po_id', 'value' => $id]);
                $this->db->insert_batch('purchase_order_other_fees', $others['fees']);
            }
            
            if(isset($others['checks']))
            {
                array_walk($others['checks'], 'insert_prop', ['name' => 'po_id', 'value' => $id]);
                $this->db->insert_batch('purchase_order_issued_checks', $others['checks']);
            }
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function get($with_details = FALSE, $search_token = array(), $filter = array(), $limit = 9999999, $offset = 0) {
        //get general details
        $this->db->select('p_order.id, p_order.force_close, p_order.type, p_order.fk_maintainable_supplier_id, DATE_FORMAT(p_order.date, "%M %d, %Y") as formatted_date,'
                . 'p_order.date as date, p_order.remarks, p_order.status, p_order.is_locked,  p_order.is_printed,'
                . 'supplier.name as supplier, supplier.address AS supplier_address, SUM(order_detail.unit_price*order_detail.quantity) as total_amount', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as p_order');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as order_detail', 'order_detail.fk_purchase_order_id = p_order.id');
        $this->db->join('maintainable_suppliers as supplier', 'supplier.id = p_order.fk_maintainable_supplier_id');
        $this->db->group_by('p_order.id');
        $this->db->order_by('p_order.id', 'DESC');
        if ($search_token) {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $data = $this->db->limit($limit, $offset)->get()->result_array();
        //gather all order ids
        $order_ids = $data ? array_map(function($var) {
                    return $var['id'];
                }, $data) : 0;//end
        //get paid total amount paid
        $this->db->select('FORMAT(SUM(receiving_detail.this_receive * order_detail.unit_price),2) as amount_paid, order_detail.fk_purchase_order_id as id', FALSE);
        $this->db->from('purchase_disbursement as dsb');
        $this->db->join('purchase_disbursement_detail as dsb_detail', 'dsb_detail.fk_purchase_disbursement_id = dsb.id');
        $this->db->join('purchase_receiving_detail as receiving_detail', 'receiving_detail.fk_purchase_receiving_id = dsb_detail.fk_purchase_receiving_id');
        $this->db->join('purchase_receiving AS rcv', 'rcv.id = receiving_detail.fk_purchase_receiving_id');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as order_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id');
        $this->db->where(array('dsb.status' => M_Status::STATUS_APPROVED));
        $this->db->where_in('rcv.fk_purchase_order_id', $order_ids);
        $this->db->group_by('rcv.fk_purchase_order_id');
        $result = $this->db->get()->result_array();
        $amounts_paid = array();
        if ($result) {
            $result = array_column($result, 'amount_paid', 'id');
        }//end
        //distribute amount paid to general detail
        foreach ($data as &$order) {
            $order['amount_paid'] = isset($amounts_paid[$order['id']]) ? $amounts_paid[$order['id']] : '0.00';
        }//end
        //no details requested, return general data
        if (!$with_details) {
            return $data;
        }//end
        //get details from gathered order ids
        $this->db->select('order_detail.id, order_detail.xrate, order_detail.fk_purchase_order_id, order_detail.fk_inventory_product_id, unit.description as unit_description, '
                . 'FORMAT(order_detail.unit_price,2) as unit_price, order_detail.unit_price AS unit_price_unformatted, order_detail.quantity, order_detail.pieces,'
                . 'CASE WHEN p_order.type = "lcl" THEN FORMAT(order_detail.unit_price*order_detail.quantity, 2) ELSE order_detail.amount END AS amount,'
                . 'product.description, product.code,'
                . 'SUM(CASE WHEN receiving.status=' . M_Status::STATUS_RECEIVED . ' THEN receiving_detail.this_receive ELSE 0 END) as quantity_received,
                SUM(CASE WHEN receiving.status=' . M_Status::STATUS_RECEIVED . ' THEN receiving_detail.this_receive ELSE 0 END) as quantity_received', FALSE);
        $this->db->from(self::TABLE_NAME_DETAIL . ' as order_detail');
        $this->db->join('purchase_receiving_detail as receiving_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id', 'left');
        $this->db->join('purchase_receiving as receiving', 'receiving.id = receiving_detail.fk_purchase_receiving_id', 'left');
        $this->db->join(self::TABLE_NAME_GENERAL . ' as p_order', 'p_order.id = order_detail.fk_purchase_order_id');
        $this->db->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id');
        $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id', 'left');
        $this->db->where_in('order_detail.fk_purchase_order_id', $order_ids);
        $this->db->group_by('order_detail.id');
        $details = $this->db->get()->result_array();

        foreach ($data AS &$d) {
            foreach($details AS &$de){
                if($d['id'] === $de['fk_purchase_order_id'])
                {
                    $d['details'][] = $de;
                    unset($de);
                }
            }
        }

        return $data;
    }

    public function is_valid($id, $supplier_id = FALSE, $check_status = FALSE) {
        $this->db->select('p_order.id')->from(self::TABLE_NAME_GENERAL . ' as p_order');
        if ($supplier_id) {
            $this->db->join('maintainable_suppliers', 'maintainable_suppliers.id = p_order.fk_maintainable_supplier_id');
            $this->db->where('p_order.fk_maintainable_supplier_id', $supplier_id);
        }
        if ($check_status) {
            $this->db->where('p_order.status', M_Status::STATUS_APPROVED);
        }
        if (is_array($id)) {
            $this->db->where_in('p_order.id', $id);
            $count = $this->db->get()->num_rows();
            return $count === count($id);
        } else {
            $this->db->where('p_order.id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function are_valid_details($id, $detail_ids) {
        $this->db->select('DISTINCT id', FALSE);
        $this->db->where_in('id', $detail_ids);
        $this->db->where('fk_purchase_order_id', $id);
        $this->db->from(self::TABLE_NAME_DETAIL);
        return ((int) $this->db->get()->num_rows() === (int) count($detail_ids));
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
        return $this->db->update(self::TABLE_NAME_GENERAL, array('is_locked' => ($request_state ? 1 : 0)));
    }

    public function get_unreceived($supplier_id) {
        $this->db->select('order_detail.fk_purchase_order_id as id, order_detail.quantity, '
                . ' SUM(CASE WHEN receiving.status=' . M_Status::STATUS_RECEIVED . ' THEN receiving_detail.this_receive ELSE 0 END) as delivered_qty', FALSE);
        $this->db->from(self::TABLE_NAME_DETAIL . ' as order_detail');
        $this->db->join(self::TABLE_NAME_GENERAL . ' as p_order', 'p_order.id = order_detail.fk_purchase_order_id');
        $this->db->join('purchase_receiving_detail as receiving_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id', 'left');
        $this->db->join('purchase_receiving AS receiving', 'receiving.id = receiving_detail.fk_purchase_receiving_id', 'left');
        $this->db->where(array(
            'p_order.status' => M_Status::STATUS_APPROVED,
            'p_order.fk_maintainable_supplier_id' => $supplier_id,
            'p_order.force_close !=' => 1 
        ));
        $this->db->group_by('order_detail.id');
        $result = $this->db->get()->result_array();
        $uncompleted_orders = array();
        foreach ($result as $key => $value) {
            if ((double) $value['quantity'] <= (double) $value['delivered_qty']) {
                unset($result[$key]);
            } else {
                $uncompleted_orders[] = $value['id'];
            }
        }
        $uncompleted_orders = array_unique($uncompleted_orders);
        if (array_multisort($uncompleted_orders, SORT_DESC)) {
            return $uncompleted_orders;
        }
        return FALSE;
    }

    public function get_undisbursed($supplier_id = FALSE) {
        $this->db->select('DISTINCT receiving.fk_purchase_order_id', FALSE);
        $this->db->from('purchase_receiving as receiving');
        $this->db->join('purchase_disbursement_detail as dsb_detail', 'dsb_detail.fk_purchase_receiving_id = receiving.id', 'left');
        $this->db->join('purchase_disbursement as dsb', 'dsb.id = dsb_detail.fk_purchase_disbursement_id', 'left');
        if ($supplier_id && is_numeric($supplier_id)) {
            $this->db->where('receiving.fk_maintainable_supplier_id', $supplier_id);
        }
        $this->db->where('receiving.status', M_Status::STATUS_RECEIVED);
        $this->db->where('CASE WHEN dsb.status IS NOT NULL THEN dsb.status '
                . 'NOT IN (' . M_Status::STATUS_APPROVED . ',' . M_Status::STATUS_DEFAULT . ',' . M_Status::STATUS_ACTIVE . ') ELSE 1=1 END', FALSE, FALSE);
        $this->db->order_by('receiving.fk_purchase_order_id', 'DESC');
        $undisbursed = $this->db->get()->result_array();
        return $undisbursed ? array_map(function($var) {
                    return $var['fk_purchase_order_id'];
                }, $undisbursed) : FALSE;
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
        $data = $this->db->select('status')->from('purchase_order')->where('id', $id)->get()->row_array();
        return $data['status'] == M_Status::STATUS_APPROVED;
    }
    
     /*
     * @return array
     * an array of all po not yet received 
     * 
     * @param id
     * ID of the supplier
     */
    public function for_advanced_rr($supplier_id){
        $this->db->select('ord.id, SUM(ord_dtl.unit_price * ord_dtl.quantity) AS total_amount', FALSE);
        $this->db->from('purchase_order AS ord');
        $this->db->join('purchase_receiving AS rcv', 'rcv.fk_purchase_order_id = ord.id', 'left');
        $this->db->join('purchase_order_detail AS ord_dtl', 'ord_dtl.fk_purchase_order_id = ord.id');
        $this->db->where(['ord.fk_maintainable_supplier_id' => $supplier_id, 'rcv.id'=> NULL]);
        $this->db->group_by('ord.id');
        $data = $this->db->get()->result_array();
        return $data;
    }

    public function get_other_fees($po_id)
    {
        return $this->db->get_where('purchase_order_other_fees', ['po_id' => $po_id])->result_array();
    }

    public function get_issued_checks($po_id, $check_id = FALSE)
    {
        return $this->db->get_where('purchase_order_issued_checks', ['po_id' => $po_id])->result_array();
    }

    public function get_check($check_id)
    {
        $check = $this->db->get_where('purchase_order_issued_checks', ['id' => $check_id])->row_array();
        if($check){
            $payee = $this->db->query("SELECT `name` FROM pm_maintainable_suppliers WHERE `id` = (SELECT fk_maintainable_supplier_id FROM pm_purchase_order WHERE id = {$check['po_id']});")->row_array();
            $check['payee'] = $payee['name'];
            return $check;
        }
        return FALSE;

    }

    public function get_other_fees_total($ids)
    {
        $this->db->select('SUM(amount) AS amount, po_id', FALSE);
        $this->db->from('purchase_order_other_fees');
        if(is_array($ids))
        {
            $this->db->where_in('po_id', $ids);
        }
        else
        {
            $this->db->where('po_id', $ids);
        }
        return array_column($this->db->get()->result_array(), 'amount', 'po_id');
    }

    public function get_issued_checks_from($id = FALSE)
    {
        $this->db->select('checks.*, bank.bank_name')->from('purchase_order_issued_checks AS checks');
        if($id !== FALSE)
        {
            $this->db->where('checks.id', $id);
        }
        $this->db->join('accounting_bank_account AS bank', 'bank.id = checks.bank_account');
        $data['checks'] = $this->db->get()->result_array();
        $supplier = $this->db->query("SELECT `name` FROM pm_maintainable_suppliers WHERE `id` = (SELECT `fk_maintainable_supplier_id` FROM pm_purchase_order WHERE id = {$data['checks'][0]['po_id']})")->row_array();
        $data['payee'] = $supplier['name'];
        return $data;
    }

    public function supplier($id)
    {
        $this->db->select('s.name')->from(self::TABLE_NAME_GENERAL.' AS o, maintainable_suppliers AS s');
        $this->db->where('o.fk_maintainable_supplier_id = s.id', FALSE, FALSE)->where('o.id', $id);
        $result = $this->db->get()->row_array();
        return $result ? $result['name'] : NULL;
    }


}

<?php

class M_Receipt extends CI_Model {

    const TABLE_NAME_GENERAL = 'sales_receipt';
    const TABLE_NAME_DETAIL = 'sales_receipt_detail';
    const TABLE_NAME_CHECK_TRANS = 'sales_receipt_check_transaction';

    public function add($receipt, $details, $check_transactions) {
        $this->db->trans_begin();
        $this->db->insert(self::TABLE_NAME_GENERAL, $receipt);
        $id = $this->db->insert_id();
        foreach ($details as &$detail) {
            $detail['fk_sales_receipt_id'] = $id;
        }
        foreach ($check_transactions as &$trans) {
            $trans['fk_sales_receipt_id'] = $id;
        }
        if (!empty($details)) {
            $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);
        }
        if (!empty($check_transactions)) {
            $this->db->insert_batch(self::TABLE_NAME_CHECK_TRANS, $check_transactions);
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function update($receipt_id, $receipt, $details, $check_transactions) {
        $existing_check_transactions = array();
        $new_check_transactions = array();
        $existing_check_transaction_ids = array();
        //segregate new and existing check transactions
        foreach ($check_transactions as &$trans) {
            if (array_key_exists('id', $trans)) {
                $existing_check_transaction_ids[] = $trans['id'];
                $existing_check_transactions[] = $trans;
            } else {
                $trans['fk_sales_receipt_id'] = $receipt_id;
                $new_check_transactions[] = $trans;
            }
        }
        $this->db->trans_begin();
        $this->db->where('id', $receipt_id);
        $this->db->update(self::TABLE_NAME_GENERAL, $receipt);
        //update details
        $this->db->update_batch(self::TABLE_NAME_DETAIL, $details, 'id');
        //delete non-existent check transactions
        $this->db->where('fk_sales_receipt_id', $receipt_id);
        $this->db->where_not_in('id', !empty($existing_check_transaction_ids) ? $existing_check_transaction_ids : array(0));
        $this->db->delete(self::TABLE_NAME_CHECK_TRANS);
        //update existing check transactions
        if (!empty($existing_check_transactions)) {
            $this->db->update_batch(self::TABLE_NAME_CHECK_TRANS, $existing_check_transactions, 'id');
        }
        //insert new check transactions
        if (!empty($new_check_transactions)) {
            $this->db->insert_batch(self::TABLE_NAME_CHECK_TRANS, $new_check_transactions);
        }
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    public function get($with_details = FALSE, $search_token = array(), $filter = array(), $limit = 999, $offset = 0) {
        //get general details
        $this->db->select('receipt.id, receipt.fk_sales_customer_id, receipt.or_number, receipt.remarks, '
                . 'receipt.tracking_number_type, receipt.tracking_number, '
                . 'DATE_FORMAT(receipt.date, "%M %d, %Y") as formatted_date,receipt.date, customer.company_name, receipt.status, '
                . 'SUM(receipt_detail.amount) as total_amount', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as receipt');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id');
        $this->db->join('sales_customer as customer', 'customer.id = receipt.fk_sales_customer_id');
        $this->db->group_by('receipt.id');
        if ($search_token) {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $this->db->order_by('receipt.id', 'DESC');
        $data = $this->db->limit($limit, $offset)->get()->result_array();
        if (!$with_details) {
            return $data;
        }

        //gather all receipt ids
        $receipt_ids = $data ? array_map(function($var) {
                    return $var['id'];
                }, $data) : 0;


        //get details from gatehered receipt ids
        $this->db->select('receipt_detail.id, receipt_detail.fk_sales_receipt_id, receipt_detail.fk_sales_delivery_id, receipt_detail.payment_method, receipt_detail.amount as this_payment,'
                . 'DATE_FORMAT(delivery.date, "%M %d, %Y") as transaction_date, delivery.ptn_number, SUM(delivery_detail.amount) as transaction_amount', FALSE);
        $this->db->from(self::TABLE_NAME_DETAIL . ' as receipt_detail');
        $this->db->join('sales_delivery as delivery', 'delivery.id = receipt_detail.fk_sales_delivery_id');
        $this->db->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id');
        $this->db->where_in('receipt_detail.fk_sales_receipt_id', $receipt_ids);
        $this->db->group_by('receipt_detail.id');
        $details = $this->db->get()->result_array();

        //gather all delivery ids involved
        $dr_ids = $details ? array_map(function($var) {
                    return $var['fk_sales_delivery_id'];
                }, $details) : 0;

        //get paid amount per delivery ids
        $this->db->select('receipt_detail.fk_sales_delivery_id, SUM(amount) as amount_paid');
        $this->db->from('sales_receipt_detail as receipt_detail');
        $this->db->join('sales_receipt', 'sales_receipt.id = receipt_detail.fk_sales_receipt_id');
        $this->db->where('sales_receipt.status', M_Status::STATUS_FINALIZED);
        $this->db->where_in('receipt_detail.fk_sales_delivery_id', $dr_ids ? array_unique($dr_ids) : 0);
        $this->db->group_by('receipt_detail.fk_sales_delivery_id');
        $unformatted = $this->db->get()->result_array();
        $total_payments = array();
        foreach ($unformatted as $val) {
            $total_payments[$val['fk_sales_delivery_id']] = number_format($val['amount_paid'], 2);
        }

        //get check transactions
        $this->db->select('check_transaction.*');
        $this->db->from('sales_receipt_check_transaction as check_transaction');
        $this->db->where_in('fk_sales_receipt_id', $receipt_ids);
        $check_transactions = $this->db->get()->result_array();

        //assign details and check transactions to corresponding parent receipt
        foreach ($data as &$da) {
            $da['total_amount'] = number_format($da['total_amount'], 2);
            foreach ($details as $key => &$value) {
                if ($da['id'] === $value['fk_sales_receipt_id']) {
                    $value['amount_paid'] = isset($total_payments[$value['fk_sales_delivery_id']]) ? $total_payments[$value['fk_sales_delivery_id']] : 0;
                    $value['this_payment'] = number_format($value['this_payment'], 2);
                    $value['transaction_amount'] = number_format($value['transaction_amount'], 2);
                    $da['details'][] = $value;
                    unset($details[$key]);
                }
            }
            foreach ($check_transactions as $key => &$value) {
                if ($da['id'] === $value['fk_sales_receipt_id']) {
                    $value['amount'] = number_format($value['amount'], 2);
                    $da['check'][] = $value;
                    unset($check_transactions[$key]);
                }
            }
        }
        return $data;
    }

    public function is_valid($receipt_id) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if (is_array($receipt_id)) {
            $this->db->where_in('id', $receipt_id);
            $count = $this->db->get()->num_rows();
            return $count === count($receipt_id);
        } else {
            $this->db->where('id', $receipt_id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function get_status($receipt_id) {
        $this->db->select('status');
        $data = $this->db->get_where(self::TABLE_NAME_GENERAL, array('id' => $receipt_id))->row_array();
        return $data ? $data['status'] : FALSE;
    }

}

<?php

class M_Receipt extends CI_Model {

    const TABLE_NAME_GENERAL = 'sales_receipt';

    public function __construct() {
        parent::__construct();
    }

    public function master_list() {
        $this->db->select('receipt.id, customer.company_name as customer, DATE_FORMAT(receipt.date, "%M %d, %Y") as date, receipt.tracking_number_type, receipt.tracking_number, receipt.status', FALSE);
        $this->db->from('sales_receipt as receipt')->join('sales_receipt_detail as receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id');
        $this->db->join('sales_receipt_check_transaction as check_trans', 'check_trans.fk_sales_receipt_detail_id = receipt_detail.id', 'left');
        $this->db->join('sales_customer as customer', 'customer.id = receipt.fk_sales_customer_id');
        $this->db->group_by('receipt.id')->order_by('receipt.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create($data) {
        $details = $data['details'];
        $checks = array_key_exists('check_payments', $data) ? $data['check_payments'] : array();
        unset($data['details'], $data['check_payments']);
        $this->db->trans_start();

        $this->db->insert(self::TABLE_NAME_GENERAL, $data);
        $id = $this->db->insert_id();
        foreach ($details as &$item) {
            $item['fk_sales_receipt_id'] = $id;
        }


        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            return TRUE;
        }
        $this->db->trans_rollback();
        return FALSE;
    }

}

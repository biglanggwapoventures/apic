<?php

class M_Income_Statement extends CI_Model {

    private $start_date;
    private $end_date;

    function __construct() {
        parent::__construct();
        $this->start_date = '';
        $this->end_date = '';
    }

    public function set_date_filter($start_date, $end_date = '') {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /*
     * 1. Get income from receipts
     * 2. Get cost of goods and expenses from disbursements
     * 3. Merge them
     * 4. Sort date desc
     * 5. Loop through sorted data and get total income
     */

    public function generate($limit = 30, $offset = 0) {
        /* get income based from receipts */
        $this->db->select('DATE_FORMAT(receipt.date, "%M %d, %Y") as date, FORMAT(SUM(receipt_detail.amount),2) as income', FALSE)
                ->from('sales_receipt as receipt')
                ->join('sales_receipt_detail as receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id')
                ->where('receipt.status', M_Status::STATUS_FINALIZED);
        $this->start_date ? $this->db->where('receipt.date >=', $this->start_date) : '';
        $this->end_date ? $this->db->where('receipt.date <=', $this->end_date) : '';
        $this->db->group_by('receipt.id');
        $income = $this->db->get()->result_array();
        $statements[] = $income ? $income : array();

        /* get costs based from disbursements issued on purchase receivings */
        $this->db->select('DATE_FORMAT(dsb.date, "%M %d, %Y") as date, FORMAT(SUM(receiving_detail.this_receive*order_detail.unit_price),2) as cost', FALSE)
                ->from('purchase_disbursement as dsb')
                ->join('purchase_disbursement_detail as dsb_detail', 'dsb_detail.fk_purchase_disbursement_id = dsb.id')
                ->join('purchase_receiving as receiving', 'receiving.id = dsb_detail.fk_purchase_receiving_id')
                ->join('purchase_receiving_detail as receiving_detail', 'receiving_detail.fk_purchase_receiving_id = receiving.id')
                ->join('purchase_order_detail as order_detail', 'order_detail.id = receiving_detail.fk_purchase_order_detail_id')
                ->where('dsb.status', M_Status::STATUS_APPROVED)
                ->where('dsb.payment_type', 1);
        $this->start_date ? $this->db->where('dsb.date >=', $this->start_date) : '';
        $this->end_date ? $this->db->where('dsb.date <=', $this->end_date) : '';
        $this->db->group_by('dsb.id');
        $costs = $this->db->get()->result_array();
        $statements[] = $costs ? $costs : array();

        /* get expenses based from disbursements */
        $this->db->select('DATE_FORMAT(dsb.date, "%M %d, %Y") as date,FORMAT(coa_disbursed_amount, 2) as expense', FALSE)
                ->from('purchase_disbursement as dsb')
                ->where('dsb.status', M_Status::STATUS_APPROVED);
        $this->start_date ? $this->db->where('dsb.date >=', $this->start_date) : '';
        $this->end_date ? $this->db->where('dsb.date <=', $this->end_date) : '';
        $this->db->group_by('dsb.id');
        $expenses = $this->db->get()->result_array();
        $statements[] = $expenses ? $expenses : array();

        /* merge */
        $statements = array_merge($statements[0], $statements[1], $statements[2]);

        /* sort by date */

        function date_compare($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);
            return $t1 - $t2;
        }

        usort($statements, 'date_compare');

        $data['statements'] = $statements;

        $total_income = 0;

        /* calculate total income */
        foreach ($statements as $item) {
            $total_income -= array_key_exists('expense', $item) ? str_replace(",", "", $item['expense']) : 0;
            $total_income -= array_key_exists('cost', $item) ? str_replace(",", "", $item['cost']) : 0;
            $total_income += array_key_exists('income', $item) ? str_replace(",", "", $item['income']) : 0;
        }
        $data['total_income'] = number_format($total_income, 2);
        return $data;
    }

}

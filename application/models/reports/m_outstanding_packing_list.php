<?php

class M_Outstanding_Packing_List extends CI_Model {

    public $customer;

    public function __construct() {
        parent::__construct();
    }
    
    public function generate() {
        $this->load->model('sales/m_customer');
        $this->load->library('subquery');
        $unsettled = $this->m_customer->get_unsettled($this->customer, FALSE, FALSE, FALSE, 'DESC');
        $total_count = count($unsettled);
        if (!$unsettled) {
            return $unsettled;
        }
        $this->db->select('receipt.id, receipt_detail.fk_sales_delivery_id, receipt.tracking_number_type as tracking_type, receipt.tracking_number, '
                . 'CASE WHEN check_payment.amount IS NULL THEN receipt_detail.amount ELSE 0 END as cash_payment, '
                . 'DATE_FORMAT(receipt.date, "%m/%d/%Y") as date,'
                . 'IFNULL(check_payment.check_number, "") as check_number, '
                . 'IFNULL(DATE_FORMAT(check_payment.check_date, "%m/%d/%Y"), "") as check_date, '
                . 'IFNULL(DATE_FORMAT(check_payment.deposit_date, "%m/%d/%Y"), "") as deposit_date, '
                . 'IFNULL(check_payment.amount, 0) as check_payment', FALSE)
                ->from('sales_receipt as receipt')
                ->join('sales_receipt_detail as receipt_detail', 'receipt_detail.fk_sales_receipt_id = receipt.id')
                ->join('sales_receipt_check_transaction as check_payment', 'check_payment.fk_sales_receipt_detail_id = receipt_detail.id', 'left')
//                ->where('CASE WHEN check_payment.deposit_date IS NOT NULL THEN DATE(check_payment.deposit_date) <= CURDATE() ELSE 1=1 END', FALSE, FALSE)
                ->where(['receipt.fk_sales_customer_id' => $this->customer, 'receipt.status' => M_Status::STATUS_FINALIZED])
                ->where_in('receipt_detail.fk_sales_delivery_id', array_column($unsettled, 'fk_sales_delivery_id'))
                ->having('cash_payment >', 0)->or_having('check_payment >', 0);
        $temp_payments = $this->db->order_by('receipt.date', 'DESC')->get()->result_array();
        $payments = [];

        foreach($temp_payments as $p){
            $payments[$p['fk_sales_delivery_id']][] = $p;
        }

        foreach ($unsettled as $key=>&$item) {
            $item['balance'] = $item['total_amount'] - $item['total_paid'];
            $has_prev = isset($unsettled[$key-1]);

            // Is this the first entry?
            if($has_prev){
                // This is not the first entry

                // Get the previous entry
                $prev = &$unsettled[$key-1];

                // Get current entry's month
                $date = date('m', strtotime($item['date']));

                // Is the previous entrys month and the current entry's month the same?
                $in_month = $date === date('m', strtotime($prev['date']));
                if($in_month){
                    // Yes it is, add the current entry's balance into the monthly balance
                    $month_total+=$item['balance'];
                    // Is this the last entry?
                    if(!isset($unsettled[$key+1])){
                        // Put monthly balance property
                        $item['month_total'] = $month_total;
                    }
                }else{
                    // This is the next month, put the total monthly balance to the previous entry
                    $prev['month_total'] = $month_total;
                    // Reset the monthly balance
                    $month_total = $item['balance'];
                    if(!isset($unsettled[$key+1])){
                        $item['month_total'] = $month_total;
                    }
                }
            }else{
                // This is the first entry, set monthly total to current balance
                $month_total = $item['balance'];
                if(!isset($unsettled[$key+1])){
                    $item['month_total'] = $month_total;
                }
            }
            
            unset($item['total_paid']);
            $temp = [];
            if(doubleval($item['whtax_amount']) > 0){
                $temp[] = [
                    'date' => $item['whtax_date'],
                    'whtax_amount' => $item['whtax_amount'],
                    'cash_payment' => 0,
                    'check_payment' => 0,
                    'check_number' => '', 
                    'check_date' => '', 
                    'deposit_date' => '',
                    'tracking_type' => '',
                    'tracking_number' => ''
                ];
            }
            if(isset($payments[$item['fk_sales_delivery_id']])){
                $temp = array_merge($temp, $payments[$item['fk_sales_delivery_id']]);
            }
            $item['payments'] = $temp;
        }
        return $unsettled;
    }

}

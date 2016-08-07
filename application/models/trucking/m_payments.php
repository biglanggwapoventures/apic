<?php   
/**
 * Description of m_payments
 *
 * @author Adrian Natabio
 */
class M_Payments extends CI_Model {

    protected $table = 'trucking_payment';
    protected $details_table = 'trucking_payment_detail';
    protected $check_transactions_table = 'trucking_payment_check_transaction';

    public function get_next_row_id($payments_id, $mode) {
        if(!strcmp($mode, "next")){
            $query = "SELECT id FROM pm_trucking_payment WHERE id > {$payments_id} ORDER BY id ASC LIMIT 1";
        }else{
            $query = "SELECT id FROM pm_trucking_payment WHERE id < {$payments_id} ORDER BY id DESC LIMIT 1";
        }
        return $this->db->query($query)->result_array();
    }

    public function get_min_id(){
        return $this->db->query("SELECT MIN(id) as id FROM pm_trucking_payment")->result_array();
    }

    public function get_max_id(){
        return $this->db->query("SELECT MAX(id) as id FROM pm_trucking_payment")->result_array();
    }

    public function create($data)
    {
        $check_trans = [];

        // begin transaction
        $this->db->trans_begin();

        // insert payment data
        $this->db->insert($this->table, $data['payment']);
        $id = $this->db->insert_id();


        // get payment transactions
        foreach($data['details'] AS &$detail)
        {
            $detail['fk_trucking_payment_id'] = $id;
            $this->db->insert($this->details_table, $detail);
            if($detail['payment_method'] === 'Check')
            {
                $temp = $data['check'];
                $temp['fk_trucking_payment_id'] = $id;
                $temp['fk_trucking_payment_detail_id'] = $this->db->insert_id();
                $temp['amount'] = $detail['amount'];
                $check_trans[] = $temp;
            }
        }

        if(!empty($check_trans)){
            $this->db->insert_batch($this->check_transactions_table, $check_trans);
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return FALSE;
        }else{
            $this->db->trans_commit();
            return $id;
        }
    }


    public function update($id, $data)
    {
        $check_trans = [];

        $this->db->trans_begin();

        $this->db->update($this->table, $data['payment'], ['id' => $id]);

        $this->db->update_batch($this->details_table, $data['details'], 'id');

        $this->db->delete($this->check_transactions_table, ['fk_trucking_payment_id' => $id]);

        if(isset($data['check']))
        {
            foreach($data['details'] AS $row)
            {
                $temp = $data['check'];
                $temp['fk_trucking_payment_id'] = $id;
                $temp['fk_trucking_payment_detail_id'] = $row['id'];
                $temp['amount'] = $row['amount'];
                $check_trans[] = $temp;
            }
            $this->db->insert_batch($this->check_transactions_table, $check_trans);   
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }
        else
        {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function all($page = 1, $params = FALSE)
    {
        $limit = 100;
        $offset = ($page <= 1 ? 0 : ($page-1)*$limit);
        
        $this->db->select('rec.id, DATE_FORMAT(rec.date, "%d-%b-%Y") AS `date`, customer.company_name AS customer, rec.tracking_number, rec.tracking_number_type, SUM(recd.amount) AS  total_amount, '
            .'CASE WHEN rec.status = '.M_Status::STATUS_FINALIZED.' THEN 1 ELSE 0 END AS approved', FALSE);
        $this->db->from($this->table.' AS rec')->join($this->details_table.' as recd', 'recd.fk_trucking_payment_id = rec.id');
        $this->db->join('sales_customer AS customer', 'customer.id = rec.fk_sales_customer_id');
        // $this->db->join('sales_customer AS customer', 'customer.id = rec.fk_sales_customer_id');
        if($params !== FALSE)
        {
            $this->db->where($params);
        }
        $this->db->limit($limit, $offset)->group_by('rec.id')->order_by('rec.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function is_valid($id)
    {
        $this->db->select('id')->from($this->table);
        if (is_array($id))
        {
            $this->db->where_in('id', $id);
            $count = $this->db->get()->num_rows();
            return $count === count($id);
        }
        else
        {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }


    public function get($id)
    {
        // get master reciept  info
        $this->db->select('customer.company_name AS customer, rec.deposit_date, rec.`date`, rec.`status`, rec.tracking_number AS tracking_no, rec.tracking_number_type AS tracking_type, rec.remarks, rec.pay_to, rec.pay_from', FALSE);
        $this->db->from($this->table.' AS rec')->where('rec.id', $id)->join('sales_customer AS customer', 'customer.id = rec.fk_sales_customer_id');
        $data['payment'] = $this->db->get()->row_array();

        //get payment transacitons
        $this->db->select('recd.id, recd.fk_tracking_packing_list_id AS pl_id, recd.amount, pl.`date`, recd.payment_method', FALSE);
        $this->db->from($this->details_table.' AS recd');
        $this->db->where('recd.fk_trucking_payment_id', $id);
        $this->db->join('tracking_packing_list AS pl', 'pl.id = recd.fk_tracking_packing_list_id');
        $data['details'] = $this->db->get()->result_array();

        $pl_ids = array_column($data['details'], 'pl_id');

        //get total amount paid per pl
        $this->db->select('(SUM(CASE WHEN recd.payment_method = "Cash" OR chk.deposit_date <= CURDATE() THEN recd.amount ELSE 0 END)) AS amount, recd.fk_tracking_packing_list_id AS pl_id', FALSE);
        $this->db->from($this->details_table.' AS recd');
        $this->db->join('trucking_payment AS rec', 'rec.id = recd.fk_trucking_payment_id');
        $this->db->join('tracking_packing_list AS pl', 'pl.id = recd.fk_tracking_packing_list_id');
        $this->db->join('trucking_payment_check_transaction AS chk', 'chk.fk_trucking_payment_detail_id = rec.id', 'left');
        $this->db->where_in('fk_tracking_packing_list_id', $pl_ids);
        $this->db->where('rec.status', M_Status::STATUS_FINALIZED);
        $this->db->group_by('recd.fk_tracking_packing_list_id');
        $paid = array_column($this->db->get()->result_array(), 'amount', 'pl_id');

        //get pl amount
        // $this->db->select('SUM(pld.this_packing_list * (ordd.unit_price - ordd.discount)) AS amount, pld.fk_packing_list_id AS pl_id', FALSE);
        // $this->db->from('tracking_packing_list_details AS pld')->join('sales_order_detail AS ordd', 'ordd.id = pld.fk_sales_order_detail_id');
        // $this->db->where_in('pld.fk_packing_list_id', $pl_ids);
        // $this->db->group_by('pld.fk_packing_list_id');
        $this->db->select('pl.net_amount AS amount, pl.id AS pl_id', FALSE);
        $this->db->from('tracking_packing_list AS pl');
        $this->db->group_by('pl.id');   
        $pl_amount = array_column($this->db->get()->result_array(), 'amount', 'pl_id');

        //get check if any
        $this->db->select('chk.*')->from($this->check_transactions_table.' AS chk')->where('fk_trucking_payment_id', $id);
        $data['payment']['check'] = $this->db->get()->row_array();


        foreach($data['details'] AS &$row)
        {
            $row['amount_paid'] = isset($paid[$row['pl_id']]) ? $paid[$row['pl_id']] : 0;
            $row['pl_amount'] = $pl_amount[$row['pl_id']];
        }

        return $data;
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
   
   
    public function get_unsettled($id, $pdc_is_paid = FALSE, $add_whtax_to_paid = FALSE, $date_range = FALSE, $date_order = FALSE) {
        $this->load->library('subquery');
        $this->db->select('packing_list.id as fk_tracking_packing_list_id, packing_list.net_amount, DATE_FORMAT(packing_list.date, "%m/%d/%Y") as date', FALSE);
        // $this->db->select('packing_list.id as fk_packing_list_id, packing_list.net_amount, DATE_FORMAT(packing_list.date, "%m/%d/%Y") as date, packing_list.fk_sales_order_id as order_id', FALSE);
        // $this->db->select('credit_memo_amount as whtax_amount, "" as whtax_date', FALSE);
        $this->db->select('packing_list.net_amount as total_amount', FALSE);
        $this->db->select('IFNULL(payments.total_paid, 0) as total_paid', FALSE);
        if ($date_range !== FALSE) {
            $this->db->where('DATE(packing_list.date) >= DATE("' . $date_range['start'] . '")', FALSE, FALSE);
            $this->db->where('DATE(packing_list.date) <= DATE("' . $date_range['end'] . '")', FALSE, FALSE);
        }
        $this->db->from('tracking_packing_list as packing_list');
        // $this->db->join('sales_order as s_order', 's_order.id = packing_list.fk_sales_order_id');
        $this->db->join('sales_customer as customer', 'customer.id = packing_list.fk_sales_customer_id');
        $this->db->join('tracking_packing_list_details as packing_list_details', 'packing_list_details.fk_packing_list_id = packing_list.id');
        // $this->db->join('sales_order_detail as s_order_detail', 's_order_detail.id = packing_list_details.fk_sales_order_detail_id');

        //SUBQUERY START
        $sub = $this->subquery->start_subquery('JOIN', 'LEFT', 'payments.pl_id = packing_list.id');
        $sub->select('payment_detail.fk_tracking_packing_list_id as pl_id');
        if (!$pdc_is_paid) {
            $sub->select('SUM((CASE WHEN payment_detail.payment_method = "Cash" THEN payment_detail.amount ELSE 0 END) + (CASE WHEN check_trans.amount IS NOT NULL AND DATE(check_trans.deposit_date) <= CURDATE() THEN check_trans.amount ELSE 0 END)) as total_paid', FALSE);
        } else {
            $sub->select('SUM((CASE WHEN payment_detail.payment_method = "Cash" THEN payment_detail.amount ELSE 0 END) + (CASE WHEN check_trans.amount IS NOT NULL THEN check_trans.amount ELSE 0 END)) as total_paid', FALSE);
        }
        $sub->from('trucking_payment_detail as payment_detail');
        $sub->join('trucking_payment_check_transaction as check_trans', 'check_trans.fk_trucking_payment_detail_id = payment_detail.id', 'left');
        $sub->join('trucking_payment as payment', 'payment.id = payment_detail.fk_trucking_payment_id');
        $sub->where(array('payment.fk_sales_customer_id' => $id, 'payment.status' => M_Status::STATUS_FINALIZED));
        $sub->group_by('payment_detail.fk_tracking_packing_list_id');
        $this->subquery->end_subquery('payments');
        //SUBQUERY END

        $this->db->where(array('customer.id' => $id));
        if ($date_order !== FALSE) {
            $this->db->order_by('packing_list.date', $date_order);
        } else {
            $this->db->order_by('packing_list.id', 'DESC');
        }
        return $this->db->group_by('packing_list.id')->having('total_amount > total_paid')->get()->result_array();
    }
}

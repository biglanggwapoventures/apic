<?php   
/**
 * Description of m_receipts
 *
 * @author Adrian Natabio
 */
class M_Receipts extends CI_Model {

    protected $table = 'sales_receipt';
    protected $details_table = 'sales_receipt_detail';
    protected $check_transactions_table = 'sales_receipt_check_transaction';


    public function create($data)
    {
        $check_trans = [];

        // begin transaction
        $this->db->trans_begin();

        // insert receipt data
        $this->db->insert($this->table, $data['receipt']);
        $id = $this->db->insert_id();


        // get receipt transactions
        foreach($data['details'] AS &$detail)
        {
            $detail['fk_sales_receipt_id'] = $id;
            $this->db->insert($this->details_table, $detail);
            if($detail['payment_method'] === 'Check')
            {
                $temp = $data['check'];
                $temp['fk_sales_receipt_id'] = $id;
                $temp['fk_sales_receipt_detail_id'] = $this->db->insert_id();
                $temp['amount'] = $detail['amount'];
                $check_trans[] = $temp;
            }
        }

        if(!empty($check_trans))
        {
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
            return $id;
        }
    }


    public function update($id, $data)
    {
        $check_trans = [];

        $this->db->trans_begin();

        $this->db->update($this->table, $data['receipt'], ['id' => $id]);

        $this->db->update_batch($this->details_table, $data['details'], 'id');

        $this->db->delete($this->check_transactions_table, ['fk_sales_receipt_id' => $id]);

        if(isset($data['check']))
        {
            foreach($data['details'] AS $row)
            {
                $temp = $data['check'];
                $temp['fk_sales_receipt_id'] = $id;
                $temp['fk_sales_receipt_detail_id'] = $row['id'];
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
        $this->db->from($this->table.' AS rec')->join($this->details_table.' as recd', 'recd.fk_sales_receipt_id = rec.id');
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
        $this->db->select('customer.company_name AS customer, rec.deposit_date, rec.`date`, rec.`status`, rec.tracking_number AS tracking_no, rec.tracking_number_type AS tracking_type, rec.remarks', FALSE);
        $this->db->from($this->table.' AS rec')->where('rec.id', $id)->join('sales_customer AS customer', 'customer.id = rec.fk_sales_customer_id');
        $data['receipt'] = $this->db->get()->row_array();

        //get receipt transacitons
        $this->db->select('recd.id, recd.fk_sales_delivery_id AS pl_id, recd.amount, pl.`date`, recd.payment_method', FALSE);
        $this->db->from($this->details_table.' AS recd');
        $this->db->where('recd.fk_sales_receipt_id', $id);
        $this->db->join('sales_delivery AS pl', 'pl.id = recd.fk_sales_delivery_id');
        $data['details'] = $this->db->get()->result_array();

        $pl_ids = array_column($data['details'], 'pl_id');

        //get total amount paid per pl
        $this->db->select('(SUM(CASE WHEN recd.payment_method = "Cash" OR chk.deposit_date <= CURDATE() THEN recd.amount ELSE 0 END) + IFNULL(pl.credit_memo_amount, 0)) AS amount, recd.fk_sales_delivery_id AS pl_id', FALSE);
        $this->db->from($this->details_table.' AS recd');
        $this->db->join('sales_receipt AS rec', 'rec.id = recd.fk_sales_receipt_id');
        $this->db->join('sales_delivery AS pl', 'pl.id = recd.fk_sales_delivery_id');
        $this->db->join('sales_receipt_check_transaction AS chk', 'chk.fk_sales_receipt_detail_id = rec.id', 'left');
        $this->db->where_in('fk_sales_delivery_id', $pl_ids);
        $this->db->where('rec.status', M_Status::STATUS_FINALIZED);
        $this->db->group_by('recd.fk_sales_delivery_id');
        $paid = array_column($this->db->get()->result_array(), 'amount', 'pl_id');

        //get pl amount
        $this->db->select('SUM(pld.this_delivery * (ordd.unit_price - ordd.discount)) AS amount, pld.fk_sales_delivery_id AS pl_id', FALSE);
        $this->db->from('sales_delivery_detail AS pld')->join('sales_order_detail AS ordd', 'ordd.id = pld.fk_sales_order_detail_id');
        $this->db->where_in('pld.fk_sales_delivery_id', $pl_ids);
        $this->db->group_by('pld.fk_sales_delivery_id');
        $pl_amount = array_column($this->db->get()->result_array(), 'amount', 'pl_id');

        //get check if any
        $this->db->select('chk.*')->from($this->check_transactions_table.' AS chk')->where('fk_sales_receipt_id', $id);
        $data['receipt']['check'] = $this->db->get()->row_array();


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
   
   
   
}

<?php

class M_customer extends CI_Model {

    protected $table = 'sales_customer';

    private static $credit_terms = array(
        '', 'COD', 'Days'
    );

    const TABLE_NAME = 'sales_customer';
    const CREDIT_TERMS_COD = 1;
    const CREDIT_TERMS_DAYS = 2;
    const PRICING_PRICE_COLUMN = 'price';
    const PRICING_PRODUCT_ID_COLUMN = 'fk_inventory_product_id';
    const PRICING_CUSTOMER_ID_COLUMN = 'fk_sales_customer_id';

    public static function list_credit_terms($key = FALSE) {
        return $key ? self::$credit_terms[$key] : self::$credit_terms;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////
    public function find($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data) ? $this->db->insert_id() : FALSE;
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('customer_code')->from($this->table)->where('customer_code', $code)->get()->num_rows() === 0;
    }

    public function all($search = [], $wildcards = [])
    {
        if(!empty($search)){
           $this->db->where($search);
        }
        if(!empty($wildcards)){
            $this->db->like($wildcards);
        }
        $this->db->order_by('company_name', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    public function is_active($id)
    {
        $this->db->where('status', 'a');
        return (bool)$this->find($id);
    }

    public function exists($id, $active = FALSE)
    {
        if($active === TRUE){
            $this->db->where('status', 'a');
        }
        return $this->db->select('id')->from($this->table)->where('id', $id)->get()->num_rows() > 0;
    }

    function get_uncountered_packing_list($customer_id, $excluded_packing_lists = FALSE)
    {
        $countered = $this->db->select('cr_detail.fk_sales_delivery_id')
            ->from('sales_counter_receipt AS cr')
            ->join('sales_counter_receipt_detail AS cr_detail', 'cr_detail.fk_sales_counter_receipt_id = cr.id')
            ->where('cr.fk_sales_customer_id', $customer_id)
            ->where('cr.approved_by IS NOT NULL')
            ->get()
            ->result_array();

        $countered_packing_lists = array_column($countered, 'fk_sales_delivery_id');

        if($excluded_packing_lists){
            $countered_packing_lists = array_diff($countered_packing_lists, $excluded_packing_lists);
            // print_r($countered_packing_lists);
        }

        $this->db->select('delivery.id, DATE(delivery.date) AS date, delivery.invoice_number, (SUM((order_detail.unit_price * delivery_detail.this_delivery) - (order_detail.discount * delivery_detail.this_delivery)) - IFNULL(delivery.credit_memo_amount, 0)) AS amount ', FALSE)
            ->from('sales_delivery AS delivery')
            ->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id')
            ->join('sales_order_detail AS order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id')
            ->join('sales_order AS sales', 'sales.id = order_detail.fk_sales_order_id')
            ->where('sales.fk_sales_customer_id', $customer_id)
            ->group_by('delivery.id')
            ->order_by('delivery.date', 'DESC');

        if(!empty($countered_packing_lists)){
            $this->db->where_not_in('delivery.id', $countered_packing_lists);
        }

        return $this->db->get()->result_array();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////


    public function add($data) {
        $banks = $data['bank'];
        unset($data['bank']);
        if ($this->db->insert('sales_customer', $data)) {
            $customer_id = $this->db->insert_id();
            foreach ($banks as &$b)
                $b['fk_sales_customer_id'] = $customer_id;
            $this->db->insert_batch('sales_customer_bank_account', $banks);
            return TRUE;
        }
        return FALSE;
    }

    public function get_id_by_so($order_id) {
        $data = $this->db->select('fk_sales_customer_id')->from('sales_order')->where(['id' => $order_id])->get()->row_array();
        return $data ? $data['fk_sales_customer_id'] : FALSE;
    }

    public function get_customer_products($customer_id) {
        $this->db->select('cpl.fk_inventory_product_id as product_id, '
                        . 'FORMAT(cpl.price, 2) as price, '
                        . 'FORMAT(cpl.discount, 2) as discount, '
                        . 'CONCAT("[", p.code, "] ", p.description) AS description, '
                        . 'u.description as prod_unit', FALSE)
                ->from('sales_customer_pricing AS cpl')
                ->join('inventory_product AS p', 'p.id = cpl.fk_inventory_product_id')
                ->join('inventory_unit AS u', 'u.id = p.fk_unit_id')
                ->where(['cpl.fk_sales_customer_id' => $customer_id, 'p.status' => 'a']);
        return $this->db->get()->result();
    }

    public function get_customer_price_list($customer_id)
    {
        $this->db->select('cpl.id, cpl.price, cpl.discount, p.id AS product_id, CONCAT("[", p.code, "] ", p.description) AS description, u.description AS unit, c.description AS category', FALSE);
        $this->db->from('sales_customer_pricing AS cpl');
        $this->db->join('inventory_product AS p', 'p.id = cpl.fk_inventory_product_id');
        $this->db->join('inventory_unit AS u', 'u.id = p.fk_unit_id');
        $this->db->join('inventory_category AS c', 'c.id = p.fk_category_id');
        $this->db->where('cpl.fk_sales_customer_id', $customer_id);
        return $this->db->get()->result_array();
    }

    public function get_bank($customer_id) {
        $this->db->select('name, account_number');
        $this->db->from('sales_customer_bank_account as bank_account');
        $this->db->where('bank_account.fk_sales_customer_id', $customer_id);
        $data = $this->db->get()->result_array();
        $banks = array();
        if ($data) {
            for ($x = 0; $x < count($data); $x++) {
                $banks['name'][$x] = $data[$x]['name'];
                $banks['account_number'][$x] = $data[$x]['account_number'];
            }
        } else {
            $banks = array(
                'name' => array(''),
                'account_number' => array('')
            );
        }
        return $banks;
    }

    public function get($search_token = FALSE, $filter = array(), $limit = 999, $offset = 0) {
        $this->db->select('customer.*');
        $this->db->from('sales_customer as customer');
        if ($search_token) {
            $this->db->like('company_name', $search_token, 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $this->db->order_by('customer.company_name', 'ASC');
        $customers = $this->db->limit($limit, $offset)->get()->result_array();
        foreach ($customers as &$c) {
            $c['credit_limit'] = number_format($c['credit_limit'], 2);
            $c['bank'] = $this->get_bank($c['id']); //get bank details per customer
        }
        return $customers;
    }

    // public function update($id, $data) {
    //     $banks = $data['bank'];
    //     unset($data['bank']);
    //     $this->db->where('id', $id);
    //     if ($this->db->update('sales_customer', $data)) {
    //         $this->db->where('fk_sales_customer_id', $id);
    //         if ($this->db->delete('sales_customer_bank_account')) {
    //             foreach ($banks as &$b)
    //                 $b['fk_sales_customer_id'] = $id;
    //             $this->db->insert_batch('sales_customer_bank_account', $banks);
    //             return TRUE;
    //         }
    //     }
    //     return FALSE;
    // }

    public function get_price_list($customer_id = FALSE, $active_only = FALSE) {
        $this->db->select('product.id as product_id,  pricing.price, pricing.discount');
        $this->db->from('sales_customer_pricing as pricing');
        $this->db->where(self::PRICING_CUSTOMER_ID_COLUMN, $customer_id);
        $this->db->join('inventory_product as product', 'pricing.fk_inventory_product_id = product.id');
        if($active_only){
            $this->db->where('product.status', 'Active');
        }
        $data = $this->db->get()->result_array();
        if ($data) {
            $price_list = array();
            for ($x = 0; $x < count($data); $x++) {
                $price_list['price'][$x] = number_format($data[$x]['price'], 2);
                $price_list['discount'][$x] = number_format($data[$x]['discount'], 2);
                $price_list['product_id'][$x] = $data[$x]['product_id'];
            }
            return $price_list;
        }
        return array(
            'product_id' => array(''),
            'price' => array(''),
            'discount' => array('')
        );
    }

    public function save_price_list($customer_id, $price_list) 
    {
        $this->db->trans_start();

        $existing = ['id' => [], 'item' => []];
        $new = [];

        foreach($price_list AS &$row){
            $row['fk_sales_customer_id'] = $customer_id;
            $row['price'] = str_replace(',', '', $row['price']);
            $row['discount'] = str_replace(',', '', $row['discount']);
            if(isset($row['id'])){
                $existing['id'][] = $row['id'];
                $existing['item'][] = $row;
            }else{
                $new[] = $row;
            }
        }
        
        if(empty($existing['id'])){
            $this->db->where('fk_sales_customer_id', $customer_id);
            $this->db->delete('sales_customer_pricing');
        }else{
            $this->db->where('fk_sales_customer_id', $customer_id);
            $this->db->where_not_in('id', $existing['id']);
            $this->db->delete('sales_customer_pricing');
            $this->db->update_batch('sales_customer_pricing', $existing['item'], 'id');
        }

        if(!empty($new)){
            $this->db->insert_batch('sales_customer_pricing', $new);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_name($customer_id = FALSE) {
        $result = $this->db->select('company_name')->from('sales_customer')->where(array('id' => $customer_id))->get()->row_array();
        return $result ? $result['company_name'] : FALSE;
    }

    // function delete($ID) {
    //     if ($ID) {
    //         $this->db->where('id', $ID);
    //         return $this->db->delete('sales_customer');
    //     }
    //     return FALSE;
    // }

    public function is_valid($id) {
        $this->db->select('id')->from(self::TABLE_NAME);
        if (is_array($id)) {
            $this->db->where_in('id', array_unique($id));
            return count($id) === $this->db->get()->num_rows();
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    /* =====================
      NEW FUNCTIONS 01-14-15
      ===================== */

    public function get_list() {
        $data = $this->db->select('id, company_name as name, customer_code as code, ')->from(self::TABLE_NAME)->order_by('company_name', 'ASC')->get()->result_array();
        return $data ? $data : array();
    }

    public function get_approved() {
        $data = $this->db
                        ->select('id, company_name as name, customer_code as code, credit_term, credit_limit')
                        ->from(self::TABLE_NAME)
                        ->where('status', M_Status::STATUS_APPROVED)
                        ->order_by('name', 'ASC')
                        ->get()->result_array();
        return $data ? $data : array();
    }

    /*
     * @var $date_range
     * TYPE: Array
     * IMPORTANT: Should contain keys: 'start' & 'end'
     * 
     */

    public function get_unsettled($id, $pdc_is_paid = FALSE, $add_whtax_to_paid = FALSE, $date_range = FALSE, $date_order = FALSE) {
        $this->load->library('subquery');
        $this->db->select('delivery.id as fk_sales_delivery_id, delivery.invoice_number, DATE_FORMAT(delivery.date, "%m/%d/%Y") as date, delivery.fk_sales_order_id as order_id', FALSE);
        $this->db->select('credit_memo_amount as whtax_amount, "" as whtax_date', FALSE);
        $this->db->select('SUM((s_order_detail.unit_price -  s_order_detail.discount) * delivery_detail.this_delivery) as total_amount', FALSE);
        $this->db->select('IFNULL(payments.total_paid, 0)+IFNULL(credit_memo_amount, 0) as total_paid', FALSE);
        if ($date_range !== FALSE) {
            $this->db->where('DATE(delivery.date) >= DATE("' . $date_range['start'] . '")', FALSE, FALSE);
            $this->db->where('DATE(delivery.date) <= DATE("' . $date_range['end'] . '")', FALSE, FALSE);
        }
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_order as s_order', 's_order.id = delivery.fk_sales_order_id');
        $this->db->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->db->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = delivery.id');
        $this->db->join('sales_order_detail as s_order_detail', 's_order_detail.id = delivery_detail.fk_sales_order_detail_id');

        //SUBQUERY START
        $sub = $this->subquery->start_subquery('JOIN', 'LEFT', 'payments.pl_id = delivery.id');
        $sub->select('receipt_detail.fk_sales_delivery_id as pl_id');
        if (!$pdc_is_paid) {
            $sub->select('SUM((CASE WHEN receipt_detail.payment_method = "Cash" THEN receipt_detail.amount ELSE 0 END) + (CASE WHEN check_trans.amount IS NOT NULL AND DATE(check_trans.deposit_date) <= CURDATE() THEN check_trans.amount ELSE 0 END)) as total_paid', FALSE);
        } else {
            $sub->select('SUM((CASE WHEN receipt_detail.payment_method = "Cash" THEN receipt_detail.amount ELSE 0 END) + (CASE WHEN check_trans.amount IS NOT NULL THEN check_trans.amount ELSE 0 END)) as total_paid', FALSE);
        }
        $sub->from('sales_receipt_detail as receipt_detail');
        $sub->join('sales_receipt_check_transaction as check_trans', 'check_trans.fk_sales_receipt_detail_id = receipt_detail.id', 'left');
        $sub->join('sales_receipt as receipt', 'receipt.id = receipt_detail.fk_sales_receipt_id');
        $sub->where(array('receipt.fk_sales_customer_id' => $id, 'receipt.status' => M_Status::STATUS_FINALIZED));
        $sub->group_by('receipt_detail.fk_sales_delivery_id');
        $this->subquery->end_subquery('payments');
        //SUBQUERY END

        $this->db->where(array('customer.id' => $id, 'delivery.status' => M_Status::STATUS_DELIVERED));
        if ($date_order !== FALSE) {
            $this->db->order_by('delivery.date', $date_order);
        } else {
            $this->db->order_by('delivery.id', 'DESC');
        }
        return $this->db->group_by('delivery.id')->having('total_amount > total_paid')->get()->result_array();
    }

    public function get_undelivered_orders($customer_id) 
    {
        
        $delivered_so = $this->db->select('fk_sales_order_id AS id')
            ->from('sales_delivery AS pl')
            ->join('sales_order AS so', 'so.id = pl.fk_sales_order_id')
            ->where('so.fk_sales_customer_id', $customer_id)
            ->get()->result_array();

            $this->db->select('s_order.id, agent.name AS sales_agent')
                ->from('sales_order AS s_order')
                ->join('sales_agent AS agent', 'agent.id = s_order.fk_sales_agent_id', 'left')
                ->where('s_order.fk_sales_customer_id', $customer_id);

            if($delivered_so){
                $this->db->where_not_in('s_order.id', array_column($delivered_so, 'id'));
            }

            return $this->db->get()->result_array();


    }

    public function _get_undelivered_orders($customer_id) {
        $this->load->library('subquery');
        $this->db
                ->select('DISTINCT s_order.id', FALSE)
                ->from('sales_order as s_order')
                ->join('sales_order_detail as order_detail', 'order_detail.fk_sales_order_id = s_order.id');

        $sub = $this->subquery->start_subquery('join', 'left', 'delivery.order_detail_id = order_detail.id');
        $sub->select('SUM(delivery_detail.this_delivery) as total_qty_delivered, delivery_detail.fk_sales_order_detail_id as order_detail_id', FALSE);
        $sub->from('sales_delivery as s_delivery');
        $sub->join('sales_order', 'sales_order.id = s_delivery.fk_sales_order_id');
        $sub->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = s_delivery.id');
        $sub->where(array('s_delivery.status' => M_Status::STATUS_DELIVERED, 'sales_order.fk_sales_customer_id' => $customer_id));
        $sub->group_by('delivery_detail.fk_sales_order_detail_id');
        $this->subquery->end_subquery('delivery');

        $this->db->where(array('s_order.fk_sales_customer_id' => $customer_id, 's_order.status' => M_Status::STATUS_APPROVED))
                ->where('order_detail.product_quantity > IFNULL(delivery.total_qty_delivered, 0)', FALSE, FALSE)
                ->group_by('order_detail.id')
                ->order_by('s_order.id', 'DESC');
        $data = $this->db->get()->result_array();
        return $data ? $data : array();
    }

    public function get_statement_of_account($customer_id, $date_range = FALSE) {
        $this->load->model('sales/m_delivery');
        $unsettled_pls = $this->get_unsettled($customer_id, TRUE, TRUE, $date_range, 'ASC');
        $pl_ids = array_map(function($var) {
            return $var['fk_sales_delivery_id'];
        }, $unsettled_pls);
        $delivery_line = $this->m_delivery->get_packing_list_line($pl_ids);
        foreach ($unsettled_pls as &$pl) {
            array_filter($delivery_line, function($var) USE (&$pl) {
                if ($var['pl_id'] == $pl['fk_sales_delivery_id']) {
                    $pl['line'][] = $var;
                }
            });
        }
        return $unsettled_pls;
    }

}

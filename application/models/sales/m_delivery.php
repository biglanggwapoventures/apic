<?php

class M_Delivery extends CI_Model
{

    const TABLE_NAME_GENERAL = 'sales_delivery';
    const TABLE_NAME_DETAIL = 'sales_delivery_detail';

    public function get_next_row_id($delivery_id, $mode) {
        if(!strcmp($mode, "next")){
            $query = "SELECT id FROM pm_sales_delivery WHERE id > {$delivery_id} ORDER BY id ASC LIMIT 1";
        }else{
            $query = "SELECT id FROM pm_sales_delivery WHERE id < {$delivery_id} ORDER BY id DESC LIMIT 1";
        }
        return $this->db->query($query)->result_array();
    }

    public function get_min_id(){
        return $this->db->query("SELECT MIN(id) as id FROM pm_sales_delivery")->result_array();
    }

    public function get_max_id(){
        return $this->db->query("SELECT MAX(id) as id FROM pm_sales_delivery")->result_array();
    }

    public function master_list($arr = array())
    {
        $this->load->library('subquery');
        $this->db->select('so.po_number AS po_no, pl.type, pl.id as pl_no, pl.fk_sales_order_id AS so_no, pl.is_printed, '
                . 'DATE_FORMAT(pl.date, "%M %d, %Y") AS pl_date, customer.company_name AS customer, pl.status AS status,'
                . 'FORMAT(SUM(pl_items.this_delivery * (so_items.unit_price - so_items.discount)) + IFNULL(addon.total_addon, 0), 2) as total_amount', FALSE);
        $this->db->from('sales_delivery AS pl');
        $this->db->join('sales_order AS so', 'so.id = pl.fk_sales_order_id');
        $this->db->join('sales_customer AS customer', 'customer.id = so.fk_sales_customer_id');
        $this->db->join('sales_delivery_detail AS pl_items', 'pl_items.fk_sales_delivery_id = pl.id');
        $this->db->join('sales_order_detail AS so_items', 'so_items.id = pl_items.fk_sales_order_detail_id');
        /* start subquery for joining addon to total amount */
        $sub = $this->subquery->start_subquery('join', 'left', 'addon.delivery_id = pl.id');
        $sub->select('delivery_id, SUM(unit_price * this_delivery) as total_addon');
        $sub->from('sales_order_medications');
        $sub->join('sales_order_medication_deliveries', 'medication_order_item_id = sales_order_medications.id');
        $sub->group_by('delivery_id');
        /* end subquery for join on total amount */
        $this->subquery->end_subquery('addon');
        $this->filter_functions($arr);
        $this->db->group_by('pl.id')->order_by('pl.id', 'DESC');
        return $this->db->get()->result_array();
    }

    private function filter_functions($arr)
    {
        $arr['pl_no'] ? $this->db->where('pl.id', $arr['pl_no']) : NULL;
        $arr['so_no'] ? $this->db->where('so.id', $arr['so_no']) : NULL;
        $arr['po_no'] ? $this->db->where('so.po_number', $arr['po_no']) : NULL;
        $arr['date'] ? $this->db->where('pl.date', $arr['date']) : NULL;
        $arr['customer'] ? $this->db->where('so.fk_sales_customer_id', $arr['customer']) : NULL;
        $arr['page'] && $arr['page'] - 1 > 0 ? $this->db->limit(30, 30 * ($arr['page'] - 1)) : $this->db->limit(30, 0);
    }

    function add($data)
    {
        $details = $data['details'];
        unset($data['details']);
        if ($this->db->insert('sales_delivery', $data))
        {
            $delivery_id = $this->db->insert_id();
            foreach ($details as &$d)
            {
                $d['fk_sales_delivery_id'] = $delivery_id;
            }
            $this->db->insert_batch('sales_delivery_detail', $details);
            return $delivery_id;
        }
        return FALSE;
    }

    function createSalesReceipt($salesDustomerID = false, $date = false, $trackingNumberType = false, $trackingNumber = false, $remarks = false, $status = false, $ORNumber = false, $approvedBy = false, $createdBy = false)
    {
        $this->db->start_cache();
        $this->db->flush_cache();
        $data = array(
            "fk_sales_customer_id" => $salesDustomerID,
            "date" => $date,
            "tracking_number_type" => $trackingNumberType,
            "tracking_number" => $trackingNumber,
            "remarks" => $remarks,
            "status" => $status,
            "or_number" => $ORNumber,
            "approved_by" => $approvedBy,
            "created_by" => $createdBy
        );
        $this->db->insert("pm_sales_receipt", $data);
        $id = $this->db->insert_id();
        $this->db->flush_cache();
        $this->db->stop_cache();
        return $id;
    }

    function deleteSalesReceipt($salesReceiptID)
    {
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->where_in("pm_sales_receipt.id", $salesReceiptID);
        $this->db->delete("pm_sales_receipt");
        $this->db->flush_cache();
        $this->db->where_in("pm_sales_receipt_check_transaction.fk_sales_receipt_id", $salesReceiptID);
        $this->db->delete("pm_sales_receipt_check_transaction");
        $this->db->flush_cache();
        $this->db->where_in("pm_sales_receipt_detail.fk_sales_receipt_id", $salesReceiptID);
        $this->db->delete("pm_sales_receipt_detail");
        $this->db->flush_cache();
        $this->db->stop_cache();
    }

    function createSalesReceiptDetail($salesReceiptID = false, $salesDeliveryID = false, $paymentMethod = false, $amount = false)
    {
        $this->db->start_cache();
        $this->db->flush_cache();
        $data = array(
            "fk_sales_receipt_id" => $salesReceiptID,
            "fk_sales_delivery_id" => $salesDeliveryID,
            "payment_method" => $paymentMethod,
            "amount" => str_replace(',', '', $amount)
        );
        $this->db->insert("pm_sales_receipt_detail", $data);
        $id = $this->db->insert_id();
        $this->db->flush_cache();
        $this->db->stop_cache();
        return $id;
    }

    function retrieveSalesReceiptDetail($salesReceiptID = false, $salesDeliveryID = false)
    {

        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->select("*");
        $condition = array();
        ($salesReceiptID) ? $condition["fk_sales_receipt_id"] = $salesReceiptID : null;
        ($salesDeliveryID && !is_array($salesDeliveryID)) ? $condition["fk_sales_delivery_id"] = $salesDeliveryID : null;
        (is_array($salesDeliveryID)) ? $this->db->where_in("fk_sales_delivery_id", $salesDeliveryID) : null;
        (is_array($salesReceiptID)) ? $this->db->where_in("fk_sales_receipt_id", $salesReceiptID) : null;
        (count($condition) > 0) ? $this->db->where($condition) : "";
        $result = $this->db->get("pm_sales_receipt_detail");
        if ($result->num_rows() > 0)
        {
            $this->db->flush_cache();
            $this->db->stop_cache();
            return $result->result_array();
        }
        else
        {
            $this->db->flush_cache();
            $this->db->stop_cache();
            return false;
        }
        $this->db->flush_cache();
        $this->db->stop_cache();
    }

    function createSalesReceiptCheckTransaction($salesReceiptID = false, $bankAccount = false, $checkNumber = false, $checkDate = false, $depositDate = false, $amount = false, $salesReceiptDetailID = false)
    {
        $this->db->start_cache();
        $this->db->flush_cache();
        $data = array(
            "fk_sales_receipt_id" => $salesReceiptID,
            "fk_sales_receipt_detail_ID" => $salesReceiptDetailID,
            "bank_account" => $bankAccount,
            "check_number" => $checkNumber,
            "check_date" => $checkDate,
            "deposit_date" => $depositDate,
            "amount" => str_replace(',', '', $amount)
        );
        $this->db->insert("pm_sales_receipt_check_transaction", $data);
        $id = $this->db->insert_id();
        $this->db->flush_cache();
        $this->db->stop_cache();
        return $id;
    }

    function retrieveSalesReceipt($ID, $status = false)
    {
        if (!$ID)
        {
            return array();
        }
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->select("*");
        $this->db->select("pm_sales_receipt.id");
        $this->db->join("pm_sales_customer", "pm_sales_customer.id = pm_sales_receipt.fk_sales_customer_id", "left");
        $this->db->join("pm_sales_receipt_detail", "pm_sales_receipt_detail.fk_sales_receipt_id = pm_sales_receipt.id", "left");
        $this->db->where_in("pm_sales_receipt.id", $ID);
        ($status) ? $this->db->where("status", $status) : null;
        $result = $this->db->get("pm_sales_receipt");
        if ($result->num_rows() > 0)
        {
            $this->db->flush_cache();
            $this->db->stop_cache();
            return $result->result_array();
        }
        else
        {
            $this->db->flush_cache();
            $this->db->stop_cache();
            return array();
        }
        $this->db->flush_cache();
        $this->db->stop_cache();
    }

    function retrieveSalesDeliveryCheck($deliveryIDs)
    {
        if (!$deliveryIDs)
        {
            return array();
        }
        $this->db->start_cache();
        $this->db->flush_cache();
        $this->db->select("*");
        $this->db->select("pm_sales_receipt_check_transaction.amount");
        $this->db->join("pm_sales_receipt_detail", "pm_sales_receipt_detail.fk_sales_receipt_id = pm_sales_receipt_check_transaction.fk_sales_receipt_id", "left");
        $this->db->where_in("pm_sales_receipt_check_transaction.fk_sales_receipt_id", $deliveryIDs);
        $result = $this->db->get("pm_sales_receipt_check_transaction");
        if ($result->num_rows() > 0)
        {
            $this->db->flush_cache();
            $this->db->stop_cache();
            return $result->result_array();
        }
        else
        {
            $this->db->flush_cache();
            $this->db->stop_cache();
            return array();
        }
        $this->db->flush_cache();
        $this->db->stop_cache();
    }

    /*
     * 1. Get all records from table `sales_delviery`
     * 2. Gather all ids
     * 3. Get all records from `sales_delivery_detail` 
     * 4. Assign corresponding records from step 3 to step 2 : fk_sales_delivery_id
     */

    public function get($with_details = FALSE, $search_token = array(), $filter = array(), $limit = 999, $offset = 0)
    {
        $this->load->library('subquery');
        $this->load->model('inventory/m_product');
        $this->db->select(' delivery.*, DATE_FORMAT(delivery.date, "%M %d, %Y") as formatted_date, truck.trucking_name, customer.company_name, customer.id AS customer_id, customer.address, s_order.id as order_id,delivery.status, s_order.po_number, agent.name AS sales_agent', FALSE);
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_trucking as truck', 'truck.id = delivery.fk_sales_trucking_id', 'left');
        $this->db->join('sales_order as s_order', 's_order.id = delivery.fk_sales_order_id');
        $this->db->join('sales_agent as agent', 'agent.id = s_order.fk_sales_agent_id', 'left');
        $this->db->join('sales_customer as customer', 'customer.id = s_order.fk_sales_customer_id');
        $this->db->join('sales_delivery_detail as deliv_detail', 'deliv_detail.fk_sales_delivery_id = delivery.id');
        $this->db->group_by('delivery.id');
        if ($search_token)
        {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter))
        {
            $this->db->where($filter);
        }
        $this->db->order_by('delivery.id', 'DESC');
        $data = $this->db->limit($limit, $offset)->get()->result_array();
        if (!$with_details)
        {
            return $data;
        }
        $deliveries = array();
        $delivery_ids = array();
        $order_ids = array();
        $customer_ids = array();
        for ($x = 0; $x < count($data); $x++)
        {
            $deliveries[] = $data[$x];
            $delivery_ids[] = $data[$x]['id'];
            $order_ids[] = $data[$x]['fk_sales_order_id'];
        }
        unset($data);

        $details = FALSE;
        if (!empty($delivery_ids))
        {
            //get delivery ids
            $this->db->select('deliv_detail.id, deliv_detail.fk_sales_delivery_id, deliv_detail.this_delivery, deliv_detail.delivered_units, deliv_detail.fk_sales_order_detail_id');
            $this->db->select('order_detail.discount, order_detail.product_quantity, order_detail.total_units, order_detail.unit_price, IFNULL(delivery.total_qty_delivered, 0) as total_qty_delivered, IFNULL(delivery.total_units_delivered, 0) as total_units_delivered', FALSE);
            $this->db->select('product.description, product.formulation_code, product.code');
            $this->db->select('unit.description as unit_description');
            $this->db->from('sales_delivery_detail as deliv_detail');
            $this->db->where_in('deliv_detail.fk_sales_delivery_id', $delivery_ids);
            $this->db->join('sales_order_detail as order_detail', 'order_detail.id = deliv_detail.fk_sales_order_detail_id');
            $this->db->join('inventory_product as product', 'product.id = order_detail.fk_inventory_product_id');
            $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id', 'left');
            $sub = $this->subquery->start_subquery('join', 'left', 'delivery.order_detail_id = order_detail.id');
            $sub->select('SUM(delivery_detail.this_delivery) as total_qty_delivered, SUM(delivery_detail.delivered_units) as total_units_delivered, delivery_detail.fk_sales_order_detail_id as order_detail_id', FALSE);
            $sub->from('sales_delivery as s_delivery');
            $sub->join('sales_order', 'sales_order.id = s_delivery.fk_sales_order_id');
            $sub->join('sales_delivery_detail as delivery_detail', 'delivery_detail.fk_sales_delivery_id = s_delivery.id');
            $sub->where(array('s_delivery.status' => M_Status::STATUS_DELIVERED));
            $sub->where_in('sales_order.id', $order_ids);
            $sub->group_by('delivery_detail.fk_sales_order_detail_id');
            $this->subquery->end_subquery('delivery');
            $this->db->group_by('deliv_detail.id');
            $details = $this->db->get()->result_array();
        }

        foreach ($deliveries as &$d)
        {
            $DELIVERY_ID = $d['id'];
            $formatted_details = array();
            for ($x = 0; $x < count($details); $x++)
            {
                if ($d['id'] === $details[$x]['fk_sales_delivery_id'])
                {
                    $formatted_details['id'][] = $details[$x]['id'];
                    $formatted_details['fk_sales_order_detail_id'][] = $details[$x]['fk_sales_order_detail_id'];
                    $formatted_details['prod_descr'][] = $details[$x]['description'];
                    $formatted_details['prod_code'][] = $details[$x]['code'];
                    $formatted_details['prod_formu_code'][] = $details[$x]['formulation_code'];
                    $formatted_details['product_description'][] = "{$details[$x]['description']} ({$details[$x]['formulation_code']})";
                    $formatted_details['product_quantity'][] = $details[$x]['product_quantity'];
                    $formatted_details['total_units'][] = $details[$x]['total_units'];
                    $formatted_details['this_delivery'][] = $details[$x]['this_delivery'];
                    $formatted_details['delivered_units'][] = $details[$x]['delivered_units'];
                    $formatted_details['quantity_delivered'][] = $details[$x]['total_qty_delivered'];
                    $formatted_details['units_delivered'][] = $details[$x]['total_units_delivered'];
                    $formatted_details['unit_description'][] = $details[$x]['unit_description'];
                    $formatted_details['unit_price'][] = $details[$x]['unit_price'];
                    $formatted_details['discount'][] = $details[$x]['discount'];
                }
            }
            $d['details'] = $formatted_details;
        }

        return $deliveries;
    }

    public function update($delivery_id, $data)
    {
        $details = $data['details'];
        unset($data['details']);

        //begin transaction
        $this->db->trans_begin();

        //update general
        $this->db->where('id', $delivery_id);
        $this->db->update('sales_delivery', $data);

        //update delivery line
        $this->db->update_batch('sales_delivery_detail', $details, 'id');

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

    public function get_status($id = FALSE)
    {
        if ($id)
        {
            $data = $this->db->select('status, is_locked, is_printed')->get_where('sales_delivery', array('id' => $id))->row_array();
            return $data;
        }
        return FALSE;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('sales_delivery');
    }

    public function get_uncountered($customer_id = FALSE)
    {
        $countered_ids = $this->get_countered();
        $this->db->select('delivery.id, delivery.ptn_number, DATE_FORMAT(delivery.date, "%M %d, %Y") as date, delivery.invoice_number, '
                . 'SUM(deliv_detail.amount) as total_amount', FALSE);
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_order', 'delivery.fk_sales_order_id = sales_order.id');
        $this->db->join('sales_delivery_detail as deliv_detail', 'deliv_detail.fk_sales_delivery_id = delivery.id');
        $this->db->group_by('delivery.id');
        if ($customer_id)
        {
            $this->db->where('sales_order.fk_sales_customer_id', $customer_id);
        }
        $this->db->where(array(
            'delivery.status' => M_Status::STATUS_DELIVERED
        ));
        if ($countered_ids !== 0)
        {
            $this->db->where_not_in('delivery.id', array_map(function ($var)
                    {
                        return $var['fk_sales_delivery_id'];
                    }, $countered_ids));
        }
        return $this->db->get()->result_array();
    }

    public function get_countered()
    {
        $this->db->select('DISTINCT counter_detail.fk_sales_delivery_id', FALSE);
        $this->db->from('sales_counter_receipt as counter');
        $this->db->where('counter.status', M_Status::STATUS_FINALIZED);
        $this->db->join('sales_counter_receipt_detail as counter_detail', 'counter_detail.fk_sales_counter_receipt_id = counter.id');
        $data = $this->db->get()->result_array();
        return $data ? $data : 0;
    }

    public function get_unpaid($customer_id = FALSE)
    {
        $this->db->select('delivery.id, delivery.ptn_number, DATE_FORMAT(delivery.date, "%M %d, %Y") as date,SUM(deliv_detail.amount) as total_amount, '
                . 'SUM(receipt_detail.amount) as amount_paid', FALSE);
        $this->db->from('sales_delivery as delivery');
        $this->db->join('sales_order', 'delivery.fk_sales_order_id = sales_order.id');
        $this->db->join('sales_delivery_detail as deliv_detail', 'deliv_detail.fk_sales_delivery_id = delivery.id');
        $this->db->join('sales_receipt_detail as receipt_detail', 'receipt_detail.fk_sales_delivery_id = delivery.id', 'left');
        $this->db->group_by('delivery.id');
        if ($customer_id)
        {
            $this->db->where('sales_order.fk_sales_customer_id', $customer_id);
        }
        $this->db->where(array(
            'delivery.status' => M_Status::STATUS_DELIVERED
        ));
        $customer_drs = $this->db->get()->result_array();

        $drs_id = $customer_drs ? array_map(function($var)
                {
                    return $var['id'];
                }, $customer_drs) : 0;

        //get paid amount per dr
        $this->db->select('receipt_detail.fk_sales_delivery_id, SUM(receipt_detail.amount) as amount_paid');
        $this->db->from('sales_receipt_detail as receipt_detail');
        $this->db->join('sales_receipt', 'sales_receipt.id = receipt_detail.fk_sales_receipt_id');
        $this->db->where('sales_receipt.status', M_Status::STATUS_FINALIZED);
        $this->db->where_in('receipt_detail.fk_sales_delivery_id', $drs_id);
        $this->db->group_by('receipt_detail.fk_sales_delivery_id');
        $unformatted = $this->db->get()->result_array();
        $total_payments = array();
        foreach ($unformatted as $val)
        {
            $total_payments[$val['fk_sales_delivery_id']] = $val['amount_paid'];
        }

        foreach ($customer_drs as $key => &$dr)
        {
            $amount_paid = isset($total_payments[$dr['id']]) ? $total_payments[$dr['id']] : 0;
            if ($amount_paid < $dr['total_amount'])
            { //still has dues
                $dr['amount_paid'] = $amount_paid;
            }
            else if ($amount_paid >= $dr['total_amount'])
            { //fully paid, so let's not include it
                unset($customer_drs[$key]);
            }
        }

        return $customer_drs;
    }

    /*
     * @param id - mixed
     */

    public function is_valid($id, $customer_id = FALSE)
    {
        $this->db->select('delivery.id')->from(self::TABLE_NAME_GENERAL . ' as delivery');
        if ($customer_id)
        {
            $this->db->join('sales_order', 'sales_order.id = delivery.fk_sales_order_id');
            $this->db->where('sales_order.fk_sales_customer_id', $customer_id);
        }
        if (is_array($id))
        {
            $id = array_unique($id);
            $this->db->where_in('delivery.id', $id);
            $count = $this->db->get()->num_rows();
            return $count === count($id);
        }
        else
        {
            $this->db->where('delivery.id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function get_delivery_details($ids = array(0))
    {
        $this->db->select('dr_detail.this_delivery');
        $this->db->from(self::TABLE_NAME_DETAIL . ' dr_detail');
        $this->db->join('sales_order_detail as so_detail', 'dr_detail.fk_sales_order_detail_id = so_detail.id');
        $this->db->join('inventory_product as product', 'so_detail.fk_inventory_product_id = product.id');
        $this->db->join('inventory_unit as unit', 'product.fk_unit_id = unit.id');
        $this->db->where_in('dr_detail.id', $ids);
        return $this->db->get()->result_array();
    }

    public function check_gatepass_validity($dr_ids = array(0))
    {
        $this->db->select('delivery.id')->from(self::TABLE_NAME_GENERAL . ' as delivery');
        $this->db->join('inventory_delivery_log_detail as log_detail', 'log_detail.fk_sales_delivery_id = delivery.id');
        $this->db->where('log_detail.fk_sales_delivery_id IS NULL', FALSE, FALSE);
        $this->db->where('delivery.status', M_Status::STATUS_DELIVERED)->where_in('delivery.id', $dr_ids);
        return $this->db->get()->result_array();
    }

    public function mark_printed($id)
    {
        return $this->db->where('id', $id)->update(self::TABLE_NAME_GENERAL, array('is_printed' => 1));
    }

    public function is_printed($id)
    {
        return $this->db->select('id')->get_where(self::TABLE_NAME_GENERAL, array('id' => $id, 'is_printed' => 1))->num_rows() > 0;
    }

    /* add withholding tax */

    function add_wht($wht)
    {
        return $this->db->insert('sales_delivery_whtax', $wht);
    }

    /* get withholding tax */

    function get_wht($pl_id)
    {
        return $this->db->where(['delivery_id' => $pl_id])->get('sales_delivery_whtax')->row_array();
    }

    function update_wht($wht)
    {

        $this->db->trans_begin();

        $this->db->delete('sales_delivery_whtax', ['delivery_id' => $wht['delivery_id']]);
        $this->db->insert('sales_delivery_whtax', $wht);

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

    function remove_wht($pl_id)
    {
        return $this->db->where(['delivery_id' => $pl_id])->delete('sales_delivery_whtax');
    }

    function save_addon_delivery($data, $pl_id)
    {
        foreach ($data as &$d)
        {
            $d['delivery_id'] = $pl_id;
        }
        $this->db->insert_batch('sales_order_medication_deliveries', $data);
    }

    // get the total delivered amount of addons (per addon items) for a specific PL using id
    public function get_delivered_addons($delivery_id)
    {
        /* get order id */
        $order_id = $this->get_order_id($delivery_id);

        /* load subquery library */
        $this->load->library('subquery');

        /* get addon descriptions, code, etc */
        $this->db->select('delivered.id, delivered.this_delivery, total_delivered.total_delivered_qty, '
                . 'p.product_code, p.description as product_description, '
                . 'addon.quantity as ordered_qty, addon.unit_price');
        $this->db->from('sales_order_medication_deliveries as delivered');
        $this->db->join('sales_order_medications as addon', 'addon.id = delivered.medication_order_item_id');
        $this->db->join('inventory_medications as p', 'p.id = addon.medication_id');

        /* subquery for joining the total delivered addons */
        $sub = $this->subquery->start_subquery('join', 'left', 'total_delivered.medication_order_item_id = delivered.medication_order_item_id');
        $sub->select('SUM(delivered.this_delivery) as total_delivered_qty, addon.id as medication_order_item_id', FALSE);
        $sub->from('sales_order_medication_deliveries as delivered');
        $sub->join('sales_delivery as delivery', 'delivery.id = delivered.delivery_id');
        $sub->join('sales_order_medications as addon', 'addon.id = delivered.medication_order_item_id');
        $sub->where(['addon.order_id' => $order_id, 'delivery.status' => M_Status::STATUS_DELIVERED]);
        $sub->group_by('addon.id');
        $this->subquery->end_subquery('total_delivered');

        $this->db->where('delivered.delivery_id', $delivery_id);
        return $this->db->get()->result_array();
    }

    // kuhaon ang gideliver nga addon for a specific PL using id
    public function get_addon_delivery($id)
    {
        $this->db->select('this_delivery, product_code, description, unit_price');
        $this->db->from('sales_order_medication_deliveries');
        $this->db->where(['delivery_id' => $id]);
        $this->db->join('sales_order_medications', 'sales_order_medications.id = medication_order_item_id');
        $this->db->join('inventory_medications', 'inventory_medications.id = medication_id');
        return $this->db->get()->result_array();
    }

    function get_order_id($delivery_id)
    {
        $data = $this->db->select('fk_sales_order_id')->from('sales_delivery')->where(['id' => $delivery_id])->get()->row_array();
        return $data ? $data['fk_sales_order_id'] : FALSE;
    }

    // get delivered products from a given dr numbers
    public function get_packing_list_line($pl_ids, $order = 'DESC')
    {
        if (!$pl_ids)
        {
            return;
        }
        $this->db->select('dtl.fk_sales_delivery_id as pl_id, dtl.this_delivery, (order_dtl.unit_price - order_dtl.discount) AS unit_price, p.description, p.code, '
                . '((order_dtl.unit_price - order_dtl.discount) * dtl.this_delivery) AS amount', FALSE);
        $this->db->from('sales_delivery_detail AS dtl');
        $this->db->where_in('dtl.fk_sales_delivery_id', $pl_ids);
        $this->db->where('dtl.this_delivery >', 0);
        $this->db->join('sales_order_detail AS order_dtl', 'order_dtl.id = dtl.fk_sales_order_detail_id');
        $this->db->join('inventory_product AS p', 'p.id = order_dtl.fk_inventory_product_id');
        $this->db->order_by('dtl.fk_sales_delivery_id', $order);
        return $this->db->get()->result_array();
    }

    public function is_valid_delivery_line($id, $pl_id)
    {
        $_id = is_array($id) ? $id : [$id];
        $result = $this->db->select('DISTINCT id', FALSE)->from(self::TABLE_NAME_DETAIL)->where(['fk_sales_delivery_id' => $pl_id])->where_in('id', $_id)->get();
        return $result->num_rows() === count($_id);
    }

    /*
      | -------------------------------------------------------------------
      |   CREDIT MEMO FUNCTIONS
      | -------------------------------------------------------------------
      |
      |
     */

    public function credit_memo($pl_id, $include_customer_details = FALSE)
    {

        $credit_memo = $this->db->select('id, delivery_id, date')->from('cm')->where('delivery_id', $pl_id)->get()->row_array();
        $credit_memo['other_fees'] = array_key_exists('id', $credit_memo) ? $this->credit_memo_others($credit_memo['id']) : [];
        $credit_memo['returned'] = $this->credit_memo_returns($pl_id);
        if($include_customer_details)
        {
            $this->load->model('m_customer');
            $this->load->library('subquery');
            $this->db->select('fk_sales_customer_id AS id')->from('sales_order');
            $sub = $this->subquery->start_subquery('where');
            $sub->select('fk_sales_order_id')->from('sales_delivery')->where('id', $pl_id);
            $this->subquery->end_subquery('id', '=');
            $customer = $this->db->get()->row_array();
            $temp = $this->m_customer->get(FALSE, ['customer.id' => $customer['id']]);
            $credit_memo['customer'] = $temp[0];
        }
        return $credit_memo;
    }

    public function credit_memo_returns($pl_id)
    {
        $this->db->select('returns.quantity, returns.remarks, delivery.id AS item_delivery_id, '
                . 'orders.unit_price AS product_unit_price, orders.discount AS product_unit_discount, '
                . 'products.description AS product, products.code AS product_code, products.formulation_code AS product_formulatin_code, units.description AS product_unit_description');
        $this->db->from('sales_delivery_detail AS delivery');
        $this->db->join('cm_returns AS returns', 'returns.item_delivery_id = delivery.id', 'left');
        $this->db->join('sales_order_detail AS orders', 'orders.id = delivery.fk_sales_order_detail_id');
        $this->db->join('inventory_product AS products', 'products.id = orders.fk_inventory_product_id');
        $this->db->join('inventory_unit AS units', 'units.id = products.fk_unit_id');
        $this->db->where('delivery.fk_sales_delivery_id', $pl_id);
        return $this->db->get()->result_array();
    }

    public function credit_memo_others($cm_id)
    {
        $this->db->select('cm.description, cm.amount, cm.remarks');
        $this->db->from('cm_other_fees AS cm');
        $this->db->where('cm.cm_id', $cm_id);
        return $this->db->get()->result_array();
    }

    public function create_credit_memo($data)
    {
        $this->db->trans_begin();

        $this->db->insert('cm', $data['cm']);

        $id = $this->db->insert_id();

        if(array_key_exists('returns', $data))
        {
            $this->create_credit_memo_returns($id, $data['returns']);
        }

        if(array_key_exists('others', $data))
        {
            $this->create_credit_memo_other_fees($id, $data['others']);
        }

        $this->db->update('sales_delivery', ['credit_memo_amount' => $this->calculate_credit_memo_total($data)], ['id'=> $data['cm']['delivery_id']]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;    
        
    }

    public function update_credit_memo($data)
    {
        $this->db->trans_begin();

        $id_query = $this->db->select('id')->from('cm')->where('delivery_id', $data['cm']['delivery_id'])->get()->row_array();

        if(!array_key_exists('others', $data) && !array_key_exists('returns', $data))
        {
            $this->db->delete('cm', ['delivery_id' => $data['cm']['delivery_id']]);
        }
        else
        {
            $id = $id_query['id'];

            $this->db->update('cm', $data['cm'], ['id'=>$id]);

            $this->delete_credit_memo_returns($id);
            $this->delete_credit_memo_other_fees($id);

            if(array_key_exists('returns', $data))
            {
                $this->create_credit_memo_returns($id, $data['returns']);
            }

            if(array_key_exists('others', $data))
            {
                $this->create_credit_memo_other_fees($id, $data['others']);
            }
            
        }
        $this->db->update('sales_delivery', ['credit_memo_amount' => $this->calculate_credit_memo_total($data)], ['id'=> $data['cm']['delivery_id']]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;  
    }

    public function credit_memo_summary($pl_id)
    {
        $data = [];

        $this->db->select('SUM((order_detail.unit_price-order_detail.discount)*cm_returns.quantity) AS amount', FALSE);
        $this->db->from('pm_cm AS cm');
        $this->db->join('pm_cm_returns AS cm_returns', 'cm_returns.cm_id = cm.id', 'left');
        $this->db->join('sales_delivery_detail AS delivery_detail', 'delivery_detail.id = cm_returns.item_delivery_id', 'left');
        $this->db->join('sales_order_detail AS order_detail', 'order_detail.id = delivery_detail.fk_sales_order_detail_id', 'left');
        $this->db->where('cm.delivery_id', $pl_id);
        $result = $this->db->get()->row_array();
        $data['returns_total_amount'] = $result['amount'];

        $this->db->select('SUM(others.amount) AS amount', FALSE);
        $this->db->from('pm_cm AS cm');
        $this->db->join('pm_cm_other_fees AS others', 'others.cm_id = cm.id', 'left');
        $this->db->where('cm.delivery_id', $pl_id);
        $result = $this->db->get()->row_array();
        $data['others_total_amount'] = $result['amount'];

        return $data;
    }

    public function create_credit_memo_other_fees($cm_id, $data)
    {
        $this->load->helper('pmarray');
        array_walk($data, 'insert_prop', ['name'=>'cm_id', 'value' => $cm_id]);
        $this->db->insert_batch('cm_other_fees', $data);
    }

    public function create_credit_memo_returns($cm_id, $data)
    {
        $this->load->helper('pmarray');
        array_walk($data, 'insert_prop', ['name'=>'cm_id', 'value' => $cm_id]);
        $this->db->insert_batch('cm_returns', $data);
    }


    public function delete_credit_memo_returns($cm_id)
    {
        return $this->db->delete('cm_returns', ['cm_id' => $cm_id]);
    }

    public function delete_credit_memo_other_fees($cm_id)
    {
        return $this->db->delete('cm_other_fees', ['cm_id' => $cm_id]);
    }

    public function has_credit_memo($pl_id)
    {
        return $this->db->select('id')->from('cm')->where('delivery_id', $pl_id)->get()->num_rows() === 1;
    }

    public function calculate_credit_memo_total($cm_data)
    {
        $total = 0;
        if(array_key_exists('others', $cm_data))
        {
            // get sum of other fees
            $total += array_sum(array_column($cm_data['others'], 'amount'));
        }
        if(array_key_exists('returns', $cm_data))
        {
            // get order detail from item deliveries
            $this->db->select('order_detail.unit_price, order_detail.discount, item_delivery.id');
            $this->db->from('sales_delivery_detail AS item_delivery');
            $this->db->join('sales_order_detail AS order_detail', 'order_detail.id = item_delivery.fk_sales_order_detail_id');
            $this->db->where_in('item_delivery.id', array_column($cm_data['returns'], 'item_delivery_id'));
            $details = array_column($this->db->get()->result_array(), NULL, 'id');
            array_map(function($var) USE(&$total,$details){
                $temp = $details[$var['item_delivery_id']];
                $total += ($temp['unit_price']-$temp['discount']) * $var['quantity'];
            }, $cm_data['returns']);
        }
        return $total;
    }

    /* MISC FUNCTIONS */

    public function is_valid_customer_deliveries($pl_ids, $customer_id, $approved_only = TRUE)
    {
        $this->db->select('del.id')->from('sales_delivery AS del')->join('sales_order AS ord', 'ord.id = del.fk_sales_order_id');
        $this->db->where_in('del.id', $pl_ids)->where('ord.fk_sales_customer_id', $customer_id);
        if($approved_only)
        {
            $this->db->where('del.status', M_Status::STATUS_DELIVERED);
        }
        return $this->db->get()->num_rows() === count($pl_ids);
    }

    public function get_delivered_items($delivery_id)
    {
        return $this->db->get_where(self::TABLE_NAME_DETAIL, ['fk_sales_delivery_id' => $delivery_id])->result_array();
    }

}

<?php

class M_Counter_Receipt extends CI_Model {

    const TABLE_NAME_GENERAL = 'sales_counter_receipt';
    const TABLE_NAME_DETAIL = 'sales_counter_receipt_detail';

    function add($data) {
        $details = $data['details'];
        unset($data['details']);
        if ($this->db->insert(self::TABLE_NAME_GENERAL, $data)) {
            $id = $this->db->insert_id();
            foreach ($details as &$d) {
                $d['fk_sales_counter_receipt_id'] = $id;
            }
            $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);
            return $id;
        }
        return FALSE;
    }

    function update($counter_id, $data) {
        $details = $data['details'];
        unset($data['details']);
        $this->db->where('id', $counter_id);
        if ($this->db->update(self::TABLE_NAME_GENERAL, $data)) {
            foreach ($details as &$d) {
                $d['fk_sales_counter_receipt_id'] = $counter_id;
            }

            $active_details = array_map(function ($var) {
                return $var['id'];
            }, $details);

            if ($active_details || !empty($active_details)) {
                $this->db->where('fk_sales_counter_receipt_id', $counter_id);
                $this->db->where_not_in('id', $active_details);
                $this->db->delete(self::TABLE_NAME_DETAIL);
            }

            $this->db->update_batch(self::TABLE_NAME_DETAIL, $details, 'id');
            return TRUE;
        }
        return FALSE;
    }

    function get($with_details = FALSE, $search_token = array(), $filter = array(), $limit = 999, $offset = 0) {
        $this->db->select('counter.*, DATE_FORMAT(counter.date, "%M %d, %Y") as formatted_date, SUM(deliv_detail.amount) as total_amount, customer.company_name, customer.address as customer_address', FALSE);
        $this->db->from(self::TABLE_NAME_GENERAL . ' as counter');
        $this->db->join(self::TABLE_NAME_DETAIL . ' as counter_detail', 'counter_detail.fk_sales_counter_receipt_id = counter.id');
        $this->db->join('sales_customer as customer', 'counter.fk_sales_customer_id = customer.id');
        $this->db->join('sales_delivery as delivery', 'counter_detail.fk_sales_delivery_id = delivery.id');
        $this->db->join('sales_delivery_detail as deliv_detail', 'deliv_detail.fk_sales_delivery_id = delivery.id');
        $this->db->group_by('counter.id');
        if ($search_token) {
            $this->db->like($search_token['category'], $search_token['token'], 'both');
        }
        if (!empty($filter)) {
            $this->db->where($filter);
        }
        $this->db->order_by('counter.id', 'DESC');
        $data = $this->db->limit($limit, $offset)->get()->result_array();
        if (!$with_details) {
            return $data;
        }
        $ids = !empty($data) || $data ? array_map(function($var) {
                    return $var['id'];
                }, $data) : 0;
        $this->db->select('counter_detail.id, counter_detail.fk_sales_delivery_id, counter_detail.fk_sales_counter_receipt_id, '
                . ' SUM(deliv_detail.amount) as amount, delivery.ptn_number, delivery.date', FALSE);
        $this->db->from(self::TABLE_NAME_DETAIL . ' as counter_detail');
        $this->db->join('sales_delivery as delivery', 'counter_detail.fk_sales_delivery_id = delivery.id');
        $this->db->join('sales_delivery_detail as deliv_detail', 'deliv_detail.fk_sales_delivery_id = delivery.id');
        $this->db->group_by('counter_detail.id');
        $this->db->where_in('counter_detail.fk_sales_counter_receipt_id', $ids);
        $details = $this->db->get()->result_array();
        foreach ($data as &$da) {
            $x = 0;
            $formatted_details = array();
            foreach ($details as $de) {
                if ($da['id'] === $de['fk_sales_counter_receipt_id']) {
                    $formatted_details['id'][$x] = $de['id'];
                    $formatted_details['fk_sales_delivery_id'][$x] = $de['fk_sales_delivery_id'];
                    $formatted_details['amount'][$x] = $de['amount'];
                    $formatted_details['ptn_number'][$x] = $de['ptn_number'];
                    $formatted_details['date'][$x] = $de['date'];
                    $x++;
                }
                $da['details'] = $formatted_details;
            }
        }
        return $data;
    }
    
    public function mark_printed($id) {
        return $this->db->where('id', $id)->update(self::TABLE_NAME_GENERAL, array('is_printed' => 1));
    }

    public function is_printed($id) {
        return $this->db->select('id')->get_where(self::TABLE_NAME_GENERAL, array('id' => $id, 'is_printed' => 1))->num_rows() > 0;
    }

}

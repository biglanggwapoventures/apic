<?php

class M_Agent extends CI_Model {

    private $column_names = array(
        'id' => 'agent.id'
    );

    const AGENT_ID = 'id';
    //constants to be accessed by controllers
    const AGENT_UNIT_QUOTA_COLUMN = 'fk_inventory_unit_id';

    public function add($data) {
        return $this->db->insert('sales_agent', $data);
    }

    public function update($agent_id, $data) {
        $this->db->where('id', $agent_id);
        return $this->db->update('sales_agent', $data);
    }

    public function get($search_token = FALSE, $filter = array(), $limit = 999, $offset = 0) {
        $this->db->select('agent.*, unit.description as unit_description');
        $this->db->from('sales_agent as agent');
        $this->db->join('inventory_unit as unit', 'agent.fk_inventory_unit_id = unit.id','left');
        if ($search_token) {
            $this->db->like('name', $search_token, 'both');
        }
        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $this->db->where($this->column_names[$key], $value);
            }
        }
        $this->db->order_by('agent.id', 'DESC');
        $agents = $this->db->limit($limit, $offset)->get()->result_array();
        if (!$agents) {
            return FALSE;
        }
        return $agents;
    }

    function delete($ID) {
        if ($ID) {
            $this->db->where('id', $ID);
            return $this->db->delete('sales_agent');
        }
        return FALSE;
    }

}

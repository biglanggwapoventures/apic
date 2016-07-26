<?php

class M_Trip_tickets extends CI_Model {

    protected $table = 'tracking_trip_ticket';

    public function __construct()
    {
        parent::__construct();
    }

    public function all($search = [], $wildcards = [])
    {
        $this->db->select('DISTINCT tt.*, sc.company_name AS company, st.trucking_name AS trucking , ta.name AS trucking_assistant', FALSE);
        $this->db->from('tracking_trip_ticket AS tt');
        $this->db->join('sales_customer AS sc', 'sc.id = tt.fk_sales_customer_id');
        // $this->db->where('sc.for_trucking',1);
        $this->db->join('sales_trucking AS st', 'st.id = tt.fk_sales_trucking_id');
        $this->db->join('trucking_assistants AS ta', 'ta.id = tt.fk_trucking_assistant_id');
        if(!empty($search)){
           $this->db->where($search);
        }
        if(!empty($wildcards)){
            $this->db->like($wildcards);
        }

        $this->db->order_by('tt.id', 'DESC');
        return $this->db->get($this->table)->result_array();
    }

    public function get($id)
    {   
        $this->db->select('tt.*, cust.company_name AS customer, truck.trucking_name AS truck_name, truck.driver AS truck_driver, assistant.name AS truck_assistant');
        $this->db->from('tracking_trip_ticket AS tt');
        $this->db->join('sales_customer AS cust', 'cust.id = tt.fk_sales_customer_id');
        $this->db->join('sales_trucking AS truck', 'truck.id = tt.fk_sales_trucking_id');
        $this->db->join('trucking_assistants AS assistant', 'assistant.id = tt.fk_trucking_assistant_id');

        return $this->db->get_where($this->table, ['tt.id' => $id])->row_array();
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data) ? $this->db->insert_id() : FALSE;
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function find($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function exists($id, $active = FALSE)
    {
        if($active === TRUE){
            $this->db->where('status', 'a');
        }
        return $this->db->select('id')->from($this->table)->where('id', $id)->get()->num_rows() > 0;
    }

    public function has_unique_name($name, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('name')->from($this->table)->where('name', $name)->get()->num_rows() === 0;
    }

    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('agent_code')->from($this->table)->where('agent_code', $code)->get()->num_rows() === 0;
    }

}

<?php

class M_Agent extends CI_Model {

    protected $table = 'sales_agent';

    public function __construct()
    {
        parent::__construct();
    }

    public function all($search = [], $wildcards = [])
    {
        if(!empty($search)){
           $this->db->where($search);
        }
        if(!empty($wildcards)){
            $this->db->like($wildcards);
        }
        $this->db->order_by('contact_number', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    public function get($id)
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

}

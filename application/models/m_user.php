<?php

class M_user extends CI_Model {

	protected $table = 'account';

    public function __construct() {
        parent::__construct();
    }

    public function all()
    {
    	$this->db->order_by('FirstName', 'ASC');
    	return $this->db->get($this->table)->result_array();
    }

    public function get($id, $include_module_access = FALSE)
    {
        $user = $this->db->get_where($this->table, ['id' => $id])->row_array(); 
        if($include_module_access && $user['TypeID'] != M_Account::TYPE_ADMIN)
        {
            $this->db->select('*');
            $this->db->from('module_access')->where('fk_account_id', $id);
            $user['module_access'] = $this->db->get()->row_array();
        }
        return $user;
    	
    }

    public function update($id, $data)
    {
        $this->db->trans_begin();

    	$this->db->update($this->table, $data['user'], ['id' => $id]);
        if(isset($data['module_access']))
        {
            $this->db->delete('module_access', ['fk_account_id' => $id]);
            $data['module_access']['fk_account_id'] = $id;
            $this->db->insert('module_access', $data['module_access']);
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

    public function create($data)
    {
        $this->db->trans_begin();

    	$inserted = $this->db->insert($this->table, $data['user']);
        if(isset($data['module_access']))
        {
            $data['module_access']['fk_account_id'] = $this->db->insert_id();
            $this->db->insert('module_access', $data['module_access']);
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

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function is_locked($username)
    {
        return $this->db->select('id')->from($this->table)->where([
            'Username' => $username,
            'Locked' => 1
        ])->get()->num_rows() === 1;
    }

}

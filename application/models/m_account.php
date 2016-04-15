<?php

class M_Account extends CI_Model {

    const TABLE_NAME_GENERAL = 'account';
    const TABLE_NAME_MODULE_ACCESS = 'module_access';
    const TYPE_ADMIN = 1;
    const TYPE_NORMAL = 2;

    private static $_modules = array('inventory', 'sales', 'purchases', 'production', 'warehousing', 'accounting', 'reports');
    private static $_account_fields = array('Username', 'Password', 'Email', 'TypeID', 'FirstName', 'LastName');

    public static function get_modules() {
        return self::$_modules;
    }

    public static function get_account_fields() {
        return self::$_account_fields;
    }

    //register username, password, typeid, first name, last name and email
    public function register($data) {
        $this->db->trans_begin();

        $this->db->insert(self::TABLE_NAME_GENERAL, $data);
        $user_id = $this->db->insert_id();
        $this->db->insert(self::TABLE_NAME_MODULE_ACCESS, array('fk_account_id' => $user_id));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return $user_id;
        }
    }

    //authentcates and returns id, typeid and name if authentic
    public function is_authentic($username, $password) {
        return $this->db->select('ID as id, TypeID as typeid, CONCAT(FirstName," ",LastName) as name, Avatar, role', FALSE)
                        ->get_where(self::TABLE_NAME_GENERAL, array('Username' => $username, 'Password' => md5($password)), 1)->row_array();
    }

    //checks if the account is existing
    public function is_existing($username = '', $id = FALSE) {
        return $this->db->select('ID')
                        ->from(self::TABLE_NAME_GENERAL)
                        ->where('Username', $username)
                        ->or_where('ID', $id)
                        ->limit(1)->get()->num_rows() > 0;
    }

    //retrieves all user or filter by given id  
    public function get($id = FALSE) {
        $this->load->helper('array');
        if ($id) {
            $user_data = $this->db->where('ID', $id)->get(self::TABLE_NAME_GENERAL)->row_array();
        } else {
            $user_data = $this->db->select('ID, Username, Password, Email, TypeID, FirstName, LastName, CONCAT(FirstName," ",LastName) as Name', FALSE)
                            ->get(self::TABLE_NAME_GENERAL)->result_array();
        }
        if (!$user_data) {
            return FALSE;
        }
        $module_access = $this->get_module_access(array_map(function($var) {
                    if ((int) $var['TypeID'] !== (int) M_Account::TYPE_ADMIN) {
                        return $var['ID'];
                    }
                }, $user_data));
        foreach ($user_data as &$data) {
            foreach ($module_access as $key => $access) {
                if ((int) $access['fk_account_id'] === (int) $data['ID']) {
                    $data['module_access']['rights'] = elements(self::$_modules, $access);
                    unset($module_access[$key]);
                }
            }
        }
        return $user_data;
    }

    //get module access
    public function get_module_access($user_id = array()) {
        if (is_array($user_id)) {
            return $this->db->where_in('fk_account_id', $user_id)->get(self::TABLE_NAME_MODULE_ACCESS)->result_array();
        } else if (is_numeric($user_id)) {
            return $this->db->select(implode(",", self::$_modules))->where('fk_account_id', $user_id)->get(self::TABLE_NAME_MODULE_ACCESS)->row_array();
        }
    }

    //save module access
    public function update_module_access($user_id, $new_mod_access) {
        $this->db->where('fk_account_id', $user_id);
        return $this->db->update(self::TABLE_NAME_MODULE_ACCESS, $new_mod_access);
    }

    //updte user details
    public function update($user_id, $field, $value) {
        $this->db->where('id', $user_id);
        return $this->db->update(self::TABLE_NAME_GENERAL, array($field => $value));
    }

    //checks if a user has access to a specific module
    public function has_access($userid, $module) {
        if (!$userid || !numeric($userid) || !in_array($module, self::$_modules)) {
            return FALSE;
        }
        $has_access = $this->db->select($module)->from(self::TABLE_NAME_MODULE_ACCESS)->where('fk_account_id', $userid)->row_array();
        return (int) $has_access[$module] === 1;
    }

    public function get_current_user() {
        return $this->session->userdata('user_id');
    }

    public function get_info($ID = FALSE) {
        return $this->db->select('Username, CONCAT(FirstName," ",LastName) as Name, TypeID, Email', FALSE)
                        ->get_where('account', array('ID' => $ID))->row_array();
    }

    public function get_type($id) {
        if ($id) {
            $data = $this->db->select('TypeID')
                            ->get_where('account', array('id' => $id))->row_array();
            return $data ? $data['TypeID'] : FALSE;
        }
        return FALSE;
    }

    public function is_admin_account($id) {
        $data = $this->db->select('TypeID')->get_where(self::TABLE_NAME_GENERAL, array('ID' => $id), 1)->row_array();
        return (int) $data['TypeID'] === (int) self::TYPE_ADMIN;
    }

    public function is_admin() {
        return (int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN;
    }

}

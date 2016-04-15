<?php

class Manage_Users extends PM_Controller_v2 {

    const SUBJECT = 'user';

    private $_content = array();

    public function __construct() {
        parent::__construct();

        if (!$this->m_account->is_admin()) {
            show_404();
        }

        $this->lang->load('user');
        $this->load->model(array('m_account'));
        $this->set_content_title($this->lang->line('title'));
        $this->set_content_subtitle($this->lang->line('subtitle_manage_user'));
        $this->set_active_nav(NAV_USERS);
    }

    public function index() {
        $this->add_javascript(array('users/manage-users.js', 'bootstrap-editable.min.js'));
        $this->add_css('bootstrap-editable.css');
        $user_listing = $this->m_account->get();
        $this->_content['user_listing'] = $user_listing ? $user_listing : array();
        $this->_content['normal_user_listing'] = $user_listing ? array_filter($user_listing, function($var) {
                    if ((int) $var['TypeID'] === (int) M_Account::TYPE_NORMAL) {
                        return $var;
                    }
                }) : array();
        $this->set_content('users/manage-users', $this->_content);
        $this->generate_page();
    }

    public function a_update_module_access() {
        $this->output->set_content_type('json');
        if (!$this->input->post('user_id') || !is_numeric($this->input->post('user_id')) || $this->m_account->is_admin_account($this->input->post('user_id'))) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Specify user id!')));
            return;
        }
        $this->load->helper('array');
        //filter input
        $updated_access = elements(M_Account::get_modules(), $this->input->post(), 0);
        $updated = $this->m_account->update_module_access($this->input->post('user_id'), $updated_access);
        $this->output->set_output(json_encode($this->response(!$updated, $updated ? 'Done!' : 'Error!')));
    }

    public function a_add() {
        $this->load->helper('array');
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('Username', 'Username', 'required|is_unique[account.Username]');
        $this->form_validation->set_rules('FirstName', 'First Name', 'required');
        $this->form_validation->set_rules('LastName', 'Last Name', 'required');
        $this->form_validation->set_rules('Password', 'Password', 'required]');
        $this->form_validation->set_rules('ConfirmPassword', 'Password Confirmation', 'required|matches[Password]');
        $this->form_validation->set_rules('Email', 'Email', 'callback__validate_email');
        $this->form_validation->set_rules('TypeID', 'Role', 'required|callback__validate_typeid');
        if ($this->form_validation->run()) {
            $user_data = elements(M_Account::get_account_fields(), $this->input->post(), '');
            $user_data['Password'] = md5($user_data['Password']);
            $id = $this->m_account->register($user_data);
            if ($id) {
                $data = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('id' => $id));
            } else {
                $data = $this->response(TRUE, $this->m_message->add_error(self::SUBJECT));
            }
            $this->output->set_output(json_encode($data));
            return;
        }
        $error_msg = array();
        foreach (M_Account::get_account_fields() as $field) {
            $error_msg[$field] = form_error($field, ' ', ' ');
        }
        $this->output->set_output(json_encode($this->response(TRUE, $error_msg)));
    }

    public function a_edit_details() {
        $this->load->helper(array('email', 'array'));
        $this->output->set_content_type('json');
        //do not accept any empty value
        if (!strlen(trim($this->input->post('value')))) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Do not leave any fields empty')));
            return;
        }
        //validate field name to update
        if (!in_array($this->input->post('name'), M_Account::get_account_fields())) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Error')));
            return;
        }
        //verify type id
        if ($this->input->post('name') === 'TypeID' && !in_array($this->input->post('value'), array(M_Account::TYPE_ADMIN, M_Account::TYPE_NORMAL))) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Error')));
            return;
        }
        //validate email
        if ($this->input->post('name') === 'Email' && !valid_email($this->input->post('value'))) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Invalid Email!')));
            return;
        }
        //update proper
        if ($this->m_account->update($this->input->post('pk'), $this->input->post('name'), $this->input->post('value'))) {
            $this->output->set_output(json_encode($this->response(FALSE, $this->m_message->update_success(self::SUBJECT))));
            return;
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Error')));
        }
    }

    function _validate_email($email = FALSE) {
        $this->load->helper('email');
        return $email ? valid_email($email) : TRUE;
    }

    function _validate_typeid($typeid) {
        return in_array($typeid, array(M_Account::TYPE_ADMIN, M_Account::TYPE_NORMAL));
    }

}

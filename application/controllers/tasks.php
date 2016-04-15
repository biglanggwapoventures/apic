<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Functions prefixed with "a_" are accessed via AJAX.
 *
 * @author adriannatabio
 */
class Tasks extends PM_Controller_v2 {

    CONST SUBJECT = 'task';

    function __construct() {
        parent::__construct();
        $current_user = $this->session->userdata('user_id');
        /* expired or illegal session is sent back to login screen */
        if (!$current_user) {
            redirect('login');
        }
        $this->load->model(array('m_task'));
    }

    public function get() {
        
    }

    public function a_add() {
        $this->output->set_content_type('json');
        $response = $this->response(FALSE);
        $error_messages = array();
        $this->form_validation->set_rules('assigned_to', 'Assigned To', 'required|callback__is_valid_user');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('details', 'Task Details', 'required');
        if ($this->form_validation->run()) {
            $this->load->helper('array');
            $input = elements(array('assigned_to', 'title', 'details', 'date_due', 'is_private'), $this->input->post(), NULL);
            $input['date_due'] = $input['date_due'] !== NULL ? date('Y-m-d G:i', strtotime($input['date_due'])) : NULL;
            $input['assigned_by'] = $this->session->userdata('user_i    d');
            $added = $this->m_task->add($input);
            if ($added) {
                $response['message'] = $this->m_message->add_success(self::SUBJECT);
            } else {
                $response['message'] = $this->m_message->add_error(self::SUBJECT);
                $response['error_flag'] = TRUE;
            }
        } else {
            form_error('assigned_to') ? $error_messages[] = array('field' => 'assigned_to', 'message' => form_error('assigned_to', ' ', ' ')) : '';
            form_error('title') ? $error_messages[] = array('field' => 'title', 'message' => form_error('title', ' ', ' ')) : '';
            form_error('details') ? $error_messages[] = array('field' => 'details', 'message' => form_error('details', ' ', ' ')) : '';
            $response['error_flag'] = TRUE;
            $response['message'] = "Please fill up all the necessary fields.";
            $response['data'] = $error_messages;
        }
        $this->output->set_output(json_encode($response));
    }

    public function a_delete() {
        
    }

    /* FORM VALIDATION CALLBACKS */

    function _is_valid_user($user_id = FALSE) {
        $this->load->model('m_account');
        if ($this->m_account->is_existing(FALSE, $user_id)) {
            return TRUE;
        }
        $this->form_validation->set_message('_is_valid_user', 'The %s field must contain a valid user.');
        return FALSE;
    }

}

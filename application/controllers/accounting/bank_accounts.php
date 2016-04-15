<?php

class Bank_Accounts extends PM_Controller
{

    const TITLE = 'Accounting';
    const SUBTITLE = 'Bank Accounts';
    const SUBJECT = 'bank account';

    private $viewpage_settings = array();
    private $_fields = array(
        array(
            'name' => 'bank_name',
            'label' => 'Bank Name',
            'rules' => 'required'
        ), array(
            'name' => 'bank_branch',
            'label' => 'Branch',
            'rules' => 'required'
        ), array(
            'name' => 'account_number',
            'label' => 'Account Number',
            'rules' => 'required'
        )
    );

    public function __construct()
    {
        parent::__construct();
        $this->set_active_nav(NAV_ACCOUNTING);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->load->model(array('accounting/m_bank_account'));
        $this->load->helper(array('view', 'array'));
        $this->add_javascript(array('bootstrap-editable.min.js', 'accounting-bank-accounts/manage.js'));
        $this->add_css('bootstrap-editable.css');
    }

    public function index()
    {
        $this->set_content('accounting/bank-accounts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function a_get()
    {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->output->set_status_header('200')->set_content_type('json');
        $data = $this->m_bank_account->get();
        if ($data) {
            $response = $this->response(FALSE, $this->m_message->data_fetch_success(self::SUBJECT), $data);
        } else {
            $response = $this->response(TRUE, $this->m_message->data_fetch_error(self::SUBJECT));
        }
        $this->output->set_output(json_encode($response));
        return;
    }

    public function a_add()
    {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->output->set_status_header('200')->set_content_type('json');
        foreach ($this->_fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if ($this->form_validation->run()) {
            $added = $this->m_bank_account->add(elements(array_map(function($var) {
                                return $var['name'];
                            }, $this->_fields), $this->input->post(), ''));
            if ($added) {
                $response = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('id' => $added));
            } else {
                $response = $this->response(FALSE, $this->m_message->add_error(self::SUBJECT));
            }
        } else {
            $response = $this->response(TRUE, explode(",", validation_errors(" ", ",")));
        }
        $this->output->set_output(json_encode($response));
        return;
    }

    public function a_update()
    {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->form_validation->set_rules('name', '', 'callback_name_check');
        $this->form_validation->set_rules('value', '', 'required');
        $this->form_validation->set_rules('pk', '', 'is_numeric');
        if ($this->form_validation->run()) {
            $this->output->set_status_header('200')->set_content_type('json');
            $data = elements(array('value', 'name', 'pk'), $this->input->post(), '');
            $updated = $this->m_bank_account->update($data['pk'], $data['name'], $data['value']);
            if ($updated) {
                $response = $this->response(FALSE, $this->m_message->update_success(self::SUBJECT));
            } else {
                $response = $this->response(TRUE, $this->m_message->update_error(self::SUBJECT));
            }
            $this->output->set_output(json_encode($response));
            return;
        } else {
            if (form_error('value')) {
                $response = $this->response(TRUE, 'You cannot leave this field empty');
                $this->output->set_status_header('200')->set_content_type('json')->set_output(json_encode($response));
                return;
            }
            if (form_error('name') || form_error('pk')) {
                $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
                return;
            }
        }
    }

    public function a_delete()
    {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->form_validation->set_rules('pk', '', 'is_numeric');
        if ($this->form_validation->run()) {
            $this->output->set_status_header('200')->set_content_type('json');
            $deleted = $this->m_bank_account->delete($this->input->post('pk'));
            if ($deleted) {
                $response = $this->response(FALSE, $this->m_message->delete_success(self::SUBJECT));
            } else {
                $response = $this->response(TRUE, $this->m_message->delete_error(self::SUBJECT));
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
        }
        return;
    }

    function name_check()
    {
        return in_array($this->input->post('name'), array_map(function($var) {
                    return $var['name'];
                }, $this->_fields));
    }

}

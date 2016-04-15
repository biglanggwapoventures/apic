<?php

class Suppliers extends PM_Controller_v2 {

    const SUBJECT = 'supplier';

    private $_fields = array(
        array(
            'name' => 'name',
            'label' => 'Supplier Name',
            'rules' => 'required'
        ),
        array(
            'name' => 'tin_number',
            'label' => 'TIN Number',
            'rules' => ''
        ),
        array(
            'name' => 'address',
            'label' => 'Address',
            'rules' => 'required'
        ),
        array(
            'name' => 'contact_number',
            'label' => 'Contact Number',
            'rules' => 'required'
        ),
        array(
            'name' => 'contact_person',
            'label' => 'Contact Person',
            'rules' => ''
        )
    );

    function __construct() {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Supplier');
        $this->load->model('purchases/m_supplier');
        $this->add_javascript('purchases-suppliers/manage.js');
        $this->load->helper('array');
    }

    function index() {
        $this->add_javascript('bootstrap-editable.js');
        $this->add_css('bootstrap-editable.css');
        $this->set_content('purchases/suppliers');
        $this->generate_page();
    }

    function _validate_input() {
        foreach ($this->_fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if ($this->form_validation->run()) {
            $valid_input = elements(array_map(function($var) {
                        return $var['name'];
                    }, $this->_fields), $this->input->post(), '');
            return $this->response(FALSE, '', $valid_input);
        } else {
            $error_msg = array();
            foreach ($this->_fields as $field) {
                if (form_error($field['name'])) {
                    $error_msg[] = array(
                        'field_name' => $field['name'],
                        'error_message' => form_error($field['name'], " ", " ")
                    );
                }
            }
            return $this->response(TRUE, $error_msg);
        }
    }

    function _add($data) {
        $insert_id = $this->m_supplier->add($data);
        if ($insert_id) {
            return $this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('id' => $insert_id));
        }
        return $this->response(TRUE, $this->m_message->add_error(self::SUBJECT));
    }

    function _update() {
        if (in_array($this->input->post('name'), array_map(function($var) {
                            return $var['name'];
                        }, $this->_fields)) && strlen(trim($this->input->post('value'))) && is_numeric($this->input->post('pk'))) {
            if ($this->m_supplier->update($this->input->post('name'), $this->input->post('value'), $this->input->post('pk'))) {
                return $this->response(FALSE, $this->m_message->update_success(self::SUBJECT));
            } else {
                return $this->response(TRUE, $this->m_message->update_error(self::SUBJECT));
            }
        }
        return $this->response(TRUE, 'Do not leave any fields empty.');
    }

    function _delete() {
        if ((int) $this->session->userdata('type_id') !== (int) M_Account::TYPE_ADMIN) {
            return $this->response(TRUE, sprintf('Insufficient permission.', self::SUBJECT));
        }
        if ($this->m_supplier->delete((int) $this->input->post('pk'))) {
            return $this->response(FALSE, $this->m_message->delete_success(self::SUBJECT));
        }
        return $this->response(TRUE, $this->m_message->delete_error(self::SUBJECT));
    }

    public function a_do_action($method) {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $response = '';
        switch ($method) {
            case 'add':
                $data = $this->_validate_input();
                $response = $data['error_flag'] ? $data : $this->_add($data['data']);
                break;
            case 'update':
                $response = $this->_update();
                break;
            case 'delete':
                $response = $this->_delete();
                break;
            default:
                $response = $this->response(TRUE, 'We are not sure how to process your request.');
                break;
        }
        $this->output->set_status_header('200')->set_content_type('json')->set_output(json_encode($response));
        return;
    }

    public function a_get() {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->output->set_status_header('200')->set_content_type('json');
        $data = $this->m_supplier->get($this->input->post('supplier_id'));
        if ($data) {
            $response = $this->response(FALSE, $this->m_message->data_fetch_success(self::SUBJECT), $data);
        } else {
            $response = $this->response(TRUE, $this->m_message->data_fetch_error(self::SUBJECT));
        }
        $this->output->set_output(json_encode($response));
        return;
    }

}

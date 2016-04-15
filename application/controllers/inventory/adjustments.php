<?php

class Adjustments extends PM_Controller_v2 {

    const SUBJECT = 'stock adjusment';

    private $_content = array();

    public function __construct() {
        parent::__construct();
        if (!has_access('inventory')) {
            show_error('Authorization error', 401);
        }
        //page settings
        $this->set_active_nav(NAV_INVENTORY);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Stock Adjustments');
        //load assets
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('inventory-stock-adjustments/manage.js','inventory-stock-adjustments/master-list.js', 'jquery-ui.min.js', 'plugins/datetimepicker/timepicker.js'));
        $this->load->helper('view'); //load helper
        $this->load->model(array('inventory/m_adjustments','inventory/m_unit', 'inventory/m_category', 'inventory/m_class', 'inventory/m_type', 'inventory/m_product'));
        //defaults
        
        $this->_content['base_url'] = $this->_segment_url();
        $this->_content['defaults'] = array(
            'fk_inventory_product_id' => '',
            'datetime' => '',
            'reason' => '',
            'details' => array(
                array('fk_inventory_product_id' => '',
                    'quantity' => '',
                    'product_code' => 'Code',
                    'unit_description' => 'Unit'
                )
            )
        );
    }

    function _segment_url($segment = '') {
        return base_url("inventory/adjustments/{$segment}");
    }

    public function index() {
        $this->_content['entries'] = $this->m_adjustments->get();
        $this->set_content('inventory/adjustments', $this->_content);
        $this->generate_page();
    }

    public function manage() {
        if (!$this->input->get()) {
            show_error('Error 405: Method Not Allowed', 405);
        }        
        switch ($this->input->get('do')) {
            case 'add-new-stock-adjustment':
                $this->_content['form_action'] = $this->_segment_url('a_do_action/add'); //form action
                $this->_content['form_title'] = 'Update stocks';
                $this->_content['product_list'] = $this->m_product->get();
                $this->set_content('inventory/manage-adjustment', $this->_content);
                $this->generate_page();
                break;
            default:show_404();
                break;
        }
    }
    
    public function a_do_action($method, $extras = FALSE) {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $response = '';
        switch ($method) {
            case 'add':
                $input = $this->_validate_input();
                $response = $input['error_flag'] ? $input : $this->_add($input['data']);
                break;
            case 'delete':
                $response = $this->_delete($this->input->post('pk'));
                break;
            default:
                $response = $this->response(TRUE, 'We are not sure how to process your request.');
                break;
        }
        $this->output->set_status_header('200')->set_content_type('json')->set_output(json_encode($response));
        return;
    }
    
    function _add($validated_data) {
        $id = $this->m_adjustments->add($validated_data['general'], $validated_data['details']);
        if ($id) {
            $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT));
            return $this->response(FALSE, '', array('redirect' => $this->_segment_url()));
        }
        return $this->response(TRUE, $this->m_message->add_eror(self::SUBJECT));
    }
    
    function _delete($request_id) {
        if (!$this->m_account->is_admin()) {
            return $this->response(TRUE, sprintf('You do not have the privilege to delete a %s', self::SUBJECT));
        }
        $deleted = $this->m_adjustments->delete($request_id);
        if ($deleted) {
            return $this->response(FALSE, '');
        }
        return $this->response(TRUE, $this->m_message->delete_error(self::SUBJECT));
    }
    
    function _validate_input() {
        $this->load->helper('array');
        $this->load->model('inventory/m_product');
        $response = $this->response(TRUE);
        $input = $this->input->post(); //retrieve sent data
        $info['general'] = elements(array('datetime','reason'), $input);
        $info['general']['created_by'] = $this->session->userdata('user_id');
        $required_details = array('fk_inventory_product_id', 'quantity');
        //check details structure
        //check if required keys are present and check if they have the same size
        if (count(array_intersect_key(array_flip($required_details), $input['details'])) !== count($input['details'])) {
            $response['message'][] = 'Malformed data! Please reload the page and try again.';
        } elseif (count($input['details'][$required_details[0]]) !== count($input['details'][$required_details[1]])) {
            $response['message'][] = 'Please verify that you filled up every product and quantity.';
        }
        //check if all quantities are numeric
        if ($input['details'][$required_details[1]] !== array_filter($input['details'][$required_details[1]], 'is_numeric')) {
            $response['message'][] = 'Please make sure that quantities only contain numbers.';
        }
        //check if valid products
        if ($input['details'][$required_details[0]] !== array_filter($input['details'][$required_details[0]], 'is_numeric')) {
            $response['message'][] = 'Please select valid products from the list.';
        } elseif (!$this->m_product->is_valid($input['details'][$required_details[0]])) {
            $response['message'][] = 'Please select valid products from the list.';
        }
        //has errors, return messages
        if (!empty($response['message'])) {
            return $response;
        }
        $temp = array();
        $response['error_flag'] = FALSE;
        foreach ($input['details'] as $field => $arr_value) {
            foreach ($arr_value as $index => $value) {
                $temp[$index][$field] = $value;
            }
        }
        $info['details'] = $temp;
        $response['data'] = $info;
        return $response;
    }
        
    public function generate() {
        if (!$this->input->get()) {
            show_error('Error 405: Method Not Allowed', 405);
        }
        $id = $this->input->get('id');
        $adjustment = $this->m_adjustments->get_by_id($id);
        if (!$adjustment['general']) {
            show_404();
        }
        $this->load->model(array('m_account'));
        $user = $this->m_account->get_info($this->session->userdata('user_id'));
        $adjustment['admin_name'] = $user['Name'];
        $adjustment['general']['approved_by'] = ($adjustment['general']['approved_by'])?$this->m_account->get_info($adjustment['general']['approved_by']) : '';
        $data['contents'] = $adjustment;
        $this->load->view('inventory/print-adjustment', $data);
    }
    
    public function a_do_approve() {
        if (!$this->input->get()) {
            show_error('Error 405: Method Not Allowed', 405);
        }
        $id = $this->input->get('id');
        return $this->m_adjustments->approve($id);
    }

}

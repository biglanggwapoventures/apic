<?php

class Gatepass extends PM_Controller_v2 {

    const SUBJECT = 'trip ticket';

    private $_content = array();

    public function __construct() {
        parent::__construct();
        if (!has_access('warehousing')) {
            show_404();
        }
        //page settings
        $this->set_active_nav(NAV_WAREHOUSING);
        $this->set_content_title('Warehousing');
        $this->set_content_subtitle('Trip Ticket');
        //load assets
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('warehousing-gatepass/manage.js', 'jquery-ui.min.js', 'plugins/datetimepicker/timepicker.js'));
        $this->load->helper('view'); //load helper
        $this->load->model('warehousing/m_gatepass'); //load purchase gatepass model
        //defaults
        $this->_content['defaults'] = array(
            'truck_boy' => '',
            'exit_datetime' => '',
            'fk_sales_trucking_id' => ''
        );
    }

    function _segment_url($segment = '') {
        return base_url("warehousing/gatepass/{$segment}");
    }

    public function index() {
        $this->_content['entries'] = $this->m_gatepass->get();
        $this->set_content('warehousing/gatepass', $this->_content);
        $this->generate_page();
    }

    public function manage() {
        if (!$this->input->get()) {
            show_error('Error 405: Method Not Allowed', 405);
        }
        $this->load->model(array('sales/m_trucking', 'inventory/m_product')); //load necessary models
        $this->_content['trucking_list'] = $this->m_trucking->get(); //get trucking list
        $this->_content['product_list'] = $this->m_product->get(); //get trucking list
        switch ($this->input->get('do')) {
            case 'add-new-gatepass':
                $this->_content['form_action'] = $this->_segment_url('a_do_add'); //form action
                $this->_content['form_title'] = 'Add new gatepass'; //form title
                $this->set_content('warehousing/manage-gatepass', $this->_content);
                $this->generate_page();
                break;
            default:show_404();
                break;
        }
    }

    public function generate() {
        if (!$this->input->get()) {
            show_error('Error 405: Method Not Allowed', 405);
        }
        $id = $this->input->get('id');
        $gatepass = $this->m_gatepass->get_by_id($id);
        if (!$gatepass['general']) {
            show_404();
        }
        $data['contents'] = $gatepass;
        $this->load->view('warehousing/print-gatepass', $data);
    }

    public function a_do_add() {
        if (!$this->input->post()) {
            show_error('Error 405: Method Not Allowed', 405);
        }
        $this->output->set_content_type('json'); //set content type
        $response = $this->_validate_input();
        if ($response['error_flag']) {
            $this->output->set_output(json_encode($response));
            return;
        }
        $saved = $this->m_gatepass->add($response['data']['general'], $response['data']['details']);
        if ($saved) {
            $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT));
        } else {
            $response['error_flag'] = TRUE;
            $response['message'] = $this->m_message->add_error(self::SUBJECT);
        }
        $this->output->set_output(json_encode($response));
    }

    function _validate_input() {
        $this->load->helper('array');
        $this->load->model('inventory/m_product');
        $response = $this->response(TRUE);
        $input = $this->input->post(); //retrieve sent data
        $info['general'] = elements(array('fk_sales_trucking_id', 'exit_datetime', 'truck_boy'), $input);
        $info['general']['generated_by'] = $this->session->userdata('user_id');
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

}

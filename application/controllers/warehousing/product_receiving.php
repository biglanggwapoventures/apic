<?php

class Product_Receiving extends PM_Controller_v2 {
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
        $this->set_content_subtitle('Product Receiving');
        //load purchase receiving model
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('warehousing-receive/manage.js', 'jquery-ui.min.js', 'plugins/datetimepicker/timepicker.js'));
        $this->load->helper('view'); //load helper
        $this->load->model('warehousing/m_warehousing_receive');
        $this->load->model('warehousing/m_warehousing_product_receiving');
    }

    public function index() {
        $ID = $this->input->get("ID");
        $receiverID = $this->input->get("receiver_ID");
        $remarks = $this->input->get("remarks");
        $datetime = $this->input->get("datetime");
        //$this->_content['unreceived'] = $this->m_warehousing_receive->get_unreceived();
        
        $this->_content['product_receiving'] = $this->m_warehousing_product_receiving->retrieveWarehousingProductReceiving($ID, $receiverID,  $remarks, $datetime);
        
        $this->set_content('warehousing/product-receiving', $this->_content);
        $this->generate_page();
    }

    public function a_do_receive() {
        if (!$this->input->post()) {
            show_error('Error Code 400: Bad Request', 400);
        }
        $this->output->set_content_type('json');
        if (!is_numeric($this->input->post('pk'))) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Error on RR Id!')));
            return;
        }
        $received = $this->m_warehousing_receive->do_receive($this->input->post('pk'));
        $this->output->set_output(json_encode($this->response(!$received, $received ? 'Success!' : 'Error!')));
        return;
    }
    public function manage($productReceivingID = false) {
        
        $this->load->model(array('sales/m_trucking', 'inventory/m_product')); //load necessary models
        
        if($productReceivingID == false){
            
            $this->_content['form_action'] = $this->_segment_url('a_do_add'); //form action
            $this->_content['form_title'] = 'Add new product receiving'; //form title
            $this->_content['product_list'] = $this->m_product->get(false, array(M_Product::PRODUCT_CLASS  =>  M_Product::CLASS_FINISHED) );
            $this->set_content('warehousing/manage-product-receiving', $this->_content);
            $this->generate_page();
        }else{
            show_404();
        }
    }
    
    function _segment_url($segment = '') {
        return base_url("warehousing/product_receiving/{$segment}");
    }
    public function a_do_add() {
        if (!$this->input->post()) {
        }
        $response = array();
        
        $saved = $this->m_warehousing_product_receiving->createWarehousingProductReceiving( $this->session->userdata('user_id'), $this->input->post("remarks"), $this->input->post("datetime"));
        if ($saved) {
            $this->m_warehousing_product_receiving->insertProductReceivingProduct($this->createProductArray($saved));
            $response["data"] = $saved;
            $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT));
        } else {
            $response['error_flag'] = TRUE;
            $response['message'] = $this->m_message->add_error(self::SUBJECT);
        }
        $this->output->set_output(json_encode($response));
    }
    function createProductArray($productReceivingID){
        $products = array();
        $productDetails = $this->input->post("details");
        
        foreach($productDetails["fk_inventory_product_id"] as $key => $value){
            $products[] = array(
                "fk_product_receiving_ID" => $productReceivingID,
                "product_ID" => $value,
                "quantity" => $productDetails["quantity"][$key]
            );
        }
        return $products;
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
    public function generate() {
        $ID = $this->input->get('id');
        $receiving = $this->m_warehousing_product_receiving->retrieveWarehousingProductReceiving($ID);
        $data['contents'] = $receiving;
        $this->load->view('warehousing/print-receiving', $data);
    }
}

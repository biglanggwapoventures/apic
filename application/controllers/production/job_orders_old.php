<?php

class Job_Orders extends PM_Controller_v2 {

    const SUBJECT = 'job order';

    private $_content = array();

    public function __construct() {
        parent::__construct();
        if (!has_access('warehousing')) {
            show_404();
        }
        //page settings
        $this->set_active_nav(NAV_PRODUCTION);
        $this->set_content_title('Production');
        $this->set_content_subtitle('Job Orders');
        //load assets
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('warehousing-gatepass/manage.js', 'jquery-ui.min.js', 'plugins/datetimepicker/timepicker.js','production-job-order/manage.js'));
        $this->load->helper('view'); //load helper
        //$this->load->model('warehousing/m_job_order'); //load purchase gatepass model
        //defaults
        $this->load->model("production/m_production_job_order");
        $this->_content['defaults'] = array(
        );
    }

    function _segment_url($segment = '') {
        return base_url("production/job_orders/{$segment}");
    }

    public function index() {
        //$this->add_javascript('warehousing-receive/manage.js');
        $ID = $this->input->get("ID");
        $receiverID = $this->input->get("receiver_ID");
        $remarks = $this->input->get("remarks");
        $datetime = $this->input->get("datetime");
        $this->_content['job_order_list'] = $this->m_production_job_order->retrieveProductionJobOrder($ID, $receiverID,  $remarks, $datetime, false, "zero");
        $this->set_content('production/job-orders', $this->_content);
        $this->generate_page();
    }

    public function manage($id = false) {
        
        $this->load->model(array('inventory/m_product')); //load necessary models
        $this->_content['product_list'] = $this->m_product->get(FALSE, array(M_Product::PRODUCT_CLASS => M_Product::CLASS_FINISHED)); //get trucking list
        if($id == false){
            $this->_content['form_action'] = $this->_segment_url('a_do_add'); //form action
            $this->_content['form_title'] = 'Add new job order'; //form title
            $this->set_content('production/manage-job-order', $this->_content);
            $this->generate_page();
        }else{
            $this->_content['job_order_detail'] = $this->m_production_job_order->retrieveProductionJobOrder($id);
            //PRINTR($this->_content['job_order_detail']);exit();
            $this->_content['form_action'] = $this->_segment_url(""); //form action
            $this->_content['form_title'] = 'View job order'; //form title
            $this->set_content('production/manage-job-order', $this->_content);
            $this->generate_page();
        }
    }
    public function retrieveJobOrders(){
        $result = array(
            "data" => false,
            "error" => 0
        );
        $ID = $this->input->post("ID");
        $createdByID = $this->input->post("created_by_ID");
        $remarks = $this->input->post("remarks");
        $datetime = $this->input->post("date_assigned");
        $result["data"] = $this->m_production_job_order->retrieveProductionJobOrder($ID, $createdByID,  $remarks, $datetime);
        echo json_encode($result);
    }
    public function generate() {
        $ID = $this->input->get('id');
        $jobOrder = $this->m_production_job_order->retrieveProductionJobOrder($ID);
        $data['contents'] = $jobOrder;
        $this->load->view('production/print-job-order', $data);
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
    public function a_do_add() {
        if (!$this->input->post()) {
        }
        $response = array();
        
        $saved = $this->m_production_job_order->createProductionJobOrder( $this->session->userdata('user_id'), $this->input->post("remarks"), $this->input->post("date_assigned"), $this->input->post("assigned_to"));
        if ($saved) {
            $this->m_production_job_order->insertProductReceivingProduct($this->createProductArray($saved));
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
                "fk_job_order_ID" => $productReceivingID,
                "product_ID" => $value,
                "quantity" => $productDetails["quantity"][$key]
            );
        }
        return $products;
    }
    public function a_do_order() {
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

}

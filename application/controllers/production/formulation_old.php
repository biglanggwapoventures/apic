<?php

class Formulation extends PM_Controller_v2 {

    private $_content = array();

    const SUBJECT = 'formulation';

    public function __construct() {
        parent::__construct();
        if (!has_access('production')) {
            show_404();
        }
        //page settings
        $this->set_active_nav(NAV_PRODUCTION);
        $this->set_content_title('Production');
        $this->set_content_subtitle('Formulation');
        $this->add_javascript('production-formulation/manage.js');
        $this->_content['base_url'] = $this->_segment();
        $this->load->helper('view');
        //load formulation model and product model
        $this->load->model(array('inventory/m_product', 'production/m_formulation', 'production/m_production_job_order'));
        //$this->load->model('warehousing/m_warehousing_receive');
        //set field default values
        $this->_content['defaults'] = array(
            'fk_inventory_product_id' => '',
            'remarks' => '',
            'details' => array(
                array('fk_inventory_product_id' => '',
                    'quantity' => '',
                    'unit_description' => 'Unit'
                )
            )
        );
    }

    public function _segment($method = '') {
        return base_url("production/formulation/{$method}");
    }

    public function index() {
        //$this->add_javascript('warehousing-receive/manage.js');
        $ID = $this->input->get("ID");
        $receiverID = $this->input->get("receiver_ID");
        $remarks = $this->input->get("remarks");
        $datetime = $this->input->get("datetime");
        $this->_content['add_formulation_url'] = $this->_segment("manage");
        $this->_content['job_order_list'] = $this->m_production_job_order->retrieveProductionJobOrder($ID, $receiverID,  $remarks, $datetime, false, ">");
        $this->set_content('production/formulation', $this->_content);
        $this->generate_page();
    }

    public function manage($selectedID = 0) {
        
        
        $this->_content['form_title'] = ($selectedID <= 0 ) ? 'Add new formulation':"Edit Formulation";
        $this->_content['selected_ID']  = $selectedID;      
        $this->_content['job_order_list'] = $this->m_production_job_order->retrieveProductionJobOrder(false, false,  false, false, false, "zero");
        $this->_content['raw_products'] = $this->m_product->get(FALSE, array(M_Product::PRODUCT_CLASS => M_Product::CLASS_RAW));
        $this->_content['submit_formulation_url'] = $this->_segment('saveFormulation');
        $this->set_content('production/manage-formulation', $this->_content);
        $this->generate_page();
    }

    public function a_do_action($method, $extras = FALSE) {
        if (!$this->input->post()) {//no request data
            show_error('Error Code 400: Bad Request', 400);
            return;
        }
        $this->output->set_content_type('json');
        $response = '';
        switch ($method) {
            case 'add':
                $input = $this->_validate_input();
                $response = $input['error_flag'] ? $input : $this->_add($input['data']);
                break;
            case 'update':
                $input = $this->_validate_input();
                $response = $input['error_flag'] ? $input : $this->_update($extras, $input['data']);
                break;
            case 'delete':
                $response = $this->_delete($this->input->post('pk'));
                break;
            default:
                $response = $this->response(TRUE, 'We are not sure how to process your request.');
                break;
        }
        $this->output->set_output(json_encode($response));
        return;
    }

    function _add($validated) {
        $details = $validated['details'];
        unset($validated['details']);
        $added = $this->m_formulation->add($validated, $details);
        return $this->response(!$added, $added?$this->m_message->add_success(self::SUBJECT):$this->m_message->add_error(self::SUBJECT));
    }
    function saveFormulation(){
        $response = array("data" => false, "error" => false);
        $ID = $this->input->post("ID");
        $approvalRemarks = $this->input->post("approval_remarks");
        $dateApproved = date("Y-m-d g:i a",time());
        $updateResult = $this->m_production_job_order->updateJobOrder($ID, false, false, false, $this->session->userdata('user_id'), $dateApproved, $approvalRemarks);
        if($updateResult){
            $material = $this->createMaterialArray();
            $response["data"] = $this->m_production_job_order->insertProductMaterials($material["product_IDs"], $material["material"]);
        }else{
            $response["error"] = 1;
        }
        
        echo json_encode($response);
        
    }
    function createMaterialArray(){
        $newArray = array();
        $productIDs = array();
        $products = $this->input->post("products");
        if(is_array($products)){
            foreach($products as $productKey => $productValue){
                //updating job order product quantity
                $this->m_production_job_order->updateProductionJobOrder($productKey, $productValue["job_order_product_quantity"]);
                if($productKey > 0){
                $productIDs[] = $productKey;
                    foreach($productValue["material_ID"] as $key => $value){
                        if($value > 0 && $products[$productKey]["quantity"][$key] > 0){
                            $newArray[] = array(
                                "fk_job_order_product_ID" => $productKey,
                                "product_ID" => $value,
                                "quantity" => $products[$productKey]["quantity"][$key]
                            );
                        }
                    }
                }
                
            }
            return array(
                "product_IDs" => $productIDs,
                "material" => $newArray
            );
        }else{
            return false;
        }
    }
    function _validate_input() {
        $this->load->helper('array');
        $this->form_validation->set_rules('fk_inventory_product_id', 'Finished Product', 'required|callback__validate_finished_product');
        $this->form_validation->set_rules('details', 'Formulation', 'callback__validate_formulation');
        if (!$this->form_validation->run()) {
            return $this->response(TRUE, explode(', ', validation_errors(',', ' ')));
        }
        $formulation = elements(array('fk_inventory_product_id', 'remarks', 'details'), $this->input->post());
        $details = array();
        foreach ($formulation['details']['fk_inventory_product_id'] as $key => $value) {
            $details[] = array(
                'fk_inventory_product_id' => $value,
                'quantity' => $formulation['details']['quantity'][$key]
            );
        }
        $formulation['details'] = $details;
        return $this->response(FALSE, '', $formulation);
    }

    //callbacks
    function _validate_finished_product($product) {
        if (!is_numeric($product) || !$this->m_product->is_valid_finished($product)) {
            $this->form_validation->set_message('_validate_finished', 'Please select a valid finished product!');
        }
        return TRUE;
    }

    //callbacks
    function _validate_formulation($details) {
        $raw_products = isset($details['fk_inventory_product_id']) ? $details['fk_inventory_product_id'] : array();
        $quantity = isset($details['quantity']) ? $details['quantity'] : array();
        if (!is_array($raw_products) || empty($raw_products)) {
            $this->form_validation->set_message('_validate_formulation', 'Formulation should have at least one raw productz!');
            return FALSE;
        }
        if (!$this->m_product->is_valid_raw(array_unique($raw_products))) {
            $this->form_validation->set_message('_validate_formulation', 'Please select valid raw products.');
            return FALSE;
        }
        if (!is_array($quantity) || empty($quantity)) {
            $this->form_validation->set_message('_validate_formulation', 'Formulation should have at least one raw productzz!');
            return FALSE;
        }
        if (count($quantity) !== count($raw_products)) {
            $this->form_validation->set_message('_validate_formulation', 'Please fill up everything in the formulation!');
            return FALSE;
        }
        return TRUE;
    }

}

<?php

/**
 * Description of Job_Order
 *
 * @author adriannatabio
 */
class Job_Order extends PM_Controller_v2 {

    public $segment = 'production/job_order/';
    private $id;
    private $errors = [];

    const SUBJECT = 'job order';

    public function __construct() {
        parent::__construct();
        /* SET MODULES ACCESS */
        if (!has_access('production')) {
            show_error('Authorization error', 401);
        }
        /* SET PAGE SETTINGS */
        $this->set_active_nav(NAV_PRODUCTION);
        $this->set_content_title('Production');
        $this->set_content_subtitle('Job Orders');
        /* LOAD ASSETS */

        $this->load->helper(array('view', 'array')); //load helper
        /* LOAD NECESSARY MODELS */
        $this->load->model(array('production/m_job_order'));
        $this->_content['values'] = array();
    }

    public function index() {
        $this->add_css(['daterangepicker/daterangepicker-bs3.css']);
        $this->add_javascript(array('plugins/json2html.js', 'plugins/jquery.json2html.js', 'plugins/sticky-thead.js', 'printer/printer.js', 'plugins/daterangepicker/daterangepicker.js','production-job-order/master-list.js'));
        $this->set_content('production/job-order', $this->_content);
        $this->generate_page();
    }

    public function a_master_list() {
        $this->load->helper('array');
        $parameters = elements(array('jo', 'production_code', 'date' , 'page'), $this->input->get());
        $data = $this->m_job_order->master_list($parameters);
        $this->output->set_content_type('json');
        $this->output->set_output(json_encode($data ? array('data' => $data) : array()));
    }

    public function do_print($id) {
        $data['data'] = $this->m_job_order->get($id);
        $data['data']['details'] = $this->m_job_order->get_details($id);
        $this->load->view('printables/production/job-order', $data);
    }

    public function update($id) {
        if($this->m_job_order->exists($id) === FALSE)
        {
            show_404();
            return;
        }
        if ($input = $this->input->post()) {   
            $this->id = $id;
            $this->validate('update');
            if(count($this->errors) > 0){
                $this->generate_response(TRUE, $this->errors)->to_JSON();
            }else{
                $data = $this->format($input, 'update');    
                if($data['jo']['approved_by']){
                    $exclude = $this->m_job_order->get_consumed_materials($id);
                    $unavailable = $this->check_raw_mats_availability($data['details'], $exclude);
                    if(!empty($unavailable)){
                        $this->generate_response(TRUE, $unavailable)->to_JSON();
                        return;
                    }
                }
            }
            $result = $this->m_job_order->update($id, $data);
            if($result){
                $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Job order has been successfully updated!')));
                $this->generate_response(FALSE)->to_JSON();
                return;
            }
            $this->generate_response(TRUE, ['Unexpected error has occured. Please try again later.'])->to_JSON();
            return;
        } 
        $this->load->model(['sales/m_customer','inventory/m_product']);
        $this->_content['data'] = $this->m_job_order->get($id);
        $this->add_javascript(array('numeral.js', 'production-job-order/manage.js', 'plugins/datetimepicker/timepicker.js', 'price-format.js'));
        $this->_content['form_action'] = base_url($this->segment . "update/{$id}");
        $this->_content['form_title'] = 'Update job order';
        $this->_content['finished_products'] = $this->m_product->with_formulation(['f.status' => 1]);
        $this->_content['customers'] = $this->m_customer->get_list();
        array_walk($this->_content['finished_products'], function(&$var){
            $var['description'] = $var['description'].' ['.$var['formulation_code'].']';
        });
        $this->set_content('production/manage-job-order', $this->_content);
        $this->generate_page();
    }

    /*
    *   1. Get raw mats per formulation
    *   2. Multiply raw mat quantity by its mix count
    *   3. Get raw mats stock
    *   4. Check if ok
    */
    public function check_raw_mats_availability($details, $exclude = [])
    {        
        $this->load->model('inventory/m_product', 'product');
        $this->load->model('production/m_formulation', 'formulation');
        $unavailable = [];

        $fp_ids = [];
        $mixes = [];
        foreach($details AS $d){
            $fp_ids[] = $d['fk_inventory_product_id'];
            $mixes[$d['fk_inventory_product_id']] = $d['mix_number'];
        }
        unset($details);

        $needed_raw_mats = [];
        $formulas = $this->formulation->get_raw_mats($fp_ids);
        foreach($formulas AS &$f){
            $quantity = $f['quantity'] * $mixes[$f['fp_id']];
            if(isset($needed_raw_mats[$f['rm_id']])){
                $needed_raw_mats[$f['rm_id']] += $quantity;
            }else{
                $needed_raw_mats[$f['rm_id']] = $quantity;
            }
        }
        unset($formulas);

        $rm_ids = array_keys($needed_raw_mats);
        $raw_mats_stock = $this->product->get_stocks($rm_ids);
        $raw_mats_info = $this->product->identify($rm_ids);

        foreach($needed_raw_mats AS $product_id => $quantity){
            $stock = isset($raw_mats_stock[$product_id]) ? $raw_mats_stock[$product_id] : 0;
            if(isset($exclude[$product_id])){
                $stock+=$exclude[$product_id];
            }
            if($stock < $quantity){
                $lacking = $quantity - $stock;
                $product_unit = $raw_mats_info[$product_id]['unit_description'];
                $product_description = $raw_mats_info[$product_id]['description'];
                $unavailable[] = "Lacking {$lacking} {$product_unit} for: {$product_description}";
            }
        }

        return $unavailable;
    }    

    public function create() 
    {
        if ($input = $this->input->post()) {
            $this->validate();
            if(count($this->errors) > 0){
                $this->generate_response(TRUE, $this->errors)->to_JSON();
                return;
            }else{
                $data = $this->format($input);
                if($data['jo']['approved_by']){
                    $unavailable = $this->check_raw_mats_availability($data['details']);
                    if(!empty($unavailable)){
                        $this->generate_response(TRUE, $unavailable)->to_JSON();
                        return;
                    }
                }
            }
            $result = $this->m_job_order->create($data);
            if($result){
                $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'New job order has been successfully created!')));
                $this->generate_response(FALSE)->to_JSON();
                return;
            }
        }else{
            $this->load->model(['sales/m_customer','inventory/m_product']);
            $this->add_javascript(array('numeral.js','plugins/datetimepicker/timepicker.js', 'price-format.js', 'production-job-order/manage.js'));
            $this->_content['finished_products'] = $this->m_product->with_formulation(['f.status' => 1]);
            $this->_content['customers'] = $this->m_customer->get_list();
            $this->_content['form_action'] = base_url($this->segment . 'create');
            $this->_content['form_title'] = 'Create new job order';
            array_walk($this->_content['finished_products'], function(&$var){
                $var['description'] = $var['description'].' ['.$var['formulation_code'].']';
            });
            $this->set_content('production/manage-job-order', $this->_content);
            $this->generate_page();
        }
    }

    public function a_delete() {
        $this->output->set_content_type('json');
        if ($this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Permission denied.')));
            return;
        }
        if ($this->m_job_order->delete($this->input->post('pk'))) {
            $this->output->set_output(json_encode($this->response(FALSE, 'Success')));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, 'Error on deletion')));
    }

    public function validate($mode = 'create')
    {
        $validate_code = ($mode === 'create' ? 'is_unique[production_job_order.production_code]' : 'callback_validate_production_code');
        $this->form_validation->set_rules('date_started', 'Date started', 'required|callback_validate_datetime');
        $this->form_validation->set_rules('is_approved', 'Status', 'callback_validate_status');
        $this->form_validation->set_rules('production_code', 'Production code', "required|alpha_dash|{$validate_code}");
        if($this->form_validation->run() === FALSE)
        {
            foreach($this->form_validation->error_array() as $err)
            {
                $this->errors[] = $err;
            }
        }

        $details = elements(['fk_product_inventory_id', 'mix_number', 'fk_sales_customer_id', 'detail_id'], $this->input->post());
        if(!$this->validate_finished_products($details['fk_product_inventory_id']))
        {
            $this->errors[] = 'Please select valid finished products.';
        }
        if(!$this->validate_mix_numbers($details['mix_number']))
        {
            $this->errors[] = 'Tons per mix can only contain numeric values.';
        }
        if(!$this->validate_customers($details['fk_sales_customer_id']))
        {
            $this->errors[] = 'Please select valid customers.';
        }
    }

    public function validate_datetime($datetime)
    {
        $this->load->helper('pmdate');
        $this->form_validation->set_message('validate_datetime', 'Please input valid %s');
        return is_valid_date($datetime, 'm/d/Y h:i:s A');
    }

    public function validate_finished_products($finished_products)
    {
        $this->load->model('inventory/m_product');
        //$this->form_validation->set_message('validate_finished_products', 'Please choose valid %s');
        return $this->m_product->is_valid($finished_products);
    }

    public function validate_mix_status($mix_status)
    {
        $filtered = array_filter($mix_status, function($var){
            return in_array($var, [0,1]);
        });
        //$this->form_validation->set_message('validate_mix_status', 'Please choose valid %s');
        return $filtered === $mix_status;
    }

    public function validate_customers($customers)
    {
        $this->load->model('sales/m_customer');
        //$this->form_validation->set_message('validate_customers', 'Please choose valid %s');
        return $this->m_customer->is_valid(array_unique($customers));
    }

    public function validate_mix_numbers($mix_numbers)
    {
        // $this->form_validation->set_message('validate_mix_numbers', '%s can contain only numeric values');
        return array_filter($mix_numbers, 'is_numeric') === $mix_numbers;
    }

    public function validate_status($status)
    {
        $this->form_validation->set_message('validate_status', 'Only administrators can approve job orders.');
        if($status == 1)
        {
            return $this->session->userdata('type_id') == M_Account::TYPE_ADMIN;
        }
        return TRUE;
    }

    public function validate_production_code($production_code)
    {
        $this->form_validation->set_message('validate_production_code', 'The Production code is already in use.');
        return $this->m_job_order->has_unique_code($production_code, $this->id);
    }

    public function format($input, $mode = 'create')
    {
        // Set the counter for the sequence number
        $counter = 1;
        // Now let's do some data structuring
        $data = [
            'details' => [],
            'jo' => [
                'datetime_started' => date('Y-m-d H:i:s', strtotime($input['date_started'])),
                'production_code' => $input['production_code'],
                'remarks' => $input['remarks']
            ]
        ];
        // Is this a new job order?
        if($mode === 'create'){
            // Then let's record who created this
            $data['jo']['created_by'] = $this->session->userdata('user_id');
        }
        // Is this job order marked as approved?
        if(is_admin()){
            if(isset($input['is_approved'])){
                // Then let's record who approved this
                $data['jo']['approved_by'] = $this->session->userdata('user_id');
            }else{
                $data['jo']['approved_by'] = NULL;
            }
        }
        // Let's cycle through the submitted finished products
        foreach($input['fk_product_inventory_id'] AS $key => $value){
            // Retrieve the corresponding details prior to the current processed finished product
            $temp = [
                'sequence_number' => $counter,
                'fk_inventory_product_id' => $value,
                'mix_number' => $input['mix_number'][$key],
                'fk_sales_customer_id' =>  $input['fk_sales_customer_id'][$key]
            ];
            // Is the user trying to update an existing detail?
            if($mode === 'update' && isset($input['detail_id'][$key])){
                // Let's add the its id to the structure
                $temp['id'] = $input['detail_id'][$key];
            }
            $data['details'][] = $temp;
            // Increment sequence number
            $counter++;
        }
        // Now we are done!
        return $data;
    }

    public function get_details()
    {
        $jo_no = $this->input->get('id');
        if(is_numeric($jo_no)){
            $this->generate_response($this->m_job_order->get_details($jo_no))->to_JSON();
            return;
        }
        $this->generate_response([])->to_JSON();
    }
    
}

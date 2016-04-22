<?php

class dressed_packing_list extends PM_Controller_v2

{
    
    const TITLE = 'Sales';
    const SUBTITLE = 'Packing List (Dressed)';
    const SUBJECT = 'packing list';

    protected $validation_errors = [];
    protected $action;
    protected $id;

    
    function __construct()
    {
        parent::__construct();
        check_access('sales');
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        // load necessary models
        $this->load->model('sales/m_trucking', 'trucking');
        $this->load->model('sales/trucking_assistant_model', 'assistant');
        $this->load->model('sales/dressed_packing_list_model', 'packing_list');
        $this->load->model('sales/m_sales_order', 'sales_order');
        $this->load->model('sales/m_customer', 'customer');
    }
    
    function create()
    {
        $this->load->helper('customer');
        $truckings = $this->trucking->all(['status' => 'a']);
        $trucking_assistants = $this->assistant->all(['status' => 'a']);
        $this->add_javascript([
            'plugins/moment.min.js',
            'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js',
            'numeral.js'
        ]);
        $this->set_content('sales/packing-list/manage-dressed', [
            'section_title' => 'Create new packing list for dressed sales', 
            'form_action' => base_url('sales/dressed_packing_list/store'),
            'truckings' => dropdown_format($truckings, 'id', ['trucking_name', 'plate_number'], ' ', '(', ')'),
            'trucking_assistants' => ['' => ''] + array_column($trucking_assistants, 'name', 'id'),
            'data' => []
        ])->generate_page();
    }

    function store()
    {
        $this->action = 'store';
        $this->_perform_validation();  
        if($this->form_validation->run()){
            $data = $this->_format_input();
            $created = $this->packing_list->create($data['packing_list'], $data['details']);
            if($created){
                $this->generate_response(FALSE)->to_JSON();
                return;
            }
            $this->generate_response(TRUE, ['Cannot perform action due to an unknow error.'])->to_JSON();
        }else{
            $validation_errors = $this->form_validation->error_array();
            $this->validation_errors += array_values($validation_errors);
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
        }
    }

    function edit($id  = FALSE)
    {
        if(!$id || !$packing_list = $this->packing_list->get($id)){
            show_404();
        }
        $this->load->helper('customer');
        $truckings = $this->trucking->all(['status' => 'a']);
        $trucking_assistants = $this->assistant->all(['status' => 'a']);
        $this->add_javascript([
            'plugins/moment.min.js',
            'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js',
            'numeral.js'
        ]);
        $this->set_content('sales/packing-list/manage-dressed', [
            'section_title' => "Update packing list for dressed sales # {$id}", 
            'form_action' => base_url("sales/dressed_packing_list/update/{$id}"),
            'truckings' => dropdown_format($truckings, 'id', ['trucking_name', 'plate_number'], ' ', '(', ')'),
            'trucking_assistants' => ['' => ''] + array_column($trucking_assistants, 'name', 'id'),
            'data' => $packing_list,
            'sales_order' => $this->sales_order->fetch_order_details($packing_list['fk_sales_order_id'], FALSE)
        ])->generate_page();
    }

    function update($id)
    {
        if(!$id || !$packing_list = $this->packing_list->exists($id)){
            show_404();
        }
        $this->id = $id;
        $this->action = 'update';
        $this->_perform_validation();  
        if($this->form_validation->run()){
            $data = $this->_format_input();
            $created = $this->packing_list->update($id, $data['packing_list'], $data['details']);
            if($created){
                $this->generate_response(FALSE)->to_JSON();
                return;
            }
            $this->generate_response(TRUE, ['Cannot perform action due to an unknow error.'])->to_JSON();
        }else{
            $validation_errors = $this->form_validation->error_array();
            $this->validation_errors += array_values($validation_errors);
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
        }
    }

    function do_print($id = FALSE)
    {
        if(!$id || !$packing_list = $this->packing_list->get($id)){
            show_404();
        }
        $this->load->view('printables/sales/dressed-pl',  [
            'details' => $packing_list,
            'sales_order' => $this->sales_order->fetch_order_details($packing_list['fk_sales_order_id'])
        ]);
    }
    
    function _perform_validation()
    {

        if($this->action === 'store'){
            $this->form_validation->set_rules('fk_sales_customer_id', 'customer', 'required|callback__validate_customer');
            $this->form_validation->set_rules('fk_sales_order_id', 'SO#', 'required|callback__validate_fk_sales_order_id');
        }
        $this->form_validation->set_rules('fk_sales_order_detail_id', 'product', 'required|callback__validate_fk_sales_order_detail_id');
        $this->form_validation->set_rules('invoice_number', 'invoice number', 'required|trim');
        $this->form_validation->set_rules('fk_sales_trucking_id', 'trucking', 'required|callback__validate_trucking');
        $this->form_validation->set_rules('fk_trucking_assistant_id', 'trucking assistant', 'required|callback__validate_trucking_assistant');
        $this->form_validation->set_rules('date', 'departure date and time', 'required|callback__validate_datetime');
        if(!is_array($details = $this->input->post('dd'))){
            $this->validation_errors[] = 'Please provide at least one delivery detail!';
            return;
        }
        $error_str = "The %s in delivery detail line # %s should be %s";
        foreach($details AS $index => $row){
            if(!isset($row['delivered_units']) || !is_numeric($row['delivered_units'])){
                $this->validation_errors[] = sprintf($error_str, 'no. of pieces', $index+1, 'numeric');
            }
            if(!isset($row['this_delivery']) || !is_numeric($row['this_delivery'])){
                $this->validation_errors[] = sprintf($error_str, 'kilograms', $index+1, 'numeric');
            }
        }
    }

    function _format_input()
    {
        $details = [];
        $packing_list = elements([
            'invoice_number', 
            'fk_sales_trucking_id', 
            'fk_trucking_assistant_id', 
            'remarks',
            'date'
        ], $this->input->post());
        if($this->action === 'store'){
            $packing_list += [
                'fk_sales_order_id' => $this->input->post('fk_sales_order_id'),
                'type' => 'd',
                'status' => M_Status::STATUS_DELIVERED,
                'created_by' => user_id(),
                'approved_by' => user_id()
            ];
        }
        $packing_list['date'] = date_create($packing_list['date'])->format('Y-m-d H:i:s');
        foreach($this->input->post('dd') AS $row){
            $temp = elements(['delivered_units', 'this_delivery'], $row);
            $temp['fk_sales_order_detail_id'] = $this->input->post('fk_sales_order_detail_id');
            if(isset($row['id'])){
                $temp['id'] = $row['id'];
            }
            $details[] = $temp;
        }
        return compact(['packing_list', 'details']);
    }

    function _validate_customer($customer_id)
    {
        $this->form_validation->set_message('_validate_customer', 'Please provide a valid %s.');
        return $this->customer->is_active($customer_id);
    }

    function _validate_fk_sales_order_id($so_id)
    {
        $customer_id = $this->input->post('fk_sales_customer_id');
        $unserved = $this->customer->get_undelivered_orders($customer_id);
        $this->form_validation->set_message('_validate_fk_sales_order_id', 'Please provide a valid %s.');
        return in_array($so_id, array_column($unserved, 'id'));
    }

    function _validate_fk_sales_order_detail_id($so_detail_id)
    {   
        $order_id =  $this->action === 'store' ?$this->input->post('fk_sales_order_id') : $this->packing_list->get_so_number($this->id);
        $order_details = $this->sales_order->fetch_order_details($order_id);
        $this->form_validation->set_message('_validate_fk_sales_order_detail_id', 'Please provide a valid %s.');
        return in_array($so_detail_id, array_column($order_details['items_ordered'], 'id'));
    }

    function _validate_trucking($trucking_id)
    {
        $this->form_validation->set_message('_validate_trucking', 'Please provide a valid %s.');
        return $this->trucking->exists($trucking_id, TRUE);
    }

    function _validate_trucking_assistant($trucking_assistant_id)
    {
        $this->form_validation->set_message('_validate_trucking_assistant', 'Please provide a valid %s.');
        return $this->assistant->exists($trucking_assistant_id, TRUE);
    }

    function _validate_datetime($datetime)
    {
        $this->load->helper('pmdate');
        $this->form_validation->set_message('_validate_datetime', 'Please provide a valid date and time.');
        return is_valid_date($datetime, 'm/d/Y h:i A');
    }

    function _validate_sales_agent($sales_agent)
    {
        $this->form_validation->set_message('_validate_sales_agent', 'Please provide a valid %s');
        return $this->agent->exists($sales_agent, TRUE);
    }



    
}
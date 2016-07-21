<?php

class Trip_tickets extends PM_Controller_v2

{
    

    protected $validation_errors = [];
    protected $action;
    protected $id;

    
    function __construct()
    {
        parent::__construct();
        if(!has_access('trucking')) show_error('Authorization error', 401);
        $this->set_content_title('Trucking');
        $this->set_content_subtitle('Trip Tickets');
        $this->set_active_nav(NAV_TRUCKING);


        $this->load->model('trucking/m_trip_tickets','trip_ticket');
        $this->load->model('sales/m_trucking', 'trucking');
        $this->load->model('sales/trucking_assistant_model', 'assistant');
        $this->load->model('sales/m_customer', 'customer');
    }

    function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['fk_sales_customer_id','truck','trip_type'], $this->input->get(), FALSE);
        if($params['trip_type'] && in_array($params['trip_type'], ['1', '2','3'])){
            $search['tt.trip_type'] = $params['trip_type'];
        }

        if($params['fk_sales_customer_id'] && is_numeric($params['fk_sales_customer_id'])){
            $search['tt.fk_sales_customer_id'] = $params['fk_sales_customer_id'];
        }
        
        return compact(['search', 'wildcards']);
    }

    function index()
    {
        $this->load->helper('customer');
        $this->add_javascript([
            'plugins/sticky-thead.js',
            'trucking-trip-tickets/listing.js',
            'plugins/moment.min.js'
        ]);

        $params = $this->_search_params();
        $data = $this->trip_ticket->all($params['search'], $params['wildcards']);

        $this->set_content('trucking/trip-ticket/listing', [
            'items' => $data
        ])->generate_page();
    }


    function create()
    {
        $this->load->helper('customer');
        $truckings = $this->trucking->all(['status' => 'a']);
        $trucking_assistants = $this->assistant->all(['status' => 'a']);
        $this->add_javascript([
            'plugins/moment.min.js',
            'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js',
            'jquery-ui.min.js', 
            'numeral.js',
            'trucking-trip-tickets/manage.js'
        ]);
        $this->set_content('trucking/trip-ticket/manage', [
            'section_title' => 'Create new trip ticket', 
            'form_action' => base_url('trucking/trip_tickets/store'),
            'truckings' => dropdown_format($truckings, 'id', ['trucking_name', 'plate_number'], ' ', '(', ')'),
            'trucking_assistants' => ['' => ''] + array_column($trucking_assistants, 'name', 'id'),
            'data' => ['approved_by'=> NULL]
        ])->generate_page();
    }

    function store()
    {
        $this->action('store');
        $validation = $this->_validate();
        if($validation['status']){
            $id = $this->trip_ticket->create($validation['data']);
            $this->generate_response(FALSE, '', ['id' => $id])->to_JSON();
        }else{
            $this->generate_response(TRUE, $validation['errors'])->to_JSON();
        }
    }

    function get($id  = FALSE)
    {
        if(!$id || !$trip_ticket = $this->trip_ticket->get($id)){
            show_404();
        }
        $this->load->helper('customer');
        $truckings = $this->trucking->all(['status' => 'a']);
        $trucking_assistants = $this->assistant->all(['status' => 'a']);
        $this->add_javascript([
            'plugins/moment.min.js',
            'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js',
            'jquery-ui.min.js',
            'numeral.js',
            'trucking-trip-tickets/manage.js'
        ]);
        $this->set_content('trucking/trip-ticket/manage', [
            'section_title' => "Update trip-ticket for # {$id}", 
            'form_action' => base_url("trucking/trip_tickets/update/{$id}"),
            'truckings' => dropdown_format($truckings, 'id', ['trucking_name', 'plate_number'], ' ', '(', ')'),
            'trucking_assistants' => ['' => ''] + array_column($trucking_assistants, 'name', 'id'),
            'data' => $trip_ticket
        ])->generate_page();
    }

    function update($id)
    {
        if(!$id || !$trip_ticket = $this->trip_ticket->exists($id)){
            show_404();
        }
        $this->id = $id;
        $this->action = 'update';
        $validation = $this->_validate();
        if($validation['status']){
            $id = $this->trip_ticket->update($id, $validation['data']);
            $this->generate_response(FALSE, '', ['id' => $id])->to_JSON();
        }else{
            $this->generate_response(TRUE, $validation['errors'])->to_JSON();
        }
    }

    public function delete($id)
    {
        if(!$id || !$ticket = $this->trip_ticket->find($id)){
            $this->generate_response(TRUE, 'Please select a valid tariff to delete.')->to_JSON();
            return;
        }
        if(!can_delete($ticket)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->trip_ticket->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
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
    
    function _validate()
    {

        $errors = [];
        if($this->action === 'store'){
            $this->form_validation->set_rules('fk_sales_customer_id', 'customer', 'required|callback__validate_customer');
        }
        $this->form_validation->set_rules('fk_sales_trucking_id', 'trucking', 'required|callback__validate_trucking');
        $this->form_validation->set_rules('fk_trucking_assistant_id', 'trucking assistant', 'required|callback__validate_trucking_assistant');
        $this->form_validation->set_rules('date', 'departure date and time', 'required|callback__validate_datetime');
        $this->form_validation->set_rules('trip_type', 'Trip type', 'required|numeric');

        if($this->form_validation->run()){

            $input = $this->input->post();

            if(isset($input['fk_sales_customer_id']) && $fk_sales_customer_id = trim($input['fk_sales_customer_id']))
                $data['fk_sales_customer_id'] = $fk_sales_customer_id;

            if(isset($input['fk_sales_trucking_id']) && $fk_sales_trucking_id = trim($input['fk_sales_trucking_id']))
                $data['fk_sales_trucking_id'] = $fk_sales_trucking_id;

            if(isset($input['fk_trucking_assistant_id']) && $fk_trucking_assistant_id = trim($input['fk_trucking_assistant_id']))
                $data['fk_trucking_assistant_id'] = $fk_trucking_assistant_id;

            if(isset($input['date']) && $date = trim($input['date'])){
                $date = formatDate($date,'Y-m-d','m/d/Y');
                $data['date'] = $date;
            }
            if(isset($input['remarks']) && $remarks = trim($input['remarks'])){
                $data['remarks'] = $remarks;
            }

            if(isset($input['trip_type']) && $trip_type = trim($input['trip_type']))
                $data['trip_type'] = $trip_type;

            if(can_set_status()){
                if(isset($input['approved_by']) && $input['approved_by']=='on'){
                    $data['approved_by'] = $this->session->userdata('user_id');
                } else {
                    $data['approved_by'] = NULL;
                }
            }
            $data['last_updated_by'] = $this->session->userdata('user_id');
            return [
                    'status' => TRUE,
                    'data' => $data,
                ];
        }
        return [
            'status' => FALSE,
            'errors' => array_merge($this->form_validation->errors(), $errors)
        ];
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
        return is_valid_date($datetime, 'm/d/Y');
    }

    function _validate_sales_agent($sales_agent)
    {
        $this->form_validation->set_message('_validate_sales_agent', 'Please provide a valid %s');
        return $this->agent->exists($sales_agent, TRUE);
    }

    function _check_item_availability($filled_orders, $offset = [])
    {
        if(IGNORE_STOCK_PL_ACTION){
            return [];
        }

        $unavailable = [];

        $this->load->model('sales/m_sales_order', 'sales_order');
        $this->load->model('inventory/m_product');

        $ordered_products = $this->sales_order->get_ordered_products(FALSE, [$filled_orders[0]['fk_sales_order_detail_id']]);
        $product_ids = array_values($ordered_products);
        $product_details = $this->m_product->identify($product_ids);
        $stocks = $this->m_product->get_stocks($product_ids);

        $product_id = $ordered_products[$filled_orders[0]['fk_sales_order_detail_id']];

        $available = ['units' => 0, 'pieces' => 0];

        $requested = [
            'units' => array_sum(array_column($filled_orders, 'this_delivery')), 
            'pieces' => array_sum(array_column($filled_orders, 'delivered_units')), 
        ];

        if(isset($stocks[$product_id])){
            $available['units'] += $stocks[$product_id]['available_units'];
            $available['pieces'] += $stocks[$product_id]['available_pieces'];
        }

        if($offset){
            $available['units'] += $offset['units'];
            $available['pieces'] += $offset['pieces'];
        }

        if($available['units'] < $requested['units']){
            $lacking_units = $requested['units'] - $available['units'];
            $lacking[] = "{$lacking_units} {$product_details[$product_id]['unit_description']}";
        }

        if($available['pieces'] < $requested['pieces']){
            $lacking_pieces = $requested['pieces'] - $available['pieces'];
            $lacking[] = "{$lacking_pieces} pieces";
        }

        if(!empty($lacking)){
            $unavailable[] = "Lacking ". implode(' and ', $lacking). " for: {$product_details[$product_id]['description']}";
        }

        return $unavailable;
    }

    
}
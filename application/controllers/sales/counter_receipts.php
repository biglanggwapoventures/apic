<?php

class Counter_receipts extends PM_Controller_v2
{

	protected $validation_errors = [];
	protected $id;

	function __construct()
	{
		parent::__construct();
        if(!has_access('sales')) show_error('Authorization error', 401);
        $this->set_content_title('Sales');
        $this->set_content_subtitle('Counter Receipts');
        $this->set_active_nav(NAV_SALES);
        $this->load->model('sales/m_customer', 'customer');
        $this->load->model('sales/counter_receipt_model', 'cr');
	}

	public function master_list()
	{
        $offset = $this->input->get('page');
        $page = $offset ? $offset : 1;
        $data = $this->cr->all($page, $this->_search_params());
        $this->output->set_content_type('json')->set_output(json_encode($data ? ['data' => $data] : []));	
    }

    public function _search_params()
    {
        $this->load->helper('pmdate');
        $query = [];
        $params = elements(['id', 'start_date', 'end_date', 'customer'], $this->input->get());
        if($params['id'] && is_numeric($params['id']))
        {
            $query['cr.id'] = $params['id'];
        }
        if($params['start_date'] && is_valid_date($params['start_date'], 'M-d-Y'))
        {
            $query['cr.`date` >='] = date_create_from_format('M-d-Y', $params['start_date'])->format('Y-m-d');
        }
        if($params['end_date'] && is_valid_date($params['end_date'], 'M-d-Y'))
        {
            $query['cr.`date` <='] = ddate_create_from_format('M-d-Y', $params['end_date'])->format('Y-m-d');
        }
        if($params['customer'] && $this->customer->is_valid($params['customer']))
        {
            $query['customer.id'] = $params['customer'];
        }
        return empty($query) ? FALSE : $query;
	}

	function index() 
	{
        $this->add_javascript(['plugins/sticky-thead.js', 'numeral.js', 'plugins/moment.min.js']);
        $this->set_content('sales/counter-receipts/listing', [
        	'items' => [],
        	'customers' => ['' => 'All customers'] + array_column($this->customer->all(['status' => 'a']), 'company_name', 'id')
    	])->generate_page();
    }

    function create() 
    {
        $this->set_content('sales/counter-receipts/manage', [
            'title' => 'Create new counter receipt',
            'action' => base_url('sales/counter_receipts/store'),
            'data' => [],
            'customers' => $this->customer->all(['status' => 'a'])
        ])->generate_page();
    }

    function store()
    {
    	$this->_perform_validation('c');

    	if(!empty($this->validation_errors)){
    		$this->generate_response(TRUE, $this->validation_errors)->to_JSON();
    		return;
    	}

    	$data = $this->_format_data();
		$success = $this->cr->create($data);

		$this->generate_response(!$success)->to_JSON();

		return;
	}

	function edit($id = FALSE)
	{
		if(!$id || !$data = $this->cr->get($id)){
			show_404();
		}

		$uncountered_packing_lists = [];

		$append = $data['approved_by'] ? array_column($data['details'], 'fk_sales_delivery_id') : FALSE;

		$uncountered_packing_lists = $this->customer->get_uncountered_packing_list($data['fk_sales_customer_id'], $append);

		$this->set_content('sales/counter-receipts/manage', [
            'title' => "Update CR# {$id}",
            'action' => base_url("sales/counter_receipts/update/{$id}"),
            'data' => $data,
            'uncountered_packing_lists' => $uncountered_packing_lists,
            'customers' => $this->customer->all(['status' => 'a'])
        ])->generate_page();

	}

	public function update($id = FALSE)
	{
		if(!$id || !$this->cr->exists($id)){
			show_404();
		}

		$this->id = $id;

		$this->_perform_validation('u');

		if(!empty($this->validation_errors)){
    		$this->generate_response(TRUE, $this->validation_errors)->to_JSON();
    		return;
    	}

    	$data = $this->_format_data('u');
		$success = $this->cr->update($id, $data);

		$this->generate_response(!$success)->to_JSON();

		return;
	}

	function delete()
	{
		$cr = $this->input->post('pk');
		if($this->cr->exists($cr) && !$this->cr->is_approved($cr)){
			$this->generate_response(!$this->cr->delete($cr))->to_JSON();
			return;
		}
		$this->generate_response(TRUE, ['Selected counter receipt does not exist or has already been approved.'])->to_JSON();
	}

    function _perform_validation($mode = 'c')
    {
    	$rules = [
    		['date', 'date', 'required|callback__validate_date'],
    		['fk_sales_customer_id', 'customer', 'required|integer|callback__validate_customer']
    	];
    	$this->form_validation->add_rules($rules);
    	if($this->form_validation->run()){

    		$has_valid_pls = $this->_validate_details($mode);
    		if(!$has_valid_pls){
    			$this->validation_errors[] = 'Please select packing lists to counter.';
    		}
    		return;

    	}
    	$this->validation_errors = $this->form_validation->errors();
    }

    function _format_data($mode = 'c')
    {
    	$data['cr'] = elements(['remarks', 'fk_sales_customer_id'], $this->input->post(), NULL);
    	$data['cr']['date'] = date_create_from_format('M-d-Y', $this->input->post('date'))->format('Y-m-d');

    	if($mode === 'c'){
    		$data['cr']['created_by'] = user_id();
    	}

    	if(can_set_status()){
    		$data['cr']['approved_by'] = $this->input->post('is_approved') ? user_id() : NULL;
     	}

    	$details = $this->input->post('details');
    	foreach($details AS $row){
    		$temp = ['fk_sales_delivery_id' => $row['fk_sales_delivery_id']]; 
    		if(isset($row['id']) && $mode!=='c'){
    			$temp['id'] = $row['id'];
    		}
    		$data['details'][] = $temp;
    	}
    	return $data;
    }

    function _validate_date($date)
    {
    	$this->load->helper('pmdate');
    	$this->form_validation->set_message('_validate_date', 'Please provide valid %s.');
    	return is_valid_date($date, 'M-d-Y');
    }

    function _validate_customer($customer_id)
    {
    	$this->form_validation->set_message('_validate_customer', 'Please provide valid %s.');
    	return $this->customer->exists($customer_id, TRUE);
    }

    function _validate_details($mode = 'c')
    {
    	
    	if(!is_array($details = $this->input->post('details'))){
    		return FALSE;
    	}

    	$packing_lists = array_column($details, 'fk_sales_delivery_id');

    	if(empty($packing_lists) || ($packing_lists !== array_filter($packing_lists, 'is_numeric'))){
    		return FALSE;
    	}

    	if($mode != 'c'){
    		$counter_receipt = $this->cr->get($this->id);
			$valid_packing_list = $this->customer->get_uncountered_packing_list($this->input->post('fk_sales_customer_id'), array_column($counter_receipt['details'], 'fk_sales_delivery_id'));
    	}else{
    		$valid_packing_list = $this->customer->get_uncountered_packing_list($this->input->post('fk_sales_customer_id'));
    	}

		
		return !array_diff($packing_lists, array_column($valid_packing_list, 'id'));
    }

}
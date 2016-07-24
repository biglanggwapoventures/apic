<?php

class Customer extends PM_Controller_v2
{

	function __construct()
	{
		parent::__construct();
        if(!has_access('sales')) show_error('Authorization error', 401);
        $this->set_content_title('Sales');
        $this->set_content_subtitle('Customers');
        $this->set_active_nav(NAV_SALES);
        $this->load->model('sales/m_customer', 'customer');
	}

    function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['customer_code', 'company_name', 'customer_status'], $this->input->get(), FALSE);

        if($params['customer_status'] && in_array($params['customer_status'], ['a', 'ia'])){
            $search['customer_status'] = $params['customer_status'];
        }elseif($params['customer_status'] === FALSE){
            $search['customer_status'] = 'a';
        }

        if($params['customer_code'] && trim($params['customer_code'])){
            $wildcards['customer_code'] = $params['customer_code'];
        }

        if($params['company_name'] && trim($params['company_name'])){
            $wildcards['company_name'] = $params['company_name'];
        }
        
        return compact(['search', 'wildcards']);
    }

	function index() 
	{
        $this->add_javascript(['sales-customers/listing.js', 'plugins/sticky-thead.js']);
    
        $params = $this->_search_params();
        $this->set_content('sales/customers/listing', [
            'items' => $this->customer->all($params['search'], $params['wildcards'])
        ])->generate_page();
    }

    function create() 
    {
        $this->add_javascript(['sales-customers/manage.js', 'price-format.js']);
        $this->set_content('sales/customers/manage', [
            'title' => 'Create new customer',
            'action' => base_url('sales/customer/store'),
            'data' => ['for_trucking'=> NULL],

        ])->generate_page();
    }

    function edit($id = FALSE)
    {
        if(!$id || !$customer = $this->customer->find($id)){
            show_404();
        }
        $this->add_javascript(['sales-customers/manage.js', 'price-format.js']);
        $this->set_content('sales/customers/manage', [
            'title' => "Update customer: {$customer['customer_code']}",
            'action' => base_url("sales/customer/update/{$customer['id']}"),
            'data' => $customer
        ])->generate_page();
    }

    function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $customer = $this->_format_data();
            $this->customer->create($customer);
            $this->flash_message(FALSE, 'New customer has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    function update($id = FALSE)
    {

        if(!$id || !$customer = $this->customer->find($id)){
            $this->generate_response(TRUE, 'Please select a valid customer to update.')->to_JSON();
            return;
        }
        if(!can_update($customer, 'customer_status')){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $customer = $this->_format_data();
            $this->customer->update($id, $customer);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    function delete($id)
    {
        if(!$id || !$customer = $this->customer->find($id)){
            $this->generate_response(TRUE, 'Please select a valid customer to delete.')->to_JSON();
            return;
        }
        if(!can_delete($customer, 'customer_status')){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->customer->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    function show_pricing($customer_id)
    {   
        $this->add_javascript(['sales-customers/pricing.js', 'price-format.js', 'plugins/sticky-thead.js']);
        $this->load->model('inventory/m_product', 'product');
        $active_products = $this->product->all(['p.status' => 'a']);
        array_walk($active_products, function(&$var){
            $var['description'] = "[{$var['code']}] {$var['description']}";
        });
        $this->set_content_subtitle('Set customer price list');
        $this->set_content('sales/customers/price-list', [
            'name' => $this->customer->get_name($customer_id),
            'price_list' => $this->customer->get_customer_price_list($customer_id),
            'active_products' => $active_products,
            'customer_id' => $customer_id
        ])->generate_page();
    }

    function save_customer_pricing($customer_id = FALSE) 
    {
        if($this->m_customer->save_price_list($customer_id, $this->input->post('list'))){
            $this->generate_response(FALSE)->to_JSON();    
            return;
        }
        $this->generate_response(TRUE)->to_JSON();    
    }

    function get_uncountered_packing_list($customer_id)
    {
        if($this->customer->exists((int)$customer_id, TRUE)){
             $this->generate_response(FALSE, '', $this->customer->get_uncountered_packing_list($customer_id))->to_JSON();
             return;
        }
        $this->generate_response(TRUE)->to_JSON();
    }

    function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('customer_code', 'customer code', 'trim|required|is_unique[sales_customer.customer_code]');
        }else{
            $this->form_validation->set_rules('customer_code', 'customer code', 'trim|required|callback__validate_customer_code');
        }
        $this->form_validation->set_rules('company_name', 'customer name', 'trim|required');
        $this->form_validation->set_rules('address', 'customer address', 'trim|required');
        $this->form_validation->set_rules('contact_number', 'customer number', 'trim|required');
        $this->form_validation->set_rules('contact_person', 'customer person', 'trim|required');
        $this->form_validation->set_rules('credit_limit', 'customer credit limit', 'trim|required|callback__validate_credit_limit');
        $this->form_validation->set_rules('credit_term', 'customer credit terms', 'trim|required|integer');
        if(can_set_status()){
            $this->form_validation->set_rules('customer_status', 'status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    function _format_data()
    {
        $input = elements(['customer_code', 'company_name', 'address', 'contact_number', 'contact_person', 'credit_limit', 'credit_term', 'customer_status'], $this->input->post());
        $set = elements(['for_trucking'], $this->input->post());
                if(isset($set['for_trucking']) && $set['for_trucking']=='on'){
                    $input['for_trucking'] = 1;
                } else {
                    $input['for_trucking'] = NULL;
                }
        $input['credit_limit'] = str_replace(',', '', $input['credit_limit']);
        if(!can_set_status()){
           unset($input['customer_status']);
        }
        return $input;
    }

    function _validate_customer_code($code)
    {
        $this->form_validation->set_message('_validate_customer_code', 'The %s is already in use.');
        return $this->customer->has_unique_code($code, $this->id);
    }

    function _validate_credit_limit($credit_limit)
    {
    	$this->form_validation->set_message('_validate_credit_limit', 'The %s is should only contain numbers.');
        return is_numeric(str_replace(',', '', $credit_limit));
    }
}
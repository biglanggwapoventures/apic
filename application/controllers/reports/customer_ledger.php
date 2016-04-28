<?php

class Customer_ledger extends PM_Controller_v2
{
	function __construct()
	{
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error', 401);
		$this->setTabTitle('Customer Ledger Report');
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Customer Ledger');
		$this->set_active_nav(NAV_REPORTS);	
	}

	function index()
	{
		$this->load->helper('pmdate');
		$this->load->model('reports/customer_ledger_model', 'report');
		$this->load->model('sales/m_customer', 'customer');

		$this->add_javascript(['plugins/sticky-thead.js', 'printer/print.js', 'plugins/loadash.js', 'customer-ledger/customer-ledger.js']);

		$customers = ['' => ''] + array_column($this->customer->all(['status' => 'a']), 'company_name', 'id');
		$params = elements(['customer', 'date'], $this->input->get(), FALSE);

		if(!is_valid_date($params['date'])){
			$params['date'] = date('Y-m-d');
		}

		if($this->customer->exists($params['customer'], TRUE)){
			$customer_info = $this->customer->find($params['customer']);
			$data = $this->report->generate($params['customer'], $params['date']);
		}

		$this->set_content('reports/customer-ledger/index', compact('params', 'customers', 'data', 'customer_info'))->generate_page();

	}

}
<?php

class Collection_report extends PM_Controller_v2
{
	function __construct()
	{
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error', 401);
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Collection Report');
		$this->set_active_nav(NAV_REPORTS);	
	}

	function index()
	{
		$this->add_css('bootstrap-editable.css');
		$this->add_javascript(['plugins/sticky-thead.js', 'bootstrap-editable.min.js']);
		// load necessary models 
		$this->load->model('sales/m_customer', 'customer');
		$this->load->model('sales/m_agent', 'agent');
		$this->load->model('reports/collection_report_model', 'report');
		// load date helper
		$this->load->helper('pmdate');
		// index customers and agents
		$customers = [''] + array_column($this->customer->all(['status' => 'a']), 'company_name', 'id');
		$agents = [''] + array_column($this->agent->all(['status' => 'a']), 'name', 'id');

		$get = elements(['start_date', 'end_date', 'customer', 'sales_agent'], $this->input->get(), FALSE);

		if(is_valid_date($get['start_date'])){
			$params['start_date'] = date_create($get['start_date'])->format('M d, Y');
		}else{
			$get['start_date'] = date('Y-m-d');
			$params['start_date'] = date_create()->format('M d, Y');
		}

		if(is_valid_date($get['end_date'])){
			$params['end_date'] = date_create($get['end_date'])->format('M d, Y');
		}else{
			$get['end_date'] = date('Y-m-d');
			$params['end_date'] = date_create()->format('M d, Y');
		}

		$params['customer'] = isset($customers[$get['customer']]) && $customers[$get['customer']] ? $customers[$get['customer']] : 'All customers';
		$params['sales_agent'] = isset($agents[$get['sales_agent']]) && $agents[$get['sales_agent']]? $agents[$get['sales_agent']] : 'All sales agent';

		$data = [];

		$data = $this->report->generate($get['customer'], $get['sales_agent'], $get['start_date'], $get['end_date']);

		$this->set_content('reports/collection-report/index.php', compact(['customers', 'agents', 'params', 'data']))->generate_page();
	}
}
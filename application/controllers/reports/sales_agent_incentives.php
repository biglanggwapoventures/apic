<?php

class Sales_agent_incentives extends PM_Controller_v2
{
	function __construct()
	{
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error', 401);
		$this->setTabTitle('Sales Agent Incentive - Report');
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Collection Report');
		$this->set_active_nav(NAV_REPORTS);	
	}

	function index()
	{

		$get = elements(['start_date', 'end_date', 'sales_agent'], $this->input->get(), FALSE);

		$this->add_javascript(['printer/print.js', 'plugins/sticky-thead.js', 'plugins/loadash.js', 'sales_agent_incentives/sales_agent_incentives.js']);
		// load necessary models 
		$this->load->model('sales/m_agent', 'agent');
		$this->load->model('reports/sales_agent_incentive_model', 'report');
		// load date helper
		$this->load->helper('pmdate');
		// index customers and agents

		$agents_data = $this->agent->all(['status' => 'a']);

		$agents = [''] + array_column($agents_data, 'name', 'id');

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

		$data = [];

		if($get['sales_agent']){
			$agents_data_indexed = array_column($agents_data, NULL, 'id');
			$params['sales_agent'] = $agents_data_indexed[$get['sales_agent']];
		}


		$data = $this->report->generate($get['sales_agent'], $get['start_date'], $get['end_date']);

		$this->set_content('reports/agent-incentives/index.php', compact(['agents', 'params', 'data']))->generate_page();
	}
}
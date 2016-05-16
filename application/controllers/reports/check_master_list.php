<?php

class Check_master_list extends PM_Controller_v2
{
	function __construct()
	{
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error', 401);
		$this->setTabTitle('Check Master List - Report');
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Check Master List');
		$this->set_active_nav(NAV_REPORTS);	
	}

	function index()
	{
		$this->load->helper('pmdate');
		$this->load->model('reports/check_master_list_model', 'report');
		$this->load->model('accounting/m_bank_account', 'bank');

		$bank_accounts = array_column($this->bank->get(), NULL, 'id');

		$this->add_javascript(['plugins/sticky-thead.js', 'printer/print.js', 'plugins/loadash.js', 'check-master-list/check-master-list.js']);

		$params = elements(['bank_account', 'check_number_start', 'check_number_end'], $this->input->get(), FALSE);
		$data = $this->report->generate($params['bank_account'], $params['check_number_start'], $params['check_number_end']);
		if($params['bank_account']){
			$params['bank_account_details'] = $bank_accounts[$params['bank_account']];
		}
		$banks_dropdown = ['' => ''] + array_column($bank_accounts, 'bank_name', 'id');

		$this->set_content('reports/check-master-list/index', compact('params', 'data', 'banks_dropdown'))->generate_page();

	}

}
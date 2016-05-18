<?php

class Deposit_summary extends PM_Controller_v2
{
	function __construct()
	{
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error', 401);
		$this->setTabTitle('Deposit Summary - Report');
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Deposit Summary');
		$this->set_active_nav(NAV_REPORTS);	
		$this->load->model('reports/deposit_summary_model', 'report');
	}

	function index()
	{
		$this->load->helper('pmdate');
		
		$this->load->model('accounting/m_bank_account', 'bank');

		$bank_accounts = array_column($this->bank->get(), NULL, 'id');

		$this->add_javascript([
			'plugins/sticky-thead.js', 
			'printer/print.js', 
			'plugins/loadash.js', 
			'deposit-summary/deposit-summary.js',
		]);

		$params = elements(['bank_account', 'date'], $this->input->get(), FALSE);
		$data = $this->report->generate($params['date'], $params['bank_account']);

		if($params['bank_account']){
			$params['bank_account_details'] = $bank_accounts[$params['bank_account']];
		}

		if(!is_valid_date($params['date'])){
			$params['date'] = date('Y-m-d');
		}

		$banks_dropdown = ['' => '*ALL BANK ACCOUNTS*'] + array_column($bank_accounts, 'bank_name', 'id');

		$this->set_content('reports/deposit-summary/index', compact('params', 'data', 'banks_dropdown'))->generate_page();

	}

	function update_check_number()
	{
		$params = elements(['pk', 'name', 'value'], $this->input->post(), NULL);
		if(!in_array($params['name'], ['dummy_check', 'disbursement', 'disbursement_others']) || !is_numeric($params['pk']) || !is_numeric($params['value'])){
			$this->generate_response(TRUE)->to_JSON();
			return;
		}
		$this->report->update_check_number($params['name'], $params['pk'], $params['value']);
		$this->generate_response(FALSE)->to_JSON();
	}

}
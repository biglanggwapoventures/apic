<?php

class Payables extends PM_Controller_v2 
{

	public function __construct()
	{
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error.', 401);
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Aging of Payables');
		$this->set_active_nav(NAV_REPORTS);
	}

	public function index()
	{
		$this->add_javascript(['plugins/sticky-thead.js', 'printer/print.js', 'plugins/loadash.js', 'reports-payables/payables.js']);
		$this->load->model('maintainable/m_supplier', 'supplier');
		$this->load->model('reports/m_payables', 'payables');
		$this->supplier->fields = ['id','name'];
		$this->add_css('reports/ageing-of-receivables.css');
		$payables = $this->payables->generate();
		ksort($payables);
		$this->set_content('reports/aging-payables', [
			'data' => $payables,
			'suppliers' => array_column($this->supplier->all(), 'name', 'id')
		]);
		$this->generate_page();
	}
}
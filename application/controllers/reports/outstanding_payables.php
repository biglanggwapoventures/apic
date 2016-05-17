<?php

class Outstanding_payables extends PM_Controller_v2 
{
	public function __construct()
	{
		parent::__construct();
		if(!has_access('special_reports')) show_error('Authorization error.', 401);
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Outstanding Payables');
		$this->set_active_nav(NAV_SPECIAL_REPORTS);
		
	}

	public function index()
	{
		$this->load->model('maintainable/m_supplier', 'supplier');
		$this->load->model('reports/m_outstanding_prr', 'payables');

		//check if supplier is specified
		$supplier_id = $this->input->get('supplier_id');
		if(is_numeric($supplier_id) && $this->supplier->is_valid($supplier_id))
		{
			// if it is, retrieve outstanding rrs
			$supplier_info = $this->supplier->get($supplier_id);
			$data = $this->payables->generate($supplier_id);
		}
        $this->add_javascript(['bootstrap-editable.min.js', 'reports/outstanding-purchase-rr.js']);
		$this->add_css(array('reports/outstanding-packing-list.css', 'reports/outstanding-payables.css'));

		$this->supplier->fields = ['id', 'name'];
		$this->set_content('reports/outstanding-payables', [
			'suppliers' => $this->supplier->all(),
			'data' => isset($data) ? $data : FALSE,
			'supplier_info' => isset($supplier_info) ? $supplier_info : FALSE
		]);

		$this->generate_page();
	}
	
}
<?php

class Product_type_sales extends PM_Controller_v2 
{
	public function __construct(){
		parent::__construct();
		if(!has_access('reports')){
			show_error('Authorization error', 401);
		}
		$this->setTabTitle('Product Type Sales');
		$this->set_active_nav(NAV_REPORTS);
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Product Type Sales');
	}



	public function index(){
		function cmp($a, $b)
		{
			return $b['value_total'] - $a['value_total'];
		}
		$this->load->model('reports/m_product_category_sales', 'report');
		$this->load->model('sales/m_customer', 'customer');
		$this->load->model('inventory/m_category', 'category');
		$this->load->model('sales/m_agent', 'agent');
		
		$params = elements(['start_date', 'end_date', 'categories', 'agent_id'], $this->input->get(), FALSE);
		$data = $this->report->generate($params['start_date'], $params['end_date'], $params['categories'], $params['agent_id']);
		usort($data, 'cmp');
		$this->set_content('reports/product-type-sales/index',[
			'data' =>  $data,
			'customers' => array_column($this->customer->all(['status' => 'a']), 'company_name', 'id'),
			'categories' => array_column($this->category->all(['status' => 'a']), NULL, 'id'),
			'agents' => ['' => ''] + array_column($this->agent->all(['status' => 'a']), 'name', 'id')
		]);
		$this->generate_page();
	}
}
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
		$data = $this->report->generate($this->input->get('start_date'), $this->input->get('end_date'), $this->input->get('categories'));
		usort($data, 'cmp');
		$this->set_content('reports/product-type-sales/index',[
			'data' =>  $data,
			'customers' => array_column($this->customer->all(['status' => 'a']), 'company_name', 'id'),
			'categories' => array_column($this->category->all(['status' => 'a']), NULL, 'id')
		]);
		$this->generate_page();
	}

	public function test(){
		function cmp($a, $b)
		{
			return $b['value_total'] - $a['value_total'];
		}
		$this->load->model('reports/m_product_type_sales', 'report');
		$this->load->model('sales/m_customer', 'customer');
		$data = $this->report->get_manual($this->input->get('start_date'), $this->input->get('end_date'));
		usort($data, 'cmp');
		$this->set_content('reports/product-type-sales/index',[
			'data' =>  $data,
			'customers' => array_column($this->customer->get(), 'company_name', 'id')
		]);
		$this->generate_page();
	}
}
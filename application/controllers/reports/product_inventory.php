<?php

class Product_inventory extends PM_Controller_v2 
{
	public function __construct(){
		parent::__construct();
		if(!has_access('reports')) show_error('Authorization error', 401);
		$this->set_content_title('Reports');
		$this->set_content_subtitle('Product Inventory');
		$this->set_active_nav(NAV_REPORTS);	
	}

	public function index()
	{
		$this->load->model('inventory/m_product', 'product');
		$product_id = $this->input->get('product_id');

		if(!is_numeric($product_id) || !$this->product->is_valid($product_id))
		{
			return;
		}

		$stock = $this->product->get_stocks($product_id);
		$product = $this->product->get(FALSE, ['id' => $product_id]);
		$this->add_javascript(['plugins/sticky-thead.js', 'reports/product-inventory.js']);
		$this->set_content('reports/product-inventory', [
			'current_cost' => [],
			'current_stock' => $stock ? $stock[$product_id] : 0,
			'data' => $this->product->get_logs($product_id),
			'product' => $product[0]
		]);
		$this->generate_page();
	}
}
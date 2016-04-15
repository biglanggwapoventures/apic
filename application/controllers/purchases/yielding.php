<?php

class Yielding extends PM_Controller_v2 {

    function __construct()
    {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Yielding');
    }

    function index()
    {
    	$this->load->model('purchases/m_purchase_receiving');
    	$this->load->model('inventory/m_product', 'product');
    	$rr_no = $this->input->get('rr');
    	$data = $this->m_purchase_receiving->get(TRUE, FALSE, ['receiving.id' => $rr_no]);
    	$this->set_content('purchases/yielding', [
    		'form_title' => "Yield products from RR# {$rr_no}",
    		'form_action' => base_url('purchases/yielding/save'),
    		'product_list' => $this->product->get_list(),
    		'data' => $data[0]
		])->generate_page();
    }

    function save()
    {
    	$this->generate_response($this->input->post())->to_JSON();
    }
}
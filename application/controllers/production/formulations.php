<?php

class Formulations extends PM_Controller_v2 
{

	protected $id;

	public function __construct()
	{
		parent::__construct();
		if(!has_access('production')){
			show_error();
		}
		$this->set_active_nav(NAV_PRODUCTION);
		$this->setTabTitle('Formulations');
		$this->set_content_title('Production');
		$this->set_content_subtitle('Formulations');
		$this->load->model('production/m_formulation', 'formulation');
	}

	public function index()
	{
		$this->add_javascript(['plugins/sticky-thead.js', 'production-formulation/master-list.js']);
		$this->set_content('production/formulations', [

		]);
		$this->generate_page();
	}

	public function create()
	{
		$this->load->model('inventory/m_product', 'product');
		$this->add_javascript(['numeral.js', 'production-formulation/manage.js']);
		$this->set_content('production/manage-formulation', [
			'title' => "Create new formulation",
			'raw_mats' => $this->product->get(FALSE, [M_Product::PRODUCT_CLASS => M_Product::CLASS_RAW, M_Product::PRODUCT_STATUS => 'Active']),
			'data' => [ 'formulation' => [], 'raw_mats' =>[] ],
			'action' => 'c'
		]);
		$this->generate_page();
	}

	public function edit($id = FALSE)
	{
		if(!$id || !$formulation = $this->formulation->find($id)){
			show_404();
		}
		$this->load->model('inventory/m_product', 'product');
		$this->add_javascript(['numeral.js', 'production-formulation/manage.js']);
		$this->set_content('production/manage-formulation', [
			'title' => "Update formulation: {$formulation['formulation']['formulation_code']}",
			'raw_mats' => $this->product->get(FALSE, [M_Product::PRODUCT_CLASS => M_Product::CLASS_RAW, M_Product::PRODUCT_STATUS => 'Active']),
			'data' => $formulation,
			'action' => 'e'
		]);
		$this->generate_page();
	}

	public function store()
	{
		$result = $this->_perform_validation();
		if($result['error_flag']){
			$this->generate_response(['result' => FALSE, 'messages' => $result['message']])->to_JSON();
			return;
		}
		$created = $this->formulation->create($this->_format_data());
		if($created){
			$this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully created new formulation!')));
			$this->generate_response(['result' => TRUE])->to_JSON();
			return;
		}
		$this->generate_response(['result' => FALSE, 'messages' => 'Cannot create formulation due to an unknown error. Please try again later.'])->to_JSON();
	}

	public function update($id)
	{
		$this->id = $id;
		$result = $this->_perform_validation('u');
		if($result['error_flag']){
			$this->generate_response(['result' => FALSE, 'messages' => $result['message']])->to_JSON();
			return;
		}
		$updated = $this->formulation->update($id, $this->_format_data('u'));
		if($updated){
			$this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully updated the formulation!')));
			$this->generate_response(['result' => TRUE])->to_JSON();
			return;
		}
		$this->generate_response(['result' => FALSE, 'messages' => 'Cannot update formulation due to an unknown error. Please try again later.'])->to_JSON();
	}


	public function master_list()
	{
		$params = $this->search_params();
		$result = $this->formulation->all($params['query'], $params['like']);
		$this->generate_response(['result' => TRUE, 'data' => $result])->to_JSON();
	}


	public function search_params()
	{
		$params = elements(['formulation_code', 'status'], $this->input->get());
		$query = [];
		$like = [];
		if($params['formulation_code']){
			$like['formulation_code'] = $params['formulation_code'];
		}
		if($params['status'] === 'a'){
			$query['status'] = 1;
		}else if($params['status'] === 'ia'){
			$query['status'] = 0;
		}
		return [
			'query' => $query,
			'like' => $like
		];
	}

	public function _perform_validation($action = 'c')
	{	
		
		if($action === 'c'){
			// this is a new formulation

			// make sure formulation code is not empty
			if(!$code = $this->input->post('formulation_code')){
				return $this->response(TRUE, ['Formulation code is required!']);
			}
			// make sure the formulation code is unique
			if(!$this->formulation->has_unique_code($code)){
				return $this->response(TRUE, ['Formulation code is already in use!']);
			}

		}else{
			// this is an updated formulation
			if($this->formulation->is_active($this->id) && !is_admin()){
				return $this->response(TRUE, ['You are not allowed to update active formulations!']);
			}
			// make sure formulation code is not empty
			if(!$code = $this->input->post('formulation_code')){
				return $this->response(TRUE, ['Formulation code is required!']);
			}
			if(!$this->formulation->has_unique_code($code, $this->id)){
				return $this->response(TRUE, ['Formulation code is already in uses!']);
			}
		}
		if(!is_admin() && in_array([0,1], $this->input->post('status'))){
			return $this->response(TRUE, ['Only administrators can update the formulation status!']);
		}
		$formula = $this->input->post('formula');
		$raw_mats = [];
		if(!is_array($formula) || $formula === NULL){
			return $this->response(TRUE, ['Malformed payload.']);
		}
		foreach($formula AS $row){
			if(!is_array($row) || !isset($row['quantity']) || !isset($row['fk_inventory_product_id'])){
				return $this->response(TRUE, ['Malformed payload.']);
			}
			if($row['quantity'] <= 0){
				return $this->response(TRUE, ['All of the quantities must be greater than 0!']);
			}
			$raw_mats[] = $row['fk_inventory_product_id'];
		}
		return $this->response(FALSE);
	}

	public function _format_data($action = 'c')
	{
		$data = [];
		if($code = $this->input->post('formulation_code')){
			$data['formulation']['formulation_code'] = $code;
		}
		if($this->input->post('status') !==  NULL){
			$data['formulation']['status'] = $this->input->post('status');
		}
		
		$data['formula'] = array_map(function($var) USE($action){
			$temp = elements(['quantity', 'fk_inventory_product_id'], $var);
			if($action === 'u' && isset($var['id'])){
				$temp['id'] = $var['id'];
			}
			return $temp;
		}, $this->input->post('formula'));

		return $data;
	}

	public function get_cost($product_id)
	{
		if(!is_admin()){
			$this->generate_response(['result' => FALSE, 'message' => 'Authorization error'])->to_JSON();
			return;
		}
		$cost = $this->formulation->get_cost($product_id);
		$this->generate_response(['result' => TRUE, 'data' => ['cost' => $cost]])->to_JSON();
	}

}
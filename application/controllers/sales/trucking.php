<?php

class Trucking extends PM_Controller_v2
{

	public function __construct()
	{
		parent::__construct();
        if(!has_access('sales')) show_error('Authorization error', 401);
        $this->set_content_title('Sales');
        $this->set_content_subtitle('Trucking');
        $this->set_active_nav(NAV_SALES);
        $this->load->model('sales/m_trucking', 'trucking');
	}

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['trucking_name', 'driver', 'plate_number', 'status'], $this->input->get(), FALSE);

        if($params['status'] && in_array($params['status'], ['a', 'ia'])){
            $search['status'] = $params['status'];
        }elseif($params['status'] === FALSE){
            $search['status'] = 'a';
        }

        if($params['trucking_name'] && trim($params['trucking_name'])){
            $wildcards['trucking_name'] = $params['trucking_name'];
        }

        if($params['driver'] && trim($params['driver'])){
            $wildcards['driver'] = $params['driver'];
        }

        if($params['plate_number'] && trim($params['plate_number'])){
            $wildcards['plate_number'] = $params['plate_number'];
        }

        
        return compact(['search', 'wildcards']);
    }

	public function index() 
	{
        $this->add_javascript(['sales-trucking/listing.js', 'plugins/sticky-thead.js']);
    
        $params = $this->_search_params();
        $this->set_content('sales/trucking/listing', [
            'items' => $this->trucking->all($params['search'], $params['wildcards'])
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript(['sales-trucking/manage.js']);
        $this->set_content('sales/trucking/manage', [
            'title' => 'Create new trucking',
            'action' => base_url('sales/trucking/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$trucking = $this->trucking->get($id)){
            show_404();
        }
        $this->add_javascript(['sales-trucking/manage.js']);
        $this->set_content('sales/trucking/manage', [
            'title' => "Update trucking: {$trucking['trucking_name']}",
            'action' => base_url("sales/trucking/update/{$trucking['id']}"),
            'data' => $trucking
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $trucking = $this->_format_data();
            $this->trucking->create($trucking);
            $this->flash_message(FALSE, 'New trucking has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$trucking = $this->trucking->get($id)){
            $this->generate_response(TRUE, 'Please select a valid trucking to update.')->to_JSON();
            return;
        }
        if(!can_update($trucking)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $trucking = $this->_format_data();
            $this->trucking->update($id, $trucking);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$trucking = $this->trucking->get($id)){
            $this->generate_response(TRUE, 'Please select a valid trucking to delete.')->to_JSON();
            return;
        }
        if(!can_delete($trucking)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->trucking->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('plate_number', 'trucking plate number', 'trim|required|is_unique[sales_trucking.plate_number]');
        }else{
            $this->form_validation->set_rules('plate_number', 'trucking plate number', 'trim|required|callback__validate_plate_number');
        }
        $this->form_validation->set_rules('driver', 'trucking driver', 'trim|required');
        $this->form_validation->set_rules('trucking_name', 'trucking name', 'trim|required');
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'trucking status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['plate_number', 'trucking_name', 'driver', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_plate_number($plate_number)
    {
        $this->form_validation->set_message('_validate_plate_number', 'The %s is already in use.');
        return $this->trucking->has_unique_plate_number($plate_number, $this->id);
    }
}
<?php

class Units extends PM_Controller_v2
{

	public function __construct()
	{
		parent::__construct();
        if(!has_access('inventory')) show_error('Authorization error', 401);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Units');
        $this->set_active_nav(NAV_INVENTORY);
        $this->load->model('inventory/m_unit', 'unit');
	}

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['status', 'description'], $this->input->get(), FALSE);

        if($params['status'] && in_array($params['status'], ['a', 'ia'])){
            $search['status'] = $params['status'];
        }elseif($params['status'] === FALSE){
            $search['status'] = 'a';
        }

        if($params['description'] && trim($params['description'])){
            $wildcards['description'] = $params['description'];
        }
        
        return compact(['search', 'wildcards']);
    }

	public function index() 
	{
        $this->add_javascript(['inventory-units/listing.js', 'plugins/sticky-thead.js']);

        $params = $this->_search_params();

        $this->set_content('inventory/units/listing', [
            'items' => $this->unit->all($params['search'], $params['wildcards'])
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript('inventory-units/manage.js');
        $this->set_content('inventory/units/manage', [
            'title' => 'Create new unit',
            'action' => base_url('inventory/units/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$unit = $this->unit->get($id)){
            show_404();
        }
        $this->add_javascript('inventory-units/manage.js');
        $this->set_content('inventory/units/manage', [
            'title' => "Update unit: {$unit['description']}",
            'action' => base_url("inventory/units/update/{$id}"),
            'data' => $unit
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $unit = $this->_format_data();
            $this->unit->create($unit);
            $this->flash_message(FALSE, 'New unit has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$unit = $this->unit->get($id)){
            $this->generate_response(TRUE, 'Please select a valid unit to update.')->to_JSON();
            return;
        }
        if(!can_update($unit)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $unit = $this->_format_data();
            $this->unit->update($id, $unit);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$unit = $this->unit->get($id)){
            $this->generate_response(TRUE, 'Please select a valid unit to delete.')->to_JSON();
            return;
        }
        if(!can_delete($unit)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->unit->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('description', 'unit description', 'trim|required|is_unique[inventory_unit.description]');
        }else{
            $this->form_validation->set_rules('description', 'unit description', 'trim|required|callback__validate_unit_description');
        }
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'unit status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['description', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_unit_description($description)
    {
        $this->form_validation->set_message('_validate_unit_description', 'The %s is already in use.');
        return $this->unit->has_unique_description($description, $this->id);
    }
}
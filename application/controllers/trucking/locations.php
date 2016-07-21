<?php

class Locations extends PM_Controller_v2
{

	public function __construct()
	{
		parent::__construct();
        if(!has_access('trucking')) show_error('Authorization error', 401);
        $this->set_active_nav(NAV_TRUCKING);
        $this->set_content_title('Trucking');
        $this->set_content_subtitle('Locations');
        $this->load->model('trucking/m_locations', 'location');
	}

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['name'], $this->input->get(), FALSE);

        if($params['name'] && trim($params['name'])){
            $wildcards['name'] = $params['name'];
        }

        
        return compact(['search', 'wildcards']);
    }

	public function index() 
	{
        $this->add_javascript(['trucking-locations/listing.js', 'plugins/sticky-thead.js']);

        $params = $this->_search_params();

        $this->set_content('trucking/locations/listing', [
            'items' => $this->location->all(null,$params['wildcards'])
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript('trucking-locations/manage.js');
        $this->set_content('trucking/locations/manage', [
            'title' => 'Create new location',
            'action' => base_url('trucking/locations/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$location = $this->location->get($id)){
            show_404();
        }
        $this->add_javascript('trucking-locations/manage.js');
        $this->set_content('trucking/locations/manage', [
            'title' => "Update location name: {$location['name']}",
            'action' => base_url("trucking/locations/update/{$id}"),
            'data' => $location
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $location = $this->_format_data();
            $this->location->create($location);
            $this->flash_message(FALSE, 'New location has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$location = $this->location->get($id)){
            $this->generate_response(TRUE, 'Please select a valid location to update.')->to_JSON();
            return;
        }
        if(!can_update($location)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $location = $this->_format_data();
            $this->location->update($id, $location);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$location = $this->location->get($id)){
            $this->generate_response(TRUE, 'Please select a valid agent to delete.')->to_JSON();
            return;
        }
        if(!can_delete($location)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->location->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('name', 'location name', 'trim|required|is_unique[tracking_location.name]');
        }else{
            $this->form_validation->set_rules('name', 'location name', 'trim|required|callback__validate_location_name');
        }
        
    }

    public function _format_data()
    {
        $input = elements(['name'], $this->input->post());
        $input['last_updated_by'] = $this->session->userdata('user_id');
        return $input;
    }

    public function _validate_location_name($name)
    {
        $this->form_validation->set_message('_validate_unit_description', 'The %s is already in use.');
        return $this->location->has_unique_name($name, $this->id);
    }
}
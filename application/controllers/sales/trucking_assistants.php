<?php

class trucking_assistants extends PM_Controller_v2
{

	public function __construct()
	{
		parent::__construct();
        check_access('inventory');
        $this->set_content_title('Sales');
        $this->set_content_subtitle('Trucking Assistants');
        $this->set_active_nav(NAV_SALES);
        $this->load->model('sales/trucking_assistant_model', 'assistant');
	}

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['status', 'name'], $this->input->get(), FALSE);

        if($params['status'] && in_array($params['status'], ['a', 'ia'])){
            $search['status'] = $params['status'];
        }elseif($params['status'] === FALSE){
            $search['status'] = 'a';
        }

        if($params['name'] && trim($params['name'])){
            $wildcards['name'] = $params['name'];
        }
        
        return compact(['search', 'wildcards']);
    }

	public function index() 
	{
        $this->add_javascript(['sales-trucking-assistant/listing.js', 'plugins/sticky-thead.js']);
    
        $params = $this->_search_params();

        $this->set_content('sales/trucking-assistants/listing', [
            'items' => $this->assistant->all($params['search'], $params['wildcards'])
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript('sales-trucking-assistant/manage.js');
        $this->set_content('sales/trucking-assistants/manage', [
            'title' => 'Create new trucking assistant',
            'action' => base_url('sales/trucking_assistants/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$assistant = $this->assistant->get($id)){
            show_404();
        }
        $this->add_javascript('sales-trucking-assistant/manage.js');
        $this->set_content('sales/trucking-assistants/manage', [
            'title' => "Update assistant: {$assistant['name']}",
            'action' => base_url("sales/trucking_assistants/update/{$id}"),
            'data' => $assistant
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $assistant = $this->_format_data();
            $this->assistant->create($assistant);
            $this->flash_message(FALSE, 'New trucking assistant has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$assistant = $this->assistant->get($id)){
            $this->generate_response(TRUE, 'Please select a valid assistant to update.')->to_JSON();
            return;
        }
        if(!can_update($assistant)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $assistant = $this->_format_data();
            $this->assistant->update($id, $assistant);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$assistant = $this->assistant->get($id)){
            $this->generate_response(TRUE, 'Please select a valid assistant to delete.')->to_JSON();
            return;
        }
        if(!can_delete($assistant)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->assistant->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('name', 'assistant name', 'trim|required|is_unique[trucking_assistants.name]');
        }else{
            $this->form_validation->set_rules('name', 'assistant name', 'trim|required|callback__validate_assistant_name');
        }
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'category status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['name', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_assistant_name($name)
    {
        $this->form_validation->set_message('_validate_assistant_name', 'The %s is already in use.');
        return $this->assistant->has_unique_name($name, $this->id);
    }
}
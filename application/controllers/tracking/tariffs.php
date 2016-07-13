<?php

class Tariffs extends PM_Controller_v2
{

    public function __construct()
    {
        parent::__construct();
        if(!has_access('tracking')) show_error('Authorization error', 401);
        $this->set_content_title('Tracking');
        $this->set_content_subtitle('Tariffs');
        $this->set_active_nav(NAV_TRACKING);
        $this->load->model('tracking/m_tariffs', 'tariff');
    }

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['status', 'name', 'area'], $this->input->get(), FALSE);

        if($params['status'] && in_array($params['status'], ['a', 'ia'])){
            $search['status'] = $params['status'];
        }elseif($params['status'] === FALSE){
            $search['status'] = 'a';
        }

        if($params['name'] && trim($params['name'])){
            $wildcards['name'] = $params['name'];
        }
        if($params['area'] && trim($params['area'])){
            $wildcards['area'] = $params['area'];
        }

        
        return compact(['search', 'wildcards']);
    }

    public function index() 
    {
        $this->add_javascript(['tracking-tariffs/listing.js', 'plugins/sticky-thead.js']);

        $params = $this->_search_params();

        $this->set_content('tracking/tariffs/listing', [
            'items' => $this->tariff->all()
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript('tracking-tariffs/manage.js');
        $this->set_content('tracking/tariffs/manage', [
            'title' => 'Create new tariff',
            'action' => base_url('tracking/tariffs/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$agent = $this->agent->get($id)){
            show_404();
        }
        $this->add_javascript('tracking-tariffs/manage.js');
        $this->set_content('tracking/tariffs/manage', [
            'title' => "Update sales agent: {$agent['name']}",
            'action' => base_url("tracking/tariffs/update/{$id}"),
            'data' => $agent
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $agent = $this->_format_data();
            $this->agent->create($agent);
            $this->flash_message(FALSE, 'New agent has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$agent = $this->agent->get($id)){
            $this->generate_response(TRUE, 'Please select a valid agent to update.')->to_JSON();
            return;
        }
        if(!can_update($agent)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $agent = $this->_format_data();
            $this->agent->update($id, $agent);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$agent = $this->agent->get($id)){
            $this->generate_response(TRUE, 'Please select a valid agent to delete.')->to_JSON();
            return;
        }
        if(!can_delete($agent)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->agent->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('name', 'agent name', 'trim|required|is_unique[sales_agent.name]');
            $this->form_validation->set_rules('agent_code', 'agent code', 'trim|required|alpha_numeric|is_unique[pm_sales_agent.agent_code]');
        }else{
            $this->form_validation->set_rules('name', 'agent name', 'trim|required|callback__validate_agent_name');
            $this->form_validation->set_rules('agent_code', 'agent code', 'trim|required|alpha_numeric|callback__validate_agent_code');
        }
        $this->form_validation->set_rules('area', 'agent area', 'trim|required');
        $this->form_validation->set_rules('commission_rate', 'agent commission rate', 'trim|required|numeric');
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'agent status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['name', 'area', 'commission_rate', 'agent_code', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_agent_name($name)
    {
        $this->form_validation->set_message('_validate_unit_description', 'The %s is already in use.');
        return $this->agent->has_unique_name($name, $this->id);
    }

    public function _validate_agent_code($code)
    {
        $this->form_validation->set_message('_validate_agent_code', 'The %s is already in use.');
        return $this->agent->has_unique_code($code, $this->id);
    }
}
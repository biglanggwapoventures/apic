<?php

class Trip_tickets extends PM_Controller_v2
{

    public function __construct()
    {
        parent::__construct();
        if(!has_access('tracking')) show_error('Authorization error', 401);
        $this->set_content_title('Tracking');
        $this->set_content_subtitle('Trip Tickets');
        $this->set_active_nav(NAV_TRACKING);
        $this->load->model('tracking/m_trip_tickets', 'trip_ticket');
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
        $this->add_javascript(['tracking-trip-tickets/listing.js', 'plugins/sticky-thead.js']);

        $params = $this->_search_params();

        $this->set_content('tracking/trip-ticket/listing', [
            'items' => $this->trip_ticket->all()
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript('tracking-trip-ticket/manage.js');
        $this->set_content('tracking/trip-ticket/manage', [
            'title' => 'Create new Trip Ticket',
            'action' => base_url('tracking/trip_tickets/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$trip_ticket = $this->trip_ticket->get($id)){
            show_404();
        }
        $this->add_javascript('tracking-trip-tickets/manage.js');
        $this->set_content('tracking/trip-ticket/manage', [
            'title' => "Update sales trip ticket: {$trip_ticket['name']}",
            'action' => base_url("tracking/trip_tickets/update/{$id}"),
            'data' => $trip_ticket
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $trip_ticket = $this->_format_data();
            $this->trip_ticket->create($trip_ticket);
            $this->flash_message(FALSE, 'New trip_ticket has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$trip_ticket = $this->trip_ticket->get($id)){
            $this->generate_response(TRUE, 'Please select a valid trip_ticket to update.')->to_JSON();
            return;
        }
        if(!can_update($trip_ticket)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $trip_ticket = $this->_format_data();
            $this->trip_ticket->update($id, $trip_ticket);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$trip_ticket = $this->trip_ticket->get($id)){
            $this->generate_response(TRUE, 'Please select a valid trip_ticket to delete.')->to_JSON();
            return;
        }
        if(!can_delete($trip_ticket)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->trip_ticket->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('name', 'trip_ticket name', 'trim|required|is_unique[sales_trip_ticket.name]');
            $this->form_validation->set_rules('trip_ticket_code', 'trip_ticket code', 'trim|required|alpha_numeric|is_unique[pm_sales_trip_ticket.trip_ticket_code]');
        }else{
            $this->form_validation->set_rules('name', 'trip_ticket name', 'trim|required|callback__validate_trip_ticket_name');
            $this->form_validation->set_rules('trip_ticket_code', 'trip_ticket code', 'trim|required|alpha_numeric|callback__validate_trip_ticket_code');
        }
        $this->form_validation->set_rules('area', 'trip_ticket area', 'trim|required');
        $this->form_validation->set_rules('commission_rate', 'trip_ticket commission rate', 'trim|required|numeric');
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'trip_ticket status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['name', 'area', 'commission_rate', 'trip_ticket_code', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_trip_ticket_name($name)
    {
        $this->form_validation->set_message('_validate_unit_description', 'The %s is already in use.');
        return $this->trip_ticket->has_unique_name($name, $this->id);
    }

    public function _validate_trip_ticket_code($code)
    {
        $this->form_validation->set_message('_validate_trip_ticket_code', 'The %s is already in use.');
        return $this->trip_ticket->has_unique_code($code, $this->id);
    }
}
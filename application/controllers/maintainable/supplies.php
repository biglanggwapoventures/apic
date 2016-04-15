<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of supplies
 *
 * @author Adr
 */
class Supplies extends PM_Controller_v2 {

    public function __construct() {
        parent::__construct();
        $this->set_active_nav(NAV_MAINTAINABLE);
        $this->set_content_title('Maintainable');
        $this->set_content_subtitle('Materials/Supplies');
        $this->load->model('maintainable/m_supply', 'supply');
    }

    public function index() {
        $this->add_css('bootstrap-editable.css');
        $this->add_javascript(['bootstrap-editable.min.js', 'ractivejs/ractive.min.js']);
        $this->setTabTitle('Maintainable :: Materials/Supplies');
        $this->load->model('inventory/m_unit');
        $this->set_content('maintainable/supplies', [
            'listing' => $this->supply->all(),
            'unit_listing' => $this->m_unit->get()
        ]);
        $this->generate_page();
    }

    public function ajax_add() {
        if ($this->input->is_ajax_request() === FALSE) {
            show_error('XHR required. Please contact administator.', 500, 'Request error');
        }
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('unit', 'Unit', 'required|callback__is_valid_unit');
        if ($this->form_validation->run()) {
            $data = ['description' => $this->input->post('description'), 'fk_unit_id' => $this->input->post('unit')];
            $new_supply = $this->supply->insert($data);
            if ($new_supply) {
                $response = $this->response(FALSE, 'New supply added.', ['id' => $new_supply]);
            } else {
                $response = $this->response(TRUE, 'Error while trying to add. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

    public function ajax_update() {
        if ($this->input->is_ajax_request() === FALSE) {
            show_error('XHR required. Please contact administator.', 500, 'Request error');
        }
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('value', 'Description', 'required|callback__is_valid_value');
        $this->form_validation->set_rules('pk', 'Chart ID', 'required|integer');
        $this->form_validation->set_rules('name', 'Chart ID', 'required|callback__is_valid_field');
        if ($this->form_validation->run()) {
            $field = $this->input->post('name') === 'unit' ? 'fk_unit_id' : $this->input->post('name');
            $updated = $this->supply->update( [$field => $this->input->post('value')], $this->input->post('pk') );
            if ($updated) {
                $response = $this->response(FALSE, 'Supply updated.');
            } else {
                $response = $this->response(TRUE, 'Error while trying to update. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

    public function ajax_delete() {
        if ($this->input->is_ajax_request() === FALSE) {
            show_error('XHR required. Please contact administator.', 500, 'Request error');
        }
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('id', 'Chart ID', 'required|integer');
        if ($this->form_validation->run()) {
            $deleted = $this->supply->delete($this->input->post('id'));
            if ($deleted) {
                $response = $this->response(FALSE, 'Chart deleted.');
            } else {
                $response = $this->response(TRUE, 'Error while trying to delete. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

    function _is_valid_unit($unit_id) {
        $this->load->model('inventory/m_unit');
        if ($this->m_unit->is_valid($unit_id)) {
            return TRUE;
        }
        $this->form_validation->set_message('_is_valid_unit', 'The given product unit is valid.');
        return FALSE;
    }

    function _is_valid_field($field_name) {
        if(in_array($field_name, ['description', 'unit'])){
            return TRUE;
        }
        $this->form_validation->set_message('_is_valid_field', "Unknown {$field_name} field");
        return FALSE;
    }
    
    function _is_valid_value($value) {
        $this->load->model('inventory/m_unit');
        if($this->input->post('name') === 'unit' && !$this->m_unit->is_valid($value)){
            return FALSE;
        }
        return TRUE;
    }

}

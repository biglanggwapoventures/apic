<?php

class packaging extends PM_Controller_v2 {

    protected $view_url = 'inventory/';

    function __construct() {
        parent::__construct();
        $this->load->model('inventory/Packaging_model', 'packaging');
    }

    public function index() {
        $this->setTabTitle('Packaging - Inventory');
        $this->set_active_nav(NAV_MAINTAINABLE);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Packaging');
        $this->add_javascript([
            'plugins/angular/angular.min.js',
            'plugins/angular-animate/angular-animate.min.js',
            'plugins/angular-bootstrap/ui-bootstrap-tpls-0.13.3.min.js',
            'inventory/packaging/packaging.js',
        ]);
        $this->set_content($this->view('packaging'));
        $this->generate_page();
    }
    
    public function ajax_initialize() {
        $this->output->set_content_type('json')->set_output(json_encode($this->response(FALSE, '', $this->packaging->all())));
    }

    public function ajax_manage_medication_modal() {
        $this->load->view($this->view('manage-medication-modal'));
    }

    public function ajax_create() {
        $this->output->set_content_type('json');
        $input = $this->_validate_input();
        if (!$input['result']) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors', array_values($input['error_messages']))));
            return;
        }
        $result = $this->medication->create($input['data']);
        if ($result) {
            $this->output->set_output(json_encode($this->response(FALSE, '', ['id' => $result])));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, 'An unexpected error has occured. Please try again later.')));
    }

    public function ajax_update() {
        $this->output->set_content_type('json');
        $input = $this->_validate_input();
        if (!$input['result']) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors', array_values($input['error_messages']))));
            return;
        }
        $result = $this->medication->update($this->input->post('id'), $input['data']);
        if ($result) {
            $this->output->set_output(json_encode($this->response(FALSE)));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, 'An unexpected error has occured. Please try again later.')));
    }

    

    public function _filter_input() {
        $data = elements(['code', 'description', 'fk_unit_id'], $this->input->post());
        $data['status'] = is_adm() ? $this->input->post('status') : 'Inactive';
        return $data;
    }

    /* set validation rules on input */

    public function _validate_input() {
        $this->form_validation->set_rules('code', 'item code', 'required|callback__validate_item_code');
        $this->form_validation->set_rules('description', 'item description', 'required|callback__validate_item_description');
        $this->form_validation->set_rules('fk_unit_id', 'item unit', 'required');
        $this->form_validation->set_rules('status', 'item status', 'callback__validate_status');
        if ($this->form_validation->run()) {
            return ['result' => TRUE, 'data' => $this->_filter_input()];
        }
        return ['result' => FALSE, 'error_messages' => $this->form_validation->error_array()];
    }

    /* start of callback validation functions */

    public function _validate_item_code($item_code) {
        $this->form_validation->set_message('_validate_item_code', 'The %s is already in use by another medication.');
        return $this->medication->is_unique('code', $item_code, $this->input->post('id'));
    }

    public function _validate_item_description($item_description) {
        $this->form_validation->set_message('_validate_item_description', 'The %s is already in use by another medication.');
        return $this->medication->is_unique('description', $item_description, $this->input->post('id'));
    }

    public function _validate_status($status) {
        if (!$status) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_status', 'You have entered an invalid %s.');
        return in_array($status, ['Active', 'Inactive']);
    }

    /* end of callback validation functions */
}

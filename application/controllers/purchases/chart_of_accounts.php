<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of chart_of_accounts
 *
 * @author Adr
 */
class Chart_of_accounts extends PM_Controller_v2 {

    public function __construct() {
        parent::__construct();
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Chart of accounts');
        $this->load->model('maintainable/m_chart', 'chart');
    }

    public function index() {
        $this->add_css('bootstrap-editable.css');
        $this->add_javascript('bootstrap-editable.min.js');
        $this->setTabTitle('Purchases :: Chart of accounts');
        $this->set_content('purchases/charts', [
            'listing' => $this->chart->all()
        ]);
        $this->generate_page();
    }
    
    public function ajax_add(){
        if($this->input->is_ajax_request() === FALSE){
            show_error('XHR required. Please contact administator.', 500, 'Request error'); 
        }
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if($this->form_validation->run()){
            $new_chart = $this->chart->insert($this->input->post('description'), $this->session->userdata('user_id'));
            if($new_chart){
                $response =  $this->response(FALSE, 'New chart added.', ['id' => $new_chart]);
            }else{
                $response =  $this->response(TRUE, 'Error while trying to add. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        }else{
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
        
    }
    
    public function ajax_update(){
        if($this->input->is_ajax_request() === FALSE){
            show_error('XHR required. Please contact administator.', 500, 'Request error'); 
        }
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('value', 'Description', 'required');
        $this->form_validation->set_rules('pk', 'Chart ID', 'required|integer');
        if($this->form_validation->run()){
            $new_chart = $this->chart->update($this->input->post('pk'), $this->input->post('value'));
            if($new_chart){
                $response =  $this->response(FALSE, 'Chart updated.');
            }else{
                $response =  $this->response(TRUE, 'Error while trying to update. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        }else{
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }
    
    public function ajax_delete(){
        if($this->input->is_ajax_request() === FALSE){
            show_error('XHR required. Please contact administator.', 500, 'Request error'); 
        }
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('id', 'Chart ID', 'required|integer');
        if($this->form_validation->run()){
            $deleted = $this->chart->delete($this->input->post('id'));
            if($deleted){
                $response =  $this->response(FALSE, 'Chart deleted.');
            }else{
                $response =  $this->response(TRUE, 'Error while trying to delete. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        }else{
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Formulation
 *
 * @author adriannatabio
 */
class Formulation extends PM_Controller_v2 {

    public $segment = 'production/formulation/';

    /* VIEWS */
    private $_master_list_view = 'production/formulation';
    private $_manage_content_view = 'production/manage-formulation';
    private $_content = array();

    const SUBJECT = 'formulation';

    public function __construct() {
        parent::__construct();
        /* SET MODULES ACCESS */
        if (!has_access('production')) {
            show_error('Authorization error', 401);
        }
        /* SET PAGE SETTINGS */
        $this->set_active_nav(NAV_PRODUCTION);
        $this->set_content_title('Production');
        $this->set_content_subtitle('Formulation');
        /* LOAD ASSETS */

        /* LOAD NECESSARY MODELS */
        $this->load->model(array('production/m_formulation', 'inventory/m_product'));
        $this->_content['URL_BASE'] = base_url($this->segment);
        $this->_content['raw_products'] = $this->m_product->get(FALSE, array(M_Product::PRODUCT_CLASS => M_Product::CLASS_RAW));
        $this->_content['values'] = array();
    }

    public function a_get() {
        $this->output->set_status_header(200)->set_content_type('json');
        $id = $this->input->get('id');
        $with_details = $this->input->get('with_details');
        $data = $this->m_formulation->get($id, $with_details);
        if ($data) {
            echo json_encode($this->response(FALSE, 'Successful data fetching.', $data));
            return;
        }
        echo json_encode($this->response(TRUE, 'No data retrieved'));
    }

    public function index() {
        $this->_content['entries'] = $this->m_formulation->get();
        $this->_content['URL_PAGE_UPDATE'] = base_url($this->segment . 'update');
        $this->_content['URL_PAGE_CREATE'] = base_url($this->segment . 'create');
        $this->set_content($this->_master_list_view, $this->_content);
        $this->generate_page();
    }

    public function create() {
        if (!empty($input = $this->input->post())) {
            $this->output->set_status_header(200)->set_content_type('json');
            $this->_perform_validation();
            if ($this->form_validation->run()) {
                $this->load->helper(array('array'));
                $input = $this->input->post();
                $general_info = elements(array('formulation_code', 'status'), $input);
                $details = array();
                /* GET DETAILS */
                $details = $this->_extract_details($input['formula']);
                $created = $this->m_formulation->create($general_info, $details);
                if ($created) {
                    echo json_encode($this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('id' => $created)));
                } else {
                    echo json_encode($this->response(TRUE, $this->m_message->add_error(self::SUBJECT)));
                }
            } else {
                echo json_encode($this->response(TRUE, 'Errors have occured.', $this->form_validation->error_array()));
            }
        } else {
            $this->add_javascript(array('numeral.js', 'production-formulation/manage.js'));
            $this->_content['FORM_TITLE'] = 'New Formulation';
             $this->_content['costs'] = [];
            $this->_content['URL_FORM_SUBMIT'] = base_url($this->segment . 'create');
            $this->set_content($this->_manage_content_view, $this->_content);
            $this->generate_page();
        }
    }

    public function update($id = FALSE) {
        if (!$id || !$this->m_formulation->is_valid($id)) {
            show_404();
        }
        if (!empty($input = $this->input->post())) {
            $this->output->set_status_header(200)->set_content_type('json');
            $this->_perform_validation(TRUE);
            if ($this->form_validation->run()) {
                $this->load->helper(array('array'));
                $input = $this->input->post();
                $general_info = elements(array('formulation_code', 'status'), $input);
                $details = array();
                /* GET DETAILS */
                $details = $this->_extract_details($input['formula']);
                $updated = $this->m_formulation->update($id, $general_info, $details);
                if ($updated) {
                    $message = json_encode($this->response(FALSE, $this->m_message->update_success(self::SUBJECT)));
                    $this->session->set_flashdata('FLASH_NOTIF', $message);
                    echo $message;
                } else {
                    echo json_encode($this->response(TRUE, $this->m_message->update_error(self::SUBJECT)));
                }
            } else {
                echo json_encode($this->response(TRUE, 'Errors have occured.', $this->form_validation->error_array()));
            }
        } else {
            $this->add_javascript(['numeral.js', 'production-formulation/manage.js']);
            $values = $this->m_formulation->get($id, TRUE);
            if(is_admin()){
               foreach($values[0]['details'] AS &$v){
                    $v['cost'] = $this->m_formulation->get_cost($v['raw_product_id']);
                }
            }
            $this->_content['values'] = $values[0];
            $this->_content['URL_FORM_SUBMIT'] = base_url($this->segment . "update/{$id}");
            $this->_content['FORM_TITLE'] = "Update Formulation for " . $values[0]['formulation_code'];
            $this->set_content($this->_manage_content_view, $this->_content);
            $this->generate_page();
        }
    }

    private function _perform_validation($for_update = FALSE) {
        $this->form_validation->set_rules('formulation_code', 'Formulation Code', 'required');
        $this->form_validation->set_rules('formula[fk_inventory_product_id][]', 'Formula (Raw Products)', 'required|callback__is_valid_raw_product');
        $this->form_validation->set_rules('formula[quantity][]', 'Formula (Quantity)', 'required|is_numeric');
    }

    private function _extract_details($data = array()) {
        $details = array();
        foreach ($data['fk_inventory_product_id'] as $key => $value) {
            $temp = array(
                'fk_inventory_product_id' => $value,
                'quantity' => $data['quantity'][$key]
            );
            if (isset($data['id'][$key])) {
                $temp['id'] = $data['id'][$key];
            }
            $details[] = $temp;
        }
        return $details;
    }

    /* CALLBACK VALIDATIONS */

    function _is_valid_finished_product($finished_product) {
        $this->load->model('inventory/m_product');
        if ($this->m_product->is_valid_finished($finished_product)) {
            return TRUE;
        }
        $this->form_validation->set_message('_is_valid_finished_product', '%s must contain a valid product.');
        return FALSE;
    }

    function _is_valid_raw_product($raw_products) {
        $this->load->model('inventory/m_product');
        if ($this->m_product->is_valid_raw($raw_products)) {
            return TRUE;
        }
        $this->form_validation->set_message('_is_valid_raw_product', '%s must contain valid products.');
        return FALSE;
    }

    function _is_valid_customer($customers) {
        if ($customers || !is_empty($customers)) {
            $this->load->model('sales/m_customer');
            if ($this->m_customer->is_valid($customers)) {
                return TRUE;
            }
            $this->form_validation->set_message('_is_valid_customer', '%s must contain valid customers.');
            return FALSE;
        }
        return TRUE;
    }

    // public function get_cost($product_id)
    // {
    //     if(is_admin()){
    //         $this->generate_response(FALSE, ['cost' => $this->m_formulation->get_cost($product_id)])->to_JSON();
    //         return;
    //     }

    // }

}

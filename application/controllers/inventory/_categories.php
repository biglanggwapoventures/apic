<?php

class Categories extends PM_Controller {

    private $_nav;
    private $_subtitle;
    private $_additional_css;
    private $_additional_js;
    private $_subject;

    public function __construct() {
        parent::__construct();
        /*restrict unauthorized access*/
        if(!has_access('inventory')){
            show_error('Authorization error', 401);
        }
        $this->_nav = NAV_INVENTORY;
        $this->_subtitle = 'Categories';
        $this->_subject = 'product category';
        $this->_additional_css = array('bootstrap-editable.css');
        $this->_additional_js = array(
            'inventory-maintainables.js',
            'jquery.form.min.js',
            'bootstrap-editable.min.js');
        $this->load->model('inventory/m_category');
    }

    function index() {
        $data['default_keyword'] = $this->input->get('search_keyword');
        $data['entries'] = $this->m_category->get($data['default_keyword']);
        $this->set_data($this->_nav, $this->_subtitle, $this->_additional_css, $this->_additional_js);
        $this->load_page($this->load->view('inventory/categories', $data, TRUE));
    }

    function a_add() {
        $this->form_validation->set_rules('description', 'Description', 'required|is_unique[inventory_category.description]');
        if ($this->form_validation->run()) {
            $return_id = $this->m_category->add($this->input->post('description'));
            if ($return_id) {
                $response = $this->response(FALSE, $this->m_message->add_success($this->_subject), array(
                    'key' => $return_id
                ));
                echo json_encode($response);
            } else {
                echo json_encode($this->response(TRUE, $this->m_message->add_error($this->_subject), array()));
            }
        } else {
            echo json_encode($this->response(TRUE, form_error('description', "<p class='text-danger text-center'><i class='glyphicon glyphicon-exclamation-sign'></i> ", "</p>"), array()));
        }
        exit();
    }

    function a_update() {
        $this->form_validation->set_rules('value', 'Description', 'required|is_unique[inventory_category.description]');
        $this->form_validation->set_rules('pk', 'ID', 'required');
        if ($this->form_validation->run()) {
            $updated = $this->m_category->update($this->input->post('pk'), $this->input->post('value'));
            if ($updated) {
                echo json_encode($this->response(FALSE, $this->m_message->update_success($this->_subject)));
            } else {
                echo json_encode($this->response(TRUE, $this->m_message->update_error($this->_subject)));
            }
        } else {
            echo json_encode($this->response(TRUE, form_error('value', " ", " "), array()));
        }
        exit();
    }

    function a_delete() {
        $this->form_validation->set_rules('pk', 'ID', 'required');
        if ($this->form_validation->run()) {
            $deleted = $this->m_category->delete($this->input->post('pk'));
            if ($deleted) {
                echo json_encode($this->response(FALSE, $this->m_message->delete_success($this->_subject)));
            } else {
                echo json_encode($this->response(TRUE, $this->m_message->delete_error($this->_subject), array()));
            }
        } else {
            echo json_encode($this->response(TRUE, $this->m_message->no_primary_key_error($this->_subject), array()));
        }
        exit();
    }

}

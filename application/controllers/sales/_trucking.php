<?php

class Trucking extends PM_Controller {

    const TITLE = 'Sales';
    const SUBTITLE = 'Trucking';
    const SUBJECT = 'truck';

    private $viewpage_settings = array();

    public function __construct() {
        parent::__construct();
        /*restrict unauthorized access*/
        if(!has_access('sales')){
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->add_css('bootstrap-editable.css');
        $this->add_javascript(array('sales-trucking.js', 'jquery.form.min.js', 'bootstrap-editable.min.js'));
        $this->load->model('sales/m_trucking');
    }

    public function index() {
        $this->viewpage_settings['entries'] = $this->m_trucking->get();
        $this->set_content('sales/trucking', $this->viewpage_settings);
        $this->generate_page();
    }

    public function a_add() {
        $this->form_validation->set_rules('trucking_name', 'Trucking Name', 'required');
        if ($this->form_validation->run()) {
            $id = $this->m_trucking->add($this->input->post());
            if ($id) {
                echo json_encode($this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('id' => $id)));
                exit();
            }
            echo json_encode($this->response(TRUE, '<p class="bg-red text-center">' . $this->m_message->add_error(self::SUBJECT) . '</p>'));
            exit();
        }
        echo json_encode($this->response(TRUE, validation_errors('<p class="bg-red text-center">', '</p>')));
        exit();
    }

    public function a_update() {
        $saved = $this->m_trucking->update($this->input->post());
        echo json_encode($this->response(!$saved, $saved ? $this->m_message->update_success(self::SUBJECT) : $this->m_message->update_error(self::SUBJECT)));
        exit();
    }

    public function a_delete() {
        $deleted = $this->m_trucking->delete($this->input->post('pk'));
        echo json_encode($this->response(!$deleted, $deleted ? $this->m_message->delete_success(self::SUBJECT) : $this->m_message->delete_error(self::SUBJECT)));
        exit();
    }

}

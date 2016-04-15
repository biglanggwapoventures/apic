<?php

class Outstanding_Packing_List extends PM_Controller_v2 {

    const TITLE = 'Reports';
    const SUBTITLE = 'Outstanding Packing List';

    private $_content = array();

    public function __construct() {
        parent::__construct();
        if (!has_access('reports')) {
            show_404();
        }
        $this->setTabTitle(self::TITLE . ' - ' . self::SUBTITLE);
        $this->set_active_nav(NAV_REPORTS);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->load->model('reports/m_outstanding_packing_list', 'report');
    }

    public function index() {
        $this->load->helper('customer');
        $this->add_css(array('reports/outstanding-packing-list.css', 'bootstrap-editable.css'));
        $this->add_javascript(['numeral.js', 'bootstrap-editable.min.js', 'reports/outstanding-packing-list.js']);
        $this->set_content('reports/outstanding-packing-list/view', $this->_content);
        $this->generate_page();
    }

    public function fetch() {
        $this->report->customer = (int) $this->input->get('customer_id');
        $this->output->set_content_type('html');
        $this->output->set_output($this->load->view('reports/outstanding-packing-list/fragment', ['data' => $this->report->generate()], TRUE));
    }

}

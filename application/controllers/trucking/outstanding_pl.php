<?php

class Outstanding_pl extends PM_Controller_v2 {

    const TITLE = 'Trucking';
    const SUBTITLE = 'Outstanding Packing List';

    private $_content = array();

    public function __construct() {
        parent::__construct();
        if (!has_access('trucking')) {
            show_404();
        }
        $this->setTabTitle(self::TITLE . ' - ' . self::SUBTITLE);
        $this->set_active_nav(NAV_TRUCKING);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->load->model('trucking/m_outstanding_pl', 'outstanding');
    }

    public function index() {
        $this->load->helper('customer');
        $this->add_css(array('reports/outstanding-packing-list.css', 'bootstrap-editable.css'));
        $this->add_javascript(['numeral.js', 'bootstrap-editable.min.js', 'printer/print.js', 'plugins/loadash.js', 'trucking-outstanding-pl/outstanding-pl.js']);
        $this->set_content('trucking/outstanding-pl/view', $this->_content);
        $this->generate_page();
    }

    public function fetch() {
        $this->outstanding->customer = (int) $this->input->get('customer_id');
        $this->output->set_content_type('html');
        $this->output->set_output($this->load->view('trucking/outstanding-pl/fragment', ['data' => $this->outstanding->generate()], TRUE));
    }

}

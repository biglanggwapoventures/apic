<?php

class Receivables extends PM_Controller_v2 {

    const TITLE = 'Reports';
    const SUBTITLE = 'Ageing of Receivables';

    private $_content = array();

    public function __construct() {
        parent::__construct();
        $this->setTabTitle(self::TITLE . ' - ' . self::SUBTITLE);
        $this->set_active_nav(NAV_REPORTS);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->load->model('reports/m_receivable', 'report');
    }

    public function index() {
        $this->load->helper('customer');
        $this->add_css(array('reports/ageing-of-receivables.css'));
        $this->add_javascript(['plugins/sticky-thead.js', 'printer/print.js', 'plugins/loadash.js', 'reports-receivables/receivables.js']);
        $data = $this->report->generate_report();
        ksort($data);
        $this->set_content('reports/ageing-of-receivables/view', compact('data'));
        $this->generate_page();
    }

}

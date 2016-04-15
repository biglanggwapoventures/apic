<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of receivables
 *
 * @author adriannatabio
 */
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
        $this->set_content('reports/ageing-of-receivables/view', [
            'data' => $this->report->generate()
        ]);
        $this->generate_page();
    }

}

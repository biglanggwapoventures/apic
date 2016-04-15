<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sales
 *
 * @author christian
 */
class Sales extends PM_Controller_v2 {

    const TITLE = 'Reports';
    const SUBTITLE = 'Sales';

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
        $this->load->model('reports/m_sales', 'report');
    }

    public function index() {
        $this->add_css(array('jQueryUI/jquery-ui-1.10.3.custom.min.css', 'daterangepicker/daterangepicker-bs3.css'));
        $this->add_javascript(array('jquery-ui.min.js', 'plugins/daterangepicker/daterangepicker.js', 'plugins/sticky-thead.js', 'reports/sales.js'));
        $this->load->helper(array('customer', 'product'));
        $this->set_content('reports/sales/view');
        $this->generate_page();
    }

    public function fetch() {
        $this->load->helper('array');
        $params = elements(array('daterange', 'customer', 'product', 'offset', 'include_summary'), $this->input->get(), FALSE);
        $this->report->customer_filter = $params['customer'];
        $this->report->product_filter = $params['product'];
        $this->report->offset = $params['offset'];
        if ($params['daterange']) {
            $params['daterange'] = explode(' - ', html_entity_decode($params['daterange']));
            $this->load->helper('pmdate');
            if (is_valid_date($params['daterange'])) {
                $this->report->start_date_filter = $params['daterange'][0];
                $this->report->end_date_filter = $params['daterange'][1];
            }
        }
        $this->output->set_content_type('html');
        $data = array('data' => $this->report->generate());
        if ($params['include_summary']) {
            $data['total_units'] = $this->report->get_total_units();
            $data['total_amount'] = $this->report->get_total_amount();
        }
        $this->output->set_output($this->load->view('reports/sales/fragment', $data, TRUE));
    }

}

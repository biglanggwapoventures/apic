<?php

class Customer_Pricing extends PM_Controller {

    const TITLE = 'Sales';
    const SUBTITLE = 'Customer Pricing';
    const SUBJECT = 'customer pricing';

    private $viewpage_settings = array();

    function __construct() {
        parent::__construct();
        /*restrict unauthorized access*/
        if(!has_access('sales')){
            show_404();
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
    }

    public function index() {
        $this->set_content('sales/manage-customer-pricing', $this->viewpage_settings);
    }

}

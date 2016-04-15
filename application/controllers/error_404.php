<?php

class Error_404 extends PM_Controller {

    const TITLE = '404 Error';

    public function __construct() {
        parent::__construct();
        $this->set_active_nav(NAV_ACCOUNTING);
        $this->set_content_title(self::TITLE);
    }

    public function index() {
        $this->set_content('404');
        $this->generate_page();
    }

}

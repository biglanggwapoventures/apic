<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Home extends PM_Controller_v2 {

    protected $viewpage_settings = array();

    function __construct() {
        parent::__construct();
        $this->set_content_title('Home');
    }

    public function index() {
        $this->set_content('home', $this->viewpage_settings);
        $this->generate_page();
    }

}

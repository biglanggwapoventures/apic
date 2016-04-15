<?php

class Manage_LOA extends PM_Controller_v2 {

    const SUBJECT = 'role';

    public function __construct() {
        parent::__construct();
        $this->lang->load('user');
        $this->set_content_title($this->lang->line('title'));
        $this->set_content_subtitle($this->lang->line('subtitle_loa'));
        $this->set_active_nav(NAV_USERS);
    }

    public function index() {
        $this->set_content('users/manage-loa');
        $this->generate_page();
    }

}

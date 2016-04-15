<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of messaging
 *
 * @author Adr
 */
class Messaging extends PM_Controller_v2 {

    const TITLE = 'Private Messaging';
    const SUBTITLE = 'Send messages to site users!';
    
    function __construct() {
        parent::__construct();
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->add_javascript(array('messaging/messaging.js', 'messaging/textarea-autogrow.js'));
    }
    
    public function index(){
        $this->set_content('messaging');
        $this->generate_page();
    }

    
}

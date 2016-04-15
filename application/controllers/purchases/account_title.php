<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of account_title
 *
 * @author adriannatabio
 */
class Account_Title extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->generate_page();
    }
}

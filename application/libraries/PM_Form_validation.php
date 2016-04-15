<?php

class PM_Form_validation extends CI_Form_validation {

    public function __construct($rules = array()) {
        parent::__construct($rules);
    }

    public function errors() 
    {
        return array_values($this->error_array());
    }

}

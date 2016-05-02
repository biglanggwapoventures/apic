<?php

class PM_Form_validation extends CI_Form_validation {

    function __construct($rules = array()) {
        parent::__construct($rules);
    }

    function errors() 
    {
        return array_values($this->error_array());
    }

    function add_rules($rules_array)
    {
    	foreach($rules_array AS $rule){
    		$this->set_rules($rule[0], $rule[1], $rule[2]);
    	}
    }

}

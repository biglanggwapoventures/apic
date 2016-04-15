<?php

class print_check extends CI_Controller
{
    public function index()
    {
        $this->load->view('printables/metrobank-check', [
            'pay_to' => 'Pinnacle Poultry & Livestock Inc.',
            'date' => date('Y-m-d'),
            'amount' => 1640.05
        ]);
    }
}

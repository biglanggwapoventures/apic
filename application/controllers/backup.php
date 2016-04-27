<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Backup extends PM_Controller_v2 {

    function __construct() {
        parent::__construct();
    }

    public function database(){
        $this->load->helper('download');
        date_default_timezone_set('Asia/Manila');
        $fileName = "APIC_Backup_".date('Y-m-d__h-i-s_a').".sql";

        $CI =& get_instance();
        $CI->load->database();

        $cmd = 'c:\xampp\mysql\bin\mysqldump --opt -u '.$CI->db->username.' -p'.$CI->db->password.' -h'.$CI->db->hostname.' '.$CI->db->database.' > assets/dbbackup/'.$fileName;
        exec($cmd);

        $data = file_get_contents(base_url()."assets/dbbackup/".$fileName);
        force_download($fileName, $data);
    }

}

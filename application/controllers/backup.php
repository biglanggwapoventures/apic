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

        $cmd = 'mysqldump -u '.$CI->db->username.' -p'.$CI->db->password.' -h'.$CI->db->hostname.' '.$CI->db->database.' > assets/'.$fileName;
        $output = NULL;
        $return = NULL;
        exec($cmd, $output, $return);

        echo $cmd;

        echo "<pre>";
        print_r($output);
        echo "</pre>";

        echo "return = ".$return;

        // $data = file_get_contents(base_url()."assets/dbbackup/".$fileName);
        // force_download($fileName, $data);
    }

}

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Backup extends PM_Controller_v2 {

    function __construct() {
        parent::__construct();
    }

    public function database() {
        $fileName = "APIC Backup ".date('Y-m-d h:i:s a');

        // Load the DB utility class
        $this->load->dbutil();

        // Backup your entire database and assign it to a variable
        $backup =& $this->dbutil->backup([
            'format'    => 'zip',
            'filename'  => $fileName.".sql"
            ]);

        // Load the file helper and write the file to your server
        $this->load->helper('file');
        write_file(base_url().'assets/backup/'.$fileName.".zip", $backup);

        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download($fileName.".zip", $backup);
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of print
 *
 * @author Adr
 */
class Dev_Print extends CI_Controller {

    const PAPER_LENGTH = 5.5;
    const PAPER_WIDTH = 8.5;

    public function __construct() {
        parent::__construct();
    }

    public function pdf() {
        $this->load->library('fpdf17/fpdf');
        $pdf = new FPDF('L', 'in', array(self::PAPER_WIDTH, self::PAPER_LENGTH));
        $pdf->SetMargins(0.1, 0.1);
         $pdf->SetFont('Arial', '', 12);
        $pdf->AddPage();
        $pdf->Cell(0, 0.5, 'Title', 1, 1, 'C');
        $pdf->AddPage();
        $pdf->Cell(0, 0.5, 'Title', 1, 1, 'C');
        
        $pdf->Output();
    }

    public function index() {
        $this->load->view('dev/print');
    }

}

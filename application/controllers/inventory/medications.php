<?php

/**
 * Description of Medications
 *
 * @author Adr
 */
class Medications extends PM_Controller_v2 {

    public function __construct() {
        parent::__construct();
        $this->set_active_nav(NAV_INVENTORY);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Medications');
        $this->load->model('inventory/m_medications', 'medications');
        $this->load->helper('array');
    }

    public function index() {
        $this->setTabTitle('Inventory :: Medications');
        $this->set_content('inventory/medications', [
            'items' => $this->medications->all()
        ]);
        $this->generate_page();
    }

    public function add() {
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('product_code', 'Product Code', 'required|is_unique[inventory_medications.product_code]');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $d = elements(['product_code', 'description'], $this->input->post());
            $insert_id = $this->medications->insert($d['product_code'], $d['description']);
            if ($insert_id) {
                $this->output->set_output(json_encode(['result' => TRUE, 'data' => ['id' => $insert_id]]));
                return;
            }
            $this->output->set_output(json_encode(['result' => FALSE]));
        } else {
            $this->output->set_output(json_encode(['result' => FALSE, 'err' => $this->form_validation->error_array()]));
        }
    }
    
    public function remove(){
        $this->output->set_content_type('json');
        $deleted = $this->medications->delete($this->input->post('id'));
        if($deleted){
            $this->output->set_output(json_encode(['result' => TRUE]));
            return;
        }
        $this->output->set_output(json_encode(['result' => FALSE]));
    }

}

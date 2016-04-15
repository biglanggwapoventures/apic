<?php

class Counters extends PM_Controller {

    const TITLE = 'Sales';
    const SUBTITLE = 'Counter Receipts';
    const SUBJECT = 'counter receipt';

    private $viewpage_settings = array();

    public function __construct() {
        parent::__construct();
        /*restrict unauthorized access*/
        if(!has_access('sales')){
            show_404();
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->load->model(array('sales/m_counter_receipt'));
        $this->load->helper('view');
        $this->add_javascript('numeral.js');
        $this->viewpage_settings['defaults'] = array(
            'fk_sales_customer_id' => '',
            'invoice_number' => '',
            'date' => '',
            'remarks' => '',
            'status' => M_Status::STATUS_DEFAULT
        );
    }

    public function index() {
        $this->add_javascript('sales-counter-receipts/master-list.js');
        $this->viewpage_settings['url'] = base_url('sales/counters');
        $this->viewpage_settings['default_keyword'] = $this->input->get('search_keyword');
        $this->viewpage_settings['entries'] = $this->m_counter_receipt->get(FALSE);
        $this->set_content('sales/counter-receipts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function add() {
        $this->add_javascript(array('jquery-ui.min.js', 'sales-counter-receipts/manage.js'));
        $this->add_css(array('jQueryUI/jquery-ui-1.10.3.custom.min.css', 'iCheck/all.css'));
        $this->load->model('sales/m_customer');
        $this->viewpage_settings['form_title'] = sprintf('Add new %s', self::SUBJECT);
        $this->viewpage_settings['action'] = base_url('sales/counters/a_add');
        $this->viewpage_settings['customers'] = dropdown_format($this->m_customer->get(), 'id', 'company_name', '');
        $this->set_content('sales/manage-counter-receipts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function update($counter_id) {
        $this->add_javascript(array('jquery-ui.min.js', 'sales-counter-receipts/manage.js'));
        $this->add_css(array('jQueryUI/jquery-ui-1.10.3.custom.min.css', 'iCheck/all.css'));
        $this->load->model('sales/m_customer');
        $this->viewpage_settings['form_title'] = sprintf('Update %s #%d ', self::SUBJECT, $counter_id);
        $this->viewpage_settings['action'] = base_url("sales/counters/a_update/{$counter_id}");
        $this->viewpage_settings['customers'] = dropdown_format($this->m_customer->get(), 'id', 'company_name', '');
        $defaults = $this->m_counter_receipt->get(TRUE, FALSE, array('counter.id' => $counter_id));
        $this->viewpage_settings['defaults'] = $defaults[0];
        $this->set_content('sales/manage-counter-receipts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function a_add() {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            echo json_encode($this->response(TRUE, ''));
        }
        $this->form_validation->set_rules('fk_sales_customer_id', 'Customer', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('details', 'Counter Receipt Details', 'callback_cr_detail_check');
        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $details = $input['details'];
            unset($input['details']);
            foreach ($details['fk_sales_delivery_id'] as $delivery_id) {
                $input['details'][] = array(
                    'fk_sales_delivery_id' => $delivery_id
                );
            }
            $added = $this->m_counter_receipt->add($input);
            if ($added) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT, "C.R. # {$added}"));
                $response = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('redirect' => base_url('sales/counters')));
            } else {
                $response = $this->response(TRUE, array($this->m_message->add_error(self::SUBJECT)));
            }
        } else {
            $response = $this->response(TRUE, explode(",", validation_errors("", ",")));
        }
        header("Content-Type: application/json; charset=utf-8", true);
        echo(json_encode($response));
        exit();
    }

    public function a_update($counter_id) {
        $response = '';
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('details', 'Counter Receipt Details', 'callback_cr_detail_check');
        if ($this->form_validation->run()) {
            $input = $this->input->post();
            $details = $input['details'];
            if (!isset($input['status'])) {
                $input['status'] = M_Status::STATUS_DEFAULT;
            }
            unset($input['details']);
            foreach ($details['fk_sales_delivery_id'] as $key => $value) {
                $input['details'][] = array(
                    'id' => $details['id'][$key],
                    'fk_sales_delivery_id' => $value,
                );
            }
            $added = $this->m_counter_receipt->update($counter_id, $input);
            if ($added) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->update_success(self::SUBJECT, "C.R. # {$counter_id}"));
                $response = $this->response(FALSE, $this->m_message->update_success(self::SUBJECT), array('redirect' => base_url('sales/counters')));
            } else {
                $response = $this->response(TRUE, array($this->m_message->update_error(self::SUBJECT)));
            }
        } else {
            $response = $this->response(TRUE, explode(",", validation_errors("", ",")));
        }
        header("Content-Type: application/json; charset=utf-8", true);
        echo(json_encode($response));
        exit();
    }

    function cr_detail_check() {
        $details = $this->input->post('details');
        if (isset($details['fk_sales_delivery_id']) && is_array($details['fk_sales_delivery_id'])) {
            return TRUE;
        }
        $this->form_validation->set_message('cr_detail_check', 'You need to select at least one packing list #');
        return FALSE;
    }

    public function do_print() {
        $id = $this->input->get('id');
        if (!$id || !is_numeric($id)) { //invalid input
            show_404();
        }
        if ($this->m_counter_receipt->is_printed($id)) { //already printed
            echo "Sorry, this has already been printed. Please contact administrator should you request for a reprinting.";
            return;
        }
        $result = $this->m_counter_receipt->get(TRUE, FALSE, array('counter.id' => (int) $id)); //get details
        if (!$result) { // no such packing list with id
            show_404();
        }
        $this->m_counter_receipt->mark_printed($id); //mark as printed
        $data['contents'] = $result[0];
        $this->load->view('sales/print-cr', $data);
    }

}

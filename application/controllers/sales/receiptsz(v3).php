<?php

class Receipts extends PM_Controller {

    const TITLE = 'Sales';
    const SUBTITLE = 'Receipts';
    const SUBJECT = 'sales receipt';

    private $viewpage_settings = array();
    private $_fields = array(
        array(
            'name' => 'fk_sales_customer_id',
            'label' => 'Customer',
            'rules' => 'required|callback_is_valid_customer'
        ),
        array(
            'name' => 'or_number',
            'label' => '',
            'rules' => ''
        ),
        array(
            'name' => 'date',
            'label' => 'Date',
            'rules' => 'required'
        ),
        array(
            'name' => 'tracking_number_type',
            'label' => 'Tracking Number Type',
            'rules' => 'required'
        ), array(
            'name' => 'tracking_number',
            'label' => 'Tracking Number',
            'rules' => 'required'
        ),
        array(
            'name' => 'remarks',
            'label' => '',
            'rules' => ''
        ),
        array(
            'name' => 'status',
            'label' => 'Status',
            'rules' => 'callback_status_check'
        ),
        array(
            'name' => 'details',
            'label' => 'Receipt Details',
            'rules' => 'callback_validate_receipt_details'
        ), array(
            'name' => 'check',
            'label' => 'Check Transactions',
            'rules' => 'callback_validate_check_transactions'
        )
    );

    public function __construct() {
        parent::__construct();
        /*restrict unauthorized access*/
        if(!has_access('sales')){
            show_404();
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->load->model(array('sales/m_receipt'));
        $this->load->helper(array('view', 'array'));
        $this->add_javascript('numeral.js');
        $this->viewpage_settings['defaults'] = array(
            'fk_sales_customer_id' => '',
            'or_number' => '',
            'date' => '',
            'tracking_number_type' => '',
            'tracking_number' => '',
            'remarks' => '',
            'status' => M_Status::STATUS_DEFAULT,
            'total_amount' => 0.00
        );
    }

    public function index() {
        $this->viewpage_settings['url'] = base_url('sales/receipts');
        $this->viewpage_settings['default_keyword'] = $this->input->get('search_keyword');
        $this->viewpage_settings['entries'] = $this->m_receipt->get(FALSE);
        $this->set_content('sales/receipts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function add() {
        $this->add_javascript(array('jquery-ui.min.js', 'sales-receipts/manage.js', 'price-format.js'));
        $this->add_css(array('jQueryUI/jquery-ui-1.10.3.custom.min.css'));
        $this->load->model(array('sales/m_customer', 'accounting/m_bank_account'));
        $this->viewpage_settings['form_title'] = sprintf('Add new %s', self::SUBJECT);
        $this->viewpage_settings['action'] = base_url('sales/receipts/a_add');
        $this->viewpage_settings['customers'] = dropdown_format($this->m_customer->get(), 'id', 'company_name', '');
        $this->viewpage_settings['bank_accounts'] = dropdown_format($this->m_bank_account->get(), 'id', array('bank_name', 'bank_branch'), '');
        $this->set_content('sales/manage-receipts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function update($receipt_id) {
        if (!$this->m_receipt->is_valid($receipt_id)) {
            show_404('404');
        }
        $receipt_details = $this->m_receipt->get(TRUE, FALSE, array('receipt.id' => $receipt_id));
        $this->add_javascript(array('jquery-ui.min.js', 'sales-receipts/manage.js', 'price-format.js'));
        $this->add_css(array('jQueryUI/jquery-ui-1.10.3.custom.min.css'));
        $this->load->model(array('sales/m_customer', 'accounting/m_bank_account'));
        $this->viewpage_settings['form_title'] = sprintf('Update %s # %d', self::SUBJECT, str_pad($receipt_id, 4, '0', STR_PAD_LEFT));
        $this->viewpage_settings['action'] = base_url("sales/receipts/a_update/{$receipt_id}");
        $this->viewpage_settings['customers'] = dropdown_format($this->m_customer->get(), 'id', 'company_name', '');
        $this->viewpage_settings['bank_accounts'] = dropdown_format($this->m_bank_account->get(), 'id', array('bank_name', 'bank_branch'));
        $this->viewpage_settings['defaults'] = $receipt_details[0];
        $this->set_content('sales/manage-receipts', $this->viewpage_settings);
        $this->generate_page();
    }

    public function a_add() {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->output->set_status_header('200')->set_content_type('json');
        foreach ($this->_fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if ($this->form_validation->run()) {
            $receipt = elements(array_map(function($var) {
                        return $var['name'];
                    }, $this->_fields), $this->input->post(), '');
            unset($receipt['details'], $receipt['check']);
            if (!$receipt['status']) {
                $receipt['status'] = M_Status::STATUS_DEFAULT;
            }
            $details = $this->_format_receipt_details();
            $check_transactions = $this->_format_check_transactions();
            $added = $this->m_receipt->add($receipt, $details, $check_transactions);
            if ($added) {
                $response = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT), array('redirect' => base_url('sales/receipts')));
            } else {
                $response = $this->response(TRUE, $this->m_message->add_error(self::SUBJECT));
            }
        } else {
            $response = $this->response(TRUE, explode(",", validation_errors(" ", ",")));
        }
        $this->output->set_output(json_encode($response));
    }

    public function a_update($receipt_id) {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->output->set_status_header('200')->set_content_type('json');
        foreach ($this->_fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if ($this->form_validation->run()) {
            $receipt = elements(array_map(function($var) {
                        return $var['name'];
                    }, $this->_fields), $this->input->post(), '');
            unset($receipt['details'], $receipt['check']);
            if (!$receipt['status']) {
                $receipt['status'] = M_Status::STATUS_DEFAULT;
            }
            $details = $this->_format_receipt_details();
            $check_transactions = $this->_format_check_transactions();
            //debug
            // $test['receipt'] = $receipt;
            // $test['details'] = $details;
            // $test['check'] = $check_transactions;
            //$this->output->set_output(json_encode($test));
            // return;
            //debug done
            $updated = $this->m_receipt->update($receipt_id, $receipt, $details, $check_transactions);
            if ($updated) {
                $response = $this->response(FALSE, $this->m_message->update_success(self::SUBJECT), array('redirect' => base_url('sales/receipts')));
            } else {
                $response = $this->response(TRUE, $this->m_message->update_error(self::SUBJECT));
            }
        } else {
            $response = $this->response(TRUE, explode(",", validation_errors(" ", ",")));
        }
        $this->output->set_output(json_encode($response));
    }

    function status_check() {
        if ((int) $this->input->post('status') === (int) M_Status::STATUS_FINALIZED) {
            if ($this->m_account->is_admin()) {
                return TRUE;
            } else {
                $this->form_validation->set_message('status_check', 'You are not authorized to finalize sales receipts.');
                return FALSE;
            }
        }
        return TRUE;
    }

    //callback customer field form validation
    function is_valid_customer() {
        $this->load->model('sales/m_customer');
        if ($this->m_customer->is_valid($this->input->post('fk_sales_customer_id'))) {
            return TRUE;
        }
        $this->form_validation->set_message('is_valid_customer', 'You must provide a valid customer!');
        return FALSE;
    }

    //callback receipt details form validation
    function validate_receipt_details() {
        $this->load->model('sales/m_delivery');
        $details = $this->input->post('details');
        if (!$this->m_delivery->is_valid($details['fk_sales_delivery_id'], $this->input->post('fk_sales_customer_id'))) {
            $this->form_validation->set_message('validate_receipt_details', 'All transactions must be associated with the selected customer.');
            return FALSE;
        }
        $payment_method = $details['payment_method'];
        $this->form_validation->set_message('validate_receipt_details', 'Payment methods must be on cash or check only.');
        if (count($payment_method) !== count($details['fk_sales_delivery_id'])) {
            return FALSE;
        }
        foreach ($payment_method as $method) {
            if (!in_array($method, array(1, 2,'Cash','Check'))) {
                return FALSE;
            }
        }
        return TRUE;
    }

    //callback check transactions form validation
    function validate_check_transactions() {
        $check_transactions = $this->input->post('check');
        if (!isset($check_transactions['fk_accounting_bank_account_id'])) {
            return TRUE;
        }
        if (!is_array($check_transactions['fk_accounting_bank_account_id'])) {
            $this->form_validation->set_message('validate_check_transactions', 'There is something wrong with the check transaction details.');
            return FALSE;
        }
        $this->load->model('accounting/m_bank_account');
        if (!$this->m_bank_account->is_valid($check_transactions['fk_accounting_bank_account_id'])) {
            $this->form_validation->set_message('validate_check_transactions', 'You must use valid bank accounts for check transactions.');
            return FALSE;
        }
        $validated = TRUE;
        $this->form_validation->set_message('validate_check_transactions', 'Please make sure you have filled up everything in the check transactions');
        for ($i = 0; $i < count($check_transactions['fk_accounting_bank_account_id']); $i++) {
            if (!isset($check_transactions['check_number'][$i]) || !strlen(trim($check_transactions['check_number'][$i]))) {
                $validated = FALSE;
                break;
            }
            if (!isset($check_transactions['deposit_date'][$i]) || !strlen(trim($check_transactions['deposit_date'][$i]))) {
                $validated = FALSE;
                break;
            }
            if (!isset($check_transactions['check_date'][$i]) || !strlen(trim($check_transactions['check_date'][$i]))) {
                $validated = FALSE;
                break;
            }
            if (!isset($check_transactions['amount'][$i]) || !strlen(trim($check_transactions['amount'][$i]))) {
                $validated = FALSE;
                break;
            }
        }
        return $validated;
    }

    //formats receipt details from input structure to database structure
    private function _format_receipt_details() {
        $formatted_data = array();
        $receipt_details = $this->input->post('details');
        $drs = isset($receipt_details['fk_sales_delivery_id']) ? count($receipt_details['fk_sales_delivery_id']) : 0;
        for ($x = 0; $x < $drs; $x++) {
            $data = array(
                'fk_sales_delivery_id' => $receipt_details['fk_sales_delivery_id'][$x],
                'payment_method' => isset($receipt_details['payment_method'][$x]) ? $receipt_details['payment_method'][$x] : 1,
                'amount' => isset($receipt_details['amount'][$x]) ? str_replace(",", "", $receipt_details['amount'][$x]) : 0
            );
            if (isset($receipt_details['id'][$x])) {
                $data['id'] = $receipt_details['id'][$x];
            }
            $formatted_data[] = $data;
        }
        return $formatted_data;
    }

    //formats check transaction details from input structure to database structure
    private function _format_check_transactions() {
        $formatted_data = array();
        $check_details = $this->input->post('check');
        $checks = isset($check_details['fk_accounting_bank_account_id']) ? count($check_details['fk_accounting_bank_account_id']) : 0;
        for ($x = 0; $x < $checks; $x++) {
            $data = array(
                'fk_accounting_bank_account_id' => $check_details['fk_accounting_bank_account_id'][$x],
                'check_number' => isset($check_details['check_number'][$x]) ? $check_details['check_number'][$x] : '',
                'check_date' => isset($check_details['check_date'][$x]) ? $check_details['check_date'][$x] : '',
                'deposit_date' => isset($check_details['deposit_date'][$x]) ? $check_details['deposit_date'][$x] : '',
                'amount' => isset($check_details['amount'][$x]) ? str_replace(",", "", $check_details['amount'][$x]) : 0
            );
            if (isset($check_details['id'][$x])) {
                $data['id'] = $check_details['id'][$x];
            }
            $formatted_data[] = $data;
        }
        return $formatted_data;
    }

}

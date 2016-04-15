<?php

class Other_Disbursements extends PM_Controller_v2 {

    public function __construct() {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Other Disbursements');
        $this->load->model('purchases/m_purchase_disbursement');
    }

    public function index() {
        $this->setTabTitle('Purchases Disbursement (Others)');
        $this->add_javascript(['plugins/sticky-thead.js', 'numeral.js', 'printer/printer.js', 'purchases-disbursements/others.js']);
        $this->set_content('purchases/disbursement/others/master-list', [
            // 'entries' => $this->m_purchase_disbursement->master_list('others'),
            'entries' => []
        ]);
        $this->generate_page();
    }

    public function ajax_master_list(){
        if($this->input->is_ajax_request()){
            $offset = $this->input->get('page');
            $page = $offset ? $offset : 1;
            $params = $this->search_params();
            $data = $this->m_purchase_disbursement->master_list('others', $page, $params['query'], $params['like']);
            $this->generate_response($data ? ['data' => $data] : [])->to_JSON();
        }
    }

    public function search_params()
    {
        $this->load->helper('pmdate');
        $query = [];
        $like = [];
        $params = elements(['id', 'start_date', 'end_date', 'check_number', 'payee', 'remarks'], $this->input->get());
        if($params['id'] && is_numeric($params['id']))
        {
            $query['dsb.id'] = $params['id'];
        }
        if($params['start_date'] && is_valid_date($params['start_date'], 'm/d/Y'))
        {
            $query['dsb.date >='] = date('Y-m-d', strtotime($params['start_date']));
        }
        if($params['end_date'] && is_valid_date($params['end_date'], 'm/d/Y'))
        {
            $query['dsb.date <='] = date('Y-m-d', strtotime($params['end_date']));
        }
        if($params['check_number'])
        {
            $query['payment.check_number'] = $params['check_number'];
        }
        if($params['payee'])
        {
            $like['payee'] = $params['payee'];
        }
        if($params['remarks'])
        {
            $like['remarks'] = $params['remarks'];
        }
        return ['query' => $query, 'like' => $like];
    }

    public function manage() {
        $action = $this->input->get('do');
        $view_data = [];
        switch ($action) {
            case 'new-check-voucher':
                $this->setTabTitle('New check voucher :: Purchases');
                $view_data['form_title'] = 'Add new check voucher';
                $view_data['mode'] = 'new';
                $view_data['action'] = base_url('purchases/other_disbursements/ajax_do_action/add');
                break;
            case 'update-check-voucher':
                $this->setTabTitle('Update check voucher :: Purchases');
                $id = $this->input->get('id');
                $view_data['data'] = $this->m_purchase_disbursement->get($id, 'others');
                $view_data['form_title'] = "Update check voucher # {$id}";
                $view_data['action'] = base_url("purchases/other_disbursements/ajax_do_action/update/{$id}");
                break;
            default:
                show_404();
        }
        $this->load->model(['maintainable/m_chart', 'maintainable/m_supplier', 'accounting/m_bank_account']);
        $view_data['charts'] = dropdown_format($this->m_chart->all(), 'id', 'description');
        $view_data['suppliers'] = dropdown_format($this->m_supplier->all(), 'id', 'name');
        $view_data['suppliers'] = dropdown_format($this->m_supplier->all(), 'id', 'name');
        $view_data['bank_accounts'] = dropdown_format($this->m_bank_account->get(), 'id', ['bank_name', 'bank_branch']);
        $this->set_content('purchases/disbursement/others/manage', $view_data);
        $this->add_javascript(['numeral.js', 'price-format.js']);
        $this->generate_page();
    }

    public function ajax_do_action($method, $id = FALSE) {
        $response = '';
        switch ($method) {
            case 'add':
                $response = $this->_add();
                break;
            case 'update':
                $response = $this->_update($id);
                break;
            default: show_404();
        }
        $this->output->set_content_type('json')->set_output(json_encode($response));
    }

    public function _add() {
        $validated_data = $this->validate_input();
        if ($validated_data['error_flag']) {
            return $validated_data;
        }
        $id = $this->m_purchase_disbursement->create_check_voucher($validated_data['data']);
        if ($id) {
            $response = $this->response(FALSE, $this->m_message->add_success('check voucher', "C.V. # {$id}"), ['redirect' => base_url('purchases/other_disbursements')]);
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($response));
        } else {
            $response = $this->response(TRUE, 'An unexpected error has occured. Please try again.');
        }
        return $response;
    }

    public function _update($id) {
        $validated_data = $this->validate_input('update');
        if ($validated_data['error_flag']) {
            return $validated_data;
        }
        $updated = $this->m_purchase_disbursement->update_check_voucher($id, $validated_data['data']);
        if ($updated) {
            $response = $this->response(FALSE, $this->m_message->update_success('check voucher'));
        } else {
            $response = $this->response(TRUE, 'An unexpected error has occured. Please try again.');
        }
        return $response;
    }

    private function validate_input($method = 'create') {
        $this->form_validation->set_rules('payee', 'Payee', 'required');
        $this->form_validation->set_rules('line', '', 'callback__validate_liquidation');
        $this->form_validation->set_rules('payment_type', '', 'callback__validate_payment_type');
        $this->form_validation->set_rules('amount', 'Payment Amount', 'required');
        $this->form_validation->set_rules('fk_accounting_bank_account_id', '', 'callback__validate_bank_account');
        $this->form_validation->set_rules('check_number', 'Check Number', 'callback__validate_check_number');
        $this->form_validation->set_rules('check_date', '', 'callback__validate_check_date');
        $this->form_validation->set_rules('check_type', 'Check Type', 'callback__validate_check_type');
        $this->form_validation->set_rules('status', 'Status', 'integer|callback__validate_status');
        if ($this->form_validation->run()) {
            return $this->response(FALSE, '', $this->_format_data($method));
        }
        return $this->response(TRUE, array_values($this->form_validation->error_array()));
    }

    public function _format_data($method) {
        $input = $this->input->post();
        $data['general'] = elements(['remarks', 'payee', 'status'], $input, NULL);
        if ($method === 'create') {
            $data['general']['created_by'] = $this->session->userdata('user_id');
        }
        if ($data['general']['status'] == M_Status::STATUS_APPROVED) {
            $data['general']['is_locked'] = 1;
            $data['general']['approved_by'] = $this->session->userdata('user_id');
        } else {
            $data['general']['is_locked'] = 0;
            $data['general']['status'] = M_Status::STATUS_DEFAULT;
        }
        $data['payment'] = elements(['payment_type', 'check_type', 'fk_accounting_bank_account_id', 'check_number', 'check_date'], $input, NULL);
        $data['payment']['amount'] = str_replace(',', '', $input['amount']);
        $data['liquidation'] = array_map(function($var) USE ($method) {
            $temp = elements(['date', 'account_id', 'description'], $var);
            $temp['amount'] = str_replace(",", "", $var['amount']);
            $method === 'update' && array_key_exists('id', $var) ? ($temp['id'] = $var['id']) : NULL;
            return $temp;
        }, $input['line']);
        return $data;
    }

    /*
      | -------------------------------------------------------------------
      |  Callback functions used in form validations
      | -------------------------------------------------------------------
      | These are functions used for validating data.
      |
      | Prototype:
      |
      |     $this->form_validation->set_rules('date', 'Date', 'callback__validate_date');
      |
     */

    public function _validate_liquidation($liquidation_array) {
        $liquidation_array = $this->input->post('line');
        $this->load->helper('pmdate');
        $errors = [];
        $account_ids = [];
        foreach ($liquidation_array as $data) {
            if (is_valid_date($data['date']) === FALSE) {
                $errors[] = 'invalid date';
            }
            if (is_numeric(str_replace(",", "", $data['amount'])) === FALSE) {
                $errors[] = 'invalid amount';
            }
            if (!preg_replace('/\s+/', '', $data['description'])) {
                $errors[] = 'invalid description';
            }
            $account_ids[] = $data['account_id'];
        }
        $this->load->model('maintainable/m_chart');
        if (empty($account_ids) || $this->m_chart->is_valid(array_unique($account_ids)) === FALSE) {
            $errors[] = 'invalid chart of accounts';
        }
        if (count($errors) > 0) {
            $this->form_validation->set_message('_validate_liquidation', 'Liquidation contains ' . implode(', ', array_unique($errors)));
            return FALSE;
        }
        return TRUE;
    }
    
    public function _validate_date($date) {
        $this->load->helper('pmdate');
        if (is_valid_date($date)) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_date', 'Please enter a valid date with format YYYY-MM-DD');
        return FALSE;
    }

    public function _validate_payment_type($payment_type) {
        if (in_array($payment_type, ['check', 'cash'])) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_payment_type', 'Invalid payment type.');
        return FALSE;
    }

    public function _validate_bank_account($bank_account) {
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        $this->load->model('accounting/m_bank_account');
        if ($this->m_bank_account->is_valid($bank_account)) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_bank_account', 'Invalid bank account.');
        return FALSE;
    }

    public function _validate_check_number($check_number) {
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        if (strlen(trim($check_number)) > 3) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_check_number', 'The %s is required.');
        return FALSE;
    }

    public function _validate_check_date($check_date) {
        $this->load->helper('pmdate');
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        if (is_valid_date($check_date)) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_check_date', 'Please provide a valid check date.');
        return FALSE;
    }

    public function _validate_check_type($type)
    {
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_check_type', 'Please select a valid check type');
        return in_array($type, ['mb', 'xmb', 'rcbc', 'xrcbc']);
    }

    public function _validate_status($status = FALSE) {
        if (!$status) {
            return TRUE;
        }
        if (in_array($status, [M_Status::STATUS_DEFAULT, M_Status::STATUS_APPROVED]) === FALSE) {
            $this->form_validation->set_message('_validate_status', 'Please provide a valid status.');
            return FALSE;
        }
        if ($status == M_Status::STATUS_APPROVED && $this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
            $this->form_validation->set_message('_validate_status', 'You do not have permission to approve items.');
            return FALSE;
        }
        return TRUE;
    }

    public function do_print() {
        $id = $this->input->get('id');
        if (is_numeric($id) === FALSE || $this->m_purchase_disbursement->is_approved($id) === FALSE) {
            echo 'Please make sure the purchase receiving is approved before printing.';
            return;
        }
        $this->load->view('printables/purchases/disbursement-others', [
            'details' => $this->m_purchase_disbursement->get($id, 'others')
        ]);
    }

    public function ajax_print_check()
    {
        $id = $this->input->get('id');
        if(!$id || !is_numeric($id) || !$this->m_purchase_disbursement->is_approved($id)){
            echo 'Please make sure the voucher is approved before printing.';
            return;
        }
        $check_details = $this->m_purchase_disbursement->get_check_details($id);
        if($check_details){
            $view = 'printables/';
            switch($check_details['check_type']){
                case 'mb': 
                    $view .= 'metrobank-check';
                    break;
                case 'xmb': 
                    $view .= 'metrobank-cross-check';
                    break;
                case 'rcbc': 
                    $view .= 'rcbc-check';
                    break;
                case 'xrcbc': 
                    $view .= 'rcbc-cross-check';
                    break;
            }
            $this->load->view($view, [
                'date' => $check_details['check_date'],
                'pay_to' => $this->m_purchase_disbursement->payee($id),
                'amount' => $check_details['amount']
            ]);
            return;
        }
        echo "The voucher does not have any checks to print.";
       
    }


}

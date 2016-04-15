<?php

class Disbursements extends PM_Controller_v2 {

    const SUBJECT = 'purchase disbursement';

    private $viewpage_settings = array();
    private $_fields = array(
        array(
            'name' => 'disbursement_type',
            'label' => 'Disbursement Type',
            'rules' => 'callback__validate_disbursement_type'
        ),
        array(
            'name' => 'date',
            'label' => 'Date',
            'rules' => 'required'
        ),
        array(
            'name' => 'fk_maintainable_supplier_id',
            'label' => 'Supplier Name',
            'rules' => 'callback__validate_supplier'
        ),
        array(
            'name' => 'fk_purchase_order_id',
            'label' => 'PO Number',
            'rules' => 'callback__validate_po_num'
        ),
        array(
            'name' => 'payee',
            'label' => 'Payee',
            'rules' => 'callback__remarks_check'
        ),
        array(
            'name' => 'remarks',
            'label' => 'Remarks',
            'rules' => 'callback__remarks_check'
        ),
        array(
            'name' => 'disbursement_details',
            'label' => 'Purchase Disbursement Details',
            'rules' => 'callback__validate_disbursement_details'
        ),
        array(
            'name' => 'payment_type',
            'label' => 'Payment Type',
            'rules' => 'callback__validate_payment_type'
        ),
        array(
            'name' => 'amount',
            'label' => 'Amount paid',
            'rules' => 'required'
        ),
        array(
            'name' => 'fk_accounting_bank_account_id',
            'label' => 'Bank Account',
            'rules' => 'callback__validate_bank_account'
        ),
        array(
            'name' => 'check_number',
            'label' => 'Check Number',
            'rules' => 'callback__validate_check_number'
        ),
        array(
            'name' => 'check_date',
            'label' => 'Check Date',
            'rules' => 'callback__validate_check_date'
        ),
        array(
            'name' => 'check_type',
            'label' => 'Check Type',
            'rules' => 'callback__validate_check_type'
        ),
        array(
            'name' => 'status',
            'label' => 'Status',
            'rules' => 'callback__validate_status'
        ),
    );

    function __construct() {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Disbursements');
        $this->load->helper(array('view', 'array'));
        $this->load->model('purchases/m_purchase_disbursement');
        $this->viewpage_settings['defaults'] = array(
            'disbursement_type' => FALSE,
            'fk_maintainable_supplier_id' => FALSE,
            'date' => FALSE,
            'remarks' => FALSE,
            'payee' => '',
            'status' => M_Status::STATUS_DEFAULT,
            'payment_type' => FALSE,
            'amount' => FALSE,
            'check_number' => FALSE,
            'check_date' => FALSE,
            'fk_accounting_bank_account_id' => FALSE,
            'fk_purchase_order_id' => FALSE
        );
    }

    function _segment_url($segment = '') {
        return base_url("purchases/disbursements/{$segment}");
    }

    public function ajax_master_list(){
        if($this->input->is_ajax_request()){
            $offset = $this->input->get('page');
            $page = $offset ? $offset : 1;
            $data = $this->m_purchase_disbursement->master_list(['rr', 'advance'], $page, $this->search_params());
            $this->generate_response($data ? ['data' => $data] : [])->to_JSON();
        }
    }

    public function search_params()
    {
        $this->load->model('maintainable/m_supplier', 'supplier');
        $this->load->helper('pmdate');
        $query = [];
        $params = elements(['id', 'start_date', 'end_date', 'supplier', 'check_number'], $this->input->get());
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
        if($params['supplier'] && $this->supplier->is_valid($params['supplier']))
        {
            $query['dsb.fk_maintainable_supplier_id'] = $params['supplier'];
        }
        if($params['check_number'])
        {
            $query['payment.check_number'] = $params['check_number'];
        }
        return empty($query) ? FALSE : $query;
    }

    function index() {
        $this->setTabTitle('Purchase Disbursements');
        $this->load->model('maintainable/m_supplier', 'supplier');
        $this->viewpage_settings['suppliers'] = ['' => ''] + array_column($this->supplier->all(), 'name', 'id');
        $this->add_javascript(['plugins/sticky-thead.js', 'numeral.js', 'printer/printer.js', 'purchases-disbursements/master-list.js']);
        $this->set_content('purchases/disbursements', $this->viewpage_settings);
        $this->generate_page();
    }

    public function manage() {
        $this->load->model(array('accounting/m_bank_account', 'maintainable/m_supplier', 'maintainable/m_chart'));
        $this->add_javascript(array('purchases-disbursements/manage.js', 'numeral.js', 'price-format.js', 'jquery-ui.min.js'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        if ($this->input->get('do') === 'new-purchase-disbursement') {
            $this->setTabTitle('New purchase disbursement');
            $this->viewpage_settings['mode'] = 'new';
            $this->viewpage_settings['form_title'] = sprintf('Add new %s', self::SUBJECT);
            $this->viewpage_settings['action'] = $this->_segment_url('a_do_action/add');
            $this->viewpage_settings['is_locked'] = FALSE;
            $this->viewpage_settings['suppliers'] = dropdown_format($this->m_supplier->all(), 'id', 'name');
        } elseif ($this->input->get('do') === 'update-purchase-disbursement' && $this->m_purchase_disbursement->is_valid($this->input->get('id'))) {
            $id = $this->input->get('id');
            $this->setTabTitle("Update purchase disbursement # {$id}");
            $this->viewpage_settings['defaults'] = $this->m_purchase_disbursement->get($id);
            $this->viewpage_settings['form_title'] = sprintf('Update %s # %d', self::SUBJECT, $id);
            $this->viewpage_settings['action'] = $this->_segment_url("a_do_action/update/{$id}");
            $this->viewpage_settings['is_locked'] = (int) $this->viewpage_settings['defaults']['is_locked'] === 1;
        } else {
            show_404();
        }
        $this->viewpage_settings['charts'] = array_column($this->m_chart->all(), 'description', 'id');
        $this->viewpage_settings['bank_accounts'] = dropdown_format($this->m_bank_account->get(), 'id', ['bank_name', 'bank_branch']);
        $this->set_content('purchases/manage-disbursement', $this->viewpage_settings);
        $this->generate_page();
    }

    function _validate_input($method = 'new') {
        $fields = '';
        $required_fields = ['disbursement_type', 'fk_maintainable_supplier_id', 'fk_purchase_order_id','date', 'remarks', 'status', 'payee'];
        //initiate form validation rules
        if ($method === 'update') {
            $fields = array_filter($this->_fields, function($var) USE($required_fields) {
                return in_array($var['name'], [$required_fields[1],$required_fields[2]]) === FALSE;
            });
            unset($required_fields[1],$required_fields[2]);
        } else {
            $fields = $this->_fields;
        }
        foreach ($fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if (!$this->form_validation->run()) {
            return $this->response(TRUE, explode(",", validation_errors(' ', ',')));
        }
        $data = $this->input->post();
        //get all fields
        $general_data = elements($required_fields, $data, NULL);
        if ($general_data['status'] === NULL) {
            $general_data['status'] = M_Status::STATUS_DEFAULT;
        }
        //separate the details
        $line_temp = array_key_exists('disbursement_details', $data) ? $data['disbursement_details'] : [];
        $line = array();
        //separate the payment
        $payment = elements(['payment_type', 'check_date', 'check_number', 'fk_accounting_bank_account_id', 'check_type'], $data, NULL);
        $payment['amount'] = str_replace(',', '', $data['amount']);
        //destroy variables not used in the db table
        unset($data['disbursement_details'], $data['payment']);
        //format the details.. if there are any
        if (array_key_exists('disbursement_type', $data) && $data['disbursement_type'] === 'rr') {
            foreach ($line_temp['fk_purchase_receiving_id'] as $key => $value) {
                $temp = [
                    'fk_purchase_receiving_id' => $value,
                    // 'include' => $line_temp['include'][$key],
                    'chart_id' => $line_temp['chart_id'][$key] ?: NULL,
                ];
                if (isset($line_temp['id'][$key])) {
                    $temp['id'] = $line_temp['id'][$key];
                }
                $line[] = $temp;
            }
        }
        return $this->response(FALSE, '', ['general' => $general_data, 'line' => $line, 'payment' => $payment]);
    }

    public function a_do_action($method, $extras = FALSE) {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $response = '';
        switch ($method) {
            case 'add':
                $input = $this->_validate_input();
                if ($input['error_flag']) {
                    $response = $input;
                } else {
                    $input['data']['general']['created_by'] = $this->session->userdata('user_id');
                    $response = $this->_add($input['data']);
                }
                break;
            case 'update':
                $input = $this->_validate_input('update');
                $response = $input['error_flag'] ? $input : $this->_update($extras, $input['data']);
                break;
            case 'delete':
                $response = $this->_delete($this->input->post('pk'));
                break;
            default:
                $response = $this->response(TRUE, 'We are not sure how to process your request.');
                break;
        }
        $this->output->set_status_header('200')->set_content_type('json')->set_output(json_encode($response));
        return;
    }

    /* CRUD REMAPS */

    function _add($validated_data) {
        $id = $this->m_purchase_disbursement->add($validated_data['general'], $validated_data['line'], $validated_data['payment']);
        if ($id) {
            $response = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT, "CV # {$id}"), array('redirect' => $this->_segment_url()));
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($response));
            return $response;
        }
        return $this->response(TRUE, $this->m_message->add_eror(self::SUBJECT));
    }

    function _update($id, $validated_data) {
        if ((int) $this->session->userdata('type_id') !== (int) M_Account::TYPE_ADMIN && $this->m_purchase_disbursement->is_locked($id)) {
            return $this->response(TRUE, sprintf('You cannot update a locked %s', self::SUBJECT));
        }
        $general = $validated_data['general'];
        unset($general['payment_type'], $general['fk_purchase_supplier_id'], $general['fk_purchase_order_id']);
        $updated = $this->m_purchase_disbursement->update($id, $general, $validated_data['line'], $validated_data['payment']);
        if ($updated) {
            return $this->response(FALSE, '', array('redirect' => $this->_segment_url()));
        }
        return $this->response(TRUE, $this->m_message->update_error(self::SUBJECT));
    }

    function _delete($id) {
        if ((int) $this->session->userdata('type_id') !== (int) M_Account::TYPE_ADMIN) {
            return $this->response(TRUE, sprintf('You do not have the privilege to delete a %s', self::SUBJECT));
        }
        $deleted = $this->m_purchase_disbursement->delete($id);
        if ($deleted) {
            return $this->response(FALSE, '');
        }
        return $this->response(TRUE, $this->m_message->delete_error(self::SUBJECT));
    }

    /* END CRUD REMAPS */

    /* SPECIAL FUNCTIONS */

    public function change_lock_state() {
        $this->output->set_content_type('json');
        if ($this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
            $this->output->set_output(json_encode($this->response(TRUE, sprintf('You do not have the privilege to unlock a %s', self::SUBJECT))));
        }
        $do_lock = FALSE;
        $request_state = $this->input->post('request_state');
        if ($request_state === 'do_lock') {
            $do_lock = TRUE;
        } else if ($request_state === 'do_unlock') {
            $do_lock = FALSE;
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, sprintf('We are not sure how to process your request'))));
        }
        if ($this->m_purchase_disbursement->change_lock_state($this->input->post('order_id'), $do_lock)) {
            $this->output->set_output(json_encode($this->response(FALSE, '')));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'An unknown error has occured.')));
        }
    }

    /* FORM VALIDATION CALLBACKS */

    function _validate_disbursement_type($payment_type) {
        if (!in_array($payment_type, ['rr', 'advance'])) {
            $this->form_validation->set_message('_validate_payment_type', 'Invalid %s!');
            return FALSE;
        }
        return TRUE;
    }

    function _validate_payment_method($payment_method) {
        if (!in_array($payment_method, array('cash', 'check'))) {
            $this->form_validation->set_message('_validate_payment_method', 'Invalid %s!');
            return FALSE;
        }
        return TRUE;
    }

    function _validate_supplier($supplier = FALSE) {
        $this->load->model('maintainable/m_supplier');
        if ($this->m_supplier->is_unique('id', $supplier)) {
            $this->form_validation->set_message('_is_valid_supplier', 'Please select a valid supplier.');
            return FALSE;
        }
        return TRUE;
    }

    function _validate_purchase_order($fk_purchase_order_id = FALSE) {
        $this->load->model('purchases/m_purchase_order');
        if ($fk_purchase_order_id && !$this->m_purchase_order->is_valid($fk_purchase_order_id, $this->input->post('fk_purchase_supplier_id'), TRUE)) {
            $this->form_validation->set_message('_validate_purchase_order', 'Please select a valid %s.');
            return FALSE;
        }
        return TRUE;
    }

    function _validate_status($status = FALSE) {
        if ($status == M_Status::STATUS_APPROVED && $this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
            $this->form_validation->set_message('_validate_status', sprintf('You are not authorized to approve %ss', self::SUBJECT));
            return FALSE;
        }
        return TRUE;
    }

    function _validate_disbursement_details($details = FALSE) {
        $details = $this->input->post('disbursement_details');
        if ($this->input->post('disbursement_type') === 'advance') {
            return TRUE;
        }
        $this->load->model('purchases/m_purchase_receiving');
        $detail_ids = isset($details['fk_purchase_receiving_id']) ? $details['fk_purchase_receiving_id'] : array();
        if (empty($detail_ids)) {
            $this->form_validation->set_message('_validate_disbursement_details', sprintf('Please select at least one purchase transaction.'));
            return FALSE;
        }
        if (!$this->m_purchase_receiving->is_valid($detail_ids, $this->input->post('fk_purchase_order_id'), $this->input->post('fk_purchase_supplier_id'), TRUE)) {
            $this->form_validation->set_message('_validate_disbursement_details', sprintf('There is something wrong with the selected transactions.'));
            return FALSE;
        }
        return TRUE;
    }

    function _validate_payment_type($payment_type) {
        if (in_array($payment_type, ['check', 'cash'])) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_payment_type', 'Invalid payment type.');
        return FALSE;
    }

    function _validate_check_type($type)
    {
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_check_type', 'Please select a valid check type');
        return in_array($type, ['mb', 'xmb', 'rcbc', 'xrcbc']);
    }

    function _validate_bank_account($bank_account) {
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

    function _validate_check_number($check_number) {
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        if (strlen(trim($check_number)) > 3) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_check_number', 'The %s is required.');
        return FALSE;
    }

    function _validate_check_date($check_date) {
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

    function _validate_deposit_date($deposit_date) {
        if ($this->input->post('payment_type') === 'cash') {
            return TRUE;
        }
        $this->load->helper('pmdate');
        if (is_valid_date($deposit_date)) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_deposit_date', 'Please provide a valid deposit date.');
        return FALSE;
    }
    
    function _validate_po_num($po_num) {
        if ($this->input->post('disbursement_type') === 'rr') {
            return TRUE;
        }
        $this->load->model('purchases/m_purchase_order');
        if ($this->m_purchase_order->is_valid($po_num)) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_po_num', 'Please provide a valid PO number.');
        return FALSE;
    }

    function _remarks_check()
    {
        return TRUE;
    }

    /* END OF FORM VALIDATION CALLBACKS */

    //print
    public function do_print() {
        $type = $this->input->get('disbursement_type');
        $id = $this->input->get('id');
        if (!is_numeric($id) || !$this->m_purchase_disbursement->is_approved($id) || ! in_array($type, ['rr', 'advance'])) {
            echo 'Please make sure the voucher is approved before printing.';
            return;
        }
        if($type==='rr'){
            $this->load->view('printables/purchases/disbursement-rr', [
                'details' => $this->m_purchase_disbursement->get($id)
            ]);
        }else{
            $this->load->model('purchases/m_purchase_order');
            $po_id = $this->input->get('po');
            $po = $this->m_purchase_order->get(TRUE, FALSE, array('p_order.id' => $po_id));
            $details = $this->m_purchase_disbursement->get($id);
            $details['po_line'] = elements(['details', 'id'], $po[0]);
            $this->load->view('printables/purchases/disbursement-advance', [
                'details' => $details
            ]);
        }
        
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

<?php

class Payments extends PM_Controller_v2 {

    private $validation_errors = [];

    function __construct() {
        parent::__construct();
        $this->set_active_nav(NAV_TRUCKING);
        $this->set_content_title('Trucking');
        $this->set_content_subtitle('Payments');
        $this->load->model('trucking/m_payments', 'payments');
    }

    public function index() {
        $this->load->helper('customer');
        $this->setTabTitle('Trucking - Payments');
        $this->add_javascript(['numeral.js', 'plugins/sticky-thead.js', 'trucking-payments/master-list.js']);
        $this->set_content('trucking/payments/listing');
        $this->generate_page();
    }
    
    public function ajax_master_list(){
        if($this->input->is_ajax_request()){
            $offset = $this->input->get('page');
            $page = $offset ? $offset : 1;
            $data = $this->payments->all($page, $this->search_params());
            $this->output->set_content_type('json')->set_output(json_encode($data ? ['data' => $data] : []));
        }
    }

    public function search_params()
    {
        $this->load->model('sales/m_customer', 'customer');
        $this->load->helper('pmdate');
        $query = [];
        $params = elements(['id', 'start_date', 'end_date', 'tracking_no', 'tracking_type', 'customer'], $this->input->get());
        if($params['id'] && is_numeric($params['id']))
        {
            $query['rec.id'] = $params['id'];
        }
        if($params['start_date'] && is_valid_date($params['start_date'], 'm/d/Y'))
        {
            $query['rec.`date` >='] = date('Y-m-d', strtotime($params['start_date']));
        }
        if($params['end_date'] && is_valid_date($params['end_date'], 'm/d/Y'))
        {
            $query['rec.`date` <='] = date('Y-m-d', strtotime($params['end_date']));
        }
        if($params['customer'] && $this->customer->is_valid($params['customer']))
        {
            $query['customer.id'] = $params['customer'];
        }
        // if($params['check_number'])
        // {
        //     $query['customer.id'] = $params['customer'];
        // }
        if($params['tracking_no'] && in_array($params['tracking_type'], ['PTN', 'CR']))
        {
            $query['rec.tracking_number'] = $params['tracking_no'];
            $query['rec.tracking_number_type'] = $params['tracking_type'];
        }
        return empty($query) ? FALSE : $query;
    }
    
    public function ajax_get_unsettled(){
        if($this->input->is_ajax_request()){
            $unsettled = $this->payments->get_unsettled($this->input->get('customer_id'), TRUE);
            $this->output->set_content_type('json')->set_output(json_encode($unsettled));
            return;
        }
        show_error('Error 403: Forbidden', 403);
    }
    
    public function create(){
        $this->load->model('accounting/m_bank_account', 'bank_account');
        $this->load->helper('customer');
        $this->add_javascript(['price-format.js' ,'numeral.js','trucking-payments/manage.js']);
        $this->setTabTitle('Trucking - Create trucking payment');
        $this->set_content('trucking/payments/manage', [
            'form_title' => 'Create new trucking payment',
            'form_action' => base_url('trucking/payments/ajax_create'),
            'bank_accounts' => $this->bank_account->get()
        ]); 
        $this->generate_page();
    }

    public function update($id = FALSE)
    {
        if($id === FALSE || !$this->payments->is_valid($id))
        {
            show_404();
        }
        $this->load->model('accounting/m_bank_account', 'bank_account');
        $this->load->helper('customer');
        $this->add_javascript(['price-format.js' ,'numeral.js','trucking-payments/manage.js']);
        $this->setTabTitle('Trucking - Update trucking payment #'.$id);

        $viewpage_settings = array();

        if($this->payments->get_max_id()[0]['id'] == $id){
            $payments_next_id = $this->payments->get_min_id();
            if($payments_next_id[0]['id'] == $id){
                $payments_next_id = array();
            }
        }else{
            $payments_next_id = $this->payments->get_next_row_id($id, "next");
        }

        if($this->payments->get_min_id()[0]['id'] == $id){
            $payments_prev_id = $this->payments->get_max_id();
            if($payments_prev_id[0]['id'] == $id){
                $payments_prev_id = array();
            }
        }else{
            $payments_prev_id = $this->payments->get_next_row_id($id, "prev");
        }

        if(!empty($payments_next_id)){
            $_id = $payments_next_id[0]['id'];
            $viewpage_settings['payments_next_info'] = base_url("trucking/payments/update/{$_id}");
            $viewpage_settings['payments_next_id'] = $_id;
        }else{
            $viewpage_settings['payments_next_info'] = 0;
        }

        if(!empty($payments_prev_id)){
            $_id = $payments_prev_id[0]['id'];
            $viewpage_settings['payments_prev_info'] = base_url("trucking/payments/update/{$_id}");
            $viewpage_settings['payments_prev_id'] = $_id;
        }else{
            $viewpage_settings['payments_prev_info'] = 0;
        }

        $viewpage_settings['form_title'] = 'Update sales receipt # '.$id;
        $viewpage_settings['form_action'] = base_url("trucking/payments/ajax_update/{$id}");
        $viewpage_settings['bank_accounts'] = $this->bank_account->get();
        $viewpage_settings['data'] = $this->payments->get($id);

        $this->set_content('trucking/payments/manage', $viewpage_settings); 
        $this->generate_page();
    }

    public function ajax_create()
    {
        $this->validate();
        if(empty($this->validation_errors))
        {
            $receipt = $this->format($this->input->post());
            $result = $this->payments->create($receipt);
            if(!$result)
            {
                $this->generate_response(TRUE, ['Unable to create new trucking payment. Please try again later.'])->to_JSON();
                return;
            }
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'New trucking payment has been created!')));
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
    }

    public function ajax_update($id = FALSE)
    {
        if($id === FALSE || !$this->payments->is_valid($id))
        {
            $this->generate_response(TRUE, ['Trucking payment #'.$id.' does not exist.'])->to_JSON();
            return;
        }
        $this->validate('update');
        if(!empty($this->validation_errors))
        {
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
        $payment = $this->format($this->input->post(), 'update');
        $result = $this->payments->update($id, $payment);
        if(!$result)
        {
            $this->generate_response(TRUE, ['Unable to update trucking payment. Please try again later.'])->to_JSON();
            return;
        }
        $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Trucking payment has been updated!')));
        $this->generate_response(FALSE)->to_JSON();
        return;
    }

    public function ajax_delete()
    {
        $deleted = $this->payments->delete($this->input->post('id'));
        if($deleted)
        {
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE)->to_JSON();
    }

    /* FORM VALIDATION */

    public function validate($mode = 'create')
    {
        if($mode === 'create')
        {
            $this->form_validation->set_rules('customer', 'Customer', 'callback_validate_customer');
        }
        $this->form_validation->set_rules('status', 'Status', 'callback_validate_status');
        $this->form_validation->set_rules('tracking_type', 'Tracking Type', 'callback_validate_tracking_type');
        $this->form_validation->set_rules('tracking_no', 'Tracking No.', 'required');
        $this->form_validation->set_rules('pay_to', 'Pay to', 'callback_validate_pay_to');
        // $this->form_validation->set_rules('pay_from', 'customer bank', 'callback_validate_pay_from');
        $this->form_validation->set_rules('deposit_date', 'Deposit date', 'required|callback_validate_deposit_date');
        $this->form_validation->set_rules('payment[type]', 'Payment Type', 'callback_validate_payment_type');
        $this->form_validation->set_rules('payment[amount]', 'Payment Amount', 'callback_validate_payment_amount');
        $this->form_validation->set_rules('payment[check_number]', 'Check number', 'callback_validate_check_number');
        $this->form_validation->set_rules('payment[check_date]', 'Check date', 'callback_validate_check_date');
        if(!$this->form_validation->run())
        {
            foreach($this->form_validation->error_array() AS $field => $error)
            {
                $this->validation_errors[] = $error;
            }
        }
        else
        {
            return;
        }
        $details = $this->input->post('details');
        if(empty($details) || !is_array($details))
        {
            $this->validation_errors[] = 'Please provide payment details.';
        }
        else
        {
            $this->load->model('sales/m_delivery', 'delivery');
            $pl_ids = array_column($details, 'pl_id');
            $payments = array_column($details, 'this_payment');
            $validated_payments = array_filter($payments, function($var){
                return $this->form_validation->decimal(str_replace(',', '', $var));
            });
            if($payments !== $validated_payments)
            {
                $this->validation_errors[] = 'Packing list payment amounts should be in decimal form';
            }
            // if(!$this->delivery->is_valid_customer_deliveries($pl_ids, $this->input->post('customer')))
            // {
            //     $this->validation_errors[] = 'Please ensure that the packing lists provided are under the selected customer.';
            // }
        }
        
    }

    public function format($input, $mode = 'create')
    {
        
        $data['payment'] = [
            'tracking_number_type' => $input['tracking_type'],
            'tracking_number' => $input['tracking_no'],
            'remarks' => $input['remarks'],
            'deposit_date' => date_create($input['deposit_date'])->format('Y-m-d'),
            'pay_to' => $input['pay_to'],
            'pay_from' => $input['pay_from'],
            'date' => date_create($input['date'])->format('Y-m-d'),
        ];
        if($mode ==='create'){
            $status = isset($input['status']) ? M_Status::STATUS_FINALIZED : M_Status::STATUS_DEFAULT;
            $data['payment']['fk_sales_customer_id'] = $input['customer'];
            $data['payment']['created_by'] = $this->session->userdata('user_id');
            $data['payment']['status'] = $status;
            $data['payment']['approved_by'] = ($status == M_Status::STATUS_FINALIZED ? $this->session->userdata('user_id') : NULL);
        }else{
            if($this->session->userdata('type_id') == M_Account::TYPE_ADMIN && isset($input['status'])){
                $data['payment']['status'] =  M_Status::STATUS_FINALIZED;
                $data['payment']['approved_by'] =  $this->session->userdata('user_id');
            }else if($this->session->userdata('type_id') == M_Account::TYPE_ADMIN && !isset($input['status'])){
                $data['payment']['status'] = M_Status::STATUS_DEFAULT;
                $data['payment']['approved_by'] =  NULL;
            }
            
        }
        foreach($input['details'] AS $row){
            $amount = str_replace(',', '', $row['this_payment']);
            $temp  = [
                'fk_tracking_packing_list_id' => $row['pl_id'],
                'payment_method' => ($input['payment']['type'] === 'check' ? 'Check' : 'Cash'),
                'amount' => $amount
            ];
            if($mode === 'update' && isset($row['id'])){
                $temp['id'] = $row['id'];
            }
            $data['details'][] = $temp;
            
        }
        if($input['payment']['type'] === 'check'){
            $data['check'] = [
                'bank_account' => $data['payment']['pay_from'],
                'pay_to' => $data['payment']['pay_to'],
                'check_number' => $input['payment']['check_number'],
                'check_date' => date('Y-m-d', strtotime($input['payment']['check_date'])),
                'deposit_date' => $data['payment']['deposit_date'],
                'check_amount' => str_replace(',', '', $input['payment']['amount'])
            ];
        }

        return $data;
    }


    /* FORM VALIDATION CALLBACKS */
    public function validate_customer($customer)
    {
        $this->load->model('sales/m_customer', 'customer');
        $this->form_validation->set_message('validate_customer', 'Please select a valid %s');
        return $this->customer->is_valid($customer);
    }

    public function validate_tracking_type($tracking_type)
    {
        $tracking_types = ['PTN', 'CR'];
        $this->form_validation->set_message('validate_tracking_type', '%s can only be '.implode($tracking_types, ' or '));
        return in_array($tracking_type, $tracking_types);
    }

    public function validate_payment_type($payment_type)
    {
        $payment_types = ['cash', 'check'];
        $this->form_validation->set_message('validate_payment_type', '%s can only be '.implode($payment_types, ' or '));
        return in_array($payment_type, $payment_types);
    }

    public function validate_payment_amount($payment_amount)
    {
        $payment = $this->input->post('payment');
        if(isset($payment['type']) && $payment['type'] === 'cash')
        {
            return TRUE;
        }
        $this->form_validation->set_message('validate_payment_amount', 'Payment amount must be in decimal form.');
        return $this->form_validation->decimal(str_replace(',', '', $payment_amount));
    }

    public function validate_pay_to($pay_to)
    {
        $this->load->model('accounting/m_bank_account', 'bank');
        $this->form_validation->set_message('validate_pay_to', 'The %s field is required.');
        return $this->bank->is_valid($pay_to);
    }

    public function validate_check_number($check_number)
    {
        $payment = $this->input->post('payment');
        if(isset($payment['type']) && $payment['type'] === 'cash')
        {
            return TRUE;
        }
        $this->form_validation->set_message('validate_check_number', 'The %s field is required.');
        return $this->form_validation->required($check_number);
    }

    public function validate_check_date($check_date)
    {
        $payment = $this->input->post('payment');
        if(isset($payment['type']) && $payment['type'] === 'cash')
        {
            return TRUE;
        }
        $this->load->helper('pmdate');
        $this->form_validation->set_message('validate_check_date', 'Please enter a valid check date: mm/dd/yyyy');
        return is_valid_date($check_date, 'm/d/Y');
    }

    public function validate_deposit_date($deposit_date)
    {
        $this->load->helper('pmdate');
        $this->form_validation->set_message('validate_deposit_date', 'Please enter a valid deposit date: mm/dd/yyyy');
        return is_valid_date($deposit_date, 'm/d/Y');
    }

    public function validate_status($status)
    {
        if($status==M_Status::STATUS_FINALIZED)
        {
            $this->form_validation->set_message('validate_status', 'Only administrators can finalize trucking payment.');
            return $this->session->userdata('type_id') == M_Account::TYPE_ADMIN;
        }
        return TRUE;
    }
}

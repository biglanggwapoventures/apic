<?php

class Debit_Memo extends PM_Controller_v2
{

    CONST VIEW_PATH = 'accounting/debit_memo/';

    private $validation_errors = [];

    public function __construct()
    {
        parent::__construct();
        if (!has_access('accounting'))
            show_error('Authorization error', 401);
        $this->set_active_nav(NAV_ACCOUNTING);
        $this->set_content_title('Accounting');
        $this->set_content_subtitle('Dummy Memo');
        $this->load->model('accounting/m_debit_memo', 'debit_memo');
    }

    public function index()
    {
        $this->add_javascript(['printer/printer.js', 'numeral.js', 'accounting-debit-memo/master-list.js']);
        $this->setTabTitle('Debit Memo');
        $this->set_content(self::VIEW_PATH.'listing');
        $this->generate_page();
    }

    public function create()
    {
        $this->add_javascript(['price-format.js', 'accounting-debit-memo/manage.js']);
        $this->load->helper('customer');
        $this->load->model('accounting/m_bank_account', 'account');
        $this->setTabTitle('Create new debit memo');
        $this->set_content(self::VIEW_PATH . 'manage', [
            'form_title' => 'Create new debit memo',
            'form_action' => base_url('accounting/debit_memo/ajax_create'),
            'accounts' => $this->account->get()
        ]);
        $this->generate_page();
    }

    public function update($id = FALSE)
    {
        if ($id === FALSE || !$this->debit_memo->is_valid($id)) {
            show_404();
        }
        $this->add_javascript(['printer/printer.js', 'price-format.js', 'accounting-debit-memo/manage.js']);
        $this->load->helper('customer');
        $this->setTabTitle('Update dummy check');
        $this->set_content(self::VIEW_PATH . 'manage', [
            'form_title' => 'Update debit_memo',
            'form_action' => base_url("accounting/debit_memo/ajax_update/{$id}"),
            'dc' => $this->debit_memo->get($id)
        ]);
        $this->generate_page();
    }

    public function ajax_get()
    {
        $this->generate_response($this->debit_memo->all())->to_JSON();
    }

    public function ajax_create()
    {
        $this->validate();
        if (!empty($this->validation_errors)) {
            $this->generate_response(TRUE, array_values($this->validation_errors))->to_JSON();
            return;
        }
        $created = $this->debit_memo->create($this->format());
        if ($created) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully created new debit memo!')));
            $this->generate_response(FALSE)->to_JSON();
        } else {
            $this->generate_response(TRUE, ['Unable to create new debit memo. Please try again later.'])->to_JSON();
        }
    }

    public function ajax_update($id)
    {
        if ($id === FALSE || !$this->debit_memo->is_valid($id)) {
            $this->output->set_status_header('404');
            $this->generate_response(TRUE, ['Debit memo does not exist.'])->to_JSON();
            return;
        }
        if (!is_admin() && $this->debit_memo->is_approved($id)) {
            $this->generate_response(TRUE, ['You are not authorized to update approved debit memo. Please contact administrator.'])->to_JSON();
            return;
        }
        $this->validate('update');
        if (!empty($this->validation_errors)) {
            $this->generate_response(TRUE, array_values($this->validation_errors))->to_JSON();
            return;
        }
        $updated = $this->debit_memo->update($id, $this->format('update'));
        if ($updated) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully updated the debit memo!')));
            $this->generate_response(FALSE)->to_JSON();
        } else {
            $this->generate_response(TRUE, ['Unable to update the debit memo. Please try again later.'])->to_JSON();
        }
    }

    public function ajax_delete()
    {
        if (!is_admin()) {
            $this->generate_response(TRUE, 'You are not authorized to delete debit memo. Please contact administrator.')->to_JSON();
            return;
        }
        $id = $this->input->post('id');
        if (!$this->debit_memo->is_valid($id)) {
            $this->generate_response(TRUE, 'Debit memo does not exist.')->to_JSON();
            return;
        }
        if ($this->debit_memo->delete($id)) {
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Failed to delete debit memo.')->to_JSON();
        return;
    }

    public function ajax_print($id)
    {
        if (!is_numeric($id) || !$this->debit_memo->is_valid($id) || !$this->debit_memo->is_approved($id)) {
            show_404();
        }
        $data = $this->debit_memo->get($id);
        $this->load->model('accounting/m_bank_account', 'account');
        $this->load->view('printables/accounting/dummy-check-voucher', ['data' => $data, 'account' => $this->account->get($data['bank_account'])[0]]);
    }

    public function ajax_print_check($check_type = FALSE)
    {
        if(!$this->input->get('dc_no')){
            echo 'Please provide dummy check number.';
            return;
        }
        if(!in_array($check_type, ['rcbc', 'metrobank'])) {
            echo 'Only RCBC and Metrobank checks are available';
            return;
        }
        if(!$this->debit_memo->is_valid($this->input->get('dc_no'))) {
            echo 'Dummy check does not exist.';
            return;
        }
        $data = $this->debit_memo->get($this->input->get('dc_no'));
        $this->load->view("printables/{$check_type}-check", [
            'pay_to' => $data['payee'],
            'date' => $data['check_date'],
            'amount' => $data['check_amount']
        ]);
    }

    public function validate($mode = 'create')
    {
        if($mode === 'create')
        {
            $this->form_validation->set_rules('customer', 'Customer', 'callback_validate_customer');
        }
        $this->form_validation->set_rules('date', 'Date', 'required|callback_validate_date');
        $this->form_validation->set_rules('amount', 'Amount', 'required|callback_validate_check_amount');
        $this->form_validation->set_rules('remarks', 'Remarks', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->validation_errors = $this->form_validation->error_array();
        }
        return;
    }

    public function validate_customer($customer)
    {
        $this->load->model('sales/m_customer', 'customer');
        $this->form_validation->set_message('validate_customer', 'Please select a valid %s');
        return $this->customer->is_valid($customer);
    }

    public function validate_date($val)
    {
        $this->load->helper('pmdate');
        $this->form_validation->set_message('validate_date', 'Please enter a valid %s with format: mm/dd/yyyy');
        return is_valid_date($val, 'm/d/Y');
    }

    public function validate_bank_account($val)
    {
        $this->load->model('accounting/m_bank_account', 'account');
        $this->form_validation->set_message('validate_bank_account', 'Please choose a valid %s');
        return $this->account->is_valid($val);
    }

    public function validate_check_amount($val)
    {
        $this->form_validation->set_message('validate_check_amount', '%s must be decimal/numeric.');
        return $this->form_validation->decimal(str_replace(',', '', $val));
    }

    public function validate_status($status)
    {
        if ($status) {
            $this->form_validation->set_message('validate_status', 'You are not authorized to approve dummy checkes. Please contact administrator.');
            return is_admin();
        }
    }

    public function format($mode = 'create')
    {
        $input = $this->input->post();
        $data = elements(['amount', 'remarks'], $this->input->post());
        if (isset($input['status'])){
            $data['approved_by'] = user_id();
        }else{
            $data['approved_by'] = NULL;
        }
        $data['amount'] = str_replace(',', '', $input['amount']);
        $data['date'] = date('Y-m-d', strtotime($input['date']));
        $data['fk_sales_customer_id'] = $input['customer'];
        if ($mode === 'create') {
            $data['created_by'] = $this->session->userdata('user_id');
        }
        return $data;
    }

}

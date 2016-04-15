<?php

class Print_checks extends PM_Controller_v2
{

	public function __construct()
	{
		parent::__construct();
		if(!has_access('accounting')) show_error('Authorization error', 401);
		$this->setTabTitle('Print checks - Accounting');
		$this->set_active_nav(NAV_ACCOUNTING);
		$this->set_content_title('Accounting');
		$this->set_content_subtitle('Print check');
		$this->load->model('accounting/m_print_check', 'print_check');
	}

	public function index()
	{
		$this->load->model('accounting/m_bank_account', 'bank');
		$this->add_javascript(['printer/printer.js', 'numeral.js', 'accounting-print-checks/listing.js']);
		$this->set_content('accounting/print-checks/listing', [
			'banks' => array_column($this->bank->get(), 'bank_name', 'id')
		]);
		$this->generate_page();
	}

	public function ajax_get()
    {
        $this->generate_response($this->print_check->all())->to_JSON();
    }

	public function create()
	{
		$this->load->model('accounting/m_bank_account', 'bank');
		$this->add_javascript(['price-format.js', 'accounting-print-checks/manage.js']);
		$this->set_content('accounting/print-checks/manage', [
			'form_title' => 'Add new check to print',
			'form_action' => base_url('accounting/print_checks/ajax_create'),
			'accounts' => $this->bank->get()
		]);
		$this->generate_page();
	}

	public function update($id = FALSE)
	{
		if(!$id || !$this->print_check->is_valid($id)){
			show_404();
		}
		$this->load->model('accounting/m_bank_account', 'bank');
		$this->add_javascript(['price-format.js', 'accounting-print-checks/manage.js']);
		$this->set_content('accounting/print-checks/manage', [
			'form_title' => 'Update check to print',
			'form_action' => base_url("accounting/print_checks/ajax_update/{$id}"),
			'accounts' => $this->bank->get(),
			'c' => $this->print_check->get($id)
		]);
		$this->generate_page();
	}

	public function ajax_create()
    {
        $this->_validate();
        if (!$this->form_validation->run()) {
            $this->generate_response(TRUE, array_values($this->form_validation->error_array()))->to_JSON();
             return;
        }
        $created = $this->print_check->create($this->_format());
        if ($created) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully created new check!')));
            $this->generate_response(FALSE)->to_JSON();
        } else {
            $this->generate_response(TRUE, ['Unable to create new check. Please try again later.'])->to_JSON();
        }
    }

    public function ajax_update($id = FALSE)
    {
    	if(!$id || !$this->print_check->is_valid($id)){
			show_404();
		}
        $this->_validate();
        if (!$this->form_validation->run()) {
            $this->generate_response(TRUE, array_values($this->form_validation->error_array()))->to_JSON();
            return;
        }
        $updated = $this->print_check->update($id, $this->_format());
        if ($updated) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully updated new check!')));
            $this->generate_response(FALSE)->to_JSON();
        } else {
            $this->generate_response(TRUE, ['Unable to update new check. Please try again later.'])->to_JSON();
        }
    }

    public function ajax_delete()
    {
    	if(is_numeric($this->input->post('id'))){
    		$deleted = $this->print_check->delete($this->input->post('id'));
    		$this->generate_response(!$deleted)->to_JSON();
    		return;
    	}
    	$this->generate_response(FALSE)->to_JSON();
    }

    public function ajax_print($id = FALSE)
    {
    	if(!$id || !$this->print_check->is_valid($id)){
			show_404();
		}
		$check = $this->print_check->get($id);
		$data = [
			'date' => $check['check_date'],
			'pay_to' => $check['payee'],
			'amount' => $check['amount']
		];
		if($check['check_type'] === 'mb' || $check['check_type'] === 'xmb'){
			$this->load->view('printables/metrobank-check', $data);
			return;
		}
		$this->load->view('printables/rcbc-check', $data);
    }

	public function _validate()
	{
		$this->form_validation->set_rules('check_type', 'Check type', 'callback__validate_check_type');
		$this->form_validation->set_rules('payee', 'Pay to', 'required');
		$this->form_validation->set_rules('bank_account', 'Bank account', 'callback__validate_bank_account');
		$this->form_validation->set_rules('check_number', 'Check number', 'required');
		$this->form_validation->set_rules('check_date', 'Check date', 'callback__validate_check_date');
		$this->form_validation->set_rules('check_amount', 'Check amount', 'callback__validate_check_amount');
	}

	public function _format($mode = 'create')
	{
		$input = $this->input->post();
		$check = elements(['check_type', 'bank_account', 'payee', 'check_number'], $input);
		$check['amount'] = str_replace(',', '', $input['check_amount']);
		$check['check_date'] = date('Y-m-d', strtotime($input['check_date']));
		if($mode === 'create'){
			$check['created_by'] = $this->session->userdata('user_id');
		}
		return $check;
	}

	function _validate_check_type($check_type = FALSE)
	{
		$this->form_validation->set_message('_validate_check_type', 'Please select a valid check type');
		return in_array($check_type, ['rcbc', 'mb', 'xmb', 'xrcbc']);
	}

	function _validate_bank_account($bank)
	{
		$this->load->model('accounting/m_bank_account', 'bank');
		$this->form_validation->set_message('_validate_bank_account', 'Please select a valid bank account');
		return $this->bank->is_valid($bank);
	}

	function _validate_check_date($date)
	{
		$this->load->helper('pmdate');
		$this->form_validation->set_message('_validate_check_date', 'Please enter a valid date with format: mm/dd/yyyy.');
		return is_valid_date($date, 'm/d/Y');
	}

	function _validate_check_amount($amount)
	{
		$this->form_validation->set_message('_validate_check_amount', 'Check amount must be numeric and/or in decimal form.');
		return $this->form_validation->decimal(str_replace(',', '', $amount));
	}
}
<?php

class Dummy_checks extends PM_Controller_v2
{

    CONST VIEW_PATH = 'accounting/dummy-checks/';

    private $validation_errors = [];

    public function __construct()
    {
        parent::__construct();
        if (!has_access('accounting'))
            show_error('Authorization error', 401);
        $this->set_active_nav(NAV_ACCOUNTING);
        $this->set_content_title('Accounting');
        $this->set_content_subtitle('Dummy Checks');
        $this->load->model('accounting/m_dummy_check', 'dummy_check');
    }

    public function index()
    {
        $this->add_javascript(['printer/printer.js', 'numeral.js', 'accounting-dummy-checks/master-list.js']);
        $this->setTabTitle('Dummy Checks');
        $this->set_content(self::VIEW_PATH . 'listing');
        $this->generate_page();
    }

    public function create()
    {
        $this->add_javascript(['price-format.js', 'plugins/bootstrap-datetimepicker/moment.js', 'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js', 'accounting-dummy-checks/manage.js']);
        $this->load->model('accounting/m_bank_account', 'account');
        $this->setTabTitle('Create new dummy check');
        $this->set_content(self::VIEW_PATH . 'manage', [
            'form_title' => 'Create new dummy check',
            'form_action' => base_url('accounting/dummy_checks/ajax_create'),
            'accounts' => $this->account->get()
        ]);
        $this->generate_page();
    }

    public function update($id = FALSE)
    {
        if ($id === FALSE || !$this->dummy_check->is_valid($id)) {
            show_404();
        }
        $this->add_javascript(['printer/printer.js', 'price-format.js', 'plugins/bootstrap-datetimepicker/moment.js', 'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js', 'accounting-dummy-checks/manage.js']);
        $this->load->model('accounting/m_bank_account', 'account');
        $this->setTabTitle('Update dummy check');
        $this->set_content(self::VIEW_PATH . 'manage', [
            'form_title' => 'Update dummy check',
            'form_action' => base_url("accounting/dummy_checks/ajax_update/{$id}"),
            'accounts' => $this->account->get(),
            'dc' => $this->dummy_check->get($id)
        ]);
        $this->generate_page();
    }

    public function ajax_get($offset)
    {
        $this->generate_response($this->dummy_check->all(FALSE, $offset))->to_JSON();
    }

    public function ajax_create()
    {
        $this->validate();
        if (!empty($this->validation_errors)) {
            $this->generate_response(TRUE, array_values($this->validation_errors))->to_JSON();
            return;
        }
        $created = $this->dummy_check->create($this->format());
        if ($created) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully created new dummy check!')));
            $this->generate_response(FALSE)->to_JSON();
        } else {
            $this->generate_response(TRUE, ['Unable to create new dummy check. Please try again later.'])->to_JSON();
        }
    }

    public function ajax_update($id)
    {
        if ($id === FALSE || !$this->dummy_check->is_valid($id)) {
            $this->output->set_status_header('404');
            $this->generate_response(TRUE, ['Dummy check does not exist.'])->to_JSON();
            return;
        }
        if (!is_admin() && $this->dummy_check->is_approved($id)) {
            $this->generate_response(TRUE, ['You are not authorized to update approved dummy checkes. Please contact administrator.'])->to_JSON();
            return;
        }
        $this->validate();
        if (!empty($this->validation_errors)) {
            $this->generate_response(TRUE, array_values($this->validation_errors))->to_JSON();
            return;
        }
        $updated = $this->dummy_check->update($id, $this->format('update'));
        if ($updated) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully updated the dummy check!')));
            $this->generate_response(FALSE)->to_JSON();
        } else {
            $this->generate_response(TRUE, ['Unable to update the dummy check. Please try again later.'])->to_JSON();
        }
    }

    public function ajax_delete()
    {
        if (!is_admin()) {
            $this->generate_response(TRUE, 'You are not authorized to delete dummy checkes. Please contact administrator.')->to_JSON();
            return;
        }
        $id = $this->input->post('id');
        if (!$this->dummy_check->is_valid($id)) {
            $this->generate_response(TRUE, 'Dummy check does not exist.')->to_JSON();
            return;
        }
        if ($this->dummy_check->delete($id)) {
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Failed to delete dummy check.')->to_JSON();
        return;
    }

    public function ajax_print($id)
    {
        if (!is_numeric($id) || !$this->dummy_check->is_valid($id) || !$this->dummy_check->is_approved($id)) {
            show_404();
        }
        $data = $this->dummy_check->get($id);
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
        if(!$this->dummy_check->is_valid($this->input->get('dc_no'))) {
            echo 'Dummy check does not exist.';
            return;
        }
        $data = $this->dummy_check->get($this->input->get('dc_no'));
        $this->load->view("printables/{$check_type}-check", [
            'pay_to' => $data['payee'],
            'date' => $data['check_date'],
            'amount' => $data['check_amount']
        ]);
    }

    public function validate()
    {
        $this->form_validation->set_rules('date', 'Date', 'required|callback_validate_date');
        $this->form_validation->set_rules('payee', 'Payee', 'required');
        $this->form_validation->set_rules('bank_account', 'Bank account', 'required|callback_validate_bank_account');
        $this->form_validation->set_rules('check_number', 'Check number', 'required|numeric');
        $this->form_validation->set_rules('check_date', 'Check date', 'required|callback_validate_date');
        $this->form_validation->set_rules('check_amount', 'Check amount', 'required|callback_validate_check_amount');
        // $this->form_validation->set_rules('is_approved', '', 'required|callback_validate_status');
        if ($this->form_validation->run() === FALSE) {
            $this->validation_errors = $this->form_validation->error_array();
        }
        return;
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
        $data = elements(['check_number', 'remarks', 'payee', 'bank_account'], $this->input->post());
        // if (is_admin()) {
            $data['approved_by'] = user_id();
        // }
        $data['crossed'] = isset($input['crossed']) && (int)$input['crossed'] ? 1 : 0;
        $data['print_check_date'] = (int)!(isset($input['hide_check_date_on_print']) && (int)$input['hide_check_date_on_print']);
        $data['check_amount'] = str_replace(',', '', $input['check_amount']);
        $data['date'] = date_create($input['date'])->format('Y-m-d');
        $data['check_date']  = isset($input['check_date']) && $input['check_date']  ? date_create($input['check_date'])->format('Y-m-d') :  NULL;
        if ($mode === 'create') {
            $data['created_by'] = $this->session->userdata('user_id');
        }
        return $data;
    }

}

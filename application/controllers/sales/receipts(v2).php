<?php

class Receipts extends PM_Controller_v2 {

    const SUBJECT = 'receipt';

    private $_data = array();
    private $_errs = array();

    function __construct() {
        parent::__construct();
        $this->setTabTitle('Sales - Receipts');
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title('Sales');
        $this->set_content_subtitle('Receipts');
        $this->load->model('sales/m_receipt');
    }

    public function a_master_list() {
        $this->load->helper('array');
        //$parameters = elements(array('so', 'po', 'customer', 'date', 'page'), $this->input->get(), 0);
        $data = $this->m_receipt->master_list();
        $this->output->set_content_type('json');
        $this->output->set_output(json_encode($data ? array('data' => $data) : array()));
    }

    public function index() {
        $this->add_javascript(array('plugins/json2html.js', 'plugins/jquery.json2html.js', 'plugins/sticky-thead.js', 'sales-receipts/master-list.js'));
        $this->set_content('sales/receipts/master-list', $this->_data);
        $this->generate_page();
    }

    public function create() {
        if (!empty($input = $this->input->post())) {
            $this->output->set_content_type('json');
            $this->_perform_validation($input);
            if (empty($this->_errs)) {
                $formatted = $this->_format_input($input);
                $this->output->set_output(json_encode($formatted));
                return;
                $this->m_receipt->create($formatted);
            } else {
                $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors!', $this->_errs)));
            }
        } else {
            $this->setTabTitle('Sales - Add new receipt');
            $this->_data['form_action'] = base_url('sales/receipts/create');
            $this->_data['customers'] = $this->_customer_dropdown();
            $this->_data['banks'] = $this->_bank_dropdown();
            $this->_data['form_title'] = 'Add new sales receipt';
            $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
            $this->add_javascript(array('price-format.js', 'numeral.js', 'sales-receipts/create.js'));
            $this->set_content('sales/receipts/manage', $this->_data);
            $this->generate_page();
        }
    }

    function _perform_validation($data) {
        if (!$this->_validate_date($data['date'])) {
            $this->_errs[] = 'Receipt date is invalid. Please use the format YYYY-MM-DD';
        }
        if (!$this->_validate_payment_method($data['details']['payment_method'])) {
            $this->_errs[] = 'Payment methods can only be cash or check. Please review your inputs.';
        }
        if (array_key_exists('check_payments', $data)) {
            if (array_key_exists('check_date', $data['check_payments']) && !$this->_validate_date($data['check_payments']['check_date'])) {
                $this->_errs[] = 'One or more of the check dates contain invalid formats. Please use the format YYYY-MM-DD';
            }
            if (array_key_exists('deposit_date', $data['check_payments']) && !$this->_validate_date($data['check_payments']['deposit_date'])) {
                $this->_errs[] = 'One or more of the deposit dates contain invalid formats. Please use the format YYYY-MM-DD';
            }
        }
    }

    function _customer_dropdown() {
        $this->load->model('sales/m_customer');
        $customers = $this->m_customer->get_approved();
        $dropdown_format = array('' => '');
        array_map(function($var) use (&$dropdown_format) {
            $dropdown_format[$var['id']] = $var['name'];
        }, $customers);
        return $dropdown_format;
    }

    function _bank_dropdown() {
        $this->load->model('accounting/m_bank_account');
        $customers = $this->m_bank_account->get();
        $dropdown_format = array('' => '');
        array_map(function($var) use (&$dropdown_format) {
            $dropdown_format[$var['id']] = "{$var['bank_name']} ({$var['bank_branch']})";
        }, $customers);
        return $dropdown_format;
    }

    function _validate_date($date, $format = 'Y-m-d') {
        if (!is_array($date)) {
            $date = (array) $date;
        }
        return array_filter($date, function($var) use($format) {
                    $d = DateTime::createFromFormat($format, $var);
                    return $d && $d->format($format) == $var;
                }) === $date;
    }

    function _validate_payment_method($payment_method = array()) {
        if (!is_array($payment_method)) {
            $payment_method = (array) $payment_method;
        }
        return array_filter($payment_method, function($var) {
                    return $var === 'Cash' || $var === 'Check';
                }) === $payment_method;
    }

    function _format_input($input) {
        $this->load->helper('array');
        $formatted = elements(array('fk_sales_customer_id', 'date', 'tracking_number_type', 'tracking_number', 'remarks', 'status'), $input, '');
        if ($this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
            $formatted['status'] = M_Status::STATUS_PENDING;
        }
        foreach ($input['details']['fk_sales_delivery_id'] as $key => $value) {
            $formatted['details'][] = array(
                'fk_sales_delivery_id' => $value,
                'payment_method' => $input['details']['payment_method'][$key],
                'amount' => str_replace(',', '', $input['details']['amount'][$key])
            );
        }
        if (empty($input['check_payments'])) {
            return $formatted;
        }
        foreach ($input['check_payments']['transaction_id'] as $key => $value) {
            $formatted['check_payments'][] = array(
                'transaction_id' => $value,
                'fk_accounting_bank_account_id' => $input['check_payments']['fk_accounting_bank_account_id'][$key],
                'check_number' => $input['check_payments']['check_number'][$key],
                'check_date' => $input['check_payments']['check_date'][$key],
                'deposit_date' => $input['check_payments']['deposit_date'][$key],
                'amount' => str_replace(',', '', $input['check_payments']['amount'][$key])
            );
        }
        return $formatted;
    }

}

<?php

class Customers extends PM_Controller_v2 {

    const TITLE = 'Sales';
    const SUBTITLE = 'Customers';
    const SUBJECT = 'customer';

    private $viewpage_settings = array();

    public function __construct() {
        parent::__construct();
        /* restrict unauthorized access */
        if (!has_access('sales')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->add_javascript(['plugins/sticky-thead.js', 'price-format.js']);
        $this->load->model(array('sales/m_customer'));
        $this->viewpage_settings['defaults'] = array(
            'company_name' => FALSE,
            'customer_code' => FALSE,
            'address' => FALSE,
            'contact_person' => FALSE,
            'contact_number' => FALSE,
            'credit_limit' => FALSE,
            'credit_term' => FALSE,
            'tin_number' => '',
            'fax_number' => '',
            'bank' => array(
                'name' => array(''),
                'account_number' => array('')
            ),
            'status' => M_Status::STATUS_DEFAULT
        );
    }

    public function a_get() {
        $filter = array();
        if ($this->input->get('customer_id')) {
            $filter = array('customer.id' => $this->input->get('customer_id'));
        }
        $data = $this->m_customer->get(FALSE, $filter);
        if ($this->input->get('for_listing')) {
            $data = array_map(function ($var) {
                return array('company_name' => $var['company_name'], 'id' => $var['id']);
            }, $data);
        }
        echo json_encode($this->response(!$data, '', $data));
        exit();
    }

    public function a_get_registered_products() {
        $this->load->helper('customer');
        $items = $this->m_customer->get_customer_products($this->input->get('id'));
        $this->output->set_output(generate_customer_product_dropdown('details[fk_inventory_product_id][]', $items, 'product_id', 'description', FALSE, FALSE, 'class="form-control product-list select-clear"'));
    }

    private function validate() {
        if ($this->form_validation->run('customer')) {
            $customer_info = $this->input->post();
            unset($customer_info['info']['credit_terms']);
            if (!array_key_exists('credit_term', $customer_info)) {
                $customer_info['credit_term'] = 0;
            }
            $bank = $customer_info['bank'];
            $banks = array();
            for ($x = 0; $x < count($bank['name']); $x++) {
                $banks[$x]['name'] = $bank['name'][$x];
                $banks[$x]['account_number'] = $bank['account_number'][$x];
            }
            $customer_info['bank'] = $banks;
            $customer_info['credit_limit'] = str_replace(",", "", $this->input->post('credit_limit'));
            if ($customer_info['status'] == M_Status::STATUS_DEFAULT) {
                unset($customer_info['status']);
            }
            return $this->response(FALSE, '', $customer_info);
        } else {
            return $this->response(TRUE, validation_errors('<li>', '</li>'));
        }
    }

    public function a_get_so() {
        if (!$this->input->is_ajax_request()) {
            echo json_encode($this->response(TRUE, 'Bad request'));
            exit();
        }
        $this->load->model(array('sales/m_sales_order'));
        $customer_id = $this->input->get('customer_id');
        $data = $this->m_sales_order->get_so_from($customer_id);
        if ($data) {
            echo json_encode($this->response(FALSE, 'Successful data fetching', $data));
        } else {
            echo json_encode($this->response(TRUE, 'No results found'));
        }

        exit();
    }

    function index() {
        $this->add_javascript('printer/printer.js');
        $this->viewpage_settings['url'] = base_url('sales/customers');
        $this->viewpage_settings['default_keyword'] = $this->input->get('search_keyword');
        $this->viewpage_settings['entries'] = $this->m_customer->get($this->viewpage_settings['default_keyword']);
        $this->set_content('sales/customers', $this->viewpage_settings);
        $this->generate_page();
    }

    function credit_limit_check() {
        $credit_limit = str_replace(",", "", $this->input->post('credit_limit'));
        if (!is_numeric($credit_limit)) {
            $this->form_validation->set_message('credit_limit_check', 'Invalid value for %s');
            return FALSE;
        }
        return TRUE;
    }

    public function update($id) {
        $this->add_javascript(['sales-customers.js']);
        $saved = FALSE;
        $customer_info = $this->m_customer->get(FALSE, array('customer.id' => $id));
        $this->viewpage_settings['form_title'] = 'Update customer';
        $this->viewpage_settings['url'] = base_url("sales/customers/update/{$id}");
        $this->viewpage_settings['credit_terms_options'] = M_Customer::list_credit_terms();
        if ($this->input->post()) {
            $input = $this->validate();
            $this->viewpage_settings['defaults'] = $this->input->post();
            if ($input['error_flag']) {
                $this->viewpage_settings['validation_errors'] = $input['message'];
                $this->viewpage_settings['defaults']['status'] = $customer_info[0]['status'];
            } else {
                $saved = $this->m_customer->update($id, $input['data']);
                $this->viewpage_settings['defaults']['status'] = $this->viewpage_settings['defaults']['status'] == M_Status::STATUS_DEFAULT ? $customer_info[0]['status'] : $this->viewpage_settings['defaults']['status'];
            }
            if ($saved) {
                $this->viewpage_settings['form_submission_success'] = $this->m_message->update_success(self::SUBJECT);
            } else {
                $error = !$this->viewpage_settings['validation_errors'] ? '<li>' . $this->m_message->update_error(self::SUBJECT) . '</li>' : $this->viewpage_settings['validation_errors'] . '</li>';
                $this->viewpage_settings['validation_errors'] = $error;
            }
        } else {
            $this->viewpage_settings['defaults'] = $customer_info[0];
        }
        $this->set_content('sales/manage-customer', $this->viewpage_settings);
        $this->generate_page();
    }

    public function add() {
        $this->add_javascript(['sales-customers.js']);
        $saved = FALSE;
        $this->viewpage_settings['form_title'] = 'Add new customer';
        $this->viewpage_settings['url'] = base_url('sales/customers/add');
        $this->viewpage_settings['credit_terms_options'] = M_Customer::list_credit_terms();
        if ($this->input->post()) {
            $input = $this->validate();
            if ($input['error_flag']) {
                $this->viewpage_settings['validation_errors'] = $input['message'];
                $this->viewpage_settings['defaults'] = $this->input->post();
                $this->viewpage_settings['defaults']['status'] = M_Status::STATUS_DEFAULT;
            } else {
                $saved = $this->m_customer->add($input['data']);
            }
            if ($saved) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT));
                redirect('sales/customers');
            } else {
                $error = !$this->viewpage_settings['validation_errors'] ? '<li>' . $this->m_message->add_error(self::SUBJECT) . '</li>' : $this->viewpage_settings['validation_errors'] . '</li>';
                $this->viewpage_settings['validation_errors'] = $error;
            }
        } else {
            
        }
        $this->set_content('sales/manage-customer', $this->viewpage_settings);
        $this->generate_page();
    }

    public function save_customer_pricing($customer_id = FALSE) 
    {
        if($this->m_customer->save_price_list($customer_id, $this->input->post('list'))){
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Price list has been updated!')));
            $this->generate_response(['result' => TRUE])->to_JSON();    
            return;
        }
         $this->generate_response(['result' => FALSE])->to_JSON();    
    }

    public function show_pricing($customer_id)
    {   
        $this->add_javascript(['sales-customer-pricing.js']);
        $this->load->model('inventory/m_product');
        $active_products = $this->m_product->get(FALSE, ['status' => 'Active']);
        array_walk($active_products, function(&$var){
            $var['description'] .= ($var['formulation_code'] ? " [{$var['formulation_code']}]" : "");
        });
        $this->set_content_subtitle('Customer Pricing');
        $this->set_content('sales/manage-customer-pricing', [
            'name' => $this->m_customer->get_name($customer_id),
            'price_list' => $this->m_customer->get_customer_price_list($customer_id),
            'active_products' => array_column($active_products, 'description', 'id'),
            'customer_id' => $customer_id
        ]);
        $this->generate_page();
    }

    public function a_get_pricing() {
        $customer_id = $this->input->get('customer_id');
        $response = array();
        if (!$customer_id) {
            $response = json_encode($this->response(TRUE, 'Please provide customer id.'));
        }
        $price_list = $this->m_customer->get_price_list($customer_id, TRUE);
        if ($price_list) {
            $response = json_encode($this->response(FALSE, 'Successfully retrieved customer\'s price list.', $price_list));
        } else {
            $response = json_encode($this->response(TRUE, 'No data retrieved.'));
        }
        echo $response;
        exit();
    }

    function a_delete() {
        $this->form_validation->set_rules('pk', 'ID', 'required');
        if ($this->form_validation->run()) {
            $deleted = $this->m_customer->delete($this->input->post('pk'));
            if ($deleted) {
                echo json_encode($this->response(FALSE, $this->m_message->delete_success(self::SUBJECT)));
            } else {
                echo json_encode($this->response(TRUE, $this->m_message->delete_error(self::SUBJECT), array()));
            }
        } else {
            echo json_encode($this->response(TRUE, $this->m_message->no_primary_key_error(self::SUBJECT), array()));
        }
        exit();
    }

    /* =====================
      NEW FUNCTIONS 01-14-15
      ===================== */

    public function a_get_list() {
        $this->output->set_content_type('json');
        $data = $this->m_customer->get_list();
        $this->output->set_output(json_encode($data ? $data : array()));
    }

    public function a_price_list() {
        $this->output->set_content_type('json');
        $data = $this->m_customer->price_list($this->input->get('customer_id'));
        $this->output->set_output(json_encode($data ? $data : array()));
    }

    public function a_get_unsettled() {
        $this->output->set_content_type('json');
        $data = $this->m_customer->get_unsettled($this->input->get('customer_id'));
        $this->output->set_output(json_encode($data ? $data : array()));
    }

    public function a_get_undelivered_so() {
        $this->output->set_content_type('json');
        $data = $this->m_customer->get_undelivered_orders($this->input->get('customer_id'));
        $this->output->set_output(json_encode($data ? $data : array()));
    }

    public function ajax_statement_of_accounts() {
        $data = $this->input->get();
        $this->load->helper('pmdate');
        $date_range = FALSE;
        if (is_valid_date($data['start_date']) && is_valid_date($data['end_date'])) {
            $date_range = ['start' => $data['start_date'], 'end' => $data['end_date']];
        }
        $this->load->model('sales/m_customer', 'customer');
        $this->load->view('printables/sales/statement-of-accounts', [
            'data' => $this->customer->get_statement_of_account($data['customer'], $date_range),
            'customer' => $this->customer->get(FALSE, ['id' => $data['customer']])
        ]);
    }

}

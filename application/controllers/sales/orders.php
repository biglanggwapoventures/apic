<?php

class Orders extends PM_Controller_v2 {

    const TITLE = 'Sales';
    const SUBTITLE = 'Orders';
    const SUBJECT = 'sales order';

    private $viewpage_settings = array();

    protected $validation_errors = [];

    public function __construct() {
        parent::__construct();
        /* restrict unauthorized access */
        if (!has_access('sales')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->add_javascript(array());
        $this->load->model(array('sales/m_sales_order', 'sales/m_customer', 'inventory/m_product', 'sales/m_agent'));
        $this->viewpage_settings['defaults'] = array(
            'fk_sales_customer_id' => '',
            'fk_sales_agent_id' => FALSE,
            'po_number' => '',
            'date' => '',
            'remarks' => '',
            'misc_charges' => array(
                'handling' => '',
                'trucking' => '',
                'medication' => '',
                'others' => ''
            ),
            'details' => array(
                'fk_inventory_product_id' => array(''),
                'product_quantity' => array(''),
                'unit_description' => array(''),
                'unit_price' => array(0),
                'discount' => array(0),
                'total_units' => array(0),
            ),
            'add_ons' => array(
                'medication_id' => array(''),
                'unit_price' => array(0),
                'quantity' => array('')
            ),
            'status' => M_Status::STATUS_DEFAULT,
            'total_amount' => 0.00
        );
    }

    public function index() {
        $this->setTabTitle('Sales - Orders');
        $this->add_javascript(array('plugins/json2html.js', 'plugins/jquery.json2html.js', 'plugins/sticky-thead.js', 'sales-order/master-list.js'));
        $this->viewpage_settings['customers'] = $this->m_customer->get_list();
        $this->set_content('sales/orders-new', $this->viewpage_settings);
        $this->generate_page();
    }

    function a_get() {
        $id = $this->input->get('order_id');
        if ($id) {
            $data = $this->m_sales_order->get(FALSE, array('s_order.id' => $id));
            echo json_encode($this->response(FALSE, 'Successful data fetching.', $data[0]));
        } else {
            echo json_encode($this->response(TRUE, 'Must provide order id!'));
        }
        exit();
    }

    public function sample()
    {
        $this->set_content('sales/sample');
        $this->generate_page();
    }

    public function a_fetch_details() {
        $this->output->set_content_type('json');
        $this->output->set_output(json_encode($this->m_sales_order->fetch_order_details($this->input->get('order_id'), TRUE)));
    }

    public function a_master_list() {
        $this->load->helper('array');
        $parameters = elements(array('so', 'po', 'customer', 'date', 'page'), $this->input->get(), 0);
        $data = $this->m_sales_order->master_list($parameters);
        $this->output->set_content_type('json');
        $this->output->set_output(json_encode($data ? array('data' => $data) : array()));
    }

    public function update($order_id) {
        $order_info = $this->m_sales_order->get(FALSE, array('s_order.id' => $order_id));
        $this->load->model(array('inventory/m_product', 'sales/m_customer'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('manage-sales-orders.js', 'price-format.js', 'numeral.js', 'jquery-ui.min.js'));
        $this->load->helper('customer');

        $customers = $this->m_customer->all(['status' => 'a']);
        array_walk($customers, function(&$var){
            $var['name'] = "[{$var['customer_code']}] {$var['company_name']}";
        });
        $this->viewpage_settings['customers'] = dropdown_format($customers, 'id', 'name', '');


        /* get add ons */
        $this->viewpage_settings['products'] = $this->m_customer->get_customer_products($order_info[0]['fk_sales_customer_id']);
        
        // get agents list
        $this->viewpage_settings['agents'] = $this->m_agent->all(['status' => 'a']);

        $this->viewpage_settings['url'] = base_url("sales/orders/update_order/{$order_id}");
        $this->viewpage_settings['form_title'] = 'Update sales order';
        if ($this->input->post()) {
            $saved = FALSE;
            $input = $this->_validate();
            if ($input['error_flag']) {
                $this->viewpage_settings['defaults'] = $this->input->post();
                $this->viewpage_settings['defaults']['fk_sales_customer_id'] = $order_info[0]['fk_sales_customer_id'];
                $this->viewpage_settings['defaults']['customer'] = $order_info[0]['customer'];
                $this->viewpage_settings['defaults']['details'] = $order_info[0]['details'];
                $this->viewpage_settings['defaults']['total_amount'] = $order_info[0]['total_amount'];
                $this->viewpage_settings['defaults']['status'] = $order_info[0]['status']; //reset status
                $this->viewpage_settings['validation_errors'] = $input['message'];
            } else {
                $input['data']['status'] = $input['data']['status'] == M_Status::STATUS_DEFAULT ? $order_info[0]['status'] : $input['data']['status'];
                $saved = $this->m_sales_order->update($order_id, $input['data']);
            }
            if ($saved) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->update_success(self::SUBJECT));
                redirect('sales/orders');
            }
        } else {
            $this->setTabTitle("Sales - Update S.O. # {$order_id}");
            $this->viewpage_settings['defaults'] = $order_info[0];
        }
        $this->set_content('sales/manage-order', $this->viewpage_settings);
        $this->generate_page();
    }

    public function add() {
        $this->load->model(array('inventory/m_product', 'inventory/m_medications'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('manage-sales-orders.js', 'price-format.js', 'numeral.js', 'jquery-ui.min.js'));
        $this->load->helper('view');

        $customers = $this->m_customer->all(['status' => 'a']);
        array_walk($customers, function(&$var){
            $var['name'] = "[{$var['customer_code']}] {$var['company_name']}";
        });
        $this->viewpage_settings['customers'] = dropdown_format($customers, 'id', 'name', '');

        // get agents list
        $this->viewpage_settings['agents'] = $this->m_agent->all(['status' => 'a']);
        
        $this->viewpage_settings['url'] = base_url('sales/orders/store');
        $this->viewpage_settings['form_title'] = 'Add new sales order';
        if ($this->input->post()) {
            $saved = FALSE;
            $input = $this->_validate();
            if ($input['error_flag']) {
                $this->viewpage_settings['defaults'] = $this->input->post();
                $this->viewpage_settings['status'] = M_Status::STATUS_DEFAULT; //reset status
            } else {
                $saved = $this->m_sales_order->add($input['data']);
            }
            if ($saved) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT));
                redirect('sales/orders');
            }
        }
        $this->set_content('sales/manage-order', $this->viewpage_settings);
        $this->generate_page();
    }
    
   

    private function _validate() {
        $this->form_validation->set_rules('fk_sales_customer_id', 'Customer', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('status', 'Status', 'callback__validate_status');
        $this->form_validation->set_rules('fk_sales_agent_id', 'sales agent', 'required|callback__validate_sales_agent');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $data['details'] = $this->_format_details();
            return $this->response(FALSE, '', $data);
        } else {
            return $this->response(TRUE, validation_errors('<li>', '</li>'));
        }
    }
    
    private function _format_details() {
        $details = $this->input->post('details');
        $formatted_details = array();
        for ($x = 0; $x < count($details['fk_inventory_product_id']); $x++) {
            if ($details['fk_inventory_product_id'][$x]) {
                $unit_price = str_replace(",", "", $details['unit_price'][$x]);
                $discount = str_replace(",", "", $details['discount'][$x]);
                $quantity = $details['product_quantity'][$x];
                $amount = ($unit_price * $quantity) - $discount;

                $formatted_details[$x] = array(
                    'fk_inventory_product_id' => $details['fk_inventory_product_id'][$x],
                    'product_quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'discount' => $discount,
                    'total_units' => $details['total_units'][$x],
                );
            }
            if ($details['fk_inventory_product_id'][$x] && isset($details['id'][$x])) {
                $formatted_details[$x]['id'] = $details['id'][$x];
            }
        }
        return $formatted_details;
    }

    /////////////------------

    function update_order($id)
    {
        $this->load->model('sales/sales_order_model', 'order');
        $this->_perform_validation();
        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
        $data = $this->_format_data();
        $success = $this->order->update($id, $data['sales_order'], $data['order_line']);
        if($success){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Unable to perform action due to an unknown error.')->to_JSON();
    }

    function store()
    {
        $this->load->model('sales/sales_order_model', 'order');
        $this->_perform_validation();
        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
        $data = $this->_format_data();
        $success = $this->order->create($data['sales_order'], $data['order_line']);
        if($success){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Unable to perform action due to an unknown error.')->to_JSON();
    }

    
    function _perform_validation()
    {
        $this->form_validation->set_rules('fk_sales_customer_id', 'customer', 'required|callback__validate_customer');
        $this->form_validation->set_rules('date', 'Date', 'required|callback__validate_date');
        $this->form_validation->set_rules('status', 'Status', 'callback__validate_status');
        $this->form_validation->set_rules('fk_sales_agent_id', 'sales agent', 'required|callback__validate_sales_agent');
        if(!$this->form_validation->run()){
            $this->validation_errors += array_values($this->form_validation->error_array());
        }
        $details = $this->input->post('details');
        if(!isset($details['fk_inventory_product_id']) 
            || !is_array($details['fk_inventory_product_id']) 
            || empty($details['fk_inventory_product_id'])
            || !$this->m_product->is_valid($details['fk_inventory_product_id'])){
            $this->validation_errors[] = 'Please select at least one product for the order.';
            return;
        }
        foreach($details['fk_inventory_product_id'] AS $key => $value){
            $line = $key + 1;
            if(!isset($details['product_quantity'][$key]) || !is_numeric($details['product_quantity'][$key])){
                $this->validation_errors[] = "Please provide an order quantity for line # {$line}";
            }
            if(!isset($details['total_units'][$key]) || ($details['total_units'][$key] && !is_numeric($details['total_units'][$key]))){
                $this->validation_errors[] = "Heads / pieces count for line # {$line} must be numeric";
            }
            if(!isset($details['unit_price'][$key]) || !is_numeric(str_replace(',', '', $details['unit_price'][$key]))){
                $this->validation_errors[] = "Unit price for line # {$line} must be numeric";
            }
            if(!isset($details['discount'][$key]) || ($details['discount'][$key] && !is_numeric(str_replace(',', '', $details['discount'][$key])))){
                $this->validation_errors[] = "Discount for line # {$line} must be numeric";
            }
        }
    }

    function _format_data()
    {
        $sales_order = elements(['po_number', 'date', 'remarks', 'fk_sales_agent_id', 'fk_sales_customer_id'], $this->input->post());
        if(can_set_status()){
            $sales_order['status'] = $this->input->post('is_approved') ? M_Status::STATUS_APPROVED : M_Status::STATUS_DEFAULT;
        }
        $order_line = [];
        $details = $this->input->post('details');
        foreach($details['fk_inventory_product_id'] AS $key => $value){
            $temp = [
                'fk_inventory_product_id' => $value,
                'product_quantity' => $details['product_quantity'][$key],
                'total_units' => $details['total_units'][$key] ?: 0,
                'unit_price' => str_replace(',', '', $details['unit_price'][$key]),
                'discount' => str_replace(',', '', $details['discount'][$key]) ?: 0,
            ];
            if(isset($details['id'][$key])){
                $temp['id'] = $details['id'][$key];
            }
            $order_line[] = $temp;
        }
        return compact(['sales_order', 'order_line']);
    }


    function _validate_customer($customer)
    {
        $this->form_validation->set_message('_validate_customer', 'Please select a valid %s');
        return $this->m_customer->exists($customer, TRUE);
    }
    function _validate_sales_agent($sales_agent)
    {
        $this->form_validation->set_message('_validate_sales_agent', 'Please select a valid %s');
        return $this->m_agent->exists($sales_agent, TRUE);
    }

    function _validate_status($status) 
    {
         $this->form_validation->set_rules('_validate_status', sprintf('You do not have the privilege to approve a ', self::SUBJECT));
         return can_set_status();
    }

    function _validate_date($date)
    {
        $this->load->helper('pmdate');
        $this->form_validation->set_message('validate_date', 'Date must be in format YYYY-MM-DD');
        return is_valid_date($date, 'Y-m-d');
    }



    /* =====================
      NEW FUNCTIONS 01-14-15
      ===================== */

    public function create() {
        if ($this->input->post()) {
            
        } else {
            $this->add_javascript(array('plugins/json2html.js', 'plugins/jquery.json2html.js', 'sales-order/view-edit.js'));
            $viewpage_extras['form_title'] = 'Create new sales order';
            $this->set_content('sales/manage-order-new', $viewpage_extras);
            $this->generate_page();
        }
    }

    public function a_delete() {
        $this->output->set_content_type('json');
        if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN) {
            $id = $this->input->post('pk');
            $this->output->set_output(json_encode($this->m_sales_order->delete($id) ? $this->response(FALSE, "Successfully delete S.O. # {$id}") : $this->response(TRUE, "Error on S.O. deletion.")));
        }
        return FALSE;
    }

}

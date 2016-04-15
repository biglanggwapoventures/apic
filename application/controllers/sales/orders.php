<?php

class Orders extends PM_Controller {

    const TITLE = 'Sales';
    const SUBTITLE = 'Orders';
    const SUBJECT = 'sales order';

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
        $this->add_javascript(array());
        $this->load->model(array('sales/m_sales_order', 'sales/m_customer', 'inventory/m_product'));
        $this->viewpage_settings['defaults'] = array(
            'fk_sales_customer_id' => '',
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
        $this->load->model(array('inventory/m_product', 'sales/m_agent', 'sales/m_customer'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('manage-sales-orders.js', 'price-format.js', 'numeral.js', 'jquery-ui.min.js'));
        $this->load->helper('customer');
        /* get add ons */
        $this->viewpage_settings['products'] = $this->m_customer->get_customer_products($order_info[0]['fk_sales_customer_id']);
        $this->viewpage_settings['agents'] = [];
        $this->viewpage_settings['url'] = base_url("sales/orders/update/{$order_id}");
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
        $this->load->model(array('inventory/m_product', 'sales/m_agent', 'inventory/m_medications'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript(array('manage-sales-orders.js', 'price-format.js', 'numeral.js', 'jquery-ui.min.js'));
        $this->load->helper('view');

        $customers = $this->m_customer->all(['status' => 'a']);
        array_walk($customers, function(&$var){
            $var['name'] = "[{$var['customer_code']}] {$var['company_name']}";
        });
        $this->viewpage_settings['customers'] = dropdown_format($customers, 'id', 'name', '');
        
        $this->viewpage_settings['url'] = base_url('sales/orders/add');
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

    function _validate_status($status) {
        if ($status == M_Status::STATUS_APPROVED && $this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
            $this->form_validation->set_rules('_validate_status', sprintf('You do not have the privilege to approve a ', self::SUBJECT));
            return FALSE;
        }
        return TRUE;
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

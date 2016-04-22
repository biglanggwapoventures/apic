<?php

class Orders extends PM_Controller_v2 {

    const SUBJECT = 'purchase order';

    private $viewpage_settings = array();
    private $_fields = array(
        array(
            'name' => 'fk_maintainable_supplier_id',
            'label' => 'Supplier Name',
            'rules' => 'required|callback__is_valid_supplier'
        ),
        array(
            'name' => 'date',
            'label' => 'Date',
            'rules' => 'required'
        ),
        array(
            'name' => 'details',
            'label' => 'Purchase Order Details',
            'rules' => 'callback__details_check'
        ),
        array(
            'name' => 'force_close',    
            'label' => 'Status',
            'rules' => 'callback__status_check'
        ),
        array(
            'name' => 'remarks',
            'label' => 'Remarks',
            'rules' => 'callback__remarks_check'
        )
    );

    function __construct() {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Orders');
        $this->load->helper(array('view', 'array'));
        $this->load->model('purchases/m_purchase_order');
        $this->viewpage_settings['defaults'] = array(
            'fk_maintainable_supplier_id' => '',
            'date' => '',
            'delivery_instructions' => '',
            'remarks' => '',
            'force_close' => 0  ,
            'total_amount' => '0.00',
            'amount_paid' => '0.00',
            'type' => 'lcl',
            'status' => M_Status::STATUS_DEFAULT,
            'details' => array(
                array(
                    'fk_inventory_product_id' => '',
                    'pieces' => '0.00',
                    'received_pieces' => '0.00',
                    'quantity' => '0.00',
                    'quantity_received' => '0.00',
                    'unit_description' => '',
                    'unit_price' => '',
                    'amount' => '0.00'
                )
            )
        );
    }

    function _segment_url($segment = '') {
        return base_url("purchases/orders/{$segment}");
    }

    public function ajax_master_list(){
        if($this->input->is_ajax_request()){
            $offset = $this->input->get('page');
            $page = $offset ? $offset : 1;
            $data = $this->m_purchase_order->all($page, $this->search_params());
            $this->generate_response($data ? ['data' => $data] : [])->to_JSON();
        }
    }

    public function search_params()
    {
        $this->load->model('maintainable/m_supplier', 'supplier');
        $this->load->helper('pmdate');
        $query = [];
        $params = elements(['id', 'start_date', 'end_date', 'supplier'], $this->input->get());
        if($params['id'] && is_numeric($params['id']))
        {
            $query['po.id'] = $params['id'];
        }
        if($params['start_date'] && is_valid_date($params['start_date'], 'm/d/Y'))
        {
            $query['po.date >='] = date('Y-m-d', strtotime($params['start_date']));
        }
        if($params['end_date'] && is_valid_date($params['end_date'], 'm/d/Y'))
        {
            $query['po.date <='] = date('Y-m-d', strtotime($params['end_date']));
        }
        if($params['supplier'] && $this->supplier->is_valid($params['supplier']))
        {
            $query['po.fk_maintainable_supplier_id'] = $params['supplier'];
        }
        return empty($query) ? FALSE : $query;
    }

    function index() {
        $this->load->model('maintainable/m_supplier', 'supplier');
        $this->setTabTitle('Purchases Orders');
        $this->add_javascript(['plugins/sticky-thead.js', 'numeral.js' ,'printer/printer.js','purchases-orders/master-list.js']);
        $this->viewpage_settings['suppliers'] = ['' => ''] + array_column($this->supplier->all(), 'name', 'id');
        $this->set_content('purchases/orders', $this->viewpage_settings);
        $this->generate_page();
    }

    public function manage() {
        $this->add_javascript(array('numeral.js', 'price-format.js','printer/printer.js', 'purchases-orders/manage.js'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->load->model(array('maintainable/m_supplier', 'inventory/m_product', 'accounting/m_bank_account'));
        $this->viewpage_settings['accounts'] = $this->m_bank_account->get();
        array_walk($this->viewpage_settings['accounts'], function(&$var){$var['bank_name'] = "{$var['bank_name']} [{$var['bank_branch']}]";});
        if ($this->input->get('do') === 'new-purchase-order') {
            $this->setTabTitle('Create new purchase order');
            $this->viewpage_settings['form_title'] = sprintf('Add new %s', self::SUBJECT);
            $this->viewpage_settings['action'] = $this->_segment_url('a_do_action/add');
            $this->viewpage_settings['is_locked'] = FALSE;
            $this->viewpage_settings['mode'] = 'new';
        } elseif ($this->input->get('do') === 'update-purchase-order' && $this->m_purchase_order->is_valid($this->input->get('id'))) {
            $id = $this->input->get('id');

            if($this->m_purchase_order->get_max_id()[0]['id'] == $id){
                $purchase_next_id = $this->m_purchase_order->get_min_id();
                if($purchase_next_id[0]['id'] == $id){
                    $purchase_next_id = array();
                }
            }else{
                $purchase_next_id = $this->m_purchase_order->get_next_row_id($id, "next");
            }

            if($this->m_purchase_order->get_min_id()[0]['id'] == $id){
                $purchase_prev_id = $this->m_purchase_order->get_max_id();
                if($purchase_prev_id[0]['id'] == $id){
                    $purchase_prev_id = array();
                }
            }else{
                $purchase_prev_id = $this->m_purchase_order->get_next_row_id($id, "prev");
            }

            if(!empty($purchase_next_id)){
                $_id = $purchase_next_id[0]['id'];
                $this->viewpage_settings['purchase_next_info'] = base_url("purchases/orders/manage?do=update-purchase-order&id={$_id}");
                $this->viewpage_settings['purchase_next_id'] = $_id;
            }else{
                $this->viewpage_settings['purchase_next_info'] = 0;
            }

            if(!empty($purchase_prev_id)){
                $_id = $purchase_prev_id[0]['id'];
                $this->viewpage_settings['purchase_prev_info'] = base_url("purchases/orders/manage?do=update-purchase-order&id={$_id}");
                $this->viewpage_settings['purchase_prev_id'] = $_id;
            }else{
                $this->viewpage_settings['purchase_prev_info'] = 0;
            }

            $this->setTabTitle("Update purchases order # {$id}");
            $order_info = $this->m_purchase_order->get(TRUE, FALSE, array('p_order.id' => $id));
            $this->viewpage_settings['defaults'] = $order_info[0];
            if($this->viewpage_settings['defaults']['type'] === 'imt')
            {
                $this->viewpage_settings['other_fees'] = $this->m_purchase_order->get_other_fees($id);
                $this->viewpage_settings['issued_checks'] = $this->m_purchase_order->get_issued_checks($id);
            }
            $this->viewpage_settings['form_title'] = sprintf('Update %s # %d', self::SUBJECT, $id);
            $this->viewpage_settings['action'] = $this->_segment_url("a_do_action/update/{$id}");
            $this->viewpage_settings['is_locked'] = $this->m_purchase_order->is_locked($id);
            $this->viewpage_settings['products'] = $this->m_supplier->get_assigned_supplies($order_info[0]['fk_maintainable_supplier_id']);
            
        } else {
            show_404();
        }
        $this->viewpage_settings['suppliers'] = dropdown_format($this->m_supplier->all(), 'id', 'name');
        $this->set_content('purchases/manage-order', $this->viewpage_settings);
        $this->generate_page();
    }

    function _validate_input() {
        foreach ($this->_fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if ($this->form_validation->run()) {
            $order['general'] = elements(array_column($this->_fields, 'name'), $this->input->post(), '');
            $order['general']['type'] = 'lcl'; // set type to be always local
            $order['general']['status'] = M_Status::STATUS_APPROVED; // set status to be always approved
            $temp = $order['general']['details'];
            unset($order['general']['details']);
            
            $count = $order['general']['type'] === 'lcl' ? count($temp['fk_inventory_product_id']) : 1;
            
            for ($x = 0; $x < $count; $x++) {
                $detail = array(
                    'fk_inventory_product_id' => $temp['fk_inventory_product_id'][$x],
                    'quantity' => $temp['quantity'][$x],
                    'pieces' => $temp['pieces'][$x],
                    'unit_price' => str_replace(",", "", $temp['unit_price'][$x])
                );
                if (isset($temp['id'][$x])) {
                    $detail['id'] = $temp['id'][$x];
                }
                if($order['general']['type'] === 'imt')
                {
                    $detail['xrate'] = $temp['xrate'][$x];
                    $detail['amount'] = str_replace(',', '', $temp['amount'][$x]);
                }
                else
                {
                    $detail['xrate'] = NULL;
                    $detail['amount'] = NULL;
                }
                $order['details'][] = $detail;
            }
            $order['others'] = FALSE;
            if($order['general']['type'] === 'imt')
            {
                $fees = $this->input->post('others');
                $checks = $this->input->post('check');
                if(!empty($fees))
                {
                    foreach($fees['desc'] AS $index => $val)
                    {
                        $order['others']['fees'][] = [
                            'description' => $val,
                            'amount' => str_replace(',', '', $fees['amount'][$index])
                        ];
                    }
                }
                if(!empty($checks))
                {
                    foreach($checks['account'] AS $index => $val)
                    {
                        $order['others']['checks'][] = [
                            'bank_account' => $val,
                            'check_number' => $checks['check_number'][$index],
                            'check_type' => $checks['check_type'][$index],
                            'check_date' => date('Y-m-d', strtotime($checks['check_date'][$index])),
                            'amount' => str_replace(',', '', $checks['amount'][$index]),
                            'remarks' => $checks['remarks'][$index],
                        ];
                    }
                }
            }
            return $this->response(FALSE, '', $order);
        } else {
            return $this->response(TRUE, $this->form_validation->error_array());
        }
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
                $input = $this->_validate_input();
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
    }

    /* CRUD REMAPS */

    function _add($validated_data) {
        $id = $this->m_purchase_order->add($validated_data['general'], $validated_data['details'], $validated_data['others']);
        if ($id) {
            $response = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT, "P.O. # {$id}"), array('redirect' => $this->_segment_url()));
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($response));
            return $response;
        }
        return $this->response(TRUE, $this->m_message->add_eror(self::SUBJECT));
    }

    function _update($order_id, $validated_data) {
        if (!$this->m_account->is_admin() && $this->m_purchase_order->is_locked($order_id)) {
            return $this->response(TRUE, sprintf('You cannot update a locked %s', self::SUBJECT));
        }
        $updated = $this->m_purchase_order->update($order_id, $validated_data['general'], $validated_data['details'], $validated_data['others']);
        if ($updated) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, $this->m_message->update_success(self::SUBJECT, "P.O. # {$order_id}"))));
            return $this->response(FALSE, '', array('redirect' => $this->_segment_url()));
        }
        return $this->response(TRUE, $this->m_message->update_error(self::SUBJECT));
    }

    function _delete($order_id) {
        if (!$this->m_account->is_admin()) {
            return $this->response(TRUE, sprintf('You do not have the privilege to delete a %s', self::SUBJECT));
        }
        $deleted = $this->m_purchase_order->delete($order_id);
        if ($deleted) {
            return $this->response(FALSE, '');
        }
        return $this->response(TRUE, $this->m_message->delete_error(self::SUBJECT));
    }

    /* END CRUD REMAPS */

    /* SPECIAL FUNCTIONS */

    public function change_lock_state() {
        $this->output->set_content_type('json');
        if ((int) $this->session->userdata('type_id') !== (int) M_Account::TYPE_ADMIN) {
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
            return;
        }
        if ($this->m_purchase_order->change_lock_state($this->input->post('order_id'), $do_lock)) {
            $this->output->set_output(json_encode($this->response(FALSE, '')));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'An unknown error has occured.')));
        }
    }

    public function a_get_unreceived() {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        if (!is_numeric($this->input->get('supplier_id'))) {
            $this->output->set_output(json_encode($this->response(TRUE, 'Please select a valid supplier id.')));
            return;
        }
        $data = $this->m_purchase_order->get_unreceived($this->input->get('supplier_id'));
        if ($data) {
            $this->output->set_output(json_encode($this->response(FALSE, $this->m_message->data_fetch_success(self::SUBJECT), $data)));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, $this->m_message->data_fetch_empty())));
    }

    public function a_get() {
        $this->output->set_status_header('200')->set_content_type('json');
        $filter_param = [
            'p_order.id' => $this->input->get('order_id')
        ];
        $data = $this->m_purchase_order->get(TRUE, FALSE, $filter_param);
        if ($data) {
            $this->output->set_output(json_encode($this->response(FALSE, $this->m_message->data_fetch_success(self::SUBJECT), $data[0])));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, $this->m_message->data_fetch_empty())));
    }

    public function ajax_for_advanced_disbursement() {
        $this->output->set_content_type('json');
        $data = $this->m_purchase_order->for_advanced_rr($this->input->get('supplier_id'));
        if ($data) {
            $this->output->set_output(json_encode($this->response(FALSE, $this->m_message->data_fetch_success(self::SUBJECT), $data)));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, $this->m_message->data_fetch_empty())));
    }

    public function a_get_undisbursed() {
        $this->output->set_status_header('200')->set_content_type('json');
        $data = $this->m_purchase_order->get_undisbursed($this->input->get('supplier_id'));
        if ($data) {
            $this->output->set_output(json_encode($this->response(FALSE, $this->m_message->data_fetch_success(self::SUBJECT), $data)));
            return;
        }
        $this->output->set_output(json_encode($this->response(TRUE, $this->m_message->data_fetch_empty())));
    }

    /* FORM VALIDATION CALLBACKS */

    function _is_valid_supplier($supplier) {
        $this->load->model('maintainable/m_supplier');
        if ($this->m_supplier->is_unique('id', $supplier)) {
            $this->form_validation->set_message('_is_valid_supplier', 'Please select a valid supplier.');
            return FALSE;
        }
        return TRUE;
    }

    function _status_check($status = FALSE) {
        if ((int) $status === (int) M_Status::STATUS_APPROVED) {
            if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN) {
                return TRUE;
            } else {
                $this->form_validation->set_message('_status_check', 'You are not authorized to close purchase orders');
                return FALSE;
            }
        } 
        return TRUE;
    }

    function _remarks_check()
    {
        return TRUE;
    }


    function _details_check($details) {
        $this->load->model('inventory/m_product');
        $products = isset($details['fk_inventory_product_id']) ? $details['fk_inventory_product_id'] : array();
        $product_quantity = isset($details['quantity']) ? $details['quantity'] : array();
        $unit_price = isset($details['unit_price']) ? $details['unit_price'] : array();
        $ids = isset($details['unit_price']) ? $details['unit_price'] : array();
        if (!empty($products) && !$this->m_product->is_valid($products)) {
            $this->form_validation->set_message('_details_check', 'Please select valid products.');
            return FALSE;
        }
        foreach ($products as $index => $value) {
            if (!isset($product_quantity[$index]) || !is_numeric($product_quantity[$index])) {
                $this->form_validation->set_message('_details_check', 'Please fill up everything in the details field.');
                return FALSE;
            }
            if (!isset($unit_price[$index]) || !is_numeric(str_replace(",", "", $unit_price[$index]))) {
                $this->form_validation->set_message('_details_check', 'Please fill up everything in the details field.');
                return FALSE;
            }
            if (!isset($ids[$index])) {
                $this->form_validation->set_message('_details_check', 'Error has occured. Please refresh the page.');
                return FALSE;
            }
        }
        return TRUE;
    }

    public function validate_type($type)
    {
        $this->form_validation->set_message('validate_type', 'Please choose a valid %s');
        return in_array($type, ['lcl', 'imt']);
    }

    /* END OF FORM VALIDATION CALLBACKS */

    //print
    public function do_print() {
        $id = $this->input->get('id');
        if (is_numeric($id) === FALSE || $this->m_purchase_order->is_approved($id) === FALSE) {
            echo 'Please make sure the purchase order is approved before printing.';
            return;
        }
        $details = $this->m_purchase_order->get(TRUE, FALSE, array('p_order.id' => $id));
        $data['details'] = $details[0];
        $this->load->view('printables/purchases/order', $data);
    }

    public function ajax_get_assigned_supplies($supplier_id) {
        $this->load->model('maintainable/m_supplier', 'supplier');
        $data = $this->supplier->get_assigned_supplies($supplier_id);
        $data_attribute = ['attr' => ['name' => 'unit', 'value' => 'unit_description'], 'text' => 'description'];
        $this->output->set_content_type('html');
        $this->output->set_output(arr_group_dropdown('', $data, 'p_id', $data_attribute, FALSE, FALSE, 'class="form-control product-listing"'));
        
    }

    public function ajax_print_voucher($issued_check_id)
    {
        $this->load->view('printables/purchases/check-voucher', [
            'data' => $this->m_purchase_order->get_issued_checks_from($issued_check_id)
        ]);
    }

    public function ajax_print_check()
    {
        $payment_id = $this->input->get('payment_id');
        $check_details = $this->m_purchase_order->get_check($payment_id);
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
            'pay_to' => $check_details['payee'],
            'amount' => $check_details['amount']
        ]);
    }

}

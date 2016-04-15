<?php

class Deliveries extends PM_Controller
{

    const TITLE = 'Sales';
    const SUBTITLE = 'Packing List';
    const SUBJECT = 'packing list';

    private $viewpage_settings = array();

    public function __construct()
    {
        parent::__construct();
        if(!has_access('sales')){
            show_error('Authorization error', 401);
        }
        $this->load->helper('view');
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->load->model(array('sales/m_delivery', 'sales/m_customer', 'inventory/m_product'));
        $this->viewpage_settings['defaults'] = array(
            'fk_sales_order_id' => '',
            'date' => '',
            'cr_number' => '',
            'ptn_number' => '',
            'invoice_number' => '',
            'remarks' => '',
            'fk_sales_trucking_id' => '',
            'status' => M_Status::STATUS_DEFAULT,
            'total_amount' => 0.00,
        );
    }

    public function index()
    {
        $this->load->helper('customer');
        $this->setTabTitle('Sales - Packing List');
        $this->add_javascript(array('plugins/json2html.js', 'plugins/jquery.json2html.js', 'plugins/sticky-thead.js', 'printer/printer.js', 'sales-deliveries/master-list.js'));
        $this->set_content('sales/packing-list/master-list', $this->viewpage_settings);
        $this->generate_page();
    }

    public function a_master_list()
    {
        $this->load->helper('array');
        $parameters = elements(array('pl_no', 'so_no', 'po_no', 'customer', 'date', 'page'), $this->input->get(), 0);
        $data = $this->m_delivery->master_list($parameters);
        $this->output->set_content_type('json');
        $this->output->set_output(json_encode($data ? array('data' => $data) : array()));
    }

    public function add()
    {
        $this->add_javascript(array('price-format.js', 'numeral.js', 'jquery-ui.min.js', 'jquery.form.min.js', 'sales-deliveries/manage.js'));
        $this->load->model(array('inventory/m_product', 'sales/m_trucking'));
        $this->load->helper('view');
        $this->viewpage_settings['products'] = $this->m_product->get(FALSE, array(M_Product::PRODUCT_CLASS => M_Product::CLASS_FINISHED));
        $this->viewpage_settings['truckers'] = $this->m_trucking->all(['status' => 'a']);

        $customers = $this->m_customer->all(['status' => 'a']);
        array_walk($customers, function(&$var){
            $var['name'] = "[{$var['customer_code']}] {$var['company_name']}";
        });
        $this->viewpage_settings['customers'] = dropdown_format($customers, 'id', 'name', '');

        $this->viewpage_settings['url'] = base_url('sales/deliveries/a_add');
        $this->viewpage_settings['form_title'] = 'Add new packing list';
        $this->set_content('sales/manage-delivery', $this->viewpage_settings);
        $this->generate_page();
    }

    public function update($delivery_id)
    {
        $deliveryList = $this->m_delivery->get(TRUE, FALSE, array('delivery.id' => $delivery_id));
        if ($deliveryList){
            $this->add_javascript(array('printer/printer.js', 'price-format.js', 'numeral.js', 'jquery-ui.min.js', 'jquery.form.min.js', 'sales-deliveries/manage.js'));
            $this->load->model(array('inventory/m_product', 'sales/m_trucking', 'accounting/m_bank_account'));
            $this->load->helper('view');
            $this->viewpage_settings['truckers'] = $this->m_trucking->all();
            $this->viewpage_settings['defaults'] = $deliveryList[0];
            $this->viewpage_settings['url'] = base_url("sales/deliveries/a_update/{$delivery_id}");
            $this->viewpage_settings['form_title'] = "Update packing list #{$delivery_id}";
            $this->viewpage_settings['credit_memo'] = $this->m_delivery->credit_memo_summary($delivery_id);
            $this->set_content('sales/manage-delivery', $this->viewpage_settings);
            $this->generate_page();
            return;
        }
        show_404();
    }

    public function _check_item_availability($filled_orders, $exclude = []){
        $unavailable = [];
        $this->load->model('sales/m_sales_order', 'sales_order');
        $ordered_products = $this->sales_order->get_ordered_products(FALSE, array_column($filled_orders, 'fk_sales_order_detail_id'));
        $product_ids = array_values($ordered_products);
        $product_details = $this->m_product->identify($product_ids);
        $stocks = $this->m_product->get_stocks(array_values($product_ids));
        foreach($filled_orders AS &$item){
            $needed = 0;

            $product_id = $ordered_products[$item['fk_sales_order_detail_id']];
            $product_description = "{$product_details[$product_id]['description']} [{$product_details[$product_id]['code']}]";
            $product_unit = $product_details[$product_id]['unit_description'];

            $stock = isset($stocks[$product_id]) ? $stocks[$product_id]: 0;
            if(isset($exclude[$item['fk_sales_order_detail_id']])){
                $stock += $exclude[$item['fk_sales_order_detail_id']]['this_delivery'];
            }
            if($item['this_delivery'] > $stock){
                $lacking = $item['this_delivery'] - $stock;
                $message = "Lacking {$lacking} {$product_unit} for: {$product_description}";
                $unavailable[] = $message;
            }
        }
        return $unavailable;
    }

    public function a_add()
    {
        $input = $this->_validate();
        if ($input['error_flag']){
            echo json_encode($this->response(TRUE, $input['message']));
            return;
        }
        // else if(!$input['error_flag'] && $input['data']['status'] == M_Status::STATUS_DELIVERED){
        //     $details = $input['data']['details'];
        //     $unavailable = $this->_check_item_availability($details);
        //     if(!empty($unavailable)){
        //         echo json_encode($this->response(TRUE, $unavailable));
        //         return;
        //     }
        // }
        $input['data']['status'] = M_Status::STATUS_DELIVERED;
        $input['data']['approved_by'] = user_id();
        $input['data']['created_by'] = user_id();
        $saved = $this->m_delivery->add($input['data']);
        if ($saved){
            /* ADD WITHOLDING TAX RECORD */
            $wht = $this->input->post('wht');
            if (is_valid_date($wht['date']))
            {
                $this->m_delivery->add_wht([
                    'date' => strtotime($wht['date']),
                    'amount' => str_replace(',', '', $wht['amount']),
                    'delivery_id' => $saved
                ]);
            }
            $response = json_encode($this->response(FALSE, $this->m_message->add_success(self::SUBJECT, "P.L. # {$saved}"), array('redirect' => base_url('sales/deliveries'))));
            $this->session->set_flashdata('FLASH_NOTIF', $response);
            echo $response;
        }else{
            echo json_encode($this->response(TRUE, $this->m_message->add_error(self::SUBJECT)));
        }
        return;
    }

    public function a_update($delivery_id)
    {
        $state = $this->m_delivery->get_status($delivery_id);
        if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN || !$state['is_locked']){
            $input = $this->_validate();
            if ($input['error_flag']){
                echo json_encode($this->response(TRUE, $input['message']));
                return;
            }
            // else{
            //     $details = $input['data']['details'];
            //     if($input['data']['status'] == M_Status::STATUS_DELIVERED && $state['status'] != M_Status::STATUS_DELIVERED){
            //         $unavailable = $this->_check_item_availability($details);
            //     }else if($input['data']['status'] == M_Status::STATUS_DELIVERED && $state['status'] == M_Status::STATUS_DELIVERED){
            //         $delivered_items = $this->m_delivery->get_delivered_items($delivery_id);
            //         $unavailable = $this->_check_item_availability($details, array_column($delivered_items, NULL, 'fk_sales_order_detail_id'));
            //     }
            //     if(!empty($unavailable)){
            //         echo json_encode($this->response(TRUE, $unavailable));
            //         return;
            //     }
            // }
            // if ($input['data']['status'] == M_Status::STATUS_DELIVERED){
            //     $input['data']['is_locked'] = 1;
            //     $input['data']['approved_by'] = $this->session->userdata('user_id');
            // }else{
            //     $input['data']['approved_by'] = NULL;
            // }
            unset($input['data']['customer-id']);
            $saved = $this->m_delivery->update($delivery_id, $input['data']);
            if ($saved){
                $this->load->helper('pmdate');
                /* UPDATE WITHOLDING TAX RECORD */
                $wht = $this->input->post('wht');
                if (is_valid_date($wht['date'])){
                    $this->m_delivery->update_wht([
                        'date' => strtotime($wht['date']),
                        'amount' => str_replace(',', '', $wht['amount']),
                        'delivery_id' => $delivery_id
                    ]);
                }else{
                    $this->m_delivery->remove_wht($delivery_id);
                }
                echo json_encode($this->response(FALSE, $this->m_message->update_success(self::SUBJECT)));
            }else{
                echo json_encode($this->response(TRUE, $this->m_message->update_error(self::SUBJECT)));
            }
        }else{
            echo json_encode($this->response(TRUE, array('This packing list is either locked or you do not have any previlege for the desired action. Please contact administrator.')));
        }
    }

    function a_delete()
    {
        $this->form_validation->set_rules('pk', 'ID', 'required');
        if ($this->form_validation->run())
        {
            $deleted = $this->m_delivery->delete($this->input->post('pk'));
            if ($deleted)
            {
                echo json_encode($this->response(FALSE, $this->m_message->delete_success(self::SUBJECT)));
            }
            else
            {
                echo json_encode($this->response(TRUE, $this->m_message->delete_error(self::SUBJECT), array()));
            }
        }
        else
        {
            echo json_encode($this->response(TRUE, $this->m_message->no_primary_key_error(self::SUBJECT), array()));
        }
        exit();
    }

    public function a_get_uncountered()
    {
        $customer_id = $this->input->get('customer_id');
        $data = $this->m_delivery->get_uncountered($customer_id);
        if ($data)
        {
            echo json_encode($this->response(FALSE, 'Successful data fetching', $data));
        }
        else
        {
            echo json_encode($this->response(TRUE, 'No results found'));
        }
        exit();
    }

    public function a_get_unpaid()
    {
        if (!$this->input->is_ajax_request())
        {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $response = '';
        $this->output->set_status_header('200')->set_content_type('json');
        $customer_id = $this->input->get('customer_id');
        if (is_numeric($customer_id))
        {
            $subject = 'unpaid packing list';
            $data = $this->m_delivery->get_unpaid($customer_id);
            if ($data)
            {
                $response = $this->response(FALSE, $this->m_message->data_fetch_success($subject), $data);
            }
            else
            {
                $response = $this->response(TRUE, $this->m_message->data_fetch_error($subject));
            }
        }
        else
        {
            $response = $this->response(TRUE, 'You need to provide a valid customer id!');
        }
        $this->output->set_output(json_encode($response));
        return;
    }

    private function _validate($mode = 'create')
    {
        $this->load->helper('array');
        $this->form_validation->set_rules('fk_sales_order_id', 'S.O. No.', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('fk_sales_trucking_id', 'Delivered by', 'required');
        if ($this->form_validation->run())
        {
            $this->load->helper('pmdate');
            $data = elements(array('fk_sales_order_id', 'date', 'fk_sales_trucking_id', 'invoice_number', 'remarks'), $this->input->post(), '');
            $data['details'] = $this->_format_details();
            unset($data['details']['total_amount']);
            return $this->response(FALSE, '', $data);
        }
        else
        {

            return $this->response(TRUE, array_values($this->form_validation->error_array()));
        }
    }
    private function _format_details()
    {
        $this->load->model('sales/m_sales_order');
        $details = $this->input->post('details');
        $formatted_details = array();
        for ($x = 0; $x < count($details['fk_sales_order_detail_id']); $x++)
        {
            if ($details['fk_sales_order_detail_id'][$x])
            {
                $delivered_quantity = abs($details['this_delivery'][$x]);
                $formatted_details[$x] = array(
                    'fk_sales_order_detail_id' => $details['fk_sales_order_detail_id'][$x],
                    'this_delivery' => $delivered_quantity,
                    'delivered_units' => abs($details['delivered_units'][$x]),
                );
            }
            if (isset($details['id'][$x]))
            {
                $formatted_details[$x]['id'] = $details['id'][$x];
            }
        }
        return $formatted_details;
    }

    //print
    public function do_print()
    {
        $id = (int) $this->input->get('id');
        $data = $this->m_delivery->get(TRUE, FALSE, ['delivery.id' => $id]);
        if (!$data)
        { //invalid input
            show_404();
        }
        $packing_list = $data[0];
        if ($packing_list['is_printed'] == 1 && $this->session->userdata('type_id') != M_Account::TYPE_ADMIN)
        { //already printed
            echo "Sorry, this packing list has already been printed. Please contact administrator should you request for a reprinting.";
            return;
        }
        if ($packing_list['status'] != M_Status::STATUS_DELIVERED)
        {
            echo "This packing list is not yet approved. Hence, printing not necessary.";
            return;
        }
        $this->merge_addon($packing_list['details'], $this->m_delivery->get_addon_delivery($packing_list['id']));
        $this->m_delivery->mark_printed($packing_list['id']); //mark as printed
        $this->load->view('printables/sales/packing-list', array('details' => $packing_list));
    }

    function _format_addons()
    {
        $addons = $this->input->post('addons');
        if (!$addons)
        {
            return NULL;
        }
        $data = [];
        foreach ($addons['medication_order_item_id'] as $key => $value)
        {
            if ($value)
            {
                $temp = [
                    'medication_order_item_id' => (int) $value,
                    'this_delivery' => (int) $addons['this_delivery'][$key]
                ];
                if (isset($addons['id'][$key]))
                {
                    $temp['id'] = $addons['id'][$key];
                }
                $data[] = $temp;
            }
        }
        return $data ? $data : NULL;
    }

    protected function merge_addon(&$details, $addon)
    {
        foreach ($addon as $item)
        {
            $details['id'][] = 0;
            $details['this_delivery'][] = $item['this_delivery'];
            $details['unit_description'][] = 'pcs';
            $details['prod_descr'][] = $item['description'];
            $details['prod_code'][] = $item['product_code'];
            $details['unit_price'][] = $item['unit_price'];
            $details['discount'][] = 0;
        }
    }

    public function credit_memo($pl_id = FALSE)
    {
        if (!$pl_id || !$this->m_delivery->is_valid($pl_id))
        {
            show_404();
        }
        $this->add_css(['sales/credit-memo.css']);
        $this->add_javascript(['price-format.js', 'numeral.js', 'sales-cm/credit-memo.js']);
        $this->set_content_subtitle("Credit Memo");
        $this->set_content('sales/credit-memo', [
            'pl_id' => $pl_id,
            'credit_memo' => $this->m_delivery->credit_memo($pl_id)
        ]);
        $this->generate_page();
    }

    public function ajax_create_credit_memo($pl_id){
        $validator = $this->_validate_credit_memo($pl_id);
        if(empty($validator))
        {
            $cm = $this->_format_cm_data('create', $pl_id);
            $result = $this->m_delivery->create_credit_memo($cm);
            if($result)
            {
                $this->generate_response(FALSE)->to_JSON();
            }
        }
        else
        {
            $this->generate_response(TRUE, $validator)->to_JSON();
        }
    }

    public function ajax_update_credit_memo($pl_id)
    {
        if(!$this->m_delivery->has_credit_memo($pl_id))
        {
            show_404();
        }
        $validator = $this->_validate_credit_memo($pl_id, 'update');
        if(empty($validator)){
            $cm = $this->_format_cm_data('update', $pl_id);
            $result = $this->m_delivery->update_credit_memo($cm);
            if($result)
            {
                $this->generate_response(FALSE)->to_JSON();
            }
        }else{
            $this->generate_response(TRUE, $validator)->to_JSON();
        }
    }

    private function _validate_credit_memo($pl_id, $mode = 'create')
    {
        $error_array = [];
        $input = $this->input->post();
        $this->load->helper('pmdate');
        if(!is_valid_date($input['date'])){
            $error_array[] = 'Invalid date format. Must be YYYY-MM-DD';
        }
        if (array_key_exists('returns', $input))
        {
            $valid_items = $this->m_delivery->is_valid_delivery_line($input['returns']['item_delivery_id'], $pl_id);
            if ($valid_items === FALSE)
            {
                $error_array[] = 'Returned items does not match with the delivered items!';
            }
            if (array_filter($input['returns']['quantity'], 'is_numeric') !== $input['returns']['quantity'])
            {
                $error_array[] = 'Returned quantity should only contain numbers!';
            }
            if (array_filter($input['returns']['remarks'], 'trim') !== $input['returns']['remarks'])
            {
                $error_array[] = 'Please fill in all the remarks for the returned items!';
            }
        }
        if (array_key_exists('others', $input))
        {
            if (array_filter($input['others']['description'], 'trim') !== $input['others']['description'])
            {
                $error_array[] = 'Please fill in all the description for the other fees!';
            }
            $amount = array_filter($input['others']['amount'], function($var)
            {
                return is_numeric(str_replace(',', '', $var));
            });
            if (array_filter($input['others']['amount']) !== $amount)
            {
                $error_array[] = 'Amount for the other fees should only contain numbers!';
            }
        }
        if(!array_key_exists('others', $input) && !array_key_exists('returns', $input) && $mode==='create'){
            $error_array[] = 'Credit memo must contain returned items or other fees!';
        }
        return $error_array;
    }

    private function _format_cm_data($mode = 'create', $delivery_id)
    {
        $data = [];
        $input = $this->input->post();
        $data['cm'] = [
            'date' => $input['date'],
            'delivery_id' => $delivery_id
        ];
        if($mode === 'create')
        {
            $data['cm']['created_by'] = $this->session->userdata('user_id');
        }
        else
        {
            $data['cm']['last_updated_by'] = $this->session->userdata('user_id'); 
        }
        if (array_key_exists('returns', $input))
        {
            foreach ($input['returns']['item_delivery_id'] as $index => $values) {
                $data['returns'][] = [
                    'item_delivery_id' => $values,
                    'quantity' => $input['returns']['quantity'][$index],
                    'remarks' => $input['returns']['remarks'][$index]
                ];
            }
        }
        if (array_key_exists('others', $input))
        {
            foreach ($input['others']['description'] as $index => $values) {
                $data['others'][] = [
                    'description' => $values,
                    'amount' => str_replace(',', '', $input['others']['amount'][$index]),
                    'remarks' => $input['others']['remarks'][$index]
                ];
            }
        }
        return $data;
    }

    public function print_credit_memo($pl_id)
    {
        $details = $this->m_delivery->credit_memo($pl_id, TRUE);
        if(isset($details['id'])){
            foreach($details['returned'] AS $r)
            {
                if($r['quantity'] > 0)
                {
                    $details['details'][] = $r;
                }
            }
            foreach($details['other_fees'] AS $others)
            {  
                $details['details'][] = [
                    'quantity' => '-', 
                    'product_unit_description' => '-', 
                    'product_unit_discount' => 0, 
                    'product' => $others['description'], 
                    'remarks' => $others['remarks'],
                    'product_unit_price' => $others['amount']
                ];
            }
            $this->load->view('printables/sales/credit-memo', ['details' => $details]);
        }
    }

    public function print_gatepass($pl_id)
    {
        $this->load->model('sales/m_trucking');
        $pl = $this->m_delivery->get(TRUE, FALSE, array('delivery.id' => $pl_id));
        $trucking = $this->m_trucking->get($pl[0]['fk_sales_trucking_id']);
        $this->load->view('printables/sales/gatepass', ['details' => $pl[0], 'trucking' => $trucking[0]]);
    }
    
}

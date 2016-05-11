<?php

class Receiving extends PM_Controller_v2 {

    const SUBJECT = 'purchase receiving';

    private $viewpage_settings = array();
    private $_fields = array(
        array(
            'name' => 'fk_maintainable_supplier_id',
            'label' => 'Supplier Name',
            'rules' => 'required|callback__is_valid_supplier'
        ),
        array(
            'name' => 'fk_purchase_order_id',
            'label' => 'P.O. Number',
            'rules' => 'required|callback__is_valid_po'
        ),
        array(
            'name' => 'pr_number',
            'label' => '',
            'rules' => ''
        ),
        array(
            'name' => 'remarks',
            'label' => 'Remarks',
            'rules' => 'callback__remarks_check'
        ),
        array(
            'name' => 'date',
            'label' => 'Date',
            'rules' => 'required'
        ),
        array(
            'name' => 'details',
            'label' => 'Purchase Receiving Details',
            'rules' => 'callback__details_check'
        ),
        array(
            'name' => 'status',
            'label' => 'Status',
            'rules' => 'callback__status_check'
        )
    );

    function __construct() {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Receiving');
        $this->load->helper(array('view', 'array'));
        $this->load->model('purchases/m_purchase_receiving');
        $this->viewpage_settings['defaults'] = array(
            'fk_maintainable_supplier_id' => '',
            'fk_purchase_order_id' => '',
            'pr_number' => '',
            'date' => '',
            'remarks' => '',
            'status' => M_Status::STATUS_DEFAULT,
            'total_amount' => '0.00',
            'miscellaneous_charges' => array(
                'others' => '0.00',
                'handling' => '0.00',
                'trucking' => '0.00'
            )
        );
    }

    function _segment_url($segment = '') {
        return base_url("purchases/receiving/{$segment}");
    }

    function index() {
        $this->load->model('maintainable/m_supplier', 'supplier');
        $this->setTabTitle('Purchase Receivings');
        $this->viewpage_settings['suppliers'] = ['' => ''] + array_column($this->supplier->all(), 'name', 'id');
        $this->add_javascript(['plugins/sticky-thead.js', 'numeral.js', 'printer/printer.js', 'purchases-receiving/master-list.js']);
        $this->set_content('purchases/receiving', $this->viewpage_settings);
        $this->generate_page();
    }

    public function ajax_master_list(){
        if($this->input->is_ajax_request()){
            $offset = $this->input->get('page');
            $page = $offset ? $offset : 1;
            $data = $this->m_purchase_receiving->all($page, $this->search_params());
            $this->generate_response($data ? ['data' => $data] : [])->to_JSON();
        }
    }

    public function search_params()
    {
        $this->load->model('maintainable/m_supplier', 'supplier');
        $this->load->helper('pmdate');
        $query = [];
        $params = elements(['id', 'po_id', 'dr_si', 'start_date', 'end_date', 'supplier'], $this->input->get());
        if($params['id'] && is_numeric($params['id']))
        {
            $query['rr.id'] = $params['id'];
        }
        if($params['po_id'] && is_numeric($params['po_id']))
        {
            $query['rr.fk_purchase_order_id'] = $params['po_id'];
        }
        if($params['dr_si'])
        {
            $query['rr.pr_number'] = $params['dr_si'];
        }
        if($params['start_date'] && is_valid_date($params['start_date'], 'm/d/Y'))
        {
            $query['STR_TO_DATE(rr.date, "%Y-%m-%d") >='] = date('Y-m-d', strtotime($params['start_date']));
        }
        if($params['end_date'] && is_valid_date($params['end_date'], 'm/d/Y'))
        {
            $query['STR_TO_DATE(rr.date, "%Y-%m-%d") <='] = date('Y-m-d', strtotime($params['end_date']));
        }
        if($params['supplier'] && $this->supplier->is_valid($params['supplier']))
        {
            $query['rr.fk_maintainable_supplier_id'] = $params['supplier'];
        }
        return empty($query) ? FALSE : $query;
    }

    public function manage() {
        $this->add_javascript(array('numeral.js', 'price-format.js', 'jquery-ui.min.js','plugins/datetimepicker/timepicker.js'));
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->load->model('accounting/m_bank_account');
        $this->viewpage_settings['bank_accounts'] = dropdown_format($this->m_bank_account->get(), 'id', array('bank_name', 'bank_branch'));
        if ($this->input->get('do') === 'new-purchase-receiving') {
            $this->setTabTitle("New purchase receiving");
            $this->viewpage_settings['mode'] = 'new';
            $this->viewpage_settings['form_title'] = sprintf('Add new %s', self::SUBJECT);
            $this->viewpage_settings['action'] = $this->_segment_url('a_do_action/add');
            $this->viewpage_settings['is_locked'] = FALSE;
        } else if ($this->input->get('do') === 'update-purchase-receiving' && $this->m_purchase_receiving->is_valid($this->input->get('id'))) {
            $id = $this->input->get('id');

            if($this->m_purchase_receiving->get_max_id()[0]['id'] == $id){
                $receiving_next_id = $this->m_purchase_receiving->get_min_id();
                if($receiving_next_id[0]['id'] == $id){
                    $receiving_next_id = array();
                }
            }else{
                $receiving_next_id = $this->m_purchase_receiving->get_next_row_id($id, "next");
            }

            if($this->m_purchase_receiving->get_min_id()[0]['id'] == $id){
                $receiving_prev_id = $this->m_purchase_receiving->get_max_id();
                if($receiving_prev_id[0]['id'] == $id){
                    $receiving_prev_id = array();
                }
            }else{
                $receiving_prev_id = $this->m_purchase_receiving->get_next_row_id($id, "prev");
            }

            if(!empty($receiving_next_id)){
                $_id = $receiving_next_id[0]['id'];
                $this->viewpage_settings['receiving_next_info'] = base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$_id}");
                $this->viewpage_settings['receiving_next_id'] = $_id;
            }else{
                $this->viewpage_settings['receiving_next_info'] = 0;
            }

            if(!empty($receiving_prev_id)){
                $_id = $receiving_prev_id[0]['id'];
                $this->viewpage_settings['receiving_prev_info'] = base_url("purchases/receiving/manage?do=update-purchase-receiving&id={$_id}");
                $this->viewpage_settings['receiving_prev_id'] = $_id;
            }else{
                $this->viewpage_settings['receiving_prev_info'] = 0;
            }

            $this->setTabTitle("Update purchases receiving # {$id}");
            $receivingList = $this->m_purchase_receiving->get(TRUE, FALSE, array('receiving.id' => $id));
            $this->viewpage_settings['defaults'] = $receivingList[0];
            $this->viewpage_settings['form_title'] = sprintf('Update %s # %d', self::SUBJECT, $id);
            $this->viewpage_settings['action'] = $this->_segment_url("a_do_action/update/{$id}");
            $this->viewpage_settings['is_locked'] = $this->m_purchase_receiving->is_locked($id);
        } else {
            show_404();
        }
        $this->load->model(['maintainable/m_supplier']);
        $this->viewpage_settings['suppliers'] = dropdown_format($this->m_supplier->all(), 'id', 'name');
        $this->set_content('purchases/manage-receiving', $this->viewpage_settings);
        $this->generate_page();
    }
    
    public function makeIDList($entries, $IDLabel = "id"){
        $IDs = array();
        if(is_array($entries)){
            foreach($entries as $value){

                $IDs[] = $value[$IDLabel];
            }
        }
        return ($IDs) ? $IDs : false;
    }
    function _validate_input() {
        foreach ($this->_fields as $field) {
            $this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
        }
        if ($this->form_validation->run()) {
            $receiving['general'] = elements(array_map(function($var) {
                        return $var['name'];
                    }, $this->_fields), $this->input->post(), '');
            if (!isset($receiving['general']['status']) || !$receiving['general']['status']) {
                $receiving['general']['status'] = M_Status::STATUS_DEFAULT;
            }
            $temp = $receiving['general']['details'];
            unset($receiving['general']['details']);
            for ($x = 0; $x < count($temp['fk_purchase_order_detail_id']); $x++) {
                $detail = array(
                    'fk_purchase_order_detail_id' => $temp['fk_purchase_order_detail_id'][$x],
                    'discount' => str_replace(',', '', $temp['discount'][$x])
                );
                
                if(is_numeric($temp['this_receive'][$x]) && (float)abs($temp['this_receive'][$x])){
                    $detail['this_receive'] = abs($temp['this_receive'][$x]);
                }else{
                    $detail['this_receive'] = NULL;
                }
                
                if(is_numeric($temp['pieces_received'][$x]) && (float)abs($temp['pieces_received'][$x])){
                    $detail['pieces_received'] = abs($temp['pieces_received'][$x]);
                }else{
                    $detail['pieces_received'] = NULL;
                }
                
                if (isset($temp['id'][$x])) {
                    $detail['id'] = $temp['id'][$x];
                }
                $receiving['details'][] = $detail;
            }
            return $this->response(FALSE, '', $receiving);
        } else {
            return $this->response(TRUE, explode(",", validation_errors(' ', ',')));
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
        return;
    }
    /* CRUD REMAPS */

    function _add($validated_data) {
        $id = $this->m_purchase_receiving->add($validated_data['general'], $validated_data['details']);
        if ($id) {
            $response = $this->response(FALSE, $this->m_message->add_success(self::SUBJECT, "R.R. # {$id}"), array('redirect' => $this->_segment_url()));
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($response));
            return $response;
        }
        return $this->response(TRUE, $this->m_message->add_eror(self::SUBJECT));
    }

    function _update($id, $validated_data) {
        if ((int) $this->session->userdata('type_id') !== (int) M_Account::TYPE_ADMIN && $this->m_purchase_receiving->is_locked($id)) {
            
            return $this->response(TRUE, sprintf('You cannot update a locked %s', self::SUBJECT));
        }
        $updated = $this->m_purchase_receiving->update($id, $validated_data['general'], $validated_data['details']);
        if ($updated) {
            $this->session->set_flashdata('form_submission_success', $this->m_message->update_success(self::SUBJECT, "P.O. # {$id}"));
            return $this->response(FALSE, '', array('redirect' => $this->_segment_url()));
        }
        return $this->response(TRUE, $this->m_message->update_error(self::SUBJECT));
    }

    function _delete($id) {
        if ((int) $this->session->userdata('type_id') !== (int) M_Account::TYPE_ADMIN) {
            return $this->response(TRUE, sprintf('You do not have the privilege to delete a %s', self::SUBJECT));
        }
        $deleted = $this->m_purchase_receiving->delete($id);
        if ($deleted) {
            return $this->response(FALSE, '');
        }
        return $this->response(TRUE, $this->m_message->delete_error(self::SUBJECT));
    }

    /* END CRUD REMAPS */

    /* SPECIAL FUNCTIONS */

    public function change_lock_state() {
        $this->output->set_content_type('json');
        if ($this->session->userdata('type_id') != M_Account::TYPE_ADMIN) {
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
        }
        if ($this->m_purchase_receiving->change_lock_state($this->input->post('order_id'), $do_lock)) {
            $this->output->set_output(json_encode($this->response(FALSE, '')));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'An unknown error has occured.')));
        }
    }

    public function a_get_undisbursed() {
        $this->output->set_content_type('json');
        $data = $this->m_purchase_receiving->get_undisbursed($this->input->get('supplier_id'));
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

    function _is_valid_po($fk_purchase_order_id) {
        $this->load->model('purchases/m_purchase_order');
        if (!$this->m_purchase_order->is_valid($fk_purchase_order_id, $this->input->post('fk_maintainable_supplier_id'), TRUE)) {
            $this->form_validation->set_message('_is_valid_po', 'Please select a valid %s.');
            return FALSE;
        }
        return TRUE;
    }

    function _status_check($status) {
        if ((int) $status === (int) M_Status::STATUS_RECEIVED) {
            if ((int) $this->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN) {
                return TRUE;
            } else {
                $this->form_validation->set_message('_status_check', 'You are not authorized to receive purchases.');
                return FALSE;
            }
        }
        return TRUE;
    }

    function _remarks_check()
    {
        return TRUE;
    }

    function _details_check() {
        $details = $this->input->post('details');
        $this->load->model('purchases/m_purchase_order');
        $detail_ids = isset($details['fk_purchase_order_detail_id']) ? $details['fk_purchase_order_detail_id'] : array();
        $received_quantities = isset($details['this_receive']) ? $details['this_receive'] : array();
        if (empty($detail_ids)) {
            $this->form_validation->set_message('_details_check', sprintf('You cannot save an empty %s', self::SUBJECT));
            return FALSE;
        }
        if (!$this->m_purchase_order->are_valid_details($this->input->post('fk_purchase_order_id'), array_unique($detail_ids))) {
            $this->form_validation->set_message('_details_check', sprintf('Details for this %s are invalid', self::SUBJECT));
            return FALSE;
        }
        foreach ($received_quantities as $key=>$value) {
            $discount = str_replace(',', '', $details['discount'][$key]);
            if ($value && !is_numeric($value)) {
                $this->form_validation->set_message('_details_check', 'Received quantities can only contain numbers.');
                return FALSE;
            }
            if ($discount && !is_numeric($discount)) {
                $this->form_validation->set_message('_details_check', 'Discount should be in decimal form.');
                return FALSE;
            }
            if ($details['pieces_received'][$key] && !is_numeric($details['pieces_received'][$key])) {
                $this->form_validation->set_message('_details_check', 'Received quantities can only contain numbers.');
                return FALSE;
            }
        }
        return TRUE;
    }

    /* END OF FORM VALIDATION CALLBACKS */

    //print
    public function do_print() {
        $id = $this->input->get('id');
        if (is_numeric($id) === FALSE || $this->m_purchase_receiving->is_approved($id) === FALSE) {
            echo 'Please make sure the purchase receiving is approved before printing.';
            return;
        }
        $details = $this->m_purchase_receiving->get(TRUE, FALSE, array('receiving.id' => $id));
        $data['details'] = $details[0];
        $this->load->view('printables/purchases/receiving', $data);
    }

}

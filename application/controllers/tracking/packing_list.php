<?php

class Packing_list extends PM_Controller_v2
{

    protected $id = NULL;
    protected $validation_errors = [];
    private $viewpage_settings = array();

    function __construct()
    {
        parent::__construct();
        if(!has_access('tracking')) show_error('Authorization error', 401);
        $this->set_content_title('Tracking');
        $this->set_content_subtitle('Packing list');
        $this->set_active_nav(NAV_TRACKING);
        $this->load->model(array('tracking/m_packing_list', 'tracking/m_tariffs','sales/m_customer'));

        $this->viewpage_settings['defaults'] = array(
            'fk_sales_customer_id' => '',
            'fk_trip_ticket_id' => FALSE,
            'date' => '',
            'fk_tariff_id' => '',
            'option' => '',
            'location' => '',
            'less' => array(
                'fk_location_id' => array(''),
                'location' => '',
                'rate' => array(''),
                'pcs' => array(0),
                'amount' => array(0)
            ),
            'approved_by' => ''

        );
    }
  
    function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['fk_sales_customer_id'], $this->input->get(), FALSE);

        if($params['fk_sales_customer_id'] && is_numeric($params['fk_sales_customer_id'])){
            $search['pl.fk_sales_customer_id'] = $params['fk_sales_customer_id'];
        }
        
        return compact(['search', 'wildcards']);
    }

    function index()
    {

        $this->load->helper('customer');
        $this->add_javascript([
            'plugins/sticky-thead.js',
            'tracking-packing-list/listing.js',
            'plugins/moment.min.js'
        ]);

        $params = $this->_search_params();
        $this->viewpage_settings['items'] = $this->m_packing_list->all($params['search']);
        $this->set_content('tracking/packing-list/listing', 
            $this->viewpage_settings
        )->generate_page();
    }

    public function create() 
    {
        $this->load->helper('customer');
        $tariffs = $this->m_tariffs->all();
        $this->viewpage_settings['customers'] = ['' => ''] + array_column($this->m_customer->all(['status' => 'a']), 'company_name', 'id');
        $this->viewpage_settings['tariffs'] = ['' => ''] + array_column($this->m_tariffs->all(['p.approved_by !=' => 'NULL']), 'code', 'id');
        $this->viewpage_settings['form_title'] = 'Add new packing list';
        $this->viewpage_settings['form_action'] = base_url('tracking/packing_list/store');

        $this->add_javascript([
            'plugins/moment.min.js',
            'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js',
            'jquery-ui.min.js', 
            'numeral.js',
            'tracking-packing-list/manage.js',
            'price-format.js'
        ]);
        $this->set_content('tracking/packing-list/manage',
             $this->viewpage_settings
        )->generate_page();
    }

    function master_list()
    {
        $params = $this->input->get();

        $items = $this->receiving->all([
            'limit' => $params['length'] ?: 100,
            'offset' => $params['start'] ?: 0,
        ]);

        $count_all = $this->receiving->count_all();

        $this->generate_response([
            'data' => $items,
            'draw' => (int)$params['draw'] ?: 1,
            'recordsFiltered' => $count_all,
            'recordsTotal' => $count_all
        ])->to_JSON();
    }


    public function get($packing_id) {
        $data = $this->m_packing_list->get($packing_id);
        if(empty($data)){
            show_404();
            return;
        }
        $this->add_css('jQueryUI/jquery-ui-1.10.3.custom.min.css');
        $this->add_javascript([
            'plugins/moment.min.js',
            'plugins/bootstrap-datetimepicker/bs-datetimepicker.min.js',
            'jquery-ui.min.js', 
            'numeral.js',
            'tracking-packing-list/manage.js',
            'price-format.js'
        ]);

        $this->load->helper('customer');
        $this->load->helper('tariff');
        $this->viewpage_settings['customers'] = ['' => ''] + array_column($this->m_customer->all(['status' => 'a']), 'company_name', 'id');

        $this->viewpage_settings['less'] = $this->m_packing_list->get_tariff_detail($data[0]['fk_tariff_id']);

        $this->viewpage_settings['form_action'] = base_url("tracking/packing_list/update/{$packing_id}");
        $this->viewpage_settings['form_title'] = "Update packing list".$packing_id;
        $this->viewpage_settings['tariffs'] = ['' => ''] + array_column($this->m_tariffs->all(), 'code', 'id');
        $ticket = $this->viewpage_settings['trip_ticket'] = ['' => ''] + array_column($this->m_packing_list->get_trip_ticket($data[0]['fk_sales_customer_id']), 'id', 'id');
        $this->viewpage_settings['defaults']['fk_trip_ticket_id'] = $this->m_packing_list->get_tariff_value($packing_id);
        $ticket_id = $this->viewpage_settings['defaults']['fk_trip_ticket_id'];
        $new = [];
        foreach ($ticket_id as $key) {
            $value = $key['fk_trip_ticket_id'];
            $new[$value] = $key['fk_trip_ticket_id'];
        }
        $this->viewpage_settings['trip_ticket'] = $this->array_pusher($this->viewpage_settings['trip_ticket'], $value, $value);
        if ($this->input->post()) {
            $saved = FALSE;
            $input = $this->input->post();
            $input = $this->_validate();
            if ($input['error_flag']) {
                $this->viewpage_settings['defaults'] = $this->input->post();
                $this->viewpage_settings['defaults']['fk_sales_customer_id'] = $data[0]['fk_sales_customer_id'];
                $this->viewpage_settings['defaults']['fk_trip_ticket_id'] = $data[0]['fk_trip_ticket_id'];
                $this->viewpage_settings['defaults']['date'] = $data[0]['date'];
                $this->viewpage_settings['defaults']['fk_tariff_id'] = $order_info[0]['fk_tariff_id'];
                $this->viewpage_settings['defaults']['approved_by'] = $data[0]['approved_by']; //reset status
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
            $this->setTabTitle("Paking List # {$packing_id}");
            $this->viewpage_settings['defaults'] = $data[0];
        }
        $this->set_content('tracking/packing-list/manage', $this->viewpage_settings);
        $this->generate_page();
    }

    function array_pusher($array, $key, $value){
        $array[$key] = $value;
        return $array;
    }

    function update($id)
    {
        $this->_validation();
        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
        $data = $this->_format_data();
        $success = $this->m_packing_list->update($id, $data['packing'], $data['order_line']);
        if($success){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Unable to perform action due to an unknown error.')->to_JSON();
    }

    function store()
    {
        $this->_validation();
        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
        $data = $this->_format_data();
        $success = $this->m_packing_list->create($data['packing'], $data['order_line']);
        if($success){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Unable to perform action due to an unknown error.')->to_JSON();
    }


public function delete($id)
    {
        if(!$id || !$packing_list = $this->m_packing_list->find($id)){
            $this->generate_response(TRUE, 'Please select a valid tariff to delete.')->to_JSON();
            return;
        }
        if(!can_delete($packing_list)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->m_packing_list->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function get_trip_ticket()
    {
        $this->load->helper('customer');
        $items = $this->m_packing_list->get_trip_ticket($this->input->get('id'));
        $set = ['' => ''] + array_column($items, 'id', 'id');
        $this->output->set_output(form_dropdown('fk_trip_ticket_id', $set, FALSE, 'class="form-control"'));

    }

    public function get_tariff_details()
    {
        $this->load->helper('tariff');
        $items = $this->m_packing_list->getDetails($this->input->get('id'));
        $this->generate_response([
            'options' => $items['tariff']['option'],
            'location' => $items['tariff']['location'],
            'details' => generate_tariff_dropdown('less[fk_location_id][]', $items['less'], 'fk_location_id', 'location', FALSE, FALSE, 'class="form-control tariff_details_list select-clear"')
        ])->to_JSON();
    }

    function _validation()
    {
        $this->form_validation->set_rules('fk_sales_customer_id', 'customer', 'required|numeric');
        $this->form_validation->set_rules('fk_trip_ticket_id', 'trip ticket', 'required|numeric');
        $this->form_validation->set_rules('fk_tariff_id', 'tariff', 'required|numeric');
        $this->form_validation->set_rules('date', 'Date', 'required');

        if(!$this->form_validation->run()){
            $this->validation_errors += array_values($this->form_validation->error_array());
        }

        $less = $this->input->post('less');
        if(!isset($less['fk_location_id']) 
            || !is_array($less['fk_location_id']) 
            || empty($less['fk_location_id'])){
            $this->validation_errors[] = 'Please select at least one product for the order.';
            return;
        }
        foreach($less['fk_location_id'] AS $key => $value){
            $line = $key + 1;
            if(!isset($less['pcs'][$key])){
                $this->validation_errors[] = "Heads / pieces count for line # {$line} has no value";
            }
            if(!isset($less['amount'][$key]) || $less['amount'][$key] == 0){
                $this->validation_errors[] = "Amount for line # {$line} has no value";
            }
        }
    }

    function _format_data()
    {
        $packing = elements(['fk_tariff_id', 'fk_trip_ticket_id', 'fk_sales_customer_id','date','approved_by'], $this->input->post());

        if(can_set_status()){
            if(isset($packing['approved_by']) && $packing['approved_by']=='on' ){
                $packing['approved_by'] = $this->session->userdata('user_id');
            } else {
                $packing['approved_by'] = NULL;
            }
        }
        $packing['last_updated_by'] = $this->session->userdata('user_id');

        $order_line = [];
        $less = $this->input->post('less');
        foreach($less['fk_location_id'] AS $key => $value){
            $amount = str_replace(",", "", $less['amount'][$key]);

            $temp = [
                'fk_location_id' => $value,
                'pcs' => abs($less['pcs'][$key]),
                'rate' => $less['rate'][$key],
                'amount' => $amount
            ];
            if(isset($less['id'][$key])){
                $temp['id'] = $less['id'][$key];
            }
            $order_line[] = $temp;
        }
        return compact(['packing', 'order_line']);
    }

}
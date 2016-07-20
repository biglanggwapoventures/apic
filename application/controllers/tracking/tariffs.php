<?php

class Tariffs extends PM_Controller_v2
{

    protected $id = NULL;

    function __construct()
    {
        parent::__construct();
        if(!has_access('tracking')) show_error('Authorization error', 401);
        $this->set_content_title('Tracking');
        $this->set_content_subtitle('Tariffs');
        $this->set_active_nav(NAV_TRACKING);
        $this->load->model(array('tracking/m_tariffs', 'tracking/m_locations'));
    }
  
    function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['code','location','option'], $this->input->get(), FALSE);
        if($params['option'] && in_array($params['option'], ['1', '2'])){
            $search['p.option'] = $params['option'];
        }

        if($params['code'] && trim($params['code'])){
            $wildcards['p.code'] = $params['code'];
        }

        if($params['location'] && is_numeric($params['location'])){
            $search['p.fk_location_id'] = $params['location'];
        }
        
        return compact(['search', 'wildcards']);
    }

    function index()
    {


        $this->add_javascript([
            'plugins/sticky-thead.js',
            'tracking-tariffs/listing.js',
            'plugins/moment.min.js'
        ]);

        $params = $this->_search_params();
        $data = $this->m_tariffs->all($params['search'], $params['wildcards']);

        $this->set_content('tracking/tariffs/listing', [
            'items' => $data,
            'locations' => $this->m_locations->all()
        ])->generate_page();
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

    public function create() 
    {
        $this->add_javascript(['tracking-tariffs/manage.js','price-format.js', 'numeral.js']);
        $this->set_content('tracking/tariffs/manage', [
            'title' => 'Create new tariff',
            'action' => base_url('tracking/tariffs/store'),
            'locations' => $this->m_locations->all(),
            'data' => ['approved_by'=> NULL],
        ])->generate_page();
    }

    function edit($id = FALSE)
    {
        if(!$id || !$tariff = $this->m_tariffs->get($id)){
            show_404();
        }

        $this->add_javascript(['tracking-tariffs/manage.js','price-format.js', 'numeral.js']);
        $this->set_content('tracking/tariffs/manage', [
            'data' => $tariff,
            'title' => "Update tariff #{$tariff['id']}",
            'action' => base_url("tracking/tariffs/update/{$tariff['id']}"),
            'locations' => $this->m_locations->all(),
        ])->generate_page();
    }

    function store()
    {
        $this->set_action('new');
        $validation = $this->_validate();
        if($validation['status']){
            $id = $this->m_tariffs->create($validation['data']);
            $this->generate_response(FALSE, '', ['id' => $id])->to_JSON();
        }else{
            $this->generate_response(TRUE, $validation['errors'])->to_JSON();
        }
    }

    function update($id = FALSE)
    {
        if(!$id || !$this->m_tariffs->exists($id)){
            $this->generate_response(TRUE, ['The tariff you are trying to update does not exist!'])->to_JSON();
            return;
        }
        $this->id = $id;

        $validation = $this->_validate();
        if($validation['status']){
            $this->m_tariffs->update($id, $validation['data']);
            $this->generate_response(FALSE, '')->to_JSON();;
        }else{
            $this->generate_response(TRUE, $validation['errors'])->to_JSON();
        }
    }

public function delete($id)
    {
        if(!$id || !$tariff = $this->m_tariffs->find($id)){
            $this->generate_response(TRUE, 'Please select a valid tariff to delete.')->to_JSON();
            return;
        }
        if(!can_delete($tariff)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->m_tariffs->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

 function _validate()
    {

        $errors = [];

        if($this->action('new')){
            $this->form_validation->set_rules('code', 'tariff code', 'trim|required|is_unique[tracking_tariff.code]');
        }else{
            $this->form_validation->set_rules('code', 'tariff code', 'trim|required|callback__validate_code_name');
        }
        $this->form_validation->set_rules('option', 'option name', 'required|numeric');
        $this->form_validation->set_rules('fk_location_id', 'location name', 'required');
       
        $less = $this->input->post('less');
        if(is_array($less) || !empty($less)){
            foreach($less AS $key => $item){
                $line = $key + 1;
                if(!is_array($item)){
                    $errors[] = "Less items in line # {$line} must contain a product and quantity.";
                    continue;
                }
                if(!isset($item['fk_location_id']) || !is_numeric($item['fk_location_id'])){
                    // echo "lol";
                    $errors[] = "Provide location for item in line # {$line}";
                }
                if(!isset($item['rate'])){
                    $errors[] = "Provide rate for item in line # {$line}";
                }
            }
        } else{
            $errors[] = "Provide entry";
        }

        if($this->form_validation->run() && empty($errors)){
            $input = $this->input->post();
            if(isset($input['code']) && $code = trim($input['code']))
                $data['tariff']['code'] = $code;

            if(isset($input['option']) && $option = trim($input['option']))
                $data['tariff']['option'] = $option;

            if(isset($input['fk_location_id']) && $fk_location_id = trim($input['fk_location_id']))
                $data['tariff']['fk_location_id'] = $fk_location_id;


            if(can_set_status()){
                $set = elements(['approved_by'], $this->input->post());
                if(isset($set['approved_by']) && $set['approved_by']=='on' ){
                    $data['tariff']['approved_by'] = $this->session->userdata('user_id');
                } else {
                    $data['tariff']['approved_by'] = NULL;
                }
            }
            if(!empty($input['less'])){
                foreach($input['less'] AS $less){
                    $temp = [
                        'fk_location_id' => $less['fk_location_id'],
                        'rate' => abs($less['rate']),
                        'kms' => abs($less['kms'])
                    ];
                    if(isset($less['id'])){
                        $temp['id'] = $less['id'];
                    }
                    $data['tariff_details'][] = $temp;
                }
            }
            $data['tariff']['last_updated_by'] = $this->session->userdata('user_id');
            return [
                    'status' => TRUE,
                    'data' => $data
                ];
        }
        return [
            'status' => FALSE,
            'errors' => array_merge($this->form_validation->errors(), $errors)
        ];
    }
    public function _validate_code_name($code)
    {
        return $this->m_tariffs->has_unique_code($code, $this->id);
    }
 

}


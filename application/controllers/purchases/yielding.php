<?php

class Yielding extends PM_Controller_v2 {

    protected $rr_no;
    protected $validation_errors = [];

    function __construct()
    {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Process products');
        $this->load->model('purchases/m_purchase_receiving');
        $this->load->model('purchases/yielding_model', 'yield');
    }

    function index()
    {
    	$this->load->model('purchases/m_purchase_receiving');
    	$this->load->model('inventory/m_product', 'product');
    	$rr_no = $this->input->get('rr');
    	$data = $this->m_purchase_receiving->get(TRUE, FALSE, ['receiving.id' => $rr_no]);
    	$this->set_content('purchases/yielding', [
    		'form_title' => "Process products from RR# {$rr_no}",
    		'form_action' => base_url("purchases/yielding/save/{$rr_no}"),
    		'product_list' => $this->product->get_list(),
    		'data' => $data[0],
            'yielding' => $this->
		])->generate_page();
    }

    function save($rr_no)
    {
        $this->rr_no = $rr_no;
        $this->_perform_validation();

        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }

        $mode = $this->yield->exists($rr_no) ? 'u' : 'c';

        if($mode === 'c'){
            $data = $this->_format_data($mode);
            // $this->generate_response($data)->to_JSON();
            // return;
            if($this->yield->create($data)){
                $this->generate_response(FALSE)->to_JSON();
                return;
            }
            $this->generate_response(TRUE, 'Cannot perform action due to an unknown error.')->to_JSON();
        }
        
    	
    }

    function _format_data($mode)
    {
        $input = $this->input->post();

        $data['yielding'] = [
            'remarks' => $input['remarks'],
            'fk_purchase_receiving_id' => $this->rr_no
        ];
        if($mode === 'c'){
            $data['yielding'] += [
                'created_by' => user_id(),
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        foreach($input['yield'] AS $source){

            $quantity = $source['quantity'];

            if(!$quantity){
                continue;
            }

            $results = [];
            $temp = [
                'fk_purchase_receiving_detail_id' => $source['rr_detail_id'],
                'quantity' => $quantity,
                'pieces' => $source['pieces']
            ];
            foreach($source['to'] AS $result){
                $results[] = [
                    'quantity' => $result['quantity'],
                    'pieces' => $result['pieces'],
                    'fk_inventory_product_id' => $result['product_id']
                ];
            }

            $temp['result'] = $results;
            $data['source'][] = $temp;

        }
        return $data;

    }

    function _perform_validation()
    {
        // check if 
        if(!is_array($yield = $this->input->post('yield'))){
            $this->validation_errors[] = "Malformed payload.";
            return;
        }
        // get receiving details
        $receiving = $this->m_purchase_receiving->get(TRUE, FALSE, ['receiving.id' => $this->rr_no]);
        // index reciving line
        $receiving_details = array_column($receiving[0]['details'], NULL, 'id');

        // iterate through payload and validate
        foreach($yield AS $key => $item){

            $product = $receiving_details[$key]['description'];

            if(!isset($receiving_details[$key])){
                $this->validation_errors[] = "Cannot further process {$product}. Item not found in RR.";
                continue;
            }

            if(!is_array($item)){
                $this->validation_errors[] = "Cannot further process {$product}.";
                continue;
            }

            if(!isset($item['quantity']) || !is_numeric($item['quantity'])){
                $this->validation_errors[] = "Provide quantity to use for further processing of {$product}.";
            }

            if(!isset($item['pieces']) || ($item['pieces'] && !is_numeric($item['pieces']))){
                $this->validation_errors[] = "Heads/pieces to use for further processing of {$product} must be in numeric form.";
            }

            if(!isset($item['to']) || !is_array($item['to'])){
                $this->validation_errors[] = "Cannot further process {$product}. Resulting products not given.";
                continue;
            }

            $line = 1;
            foreach($item['to'] AS $index => $produce){
                if(!is_array($produce)){
                    $this->validation_errors[] =  "Cannot further process {$item['description']} to product # {$line}.";
                    break;
                }
                if(!isset($produce['product_id']) || !is_numeric($produce['product_id'])){
                    $this->validation_errors[] = "Provide product for line # {$line} in {$product}";
                }
                if(!isset($produce['quantity']) || !is_numeric($produce['quantity'])){
                    $this->validation_errors[] = "Provide product quantity for line # {$line} in {$product}";
                }
                if(!isset($produce['pieces']) || ($produce['pieces'] && !is_numeric($produce['pieces']))){
                    $this->validation_errors[] = "Product pieces/heads must be in numeric form for line # {$line} in {$product}";
                }
                $line++;
            }
        }
    }
}
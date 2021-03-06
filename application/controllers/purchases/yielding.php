<?php

class Yielding extends PM_Controller_v2 {

    protected $rr_no;
    protected $validation_errors = [];
    protected $yield_types = ['live-to-dressed', 'dressed-to-cutups'];

    function __construct()
    {
        parent::__construct();
        if (!has_access('purchases')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PURCHASES);
        $this->set_content_title('Purchases');
        $this->set_content_subtitle('Process products');
        $this->load->model('purchases/m_purchase_receiving', 'receiving');
        $this->load->model('purchases/yielding_model', 'yield');
    }

    function index()
    {

        $type = $this->input->get('type');
        if(!in_array($type, $this->yield_types)){
            show_404();
        }

        $this->add_javascript('numeral.js');
    	$this->load->model('inventory/m_product', 'product');

        $rr_no = $this->input->get('rr');
        $data = $this->receiving->get(TRUE, FALSE, ['receiving.id' => $rr_no]);

        if(!$data){
            show_404();
        }

        if($type === $this->yield_types[0]){
            $used_categories = [M_Product::CATEGORY_FRESH_CHILLED_DRESSED_CHICKEN, M_Product::CATEGORY_CHICKEN_BYPRODUCTS];
            $filter_detail = M_Product::CATEGORY_LIVE_CHICKEN;
        }else{
            $used_categories = [M_Product::CATEGORY_CHICKEN_CUTUPS];
            $filter_detail = M_Product::CATEGORY_FRESH_CHILLED_DRESSED_CHICKEN;
        }

        $data[0]['details'] = array_filter($data[0]['details'], function($var) USE ($filter_detail) {
            return $var['fk_category_id'] == $filter_detail;
        });

        $products = $this->product->category_in($used_categories)->get_list(['product.status' => 'a']);

    	$this->set_content("purchases/yielding-{$type}", [
    		'form_title' => "Process products from RR# {$rr_no}",
    		'form_action' => base_url("purchases/yielding/save/{$rr_no}"),
    		'product_list' => $products,
    		'data' => $data[0],
            'yielding' => $this->yield->get($rr_no),
            'type' => $type
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

        if($this->yield->exists($rr_no)){
            $previous = $this->yield->get($rr_no);
            $offset = array_column($previous['source'], NULL, 'fk_purchase_receiving_detail_id');
            $mode = 'u';
        }else{
            $mode = 'c';
            $offset = FALSE;
        }

        $data = $this->_format_data($mode);

        $unavailable = $this->_check_item_availability($data['source'], $offset);
        if(!empty($unavailable)){
            $this->generate_response(TRUE, $unavailable)->to_JSON();
            return;
        }

        if($mode === 'c'){
            
            // $this->generate_response($data)->to_JSON();
            // return;
            if($this->yield->create($data)){
                $this->flash_message(FALSE, 'Further processing has been successful!');
                $this->generate_response(FALSE)->to_JSON();
                return;
            }
            $this->generate_response(TRUE, 'Cannot perform action due to an unknown error.')->to_JSON();

        }else{
            if($this->yield->update($rr_no, $data)){
               $this->flash_message(FALSE, 'Further processing has been successful!');
                $this->generate_response(FALSE, [], $data)->to_JSON();
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

            $results = [];

            $temp = [
                'fk_purchase_receiving_detail_id' => $source['rr_detail_id'],
                'quantity' => $quantity,
                'pieces' => $source['pieces'],
                'yield_type' => $input['yield_type'] === $this->yield_types[0] ? 'ltd' : 'dtc'
            ];

            if(isset($source['id'])){
                $temp['id'] = $source['id'];
            }

            foreach($source['to'] AS $result){
                $to = [
                    'quantity' => $result['quantity'],
                    'pieces' => $result['pieces'],
                    'fk_inventory_product_id' => $result['product_id']
                ];
                if(isset($result['id'])){
                    $to['id'] = $result['id'];
                }  
                $results[] = $to;
            }

            $temp['result'] = $results;
            $data['source'][] = $temp;

        }
        return $data;

    }

    function _perform_validation()
    {
        $type = $this->input->post('yield_type');
        if(!in_array($type, $this->yield_types)){
            $this->validation_errors[] = "Yield type not valid.";
            return;
        }

        if(!is_array($yield = $this->input->post('yield'))){
            $this->validation_errors[] = "Malformed payload.";
            return;
        }
        // get receiving details
        $receiving = $this->receiving->get(TRUE, FALSE, ['receiving.id' => $this->rr_no]);
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

            if(!isset($item['quantity']) || !is_numeric($item['quantity']) || floatval($item['quantity']) <= 0){
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
                if(!isset($produce['quantity']) || !is_numeric($produce['quantity']) || floatval($produce['quantity']) <= 0){
                    $this->validation_errors[] = "Provide product quantity for line # {$line} in {$product}";
                }
                if(!isset($produce['pieces']) || ($produce['pieces'] && !is_numeric($produce['pieces']))){
                    $this->validation_errors[] = "Product pieces/heads must be in numeric form for line # {$line} in {$product}";
                }
                $line++;
            }
        }
    }


    function _check_item_availability($details, $offsets = [])
    {
        $this->load->model('inventory/m_product', 'product');

        $unavailable = [];

        $details = array_column($details, NULL, 'fk_purchase_receiving_detail_id');
        $products = $this->receiving->item_ids(array_keys($details));

        $product_ids = array_values($products);

        $product_details = $this->product->identify($product_ids);
        $stocks = $this->product->get_stocks($product_ids);

        foreach($products AS $rr_detail_id => $item_id){

            $product = $product_details[$item_id];

            $available = [ 'units' => 0, 'pieces' => 0 ];
            $requested = [ 'units' => $details[$rr_detail_id]['quantity'], 'pieces' => $details[$rr_detail_id]['pieces'] ];

            if(isset($stocks[$item_id])){
                $available['units'] += $stocks[$item_id]['available_units'];
                $available['pieces'] += $stocks[$item_id]['available_pieces'];
            }

            if(isset($offsets[$rr_detail_id])){
                $available['units'] += $offsets[$rr_detail_id]['quantity'];
                $available['pieces'] += $offsets[$rr_detail_id]['pieces'];
            }

            $lacking = [];  

            if($requested['units'] > $available['units']){
                $lacking_units = $requested['units'] - $available['units'];
                $lacking[] = "{$lacking_units} {$product['unit_description']}";
            }

            if($requested['pieces'] > $available['pieces']){
                $lacking_pieces = $requested['pieces'] - $available['pieces'];
                $lacking[] = "{$lacking_pieces} pieces";
            }

            if(!empty($lacking)){
                $unavailable[] = "Lacking ". implode(' and ', $lacking). " for: {$product['description']}";
            }
        }

        return $unavailable;

    }


    // calculates cost of dressed from live

    function _calculate_cost($live_cost, $results)
    {
        $this->load->model('inventory/m_product', 'product');

        $results = array_column($results, NULL, 'id');

        $product_details = $this->product->identify(array_values($results));

        $total_units = 0;

        foreach ($product_details as $id => $props) {
            if($props['fk_category_id'] == M_Product::CATEGORY_FRESH_CHILLED_DRESSED_CHICKEN){
                $total_units += $props['quantity'];
            }
        }
    }
}
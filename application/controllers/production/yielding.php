<?php

class Yielding extends PM_Controller_v2
{

	protected $yield_types = [
        'live-to-dressed' => 'ltd', 
        'dressed-to-cutups' => 'dtc'
    ];
	protected $validation_errors = [];

	function __construct()
    {
        parent::__construct();
        if (!has_access('production')) show_error('Authorization error', 401);
        $this->set_active_nav(NAV_PRODUCTION);
        $this->set_content_title('Production');
        $this->set_content_subtitle('Process products');
        // $this->load->model('purchases/m_purchase_receiving', 'receiving');
        $this->load->model('production/yielding_model', 'yield');
    }

    function redirect()
    {
        $yield_types = array_flip($this->yield_types);
        $yielding_id = $this->input->get('id');
        $url_details = $this->yield->url_details($yielding_id);
        if($rr_no = $url_details['fk_purchase_receiving_id']){
            redirect("purchases/yielding/?type={$yield_types[$url_details['yield_type']]}&rr={$rr_no}");
        }
        redirect("production/yielding/edit/{$yielding_id}");
    }

    function master_list()
    {
        if($this->input->is_ajax_request()){
            $offset = $this->input->get('page');
            $page = $offset ? $offset : 1;
            $data = $this->yield->all($page, $this->search_params());
            $this->generate_response($data ? ['data' => $data] : [])->to_JSON();
        }
    }

    function search_params()
    {
        $this->load->helper('pmdate');
        $query = [];
        $params = elements(['id', 'start_date', 'end_date'], $this->input->get());
        if($params['id'] && is_numeric($params['id'])){
            $query['yield.id'] = $params['id'];
        }
        if($params['start_date'] && is_valid_date($params['start_date'], 'm/d/Y')){
            $query['yield.date >='] = date_create($params['start_date'])->format('Y-m-d');
        }
        if($params['end_date'] && is_valid_date($params['end_date'], 'm/d/Y')){
            $query['yield.date <='] = date_create($params['end_date'])->format('Y-m-d');
        }
        return empty($query) ? FALSE : $query;
    }

    function index()
    {
        $this->add_javascript(['plugins/moment.min.js', 'plugins/sticky-thead.js', 'production-yielding/master-list.js']);
    	$this->set_content('production/yielding/listing', [
    		'items' => []
		])->generate_page();
    }

    function create()
    {

        $yield_types =  array_keys($this->yield_types);
    	$type = $this->input->get('type');
        if(!in_array($type, $yield_types)){
            show_404();
        }
        $this->add_javascript(['numeral.js', 'price-format.js', "production-yielding/manage-{$this->yield_types[$type]}.js"]);
    	$this->load->model('inventory/m_product', 'product');

       
        $product_categories = [ 
        	M_Product::CATEGORY_FRESH_CHILLED_DRESSED_CHICKEN, 
        	M_Product::CATEGORY_CHICKEN_BYPRODUCTS, 
        	M_Product::CATEGORY_CHICKEN_CUTUPS,
        	M_Product::CATEGORY_LIVE_CHICKEN
    	];

        $products = $this->product->category_in($product_categories)->get_list(['product.status' => 'a']);

        $source_items = [];
        $result_items = [];

        foreach($products AS $item){

        	if($type === $yield_types[0]){

        		// if item is live, add to source list
        		if($item['fk_category_id'] == $product_categories[3]){
        			$source_items[] = $item;
        		}else if(in_array($item['fk_category_id'], [$product_categories[0], $product_categories[1]])){
        			$result_items[] = $item;
        		}

        	}else if($type === $yield_types[1]){

        		// if item is dressed, add to source list
        		if($item['fk_category_id'] == $product_categories[0]){
        			$source_items[] = $item;
        		}else if($item['fk_category_id'] == $product_categories[2]){
        			$result_items[] = $item;
        		}
        	}
        }

    	$this->set_content("production/yielding/manage-{$type}", [
    		'form_title' => "Further processing of products",
    		'form_action' => base_url("production/yielding/store?type={$type}"),
    		'source_items' => $source_items,
    		'result_items' => $result_items,
            'type' => $type,
            'yielding' => [],
            'data' => []	
		])->generate_page();
    }

    function _perform_validation()
    {
        $type = $this->input->post('yield_type');
        if(!in_array($type, array_keys($this->yield_types))){
            $this->validation_errors[] = "Yield type not valid.";
            return;
        }

        if(!is_array($yield = $this->input->post('yield'))){
            $this->validation_errors[] = "Malformed payload.";
            return;
        }

        if(!isset($yield['fk_inventory_product_id']) || !is_numeric($yield['fk_inventory_product_id'])){
        	$this->validation_errors[] = "Please select an item to process.";
        	return;
        }

        if(!isset($yield['quantity']) || !is_numeric($yield['quantity']) || floatval($yield['quantity']) <= 0){
        	$this->validation_errors[] = "Please provide quantity of product to process.";
        }

        if(!isset($yield['pieces']) || ($yield['pieces'] && !is_numeric($yield['pieces']))){
        	$this->validation_errors[] = "No. of pieces of the product to process must be numeric.";
        }

        if(!isset($yield['to']) || !is_array($yield['to'])){
            $this->validation_errors[] = "Cannot further process selected source product: Resulting products not given.";
           return;
        }

        // iterate through payload and validate
        foreach($yield['to'] AS $index => $produce){

        	$line = $index + 1;

            if(!is_array($produce)){
               $this->validation_errors[] =  "Cannot further process selected source product to resulting product in line # {$line}.";
               break;
            }

            if(!isset($produce['product_id']) || !is_numeric($produce['product_id'])){
                $this->validation_errors[] = "Provide resulting product in line # {$line}";
            }

            if(!isset($produce['quantity']) || !is_numeric($produce['quantity']) || floatval($produce['quantity']) <= 0){
                $this->validation_errors[] = "Provide quantity for resulting product in line # {$line}";
            }
            if(!isset($produce['pieces']) || ($produce['pieces'] && !is_numeric($produce['pieces']))){
                $this->validation_errors[] = "Provide pieces for resulting product in line # {$line}";
            }

        }
    }

    function _format_data($mode)
    {
        $input = $this->input->post();

        $data['yielding'] = [
            'remarks' => $input['remarks']
        ];

        if($mode === 'c'){
            $data['yielding'] += [
                'created_by' => user_id(),
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $data['source'] = elements(['fk_inventory_product_id', 'quantity'], $input['yield'], NULL);
        $data['source']['pieces'] = $input['yield']['pieces'] ?: NULL;
        $data['source']['unit_price'] = str_replace(',', '', $input['yield']['unit_price']);
       
        $data['source']['yield_type'] = $this->yield_types[$input['yield_type']];

        if(isset($input['yield']['id'])){
             $data['source']['id'] = $input['yield']['id'];
        }

        foreach($input['yield']['to'] AS $result){

            $temp = [
                'quantity' => $result['quantity'],
                'fk_inventory_product_id' => $result['product_id']
            ];

            $temp['pieces'] = $result['pieces'] ?: NULL;

            if(isset($result['id']) && $mode === 'u'){
                $temp['id'] = $result['id'];
            }  

            $data['results'][] = $temp;

        }

        return $data;

    }

    function store()
    {
    	$this->_perform_validation();

    	if(!empty($this->validation_errors)){
            
    		$this->generate_response(TRUE, $this->validation_errors)->to_JSON();
    		return;
    	}

        $data = $this->_format_data('c');

        $unavailable = $this->_check_item_availability(
            $data['source']['fk_inventory_product_id'],
            [ 'units' => $data['source']['quantity'], 'pieces' => $data['source']['pieces'] ]
        );

        if(!empty($unavailable)){
            $this->generate_response(TRUE, $unavailable)->to_JSON();
            return;
        }

        if($this->yield->create($data)){
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Product processing has been saved successfuly!');
            return;
        }
        $this->generate_response(TRUE)->to_JSON();
    }

    function update($id)
    {
        $this->_perform_validation();

        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }

        $data = $this->_format_data('u');

        $previous = $this->yield->get($id);

        $unavailable = $this->_check_item_availability(
            $data['source']['fk_inventory_product_id'],
            [ 'units' => $data['source']['quantity'], 'pieces' => $data['source']['pieces'] ],
            [ 'units' => $previous['source']['quantity'], 'pieces' => $previous['source']['pieces'] ]
        );

        if(!empty($unavailable)){
            $this->generate_response(TRUE, $unavailable)->to_JSON();
            return;
        }

        if($this->yield->update($id, $data)){
            $this->flash_message(FALSE, "Process # {$id} has been updated successfully!");
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE)->to_JSON();   
    }
    
    function edit($id = FALSE)
    {
        if(!$id || !$yielding = $this->yield->get($id)){
            show_404();
        }

        $allowed_types = array_flip($this->yield_types);

        $type = $yielding['source']['yield_type'];
        $yield_types =  array_keys($allowed_types);

        $this->add_javascript(['numeral.js', 'price-format.js', "production-yielding/manage-{$type}.js"]);
        $this->load->model('inventory/m_product', 'product');

       
        $product_categories = [ 
            M_Product::CATEGORY_FRESH_CHILLED_DRESSED_CHICKEN, 
            M_Product::CATEGORY_CHICKEN_BYPRODUCTS, 
            M_Product::CATEGORY_CHICKEN_CUTUPS,
            M_Product::CATEGORY_LIVE_CHICKEN
        ];

        $products = $this->product->category_in($product_categories)->get_list(['product.status' => 'a']);

        $source_items = [];
        $result_items = [];

        foreach($products AS $item){

            if($type === $yield_types[0]){

                // if item is live, add to source list
                if($item['fk_category_id'] == $product_categories[3]){
                    $source_items[] = $item;
                }else if(in_array($item['fk_category_id'], [$product_categories[0], $product_categories[1]])){
                    $result_items[] = $item;
                }

            }else if($type === $yield_types[1]){

                // if item is dressed, add to source list
                if($item['fk_category_id'] == $product_categories[0]){
                    $source_items[] = $item;
                }else if($item['fk_category_id'] == $product_categories[2]){
                    $result_items[] = $item;
                }
            }
        }

        $this->set_content("production/yielding/manage-{$allowed_types[$type]}", [
            'form_title' => "Update further processing of products # {$id}",
            'form_action' => base_url("production/yielding/update/{$id}"),
            'source_items' => $source_items,
            'result_items' => $result_items,
            'type' => $allowed_types[$type],
            'yielding' => [],
            'data' => $yielding
        ])->generate_page();
    }

    function _check_item_availability($product_id, $requested, $offset = FALSE)
    {   
        $unavailable = [];

        $this->load->model('inventory/m_product', 'product');
        $product_info = $this->product->identify([$product_id]);
        $stocks = $this->product->get_stocks($product_id);

        $product = $product_info[$product_id];
        unset($product_info);

        $available = ['units' => 0, 'pieces' => 0];

        if(isset($stocks[$product_id])){
            $available['units'] += $stocks[$product_id]['available_units'];
            $available['pieces'] += $stocks[$product_id]['available_pieces'];
        }

        if($offset !== FALSE){
            $available['units'] += $offset['units'];
            $available['pieces'] += $offset['pieces'];
        }
        
        $lacking = [];  

        if($requested['units'] > $available['units']){
            $lacking_units = $requested['units'] - $available['units'];
            $lacking[] = "{$lacking_units} {$product['unit_description']}";
        }

        if($requested['pieces'] > $available['pieces']){
            $lacking_pieces = abs($requested['pieces']) - $available['pieces'];
            $lacking[] = "{$lacking_pieces} pieces";
        }

        if(!empty($lacking)){
            $unavailable[] = "Lacking ". implode(' and ', $lacking). " for: {$product['description']}";
        }

        return $unavailable;

    }
}
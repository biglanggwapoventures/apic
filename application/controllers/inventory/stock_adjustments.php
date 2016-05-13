<?php

class Stock_adjustments extends PM_Controller_v2 
{

    public $url;
    private $validation_errors = [];
    private $id;

    function __construct()
    {
        parent::__construct();
        if(!has_access('inventory')) show_error('Authorization error', 401);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Stock adjustments');
        $this->set_active_nav(NAV_INVENTORY);
        $this->url = base_url('inventory/stock_adjustments');
        $this->load->model('inventory/m_product', 'product');
        $this->load->model('inventory/m_adjustments', 'adjustments');
    }

    function index()
    {
        $this->add_javascript(['stock-adjustments/master-list.js']);
        $this->set_content('inventory/stock-adjustments', [
            'data' => $this->adjustments->all()
        ]);
        $this->generate_page();
    }

    function create()
    {
        $this->add_javascript(['price-format.js', 'stock-adjustments/manage.js']);
        $products = $this->product->all(['p.status' => 'a']);
        $this->set_content('inventory/manage-stock-adjustment', [
            'form_action' => "{$this->url}/ajax_create",
            'form_title' => 'Create new stock adjustment request',
            'url' => $this->url,
            'products' => $products
        ])->generate_page();
    }

    function update($id = FALSE)
    {
        if(!$id || !$this->adjustments->is_valid($id)){
            show_404();
        }
        $this->add_javascript(['price-format.js', 'stock-adjustments/manage.js']);
        $products = $this->product->all(['p.status' => 'a']);
        $this->set_content('inventory/manage-stock-adjustment', [
            'form_action' => "{$this->url}/ajax_update/{$id}",
            'form_title' => 'Update stock adjustment request',
            'url' => $this->url,
            'products' => $products,
            'data' => $this->adjustments->get($id)
        ]);
        $this->generate_page();

    }

    function ajax_create()
    {
       $this->_perform_validation();

        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
       
        $data = $this->_format_data('c');

        if(isset($data['sa']['approved_by']) && $data['sa']['approved_by']){
            $unavailable = $this->_check_item_availability($data['details']);
            if(!empty($unavailable)){
                $this->generate_response(TRUE, $unavailable)->to_JSON();
                return;
            }
        }
       
        if($this->adjustments->create($data)){
           $this->flash_message(FALSE, 'New stock adjustment request has been created!');
           $this->generate_response(FALSE)->to_JSON();
           return;
        }
       
       $this->generate_response(TRUE)->to_JSON();

    }

    function ajax_update($id = FALSE)
    {
        if(!$id || !$this->adjustments->is_valid($id)){
            $this->generate_response(TRUE, ['The adjustment request you are trying to update does not exist.'])->to_JSON();
            return;
        }

        $this->_perform_validation();

        if(!empty($this->validation_errors)){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }
       
        $data = $this->_format_data('u');

        if(isset($data['sa']['approved_by']) && $data['sa']['approved_by']){
            $previous = $this->adjustments->get($id);
            $exclude = $previous['sa']['approved_by'] ? array_column($previous['details'], NULL, 'product_id') : [];
            $unavailable = $this->_check_item_availability($data['details'], $exclude);
            if(!empty($unavailable)){
                $this->generate_response(TRUE, $unavailable)->to_JSON();
                return;
            }
        }
       
        if($this->adjustments->update($id, $data)){
           $this->flash_message(FALSE, "Stock adjustment request # {$id} has been successfully updated!");
           $this->generate_response(FALSE)->to_JSON();
           return;
        }
       
       $this->generate_response(TRUE)->to_JSON();
    }

    function ajax_delete()
    {
        $id = $this->input->post('id');
        if(is_numeric($id) && $this->adjustments->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE)->to_JSON();
    }

    function _perform_validation()
    {
        $items = $this->input->post('items');
        if(empty($items)){
            $this->validation_errors[] =  "Please provide at least 1 (one) item for stock adjustment.";
            return;
        }
        foreach($items AS $index => $item){
            $line  = $index + 1;
            if(!is_array($item)){
                $this->validation_errors[] =  "Invalid item for line #{$line}";
                continue;
            }
            if(!isset($item['product_id']) || !is_numeric($item['product_id'])){
                $this->validation_errors[] = "Provide product for line #{$line}";
            }
            if(!isset($item['quantity']) || !is_numeric($item['quantity']) || floatval($item['quantity']) === 0){
                $this->validation_errors[] = "Provide product quantity for line #{$line}";
            }
            if(!isset($item['pieces']) || ($item['pieces'] && !is_numeric($item['pieces']))){
                $this->validation_errors[] = "No. of pieces for line #{$line} must be in numeric form";
            }
            if(!isset($item['unit_price']) || ($item['unit_price'] && !is_numeric($item['unit_price']))){
                $this->validation_errors[] = "Unit price for line #{$line} must be in numeric form";
            }
            if(!isset($item['remarks']) || !trim($item['remarks'])){
                $this->validation_errors[] = "Please provide remarks for line #{$line}";
            }
        }
    }

    function _format_data($mode)
    {
        $input = $this->input->post();
        if($mode === 'c'){
            $data['sa'] = [
                'created_at' => date('Y-m-d H:i'),
                'created_by' => user_id()
            ];
        }
        if(can_set_status()){
             $data['sa']['approved_by'] = isset($input['is_approved']) ? user_id() : NULL;
        }
        foreach($input['items'] AS $item){
            
            $temp = elements(['product_id', 'quantity', 'remarks'], $item, NULL);
            $temp['pieces']  = (float)$item['pieces'] ?: NULL;
            $temp['unit_price']  = (float)$item['unit_price'] ?: NULL;
            
            if(isset($item['id']) && $mode === 'u'){
                $temp['id'] = $item['id'];
            }
            $data['details'][] = $temp;
        }
        return $data;
    }

    function _check_item_availability($details, $exclude = [])
    {
        $unavailable = [];
        
        // retrieve only details with item stock reduction
        $items = array_filter($details, function($var){
            return $var['quantity'] < 0 || $var['pieces'] < 0;
        });
        
        if(empty($items)){
            return;
        }
        
        $product_ids = array_column($items, 'product_id');
        $product_details = $this->product->identify($product_ids);
        $stocks = $this->product->get_stocks($product_ids);

        unset($product_ids);

        foreach($items AS $item){

            $available = ['units' => 0, 'pieces' => 0];

            if(isset($stocks[$item['product_id']])){
                $available['units'] += $stocks[$item['product_id']]['available_units'];
                $available['pieces'] += $stocks[$item['product_id']]['available_pieces'];
            }

            if(isset($exclude[$item['product_id']])){

                if($exclude[$item['product_id']]['quantity'] < 0){
                    $available['units'] += abs($exclude[$item['product_id']]['quantity']);
                }else{
                    $available['units'] -= $exclude[$item['product_id']]['quantity'];
                }

                if($exclude[$item['product_id']]['pieces'] < 0){
                    $available['pieces'] += abs($exclude[$item['product_id']]['pieces']);
                }else{
                    $available['pieces'] -= $exclude[$item['product_id']]['pieces'];
                }
            }

            $requested = [ 'units' => $item['quantity'], 'pieces' => $item['pieces'] ];

            $lacking = [];  

            if($requested['units'] < 0 && $available['units'] < abs($requested['units'])){
                $lacking_units = abs($requested['units']) - $available['units'];
                $lacking[] = "{$lacking_units} {$product_details[$item['product_id']]['unit_description']}";
            }

            if($requested['pieces'] < 0 && $available['pieces'] < abs($requested['pieces'])){
                $lacking_pieces = abs($requested['pieces']) - $available['pieces'];
                $lacking[] = "{$lacking_pieces} pieces";
            }

            if(!empty($lacking)){
                $unavailable[] = "Lacking ". implode(' and ', $lacking). " for: {$product_details[$item['product_id']]['description']}";
            }
        }

        return $unavailable;
    }
}
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
            $unavailable = $this->_check_item_availability($data['details'], array_column($previous['details'], NULL, 'product_id'));
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
            
            $temp = elements(['product_id', 'quantity', 'pieces', 'unit_price', 'remarks'], $item, NULL);
            
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
        $items = array_filter($details, function($var){
            return $var['quantity'] < 0;
        });
        if(empty($items)){
            return;
        }
        $product_ids = array_column($items, 'product_id');
        $product_details = $this->product->identify($product_ids);
        $stocks = $this->product->get_stocks($product_ids);
        foreach($items AS $item){
            $item_stock = isset($stocks[$item['product_id']]) ? $stocks[$item['product_id']] : 0;
            if(isset($exclude[$item['product_id']])){
                if($exclude[$item['product_id']]['quantity'] < 0){
                    $item_stock += abs($exclude[$item['product_id']]['quantity']);
                }else{
                    $item_stock -= $exclude[$item['product_id']]['quantity'];
                }
            }
            $request_qty = abs($item['quantity']);
            if($item_stock < $request_qty){
                $lacking = $request_qty - $item_stock;
                $product_unit = $product_details[$item['product_id']]['unit_description'];
                $product_description = "{$product_details[$item['product_id']]['description']} [{$product_details[$item['product_id']]['code']}]";
                $unavailable[] = "Lacking {$lacking} {$product_unit} for: {$product_description}";
            }
        }
        return $unavailable;
    }
}
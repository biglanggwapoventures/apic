<?php

class Stock_adjustments extends PM_Controller_v2 
{

    public $url;
    private $validation_errors = [];
    private $id;

    public function __construct()
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

    public function index()
    {
        $this->add_javascript(['stock-adjustments/master-list.js']);
        $this->set_content('inventory/stock-adjustments', [
            'data' => $this->adjustments->all()
        ]);
        $this->generate_page();
    }

    public function create()
    {
        $this->add_javascript(['price-format.js', 'stock-adjustments/manage.js']);
        $products = $this->product->all();
        array_walk($products, function(&$var){
            $var['description'] = "[{$var['code']}] {$var['description']}";
        });
        // array_walk($products, function(&$var){
        //     $var['description'] = $var['description'].($var['formulation_code'] ? " [{$var['formulation_code']}]" : '');
        // });
        $this->set_content('inventory/manage-stock-adjustment', [
            'form_action' => "{$this->url}/ajax_create",
            'form_title' => 'New stock adjustment request',
            'url' => $this->url,
            'products' => $products
        ])->generate_page();
    }

    public function update($id = FALSE)
    {
        if(!$id || !$this->adjustments->is_valid($id)){
            show_404();
        }
        $this->add_javascript(['price-format.js', 'stock-adjustments/manage.js']);
        $products = $this->product->get(FALSE, [M_Product::PRODUCT_STATUS => 'Active']);
        array_walk($products, function(&$var){
            $var['description'] = $var['description'].($var['formulation_code'] ? " [{$var['formulation_code']}]" : '');
        });
        $this->set_content('inventory/manage-stock-adjustment', [
            'form_action' => "{$this->url}/ajax_update/{$id}",
            'form_title' => 'Update stock adjustment request',
            'url' => $this->url,
            'products' => $products,
            'data' => $this->adjustments->get($id)
        ]);
        $this->generate_page();

    }

    public function ajax_create()
    {
        $data = [];
        $input = $this->input->post();
        $this->validate($input);
        if(count($this->validation_errors) > 0){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }else{
            $data = $this->format($input);
            if($data['sa']['approved_by'] !== NULL){
                $unavailable = $this->_check_item_availability($data['details']);
                if(!empty($unavailable)){
                    $this->generate_response(TRUE, $unavailable)->to_JSON();
                    return;
                }
            }
        }
        $result = $this->adjustments->create($data);
        if($result){
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'New stock adjustment request has been created!')));
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, ['And unknown error has occured and the system cannot process the new request. Please try again later.'])->to_JSON();
    }

    public function ajax_update($id)
    {
        $data = [];
        if(!$id || !$this->adjustments->is_valid($id)){
            $this->generate_response(TRUE, ['The adjustment request you are trying to update does not exist.'])->to_JSON();
            return;
        }
        $this->id = $id;
        $input = $this->input->post();
        $this->validate($input);
        if(count($this->validation_errors) > 0){
            $this->generate_response(TRUE, $this->validation_errors)->to_JSON();
            return;
        }else{
            $data = $this->format($input, 'update');
            if($data['sa']['approved_by'] !== NULL){
                $previous = $this->adjustments->get($id);
                $unavailable = $this->_check_item_availability($data['details'], array_column($previous['details'], NULL, 'product_id'));
                if(!empty($unavailable)){
                    $this->generate_response(TRUE, $unavailable)->to_JSON();
                    return;
                }
            }
        }
        
        $result = $this->adjustments->update($id, $data);
        if($result){
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Stock adjustment request has been updated!')));
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, ['And unknown error has occured and the system cannot process the new request. Please try again later.'])->to_JSON();
    }

    public function ajax_delete()
    {
        $id = $this->input->post('id');
        if(is_numeric($id) && $this->adjustments->delete($id)){
            $this->generate_response(FALSE)->to_JSON();;
            return;
        }
        $this->generate_response(TRUE)->to_JSON();
    }

    public function validate($input)
    {
        if(!isset($input['quantity']) || $input['quantity'] !== array_filter($input['quantity'], 'is_numeric')){
            $this->validation_errors[] = 'Please enter valid adjustment quantities';
        }
        if(!isset($input['items']) || !is_array($input['items'])){
            $this->validation_errors[] = 'Please select at least one item to adjust';
        }else if($input['items'] !== array_filter($input['items'], 'is_numeric')){
            $this->validation_errors[] = 'Please select only valid items to adjust';
        }else if($input['items'] !== array_unique($input['items'])){
            $this->validation_errors[] = 'Please remove duplicate items';
        }
    }

    public function format($input, $mode ='create')
    {
        $data  = [
            'sa' => [],
            'details' => []
        ];
        if($mode === 'create'){
            $data['sa']['date'] = date('Y-m-d');
            $data['sa']['created_by'] = user_id();
        }
        if(can_set_status()){
            if(isset($input['is_approved']) && $input['is_approved']){
                $data['sa']['locked'] =  1;
                $data['sa']['approved_by'] =  user_id();
            }else{
                $data['sa']['locked'] =  0;
                $data['sa']['approved_by'] =  NULL;
            }
        }
        foreach($input['items'] AS $key => $value){
            $temp = [
                'product_id' => $value,
                'quantity' => isset($input['quantity'][$key]) ? $input['quantity'][$key] : 0,
                'remarks' => isset($input['remarks'][$key]) ? $input['remarks'][$key] : NULL,
            ];
            $temp['unit_price'] = isset($input['unit_price'][$key]) ? str_replace(',', '', $input['unit_price'][$key]) :0;
            if($mode === 'update' && isset($input['detail_id'][$key])){
                $temp['id'] = $input['detail_id'][$key];
            }
            $data['details'][] = $temp;
        }
        return $data;
    }

    public function _check_item_availability($details, $exclude = [])
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
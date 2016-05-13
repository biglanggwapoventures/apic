<?php

class Products extends PM_Controller_v2 {

    private $_nav;
    private $_subtitle;
    private $_additional_css;
    private $_additional_js;
    private $_subject;
    private $_defaults = array(
        'code' => '', 'description' => '', 'formulation_code' => '', 'fk_production_formulation_id' => FALSE, 'description' => '', 'class' => '', 'category' => '',
        'unit' => '', 'type' => '', 'reorder_level' => '', 'pricing_method' => '', 'status' => FALSE
    );

    public function __construct() {
        parent::__construct();
        if(!has_access('inventory')) show_error('Authorization error', 401);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Products');
        $this->set_active_nav(NAV_INVENTORY);
        $this->_additional_js = array('inventory-products.js');
        $this->load->model(array('inventory/m_unit', 'inventory/m_category', 'inventory/m_class', 'inventory/m_type', 'inventory/m_product'));
        // $this->set_data($this->_nav, $this->_subtitle, $this->_additional_css, $this->_additional_js);
    }

    private function dropdown_format($item_array, $value_key, $text_key, $first_entry = '') {
        $formatted = array();
        $formatted[''] = $first_entry;
        foreach ($item_array as $item) {
            $formatted[$item[$value_key]] = $item[$text_key];
        }
        return $formatted;
    }


    ///////////////////////////////////////////////////////////////

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['status', 'description', 'category'], $this->input->get(), FALSE);

        if($params['status'] && in_array($params['status'], ['a', 'ia'])){
            $search['p.status'] = $params['status'];
        }elseif($params['status'] === FALSE){
            $search['p.status'] = 'a';
        }

        if($params['description'] && trim($params['description'])){
            $wildcards['p.description'] = $params['description'];
        }

        if($params['category'] && is_numeric($params['category'])){
            $search['p.fk_category_id'] = $params['category'];
        }
        
        return compact(['search', 'wildcards']);
    }

    public function index() 
    {
        $this->add_javascript(['inventory-products/listing.js', 'plugins/sticky-thead.js']);

        $params = $this->_search_params();

        $products = $this->m_product->all($params['search'], $params['wildcards']);

        $stocks = $this->m_product->get_stocks(array_column($products, 'id'));

        foreach ($products AS &$item) {
            if(isset($stocks[$item['id']])){
                $item['available_units']  = $stocks[$item['id']]['available_units'];
                $item['available_pieces']  = $stocks[$item['id']]['available_pieces'];
            }
        }

        unset($stocks);

        $this->set_content('inventory/products/listing', [
            'items' => $products,
            'category' => array_column($this->m_category->all(['status'=>'a']), 'description', 'id')
        ])->generate_page();
    }

    public function create() 
    {

        $this->add_javascript('inventory-products/manage.js');

        $this->set_content('inventory/products/manage', [
            'title' => 'Create new product',
            'action' => base_url('inventory/products/store'),
            'units' => $this->m_unit->all(['status' => 'a']),
            'categories' => $this->m_category->all(['status' => 'a']),
            'data' => []
        ])->generate_page();
        
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$product = $this->m_product->find($id)){
            show_404();
        }
        $this->add_javascript('inventory-products/manage.js');
        $this->set_content('inventory/products/manage', [
            'title' => "Update product: {$product['description']}",
            'action' => base_url("inventory/products/update/{$id}"),
            'units' => $this->m_unit->all(['status' => 'a']),
            'categories' => $this->m_category->all(['status' => 'a']),
            'data' => $product
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $product = $this->_format_data();
            $this->m_product->create($product);
            $this->flash_message(FALSE, 'New product has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$product = $this->m_product->find($id)){
            $this->generate_response(TRUE, 'Please select a valid product to update.')->to_JSON();
            return;
        }
        if(!can_update($product)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $product = $this->_format_data();
            $this->m_product->update($id, $product);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$product = $this->m_product->find($id)){
            $this->generate_response(TRUE, 'Please select a valid product to delete.')->to_JSON();
            return;
        }
        if(!can_delete($product)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->m_product->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('code', 'product code', 'trim|required|is_unique[inventory_product.code]');
        }else{
            $this->form_validation->set_rules('code', 'product code', 'trim|required|callback__validate_product_code');
        }
        $this->form_validation->set_rules('description', 'product description', 'trim|required');
        $this->form_validation->set_rules('fk_unit_id', 'product unit', 'trim|required|callback__validate_product_unit');
        $this->form_validation->set_rules('fk_category_id', 'product category', 'trim|required|callback__validate_product_category');
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'category status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['code', 'description', 'fk_unit_id', 'fk_category_id', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_product_code($code)
    {
        $this->form_validation->set_message('_validate_product_code', 'The %s is already in use.');
        return $this->m_product->has_unique_code($code, $this->id);
    }

    public function _validate_product_unit($unit)
    {
        $this->form_validation->set_message('_validate_product_unit', 'Please provide a valid %s');
        return $this->m_unit->exists($unit, TRUE);
    }

    public function _validate_product_category($category)
    {
        $this->form_validation->set_message('_validate_product_category', 'Please provide a valid %s');
        return $this->m_category->exists($category, TRUE);
    }

    ///////////////////////////////////////////////////////////////

    function _index() {
        //generate table
        $filter = [];   
        $data['classes'] = [''=> 'All product classes', M_Product::CLASS_RAW => 'Raw materials', M_Product::CLASS_FINISHED => 'Finished products'];
        $data['default_keyword'] = $this->input->get('search_keyword');
        $data['default_class'] = $this->input->get('product_class');
        $data['default_status'] = $this->input->get('status');
        if($data['default_class'])
        {
            $filter[M_Product::PRODUCT_CLASS] = $data['default_class'];
        }
        if(!$data['default_status'])
        {
            $filter[M_Product::PRODUCT_STATUS] = 'Active';
        }
        else
        {
            if($data['default_status'] === 'active' || $data['default_status'] === 'inactive')
            {
                $filter[M_Product::PRODUCT_STATUS] = strtoupper($data['default_status']);
            }
        }
        $class = $data['default_class'] ?  : FALSE;
        $data['entries'] = $this->m_product->get($data['default_keyword'], $filter);
        $this->load_page($this->load->view('inventory/products', $data, TRUE));
    }

    private function validate() {
        $form_validation = FALSE;
        if ($this->input->post('class')) {
            $class = $this->input->post('class');
            if ($class == 1) {
                $form_validation = 'raw_product';
            } else {
                $form_validation = 'finished_product';
            }
        }
        if ($this->form_validation->run($form_validation)) {
            $product_data = $this->input->post();
            //$product_data['fk_pricing_method_id'] = $product_data['pricing_method'];
            if(is_admin()){
                $product_data['status'] = isset($product_data['status']) ? 'Active' : 'Inactive';
            }
            $product_data['fk_class_id'] = $product_data['class'];
            $product_data['fk_unit_id'] = $product_data['unit'];
            if ($form_validation === 'finished_product') {
                $product_data['fk_category_id'] = $product_data['category'];
                $product_data['fk_type_id'] = $product_data['type'];
                $product_data['fk_production_formulation_id'] = $product_data['formulation_code'];
            } else {
                $product_data['fk_category_id'] = NULL;
                $product_data['fk_type_id'] = NULL;
            }
            unset($product_data['pricing_method'], $product_data['class'],$product_data['category'], $product_data['unit'], $product_data['type'], $product_data['formulation_code']);
            return $this->response(FALSE, '', $product_data);
        } else {
            return $this->response(TRUE, validation_errors('<li>', '</li>'));
        }
    }

    private function load_page_content($extras = array()) {
        $data = array();
        foreach ($extras as $e) {
            $data[$e['key']] = $e['value'];
        }
        /* update: 10-13-15: added dropdown for formulation code */
        $this->load->model('production/m_formulation', 'formulation');
        $formulations = $this->formulation->get();
        $data['formulations'] = $formulations;
        /* end update */ 
        $data['units'] = $this->dropdown_format($this->m_unit->get(), 'id', 'description');
        $data['categories'] = $this->dropdown_format($this->m_category->get(), 'id', 'description');
        $data['types'] = $this->dropdown_format($this->m_type->get(), 'id', 'description');
        $data['classes'] = $this->dropdown_format($this->m_class->get(), 'id', 'description');
        $data['pricing_methods'] = $this->dropdown_format($this->m_product->list_pricing_method(), 'id', 'description');
        $this->load_page($this->load->view('inventory/manage-product', $data, TRUE));
    }

    function add() {
        $class = 1;
        $extras = array();
        if ($this->input->post()) {
            $input = $this->validate();
            if ($input['error_flag']) {
                $extras[] = array('key' => 'validation_errors', 'value' => $input['message']);
                $extras[] = array('key' => 'defaults', 'value' => $this->input->post());
                $class = $class = $this->input->post('class');
            } else {
                $saved = $this->m_product->add($input['data']);
                if ($saved) {
                    $this->session->set_flashdata('form_submission_success', $this->m_message->add_success($this->_subject));
                    redirect('inventory/products');
                } else {
                    $extras[] = array('key' => 'validation_errors', 'value' => $this->m_message->add_error($this->_subject));
                }
            }
        } else {
            $extras[] = array('key' => 'defaults', 'value' => $this->_defaults);
        }
        $extras[] = array('key' => 'disabled', 'value' => ($class == $this->m_class->raw() ? "disabled = 'disabled'" : ""));
        $this->load_page_content($extras);
    }

    function _update($ID) {
        $class = 1;
        $extras = array();
        if ($this->input->post()) {
            $input = $this->validate();
            if ($input['error_flag']) {
                $extras[] = array('key' => 'validation_errors', 'value' => $input['message']);
                $temp = $this->input->post();
                $temp['fk_production_formulation_id'] = $temp['formulation_code'];
                $temp['id'] = $ID;
                $extras[] = array('key' => 'defaults', 'value' => $temp);
                $class = $this->input->post('class');
            } else {
                $saved = $this->m_product->update($ID, $input['data']);
                if ($saved) {
                    $this->session->set_flashdata('form_submission_success', $this->m_message->update_success($this->_subject));
                    redirect('inventory/products');
                } else {
                    $extras[] = array('key' => 'validation_errors', 'value' => $this->m_message->add_error($this->_subject));
                }
            }
        } else {
            $product_data = $this->m_product->get(FALSE, array(M_Product::PRODUCT_ID => $ID));
            $extras[] = array('key' => 'defaults', 'value' => $product_data[0]);
            $class = $product_data[0]['class'];
        }
        $extras[] = array('key' => 'mode', 'value' => 'edit');
        $extras[] = array('key' => 'disabled', 'value' => ($class == $this->m_class->raw() ? "disabled = 'disabled'" : ""));
        $this->load_page_content($extras);
    }

    function a_delete() {
        $this->form_validation->set_rules('pk', 'ID', 'required');
        if ($this->form_validation->run()) {
            $deleted = $this->m_product->delete($this->input->post('pk'));
            if ($deleted) {
                echo json_encode($this->response(FALSE, $this->m_message->delete_success($this->_subject)));
            } else {
                echo json_encode($this->response(TRUE, $this->m_message->delete_error($this->_subject), array()));
            }
        } else {
            echo json_encode($this->response(TRUE, $this->m_message->no_primary_key_error($this->_subject), array()));
        }
        exit();
    }

    public function a_get() {
        $response = '';
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header('400')->set_output('Error 400: Bad Request');
            return;
        }
        $this->output->set_status_header('200')->set_content_type('json');
        $get_by_id = $this->input->get('product_id') ? array('id' => $this->input->get($this->_subject)) : FALSE;
        $data = $this->m_product->get(FALSE, $get_by_id);
        if ($data) {
            $response = $this->response(FALSE, $this->m_message->data_fetch_success($this->_subject), $data);
        } else {
            $response = $this->response(TRUE, $this->m_message->data_fetch_error($this->_subject));
        }
        $this->output->set_output(json_encode($response));
        return;
    }

    public function getFinishedProducts() {
        $this->output->set_status_header('200')->set_content_type('json');
        $products = $this->m_product->get(FALSE, array(M_Product::PRODUCT_CLASS => M_Product:: CLASS_FINISHED));
        if ($products) {
            echo json_encode($this->response(FALSE, 'Successfull data fetching.', $products));
            exit();
        }
        echo json_encode($this->response(TRUE, 'No data retrieved.'));
        exit();
    }

    public function getRawProducts() {
        $this->output->set_status_header('200')->set_content_type('json');
        $products = $this->m_product->get(FALSE, array(M_Product::PRODUCT_CLASS => M_Product:: CLASS_RAW));
        if ($products) {
            echo json_encode($this->response(FALSE, 'Successfull data fetching.', $products));
            return;
        }
        echo json_encode($this->response(TRUE, 'No data retrieved.'));
        return;
    }

    public function validate_cost_method($val)
    {
        $methods = ['fifo', 'ave'];
        $this->form_validation->set_message('validate_cost_method', 'Please enter a valid %s');
        return in_array($val, $methods);
    }

    public function validate_status($val = FALSE)
    {
        $this->form_validation->set_message('validate_status', 'Only admin can product statuses.');
        if($val)
        {
            return is_admin();
        }
        return TRUE;
    }

}

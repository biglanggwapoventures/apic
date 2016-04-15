<?php

class Categories extends PM_Controller_v2
{

	public function __construct()
	{
		parent::__construct();
        if(!has_access('inventory')) show_error('Authorization error', 401);
        $this->set_content_title('Inventory');
        $this->set_content_subtitle('Categories');
        $this->set_active_nav(NAV_INVENTORY);
        $this->load->model('inventory/m_category', 'category');
	}

    public function _search_params()
    {
        $search = [];
        $wildcards = [];

        $params = elements(['status', 'description', 'type'], $this->input->get(), FALSE);

        if($params['status'] && in_array($params['status'], ['a', 'ia'])){
            $search['status'] = $params['status'];
        }elseif($params['status'] === FALSE){
            $search['status'] = 'a';
        }

        if($params['type'] && in_array($params['type'], array_keys(category_types()))){
            $search['type'] = $params['type'];
        }

        if($params['description'] && trim($params['description'])){
            $wildcards['description'] = $params['description'];
        }
        
        return compact(['search', 'wildcards']);
    }

	public function index() 
	{
        $this->add_javascript(['inventory-categories/listing.js', 'plugins/sticky-thead.js']);
    
        $params = $this->_search_params();

        $this->set_content('inventory/categories/listing', [
            'items' => $this->category->all($params['search'], $params['wildcards'])
        ])->generate_page();
    }

    public function create() 
    {
        $this->add_javascript('inventory-categories/manage.js');
        $this->set_content('inventory/categories/manage', [
            'title' => 'Create new category',
            'action' => base_url('inventory/categories/store'),
            'data' => []
        ])->generate_page();
    }

    public function edit($id = FALSE)
    {
        if(!$id || !$category = $this->category->get($id)){
            show_404();
        }
        $this->add_javascript('inventory-categories/manage.js');
        $this->set_content('inventory/categories/manage', [
            'title' => "Update category: {$category['description']}",
            'action' => base_url("inventory/categories/update/{$id}"),
            'data' => $category
        ])->generate_page();
    }

    public function store()
    {
        $this->set_action('new');
        $this->_perform_validation();

        if($this->form_validation->run()){
            $category = $this->_format_data();
            $this->category->create($category);
            $this->flash_message(FALSE, 'New category has been created sucessfully!');
            $this->generate_response(FALSE)->to_JSON();
            return;
        }

        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function update($id = FALSE)
    {

        if(!$id || !$category = $this->category->get($id)){
            $this->generate_response(TRUE, 'Please select a valid category to update.')->to_JSON();
            return;
        }
        if(!can_update($category)){
            $this->generate_response(TRUE, 'You are not allowed to perform the desired action.')->to_JSON();
            return;
        }
        $this->id = $id;
        $this->_perform_validation();
        if($this->form_validation->run()){
            $category = $this->_format_data();
            $this->category->update($id, $category);
            $this->generate_response(FALSE)->to_JSON();
            $this->flash_message(FALSE, 'Update successful!');
            return;
        }
        $this->generate_response(TRUE, $this->form_validation->errors())->to_JSON();
    }

    public function delete($id)
    {
        if(!$id || !$category = $this->category->get($id)){
            $this->generate_response(TRUE, 'Please select a valid category to delete.')->to_JSON();
            return;
        }
        if(!can_delete($category)){
            $this->generate_response(TRUE, 'Cannot perform action')->to_JSON();
            return;
        }
        if($this->category->delete($id)){
            $this->generate_response(FALSE)->to_JSON();
            return;
        }
        $this->generate_response(TRUE, 'Cannot perform action due to an unknown error. Please try again later.')->to_JSON();
    }

    public function _perform_validation()
    {
        if($this->action('new')){
            $this->form_validation->set_rules('description', 'category description', 'trim|required|is_unique[inventory_category.description]');
        }else{
            $this->form_validation->set_rules('description', 'category description', 'trim|required|callback__validate_category_description');
        }
        $this->form_validation->set_rules('type', 'category type', 'trim|required|in_list[fg,rm,l,di]', ['in_list' => 'Please provide a valid %s']);
        if(can_set_status()){
            $this->form_validation->set_rules('status', 'category status', 'trim|required|in_list[a,ia]', ['in_list' => 'Please provide a valid %s']);
        }
        
    }

    public function _format_data()
    {
        $input = elements(['description', 'type', 'status'], $this->input->post());
        if(!can_set_status()){
           unset($input['status']);
        }
        return $input;
    }

    public function _validate_category_description($description)
    {
        $this->form_validation->set_message('_validate_category_description', 'The %s is already in use.');
        return $this->category->has_unique_description($description, $this->id);
    }
}
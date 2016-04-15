<?php

class Suppliers extends PM_Controller_v2 {

    protected $allowed_fields = ['name', 'tin_number', 'address', 'contact_number', 'contact_person', 'added_by'];
    protected $required_fields = ['name', 'contact_number', 'address'];
    protected $unique_fields = ['name'];

    public function __construct() {
        parent::__construct();
        $this->set_active_nav(NAV_MAINTAINABLE);
        $this->set_content_title('Maintainable');
        $this->set_content_subtitle('Suppliers');
        $this->load->model('maintainable/m_supplier', 'supplier');
    }

    public function index() {
        $this->add_css(['bootstrap-editable.css']);
        $this->add_javascript(['bootstrap-editable.min.js']);
        $this->setTabTitle('Maintainable :: Suppliers');
        $this->set_content('maintainable/suppliers', [
            'listing' => $this->supplier->all()
        ]);
        $this->generate_page();
    }

    public function ajax_add() {
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('name', 'Supplier Name', 'required|callback__verify_unique_insert');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('contact_number', 'Contact Number', 'required');
        if ($this->form_validation->run()) {
            $data = elements($this->allowed_fields, $this->input->post(), NULL);
            $data['added_by'] = $this->session->userdata('user_id');
            $insert_id = $this->supplier->insert($data);
            if ($insert_id) {
                $response = $this->response(FALSE, 'New supplier added.', ['id' => $insert_id]);
            } else {
                $response = $this->response(TRUE, 'Error while trying to add. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

    public function ajax_update() {
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('value', 'Value', 'required|callback__verify_value|callback__verify_unique_update');
        $this->form_validation->set_rules('pk', 'ID', 'required|integer');
        $this->form_validation->set_rules('name', 'Field', 'required|callback__verify_field');
        $this->form_validation->set_message('required', 'This field is required.');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $new_supply = $this->supplier->update([$data['name'] => $data['value']], $data['pk']);
            if ($new_supply) {
                $response = $this->response(FALSE, 'Supplier updated.');
            } else {
                $response = $this->response(TRUE, 'Error while trying to update. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

    public function ajax_delete() {
        $this->output->set_content_type('json');
        $this->form_validation->set_rules('id', 'ID', 'required|integer');
        if ($this->form_validation->run()) {
            $deleted = $this->supplier->delete($this->input->post('id'));
            if ($deleted) {
                $response = $this->response(FALSE, 'Chart deleted.');
            } else {
                $response = $this->response(TRUE, 'Error while trying to delete. Please try again.');
            }
            $this->output->set_output(json_encode($response));
        } else {
            $this->output->set_output(json_encode($this->response(TRUE, 'Validation errors.', $this->form_validation->error_array())));
        }
    }

    public function assign_materials($supplier_id) {
        $this->setTabTitle('Assign supplier materials');
        $this->load->helper('view');
        $this->load->model('inventory/m_product', 'product');
        $this->set_content('maintainable/supplier-materials', [
            'supplier' => $this->supplier->get($supplier_id),
            'options' => $products = $this->product->get_list(),
            'assigned' => $this->supplier->get_assigned_supplies($supplier_id)
        ]);
        $this->generate_page();
    }

    public function save_assigned_materials($supplier_id) {
        $data = [];
        $ids = $this->input->post('id');
        $material_ids = $this->input->post('fk_inventory_product_id');
        foreach ($material_ids as $key => $value) {
            $temp = [ 'fk_inventory_product_id' => $value, 'fk_maintainable_supplier_id' => $supplier_id];
            if (isset($ids[$key])) {
                $temp['id'] = $ids[$key];
            }
            $data[] = $temp;
        }
        $saved = $this->supplier->save_assigned_materials($supplier_id, $data);
        $this->session->set_flashdata('save_success', ['result' => $saved]);
        redirect("maintainable/suppliers/assign_materials/{$supplier_id}");
    }

    /* callback validation */

    function _verify_field($field_name) {
        if (in_array($field_name, $this->allowed_fields)) {
            return TRUE;
        }
        $this->form_validation->set_message('_verify_field', sprintf('Unknown %s field.', $field_name));
        return FALSE;
    }

    function _verify_value($value) {
        $field = $this->input->post('name');
        if (in_array($field, $this->required_fields) && !trim($value)) {
            $this->form_validation->set_message('_verify_value', sprintf('The %s field is required.', $field));
            return FALSE;
        }
        return TRUE;
    }

    function _verify_unique_update($value) {
        $field = $this->input->post('name');
        if (in_array($field, $this->unique_fields) && !$this->supplier->is_unique($field, $value)) {
            $this->form_validation->set_message('_verify_unique', sprintf('The %s must be unique.', $field));
            return FALSE;
        }
        return TRUE;
    }

    function _verify_unique_insert($value) {
        if ($this->supplier->is_unique('name', $value)) {
            return TRUE;
        }
        $this->form_validation->set_message('_verify_unique', 'The name must be unique.');
        return FALSE;
    }

}

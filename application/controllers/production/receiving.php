<?php

class Receiving extends PM_Controller_v2
{

    private $errors = [];

    public function __construct()
    {
        parent::__construct();
        if (!has_access('production')) {
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_PRODUCTION);
        $this->set_content_title('Production');
        $this->set_content_subtitle('Receiving Report');
        $this->load->model('production/m_receiving');
    }

    public function index()
    {
        $this->add_javascript(['plugins/sticky-thead.js', 'plugins/jquery.json2html.js', 'production-receiving/master-list.js']);
        $this->set_content('production/receiving');
        $this->generate_page();
    }

    public function create()
    {
        $this->add_javascript('production-receiving/manage.js');
        $this->load->model('production/m_job_order');
        $unreceived = $this->m_job_order->get_unreceived();
        $this->set_content('production/manage-receiving', [
            'form_title' => 'Create new receiving report',
            'form_action' => base_url('production/receiving/ajax_create'),
            'data' => [
                'unreceived' => $unreceived
            ]
        ]);
        $this->generate_page();
    }

    public function update($id)
    {
        if (!$this->m_receiving->exists($id)) {
            show_404();
        }
        $receiving_report = $this->m_receiving->get($id);
        $this->add_javascript('production-receiving/manage.js');
        $this->set_content('production/manage-receiving', [
            'form_title' => 'Update receiving report',
            'form_action' => base_url("production/receiving/ajax_update/{$id}"),
            'data' => [
                'rr' => $receiving_report
            ]
        ]);
        $this->generate_page();
    }

    public function ajax_create()
    {
        $this->validate();
        if (count($this->errors) > 0) {
            $this->generate_response(TRUE, $this->errors)->to_JSON();
            return;
        }
        $data = $this->format($this->input->post());
        $result = $this->m_receiving->create($data);
        if ($result) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, "New product receiving has been created! RR #{$result}")));
            $this->generate_response(FALSE)->to_JSON();
        }
    }

    public function ajax_update($id)
    {
        $this->validate('update');
        if (count($this->errors) > 0) {
            $this->generate_response(TRUE, $this->errors)->to_JSON();
            return;
        }
        $data = $this->format($this->input->post(), 'update');
        $result = $this->m_receiving->update($id, $data);
        if ($result) {
            $this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, "Receiving report #{$id} has been updated!")));
            $this->generate_response(FALSE)->to_JSON();
        }
    }

    public function ajax_master_list()
    {
        $parameters = elements(array('id', 'jo_no', 'date', 'page'), $this->input->get());
        $result = $this->m_receiving->all($parameters);
        $this->generate_response(FALSE, FALSE, $result)->to_JSON();
    }

    public function ajax_delete()
    {
        $id = $this->input->post('id');
        if (is_numeric($id)) {
            $this->m_receiving->delete($id);
            $this->generate_response(FALSE)->to_JSON();
        }
    }

    public function validate($mode = 'create')
    {
        $this->form_validation->set_rules('datetime', 'Date', 'required|callback_validate_datetime');
        if ($mode === 'create') {
            $this->form_validation->set_rules('jo_no', 'Production code', 'required|callback_validate_jo');
        }
        if (!$this->form_validation->run()) {
            foreach ($this->form_validation->error_array() AS $err) {
                $this->errors[] = $err;
            }
        }
    }

    public function validate_datetime($datetime)
    {
        $this->load->helper('pmdate');
        $this->form_validation->set_message('validate_datetime', 'Please enter valid %s');
        return is_valid_date($datetime, 'm/d/Y h:i:s A');
    }

    public function validate_jo($jo_no)
    {
        $this->load->model('production/m_job_order');
        $this->form_validation->set_message('validate_jo', 'Please choose valid %s');
        return $this->m_job_order->exists($jo_no);
    }

    public function format($input, $mode = 'create')
    {
        $data['receiving'] = [
            'datetime' => date('Y-m-d h:i:s A', strtotime($input['datetime'])),
            'remarks' => $input['remarks']
        ];
        if ($this->session->userdata('type_id') == M_Account::TYPE_ADMIN) {
            if (isset($input['is_approved'])) {
                $data['receiving']['approved_by'] = $this->session->userdata('user_id');
            } else {
                $data['receiving']['approved_by'] = NULL;
            }
        }
        if ($mode === 'create') {
            $data['receiving']['jo_no'] = $input['jo_no'];
            $data['receiving']['created_by'] = $this->session->userdata('user_id');
        }
        $data['details'] = [];
        foreach ($input['jo_detail_id'] AS $index => $value) {
            $temp = [
                'jo_detail_id' => $value,
                'quantity' => $input['quantity'][$index]
            ];
            if ($mode === 'update' && isset($input['id'][$index])) {
                $temp['id'] = $input['id'][$index];
            }
            $data['details'][] = $temp;
        }

        return $data;
    }

}

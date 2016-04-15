<?php

class Agents extends PM_Controller {

    const TITLE = 'Sales';
    const SUBTITLE = 'Agents';
    const SUBJECT = 'agent';

    private $viewpage_settings = array();

    public function __construct() {
        parent::__construct();
        /*restrict unauthorized access*/
        if(!has_access('sales')){
            show_error('Authorization error', 401);
        }
        $this->set_active_nav(NAV_SALES);
        $this->set_content_title(self::TITLE);
        $this->set_content_subtitle(self::SUBTITLE);
        $this->add_javascript(array('price-format.js', 'sales-agents.js'));
        $this->load->model(array('sales/m_agent'));
        $this->viewpage_settings['defaults'] = array(
            'name' => '',
            'area' => '',
            'unit_quantity' => '',
            'fk_inventory_unit_id' => '',
            'amount' => 0
        );
    }

    private function validate() {
        $this->form_validation->set_rules('name', 'Agent Name', 'required');
        $this->form_validation->set_rules('area', 'Agent Area', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $data['amount'] = str_replace(",", "", $data['amount']);
            return $this->response(FALSE, '', $data);
        } else {
            return $this->response(TRUE, validation_errors('<li>', '</li>'));
        }
    }

    public function index() {
        $this->viewpage_settings['url'] = base_url('sales/agents');
        $this->viewpage_settings['default_keyword'] = $this->input->get('search_keyword');
        $this->viewpage_settings['entries'] = $this->m_agent->get($this->viewpage_settings['default_keyword']);
        $this->set_content('sales/agents', $this->viewpage_settings);
        $this->generate_page();
    }

    public function add() {
        $saved = FALSE;
        $this->load->helper(array('view'));
        $this->load->model(array('inventory/m_unit'));
        $this->viewpage_settings['units'] = dropdown_format($this->m_unit->get(), 'id', array('description'), '-Choose unit-');
        $this->viewpage_settings['url'] = base_url('sales/agents/add');
        $this->viewpage_settings['form_title'] = 'Add new sales agent';

        if ($this->input->post()) {
            $input = $this->validate();
            if ($input['error_flag']) {
                $this->viewpage_settings['validation_errors'] = $input['message'];
                $this->viewpage_settings['defaults'] = $this->input->post();
            } else {
                $saved = $this->m_agent->add($input['data']);
            }
            if ($saved) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->add_success(self::SUBJECT));
                redirect('sales/agents');
            } else {
                $error = !$this->viewpage_settings['validation_errors'] ? '<li>' . $this->m_message->add_error(self::SUBJECT) . '</li>' : $this->viewpage_settings['validation_errors'] . '</li>';
                $this->viewpage_settings['validation_errors'] = $error;
            }
        }
        $this->set_content('sales/manage-agent', $this->viewpage_settings);
        $this->generate_page();
    }

    public function update($agent_id) {
        $agent_info = $this->m_agent->get(FALSE, array(M_Agent::AGENT_ID => $agent_id));
        $saved = FALSE;
        $this->load->helper(array('view'));
        $this->load->model(array('inventory/m_unit'));
        $this->viewpage_settings['defaults'] = $agent_info[0];
        $this->viewpage_settings['units'] = dropdown_format($this->m_unit->get(), 'id', array('description'), '-Choose unit-');
        $this->viewpage_settings['url'] = base_url("sales/agents/update/{$agent_id}");
        $this->viewpage_settings['form_title'] = 'Update sales agent';
        if ($this->input->post()) {
            $input = $this->validate();
            if ($input['error_flag']) {
                $this->viewpage_settings['validation_errors'] = $input['message'];
                $this->viewpage_settings['defaults'] = $this->input->post();
            } else {
                $saved = $this->m_agent->update($agent_id, $input['data']);
            }
            if ($saved) {
                $this->session->set_flashdata('form_submission_success', $this->m_message->update_success(self::SUBJECT));
                redirect('sales/agents');
            } else {
                $this->viewpage_settings['defaults'] = $this->input->post();
                $error = !isset($this->viewpage_settings['validation_errors']) ? '<li>' . $this->m_message->update_error(self::SUBJECT) . '</li>' : $this->viewpage_settings['validation_errors'] . '</li>';
                $this->viewpage_settings['validation_errors'] = $error;
            }
        }
        $this->set_content('sales/manage-agent', $this->viewpage_settings);
        $this->generate_page();
    }

    function a_delete() {
        $this->form_validation->set_rules('pk', 'ID', 'required');
        if ($this->form_validation->run()) {
            $deleted = $this->m_agent->delete($this->input->post('pk'));
            if ($deleted) {
                echo json_encode($this->response(FALSE, $this->m_message->delete_success(self::SUBJECT)));
            } else {
                echo json_encode($this->response(TRUE, $this->m_message->delete_error(self::SUBJECT), array()));
            }
        } else {
            echo json_encode($this->response(TRUE, $this->m_message->no_primary_key_error(self::SUBJECT), array()));
        }
        exit();
    }

}

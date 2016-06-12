<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __Construct() {
        parent::__Construct();
        //load the language file
        $this->lang->load('login');
    }

    private function _response($error_flag = TRUE, $response_message = '', $data = array()) {
        return array(
            'error_flag' => $error_flag,
            'message' => $response_message,
            'data' => $data
        );
    }

    public function index() {
        if (!$this->session->userdata('user_id')) {
            $this->load->view('login');
        } else {
            redirect('home');
        }
    }

    public function _curl_request($token){
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER  =>  1,
            CURLOPT_URL             => 'http://localhost:3000',
            CURLOPT_USERAGENT       =>  '',
            CURLOPT_POST            =>  1,
            CURLOPT_POSTFIELDS      =>  'token='.$token,
            CURLOPT_HTTPHEADER      =>  [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function a_do_login() {
        $response = '';
        $this->output->set_content_type('json');
        //make form validation rules
        $this->form_validation->set_rules('username', 'Username', 'required|callback__validate_username|callback_validate_not_locked');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run()) {
            $authentic = $this->m_account->is_authentic($this->input->post('username'), $this->input->post('password'));
            if ($authentic) {
                $response = $this->_response(FALSE, $this->lang->line('authentic_login'));
                $mod_access = $this->m_account->get_module_access($authentic['id']);
                $jwt = json_decode($this->_curl_request($authentic['shared_token']), true);
                $this->session->set_userdata([
                    'username' => $this->input->post('username'),
                    'name'    => $authentic['name'],
                    'type_id' => $authentic['typeid'],
                    'user_id' => $authentic['id'],
                    'role' => $authentic['role'],
                    'module_access' => empty($mod_access)?array():$mod_access,
                    'avatar' => $authentic['Avatar'],
                    'shared_token' => $authentic['shared_token'],
                    'curl_data' => $jwt
                ]);
            } else {
                $response = $this->_response(TRUE, array(array('password' => $this->lang->line('err_invalid_password'))));
            }
        } else {
            $error_messages = array();
            if (form_error('username')) {
                $error_messages[]['username'] = form_error('username', ' ', ' ');
            }
            if (form_error('password')) {
                $error_messages[]['password'] = form_error('password', ' ', ' ');
            }
            $response = $this->_response(TRUE, $error_messages);
        }
        $this->output->set_output(json_encode($response));
    }

    function do_logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    //form validation callback functions
    function _validate_username($username) {
        if ($this->m_account->is_existing($username)) {
            return TRUE;
        }
        $this->form_validation->set_message('_validate_username', $this->lang->line('err_invalid_username'));
        return FALSE;
    }
    public function validate_not_locked($username)
    {
        $this->load->model('m_user','user');
        $this->form_validation->set_message('validate_not_locked', 'Your account is suspended. Please contact administrator');
        return $this->user->is_locked($username) === FALSE;
    }

}

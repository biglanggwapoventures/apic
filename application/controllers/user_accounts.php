<?php 

class User_accounts extends PM_Controller_v2
{

	private $validation_errors = [];

	private $filename = NULL;

	public function __construct()
	{
		parent::__construct();
		if(!is_admin())
		{
			show_error('Authorization error', 401);
			return;
		}
		$this->set_active_nav(NAV_USERS);
		$this->set_content_title('User accounts');
		$this->set_content_subtitle('Manage user accounts');
		$this->load->model('m_user', 'user');
	}

	public function index()
	{
		$this->add_javascript(['users/master-list.js']);
		$this->set_content('users', [
			'users' => $this->user->all()
		]);
		$this->generate_page();
	}

	public function create()
	{
		$this->add_javascript(['jquery.form.min.js', 'users/manage.js']);
		$this->set_content('manage-users', [
			'form_action' => base_url("user_accounts/ajax_create")
		]);
		$this->generate_page();
	}

	public function update($id)
	{
		$this->add_javascript(['jquery.form.min.js', 'users/manage.js']);
		$this->set_content('manage-users', [
			'data' => $this->user->get($id, TRUE),
			'form_action' => base_url("user_accounts/ajax_update/{$id}")
		]);
		$this->generate_page();
	}

	public function get_user($id){
		echo json_encode($this->user->get($id, FALSE));
	}

	private function update_session($id){
		if($id != $this->session->userdata('user_id')) return;
		$user = $this->user->get($id);
		$this->session->set_userdata('name', $user['FirstName']." ".$user['LastName']);
		$this->session->set_userdata('type_id', $user['TypeID']);
		$this->session->set_userdata('role', $user['role']);
		$this->session->set_userdata('avatar', $user['Avatar']);
	}

	public function ajax_update($id)
	{
		$this->validate('update');
		if(!empty($this->validation_errors))
		{
			$this->generate_response(TRUE, $this->validation_errors)->to_JSON();
		}
		else
		{
			$user = $this->format($this->input->post(), 'update');
			$updated = $this->user->update($id, $user);
			if($updated)
			{
				$this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, "User has been successfully updated!")));
				$this->generate_response(FALSE)->to_JSON();
				$this->update_session($id);
				return;
			}
			$this->generate_response(TRUE, ['Unable to update user. Please try again later.'])->to_JSON();
		}
	}

	public function ajax_create()
	{
		$this->validate();
		if(!empty($this->validation_errors))
		{
			$this->generate_response(TRUE, $this->validation_errors)->to_JSON();
		}
		else
		{
			$user = $this->format($this->input->post());
			$created = $this->user->create($user);
			if($created)
			{
				$this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, "Successfully created new user: {$user['user']['username']}")));
				$this->generate_response(FALSE)->to_JSON();
				return;
			}
			$this->generate_response(TRUE, ['Unable to create new user. Please try again later.'])->to_JSON();
		}
	}


	public function ajax_delete()
	{
		if($this->user->delete($this->input->post('id')))
		{
			$this->generate_response(FALSE)->to_JSON();
			return;
		}
		$this->generate_response(TRUE)->to_JSON();
		
	}

	public function validate($mode = 'create')
	{
		if($mode === 'create')
		{
			$this->form_validation->set_rules('username', 'Username', 'required|is_unique[account.username]');
		}
		$this->form_validation->set_rules('firstname', 'First name', 'required|callback_is_alpha_space');
		$this->form_validation->set_rules('lastname', 'Last name', 'required|callback_is_alpha_space');
		$this->form_validation->set_rules('email', 'Email address', 'valid_email');
		$this->form_validation->set_rules('lock', '', 'callback_validate_lock_action');
		$this->form_validation->set_rules('role', '', 'callback_validate_role');
		if($this->input->post('password') || $mode === 'create')
		{
			$this->form_validation->set_rules('password', 'Password', 'min_length[6]');
			$this->form_validation->set_rules('confirm_password', 'Confirm password', 'required|matches[password]');
		}
		if($this->form_validation->run() === FALSE)
		{
			foreach($this->form_validation->error_array() AS $err)
			{
				$this->validation_errors[] = $err;
			}
		}
		else
		{
			if(isset($_FILES['dp']))
			{
				$this->do_upload();
			}
		}
		
	}

	public function format($input, $mode = 'create')
	{
		$data['user'] = [
			'FirstName' => $input['firstname'],
			'LastName' => $input['lastname'],
			'Email' => $input['email']
		];
		$data['user']['Locked'] = isset($input['lock']) ? 1 : 0;
		if(isset($input['type']))
		{
			$data['user']['TypeID'] = $input['type'];
			if($input['type'] == M_Account::TYPE_ADMIN){
				$data['user']['role'] = 'su';
			}
		}
		if(isset($input['password']) && trim($input['password']))
		{
			$data['user']['Password'] = md5($input['password']);
		}
		if($mode === 'create')
		{
			$data['user']['username'] = $input['username'];
		}
		if($this->filename !== NULL)
		{
			$data['user']['Avatar'] = $this->filename;
		}
		if(isset($input['type']) && $input['type'] == M_Account::TYPE_NORMAL)
		{
			$module = isset($input['module']) && is_array($input['module']) ? $input['module'] : [];
			$data['module_access'] = elements(['inventory', 'sales', 'purchases', 'production', 'accounting', 'reports'], $module, 0);
		}
		return $data;
	}


	public function do_upload()
	{
		$config['upload_path'] = 'assets/uploads';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '2048';
		$config['max_width']  = '2047';
		$config['max_height']  = '1536';
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('dp'))
		{
			foreach(explode(',', $this->upload->display_errors('',',')) AS $err)
			{
				if($err)
				{
					$this->validation_errors[] = $err;
				}
			}
			return;
		}
		$file = $this->upload->data('file_name');
		$this->filename = $file['file_name'];
		
	}

	public function is_alpha_space($val)
	{
		$this->form_validation->set_message('is_alpha_space', 'The %s should only contain alphabetic characters and spaces.');
		return ctype_alpha(str_replace(' ', '', $val));
	}

	public function validate_lock_action($val)
	{
		$this->form_validation->set_message('validate_lock_action', 'Only administrators can lock/unlock a user account.');
		if($val)
		{
			return is_admin();
		}
		return TRUE;
	}

	public function validate_role($val)
	{
		$this->form_validation->set_message('validate_role', 'Only administrators can set user account role.');
		$roles = [M_Account::TYPE_ADMIN,M_Account::TYPE_NORMAL];
		if($val)
		{
			return in_array($val, $roles) && is_admin();
		}
	}
}
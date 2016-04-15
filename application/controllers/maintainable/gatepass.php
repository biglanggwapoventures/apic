<?php

class Gatepass extends PM_Controller_v2
{

	CONST VIEW_PATH = 'maintainable/gatepass/';

	private $validation_errors = [];

	public function __construct()
	{
		parent::__construct();
		$this->set_active_nav(NAV_MAINTAINABLE);
		$this->set_content_title('Maintainable');
		$this->set_content_subtitle('Gatepass');
		$this->load->model('maintainable/m_gatepass', 'gatepass');
	}

	public function index()
	{
		$this->add_javascript(['printer/printer.js', 'maintainable/gatepass/master-list.js']);
		$this->setTabTitle('Gatepass');
		$this->set_content(self::VIEW_PATH.'listing');
		$this->generate_page();
	}

	public function create()
	{
		$this->add_javascript(['maintainable/gatepass/manage.js']);
		$this->load->model('sales/m_customer', 'customer');
		$this->load->model('sales/m_trucking', 'driver');
		$drivers = $this->driver->get();
		array_walk($drivers, function(&$var){$var['trucking_name'] = $var['trucking_name'].($var['plate_number'] ? "({$var['plate_number']})" : '');});
		$this->setTabTitle('Create new gatepass');
		$this->set_content(self::VIEW_PATH.'manage', [
			'form_title' => 'Create new gatepass',
			'form_action' => base_url('maintainable/gatepass/ajax_create'),
			'customers' => $this->customer->get(),
			'drivers' => $drivers
		]);
		$this->generate_page();
	}


	public function update($id = FALSE)
	{
		if($id === FALSE || !$this->gatepass->is_valid($id))
		{
			show_404();
		}
		$this->setTabTitle('Update gatepass');
		$this->add_javascript(['maintainable/gatepass/manage.js']);
		$this->load->model('sales/m_customer', 'customer');
		$this->load->model('sales/m_trucking', 'driver');
		$gatepass = $this->gatepass->get($id);
		$drivers = $this->driver->get();
		array_walk($drivers, function(&$var){$var['trucking_name'] = $var['trucking_name'].($var['plate_number'] ? "({$var['plate_number']})" : '');});
		$data = [
			'form_title' => 'Update gatepass',
			'form_action' => base_url("maintainable/gatepass/ajax_update/{$id}"),
			'gp' => $gatepass,
			'customers' => $this->customer->get(),
			'drivers' => $drivers
		];
		if($gatepass['type'] === 'pl')
		{
			$data['available'] = $this->gatepass->get_available($gatepass['customer_id']);
		}
		$this->set_content(self::VIEW_PATH.'manage', $data);
		$this->generate_page();
	}


	public function ajax_get()
	{
		$this->generate_response($this->gatepass->all())->to_JSON();
		return;
	}

	public function ajax_create()
	{
		$this->validate();
		if(!empty($this->validation_errors))
		{
			$this->generate_response(TRUE, array_values($this->validation_errors))->to_JSON();
			return;
		}
		$created = $this->gatepass->create($this->format());
		if($created)
		{
			$this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully created new gatepass!')));
			$this->generate_response(FALSE)->to_JSON();
		}
		else
		{
			$this->generate_response(TRUE, ['Unable to create new gatepass. Please try again later.'])->to_JSON();
		}
	}

	public function ajax_update($id)
	{
		if($id === FALSE || !$this->gatepass->is_valid($id))
		{
			$this->output->set_status_header('404');
			$this->generate_response(TRUE, ['Gatepass does not exist.'])->to_JSON();
			return;
		}
		$updated = $this->gatepass->update($id, $this->format('update', $id));
		if($updated)
		{
			$this->session->set_flashdata('FLASH_NOTIF', json_encode($this->response(FALSE, 'Successfully updated the gatepass')));
			$this->generate_response(FALSE)->to_JSON();
		}
		else
		{
			$this->generate_response(TRUE, ['Unable to update gatepass. Please try again later.'])->to_JSON();
		}
	}

	public function ajax_delete()
	{
		if(!is_admin())
		{
			$this->generate_response(TRUE, 'You are not authorized to delete the gatepass. Please contact administrator.')->to_JSON();
			return;
		}
		$id = $this->input->post('id');
		if(!$this->gatepass->is_valid($id))
		{
			$this->generate_response(TRUE, 'Gatepass does not exist.')->to_JSON();
			return;
		}
		if($this->gatepass->delete($id))
		{
			$this->generate_response(FALSE)->to_JSON();
			return;
		}
		$this->generate_response(TRUE, 'Failed to delete gatepass.')->to_JSON();
		return;
	}

	public function ajax_get_available($customer_id = FALSE)
	{
		if(!is_numeric($customer_id))
		{
			$this->generate_response(TRUE, 'Please select a valid customer.')->to_JSON();
			return;
		}
		$this->generate_response($this->gatepass->get_available($customer_id))->to_JSON();
	}

	public function validate()
	{
		$this->form_validation->set_rules('type', 'Gatepass type', 'required|callback_validate_type');
		$this->form_validation->set_rules('customer_id', 'Customer', 'callback_validate_customer');
		if($this->form_validation->run() === FALSE)
		{
			$this->validation_errors = array_values($this->form_validation->error_array());
		}
		return;
	}

	public function validate_type($val)
	{
		$this->form_validation->set_message('validate_type', 'Please select a valid %s');
		return $val && in_array($val, ['pl', 'others']);
	}

	public function validate_customer($val)
	{
		if($this->input->post('type') === 'others')
		{
			return TRUE;
		}
		$this->load->model('sales/m_customer', 'customer');
		$this->form_validation->set_message('validate_customer', 'Please select a valid %s');
		return $val && $this->customer->is_valid($val);
	}

	public function format($mode = 'create', $id = FALSE)
	{
		$input = $this->input->post();
		$data['gp']['remarks'] = $input['remarks'];

		$data['gp']['trucking'] = NULL;
		if(isset($input['trucking']) && $input['trucking'])
		{
			$data['gp']['trucking'] = $input['trucking'];
		}

		if($mode === 'create')
		{
			$data['gp']['type'] = $input['type'];
			$data['gp']['created_by'] = $this->session->userdata('user_id');
		}	
		
		if((isset($input['type']) && $input['type'] === 'pl') || ($id && $this->gatepass->get_type($id) === 'pl'))
		{
			if($mode === 'create')
			{
				$data['gp']['customer_id'] = $input['customer_id'];
			}
			$data['pls'] = $input['pl_id'];
 		}
 		else if((isset($input['type']) && $input['type'] === 'others') || ($id && $this->gatepass->get_type($id) === 'others'))
 		{
			$data['gp']['issued_to'] = $input['issued_to'];
 			foreach($input['items']['description'] AS $key => $value)
 			{
 				$data['items'][] = [
 					'description' => $value,
 					'quantity' => $input['items']['quantity'][$key]
 				];
 			}
 		}

		return $data;
	}

	public function ajax_print($id)
	{
		$this->load->view('printables/sales/gatepass2', [
			'details' => $this->gatepass->get_items($id)
		]);
	}
}
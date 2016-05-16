<?php

class Sales_agent_incentive_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function generate($agent_id, $start_date = FALSE, $end_date = FALSE)
	{
		if(!$agent_id){
			return [];
		}	
		$this->load->model('reports/collection_report_model', 'collection');
		$this->load->model('sales/m_agent', 'agent');

		$sales_agent = $this->agent->get($agent_id);

		return $this->collection->generate(FALSE, $agent_id, $start_date, $end_date);
	}


}
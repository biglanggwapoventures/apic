<?php

class Check_master_list_model extends CI_Model 
{
	function generate($bank_account, $start_check_number = FALSE, $end_check_number = FALSE)
	{
		if(!is_numeric($bank_account)) return [];

		$this->db->select('
			chk.check_number, 
			chk.check_date, 
			chk.amount,
			dsb.id AS purpose_id,
			IF(dsb.disbursement_type = "rr", "disbursement", "disbursement_others") AS purpose,
			IF(dsb.disbursement_type = "others" OR (dsb.payee IS NOT NULL AND dsb.payee != ""), payee, supplier.name) AS payee
		', FALSE)
			->from('purchase_disbursement_payments AS chk')
			->join('purchase_disbursement AS dsb', 'dsb.id = chk.fk_purchase_disbursement_id')
			->join('maintainable_suppliers AS supplier', 'supplier.id = dsb.fk_maintainable_supplier_id', 'left')
			->where('chk.fk_accounting_bank_account_id', $bank_account, FALSE);

		if(is_numeric($start_check_number)){
			$this->db->where('CAST(chk.check_number AS UNSIGNED) >=', $start_check_number, FALSE);
		}

		if(is_numeric($end_check_number)){
			$this->db->where('CAST(chk.check_number AS UNSIGNED) <=', $end_check_number, FALSE);
		}

		$disbursements = $this->db->get()->result_array();

		$this->db->select('
				chk.check_number,
				chk.check_date,
				chk.payee,
				chk.check_amount AS amount,
				chk.id AS purpose_id,
				"dummy_check" AS purpose
			', FALSE)
				->from('accounting_dummy_checks AS chk')
				->where('chk.bank_account', $bank_account, FALSE);


		if(is_numeric($start_check_number)){
			$this->db->where('CAST(chk.check_number AS UNSIGNED) >=', $start_check_number, FALSE);
		}

		if(is_numeric($end_check_number)){
			$this->db->where('CAST(chk.check_number AS UNSIGNED) <=', $end_check_number, FALSE);
		}

		$dummy_checks = $this->db->get()->result_array();

		$results = array_merge($disbursements, $dummy_checks);

		function sort_check_number($a, $b)
		{
		    return (int)$a['check_number'] - (int)$b['check_number'];
		}    

		usort($results, 'sort_check_number');

		return $results;

	}

	function update_check_number($type, $id, $check_number)
	{
		$action = [
			'disbursement' => [
				'table' => 'purchase_disbursement_payments',
				'pk_column_name' => 'fk_purchase_disbursement_id',
				'check_number_column_name' => 'check_number'
			],
			'disbursement_others' => [
				'table' => 'purchase_disbursement_payments',
				'pk_column_name' => 'fk_purchase_disbursement_id',
				'check_number_column_name' => 'check_number'
			],
			'dummy_check' => [
				'table' => 'purchase_disbursement_payments',
				'pk_column_name' => 'id',
				'check_number_column_name' => 'check_number'
			],
		];

		return $this->db->update($action[$type]['table'], [ $action[$type]['check_number_column_name'] => $check_number ], [ $action[$type]['pk_column_name'] => $id ]);
	}
}
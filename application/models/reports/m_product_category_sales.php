<?php

class M_product_category_sales extends CI_Model
{
	public $categories;


	public function __construct()
	{
		parent::__construct();
	}

	public function generate($start_date = FALSE, $end_date = FALSE, $categories = [], $sales_agent = FALSE)
	{
		if(empty($categories)){
			return [];
		}
		$this->db->where_in('id', $categories);
		$this->categories = $this->db->get('inventory_category')->result_array();
		// initialize data
		$data = [];
		$categories = array_column($this->categories, 'description', 'id');
		$results = $this->get($start_date, $end_date, $sales_agent);

		foreach($results AS $row){

			$value = ($row['product_unit_price'] - $row['product_discount']) * $row['product_quantity_kgs'];

			if(!isset($data[$row['customer_id']]['customer_id'])){
				$data[$row['customer_id']]['customer_id'] = $row['customer_id'];
			}

			if(isset($data[$row['customer_id']]['kilograms_total'])){
				$data[$row['customer_id']]['kilograms_total'] += $row['product_quantity_kgs'];
				$data[$row['customer_id']]['pieces_total'] += $row['product_quantity_pcs'];
				$data[$row['customer_id']]['value_total'] += $value;
			}else{
				$data[$row['customer_id']]['kilograms_total'] = $row['product_quantity_kgs'];
				$data[$row['customer_id']]['pieces_total'] = $row['product_quantity_pcs'];
				$data[$row['customer_id']]['value_total'] = $value;
			}	

			if(isset($data[$row['customer_id']][$categories[$row['category_id']]])){
				$data[$row['customer_id']][$categories[$row['category_id']]]['kilograms_total'] += $row['product_quantity_kgs'];
				$data[$row['customer_id']][$categories[$row['category_id']]]['pieces_total'] += $row['product_quantity_pcs'];
				$data[$row['customer_id']][$categories[$row['category_id']]]['value_total'] += $value;
			}else{
				$data[$row['customer_id']][$categories[$row['category_id']]]['kilograms_total'] = $row['product_quantity_kgs'];
				$data[$row['customer_id']][$categories[$row['category_id']]]['pieces_total'] = $row['product_quantity_pcs'];
				$data[$row['customer_id']][$categories[$row['category_id']]]['value_total'] = $value;
			}		
			
		}
		return $data;
		
	}

	public function get($start_date = FALSE, $end_date = FALSE, $sales_agent = FALSE)
	{
		$this->load->helper('pmdate');	
		$this->db->select('ip.fk_category_id AS category_id, so.fk_sales_customer_id AS customer_id, sdt.this_delivery AS product_quantity_kgs, sdt.delivered_units AS product_quantity_pcs, sod.unit_price AS product_unit_price, sod.discount AS product_discount');
		$this->db->from('sales_delivery AS sd');
		$this->db->join('sales_delivery_detail AS sdt', 'sdt.fk_sales_delivery_id = sd.id');
		$this->db->join('sales_order_detail AS sod', 'sod.id = sdt.fk_sales_order_detail_id');
		$this->db->join('sales_order AS so', 'so.id = sod.fk_sales_order_id');
		$this->db->join('inventory_product AS ip', 'ip.id = sod.fk_inventory_product_id');
		$this->db->where_in('ip.fk_category_id', array_column($this->categories, 'id'));	
		$this->db->where('sd.status', M_Status::STATUS_DELIVERED);
		
		if(is_numeric($sales_agent)){
			$this->db->where('so.fk_sales_agent_id', $sales_agent);
		}
		if(is_valid_date($start_date)){
			$this->db->where("DATE(sd.`date`) >= '{$start_date}'", FALSE, FALSE);
		}
		if(is_valid_date($end_date)){
			$this->db->where("DATE(sd.`date`) <= '{$end_date}'", FALSE, FALSE);
		}

		return $this->db->get()->result_array();
	}
}
<?php

class M_product_category_sales extends CI_Model
{
	public $categories;


	public function __construct()
	{
		parent::__construct();
	}

	public function generate($start_date = FALSE, $end_date = FALSE, $categories)
	{
		if(empty($categories)){
			return [];
		}
		$this->db->where_in('id', $categories);
		$this->categories = $this->db->get('inventory_category')->result_array();
		// initialize data
		$data = [];
		$categories = array_column($this->categories, 'description', 'id');
		$results = $this->get($start_date, $end_date);

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

	public function get($start_date = FALSE, $end_date = FALSE)
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
		if(is_valid_date($start_date)){
			$this->db->where("DATE(sd.`date`) >= '{$start_date}'", FALSE, FALSE);
		}
		if(is_valid_date($end_date)){
			$this->db->where("DATE(sd.`date`) <= '{$end_date}'", FALSE, FALSE);
		}

		return $this->db->get()->result_array();
	}


	public function get_manual($start_date = FALSE, $end_date = FALSE)
	{
		$this->load->helper('pmdate');	
		// get pl ids from date range
		$this->db->select('sd.id')->from('sales_delivery AS sd')->where('sd.status', M_Status::STATUS_DELIVERED);
		if(is_valid_date($start_date)){
			$this->db->where('sd.date >=', $start_date);
		}
		if(is_valid_date($end_date)){
			$this->db->where('sd.date <=', $end_date);
		}
		$pl_ids = array_column($this->db->get()->result_array(), 'id');
		// get order delivered items from from pl ids
		$this->db->select('fk_sales_order_detail_id AS order_detail_id, this_delivery AS product_quantity')->from('sales_delivery_detail')->where_in('fk_sales_delivery_id', $pl_ids);
		$delivery_details = $this->db->get()->result_array();
		unset($pl_ids);
		// get product id from delivered items
		$this->db->select('sales_order_detail.id, unit_price, discount, fk_inventory_product_id, fk_sales_customer_id')->from('sales_order_detail');
		$this->db->join('sales_order AS so', 'so.id = sales_order_detail.fk_sales_order_id');
		$this->db->where_in('sales_order_detail.id', array_unique(array_column($delivery_details, 'order_detail_id')));
		$ordered_items = array_column($this->db->get()->result_array(), NULL, 'id');
		// determine product info from product ids
		$this->db->select('product.id, u.quantity AS unit_q, product.fk_category_id')->from('inventory_product AS product');
		$this->db->join('inventory_unit AS u', 'u.id = product.fk_unit_id');
		$this->db->where_in('product.id', array_unique(array_column($ordered_items, 'fk_inventory_product_id')));
		$products = array_column($this->db->get()->result_array(), NULL, 'id');

		$data = [];
		$categories = array_column($this->categories, 'description', 'id');

		foreach($delivery_details AS &$row){

			$category_id = $products[$ordered_items[$row['order_detail_id']]['fk_inventory_product_id']]['fk_category_id'];

			if(isset($categories[$category_id])){
				$bags =  ($row['product_quantity'] * $products[$ordered_items[$row['order_detail_id']]['fk_inventory_product_id']]['unit_q']) / 50;
				$value = ($ordered_items[$row['order_detail_id']]['unit_price'] - $ordered_items[$row['order_detail_id']]['discount']) * $row['product_quantity'];

				$customer_id = $ordered_items[$row['order_detail_id']]['fk_sales_customer_id'];
				$category_id = $products[$ordered_items[$row['order_detail_id']]['fk_inventory_product_id']]['fk_category_id'];

				if(!isset($data[$customer_id]['customer_id'])){
					$data[$customer_id]['customer_id'] = $customer_id;
				}

				if(isset($data[$customer_id]['bags_total'])){
					$data[$customer_id]['bags_total'] += $bags;
					$data[$customer_id]['value_total'] += $value;
				}else{
					$data[$customer_id]['bags_total'] = $bags;
					$data[$customer_id]['value_total'] = $value;
				}	

				if(isset($data[$customer_id][$categories[$category_id]])){
					$data[$customer_id][$categories[$category_id]]['total_bags'] += $bags;
					$data[$customer_id][$categories[$category_id]]['value'] += $value;
				}else{
					$data[$customer_id][$categories[$category_id]]['total_bags'] = $bags;
					$data[$customer_id][$categories[$category_id]]['value'] = $value;
				}	
			}

			
		}

		return $data;
	}
}
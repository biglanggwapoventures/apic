<?php

class M_costing extends CI_Model
{
	public function purchase_receiving($product_id, $unit_price, $quantity, $receiving_detail_id)
	{
		$data = $this->db->select('SUM(remaining) AS remaining, cost', FALSE)
		->from('running_inventory')
		->where(['product_id' => $product_id, 'remaining >' => 0, 'in >', 0])
		->order_by('id', 'ASC')
		->get()->row_array();

		$remaining = isset($data['remaining']) ? $data['remaining'] : 0;
		$cost = isset($data['cost']) ? $data['cost'] : 0;

		$remaining_total  = $remaining * $cost;
		$additional = $quantity * $unit_price;

		$new_remaining_total = $remaining + $quantity;
		$new_cost = ($additional * $remaining_total) / $new_remaining_total;

		// update the costs of all remaining product
		$this->db->update('running_inventory', ['cost' => $new_cost], ['product_id' => $product_id, 'in > ' => 0, 'remaining >' => 0]);

		
		$this->db->insert('running_inventory', [
			'product_id' => $product_id,
			'quantity' => $quantity,
			'unit_price' => $unit_price,
			'cost' => $new_cost,
			'remaining' => $quantity,
			'purchase_receiving_detail_id' => $receiving_detail_id
		]);

	}
}	
<?php

class m_warehousing_product_receiving  extends CI_Model{
    
    public function createWarehousingProductReceiving($receiverID = false, $remarks = false, $datetime = false){
        $data = array(
            "receiver_ID"   => $receiverID,
            "remarks"  => $remarks,
            "datetime"   => $datetime,
        );
        $this->db->insert("pm_inventory_product_receiving",$data);
        return $this->db->insert_id();
    }
    public function insertProductReceivingProduct($products){
        if(empty($products));
        $this->db->start_cache(); 
        $this->db->flush_cache();
        $this->db->insert_batch("pm_inventory_product_receiving_product", $products);
        
        $this->db->flush_cache();
        $this->db->stop_cache(); 
    }
    public function retrieveWarehousingProductReceiving($ID = false, $receiverID = false,  $remarks = false, $datetime = false){
        $this->db->start_cache(); 
        $this->db->flush_cache();
        $this->db->select("*");
        $this->db->select("pm_inventory_product_receiving.ID");
        $this->db->from("pm_inventory_product_receiving");
        $this->db->join('pm_account', 'pm_inventory_product_receiving.receiver_ID=pm_account.ID','left');
        $this->db->order_by("pm_inventory_product_receiving.ID","asc");
        $this->db->group_by("pm_inventory_product_receiving.ID");
        
        $condition = array();
        $likeCondition = array();
        
        ($ID)    ? $condition["pm_inventory_product_receiving.ID"] =  $ID : null;
        ($receiverID)    ? $condition["pm_inventory_product_receiving.receiver_ID"] =  $receiverID : null;
        ($remarks)    ? $condition["pm_inventory_product_receiving.remarks"] =  $remarks : null;
        ($datetime)    ? $likeCondition["pm_inventory_product_receiving.datetime"] =  $datetime : null;
        (count($likeCondition) > 0) ? $this->db->like($likeCondition)   : null;
        (count($condition) > 0)     ? $this->db->where($condition)      : null;
        $result = $this->db->get();
        $this->db->flush_cache();
        
        if($result->num_rows() > 0){
            $result = $result->result_array();
            $IDs = $this->makeIDList($result);
            $this->db->flush_cache();
            $this->db->select("*");
            $this->db->select("pm_inventory_product.description, pm_inventory_unit.description as unit_description");
            $this->db->join("pm_inventory_product", " pm_inventory_product.ID = pm_inventory_product_receiving_product.product_ID");
            $this->db->join("pm_inventory_unit", " pm_inventory_unit.ID = pm_inventory_product.fk_unit_id");
            $this->db->where_in("pm_inventory_product_receiving_product.fk_product_receiving_ID",$IDs);
            $products =  $this->db->get("pm_inventory_product_receiving_product");
            if($products->num_rows() > 0){
                $products = $products->result_array();
                foreach($result as $resultKey => $resultValue){
                    if(!empty($products)){
                        $result[$resultKey]["products"] = array();
                        foreach($products as $productsKey => $productsValue){
                            if($resultValue["ID"] == $productsValue["fk_product_receiving_ID"]){
                                $result[$resultKey]["products"][] = $productsValue;
                                unset($products[$productsKey]);
                            }
                        }
                    }else{
                        $result[$resultKey] = false;
                    }
                }
            }else{
                $result = $result;
            }
            $this->db->flush_cache();
            $this->db->stop_cache();
            return ($ID)? $result[0] :  $result; 
        }else{
            $this->db->stop_cache();
            return false;
        }
    }
   
    public function makeIDList($entries){
        $IDs = array();
        foreach($entries as $value){
            $IDs[] = $value["ID"];
        }
        return $IDs;
    }
    
    
}
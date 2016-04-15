<?php

class m_production_job_order  extends CI_Model{
    
    public function createProductionJobOrder($receiverID = false, $remarks = false, $dateAssigned = false, $assignedTo = false){
        $data = array(
            "created_by"   => $receiverID,
            "remarks"  => $remarks,
            "date_assigned"   => $dateAssigned,
            "assigned_to" => $assignedTo
        );
        $this->db->insert("pm_production_job_order",$data);
        return $this->db->insert_id();
    }
    public function insertProductReceivingProduct($products){
        if(empty($products)){}
        $this->db->start_cache(); 
        $this->db->flush_cache();
        $this->db->insert_batch("pm_production_job_order_product", $products);
        $this->db->flush_cache();
        $this->db->stop_cache(); 
        return true;
    }
    public function insertProductMaterials($productID, $products){
        if(empty($products)){
            return false;
        }
        $this->db->start_cache(); 
        $this->db->flush_cache();
        $this->db->where_in("pm_production_job_order_product_material.fk_job_order_product_ID", $productID);
        $this->db->delete("pm_production_job_order_product_material");
        $this->db->flush_cache();
        $this->db->insert_batch("pm_production_job_order_product_material", $products);
        $this->db->flush_cache();
        $this->db->stop_cache(); 
        return true;
    }
    public function updateProductionJobOrder($ID, $quantity ){
        $this->db->start_cache(); 
        $this->db->flush_cache();
        $this->db->where("ID", $ID);
        $this->db->update("pm_production_job_order_product", array("quantity" => $quantity));
        $this->db->flush_cache();
        $this->db->stop_cache(); 
    }
    public function retrieveProductionJobOrder($ID = false, $receiverID = false,  $remarks = false, $dateAssigned = false, $assignedTo = false, $approvedBy = false){
        $this->db->start_cache(); 
        $this->db->flush_cache();
        $this->db->select("*");
        $this->db->select("pm_production_job_order.ID");
        $this->db->from("pm_production_job_order");
        $this->db->join('pm_account', 'pm_production_job_order.created_by=pm_account.ID','left');
        $this->db->order_by("pm_production_job_order.ID","asc");
        $this->db->group_by("pm_production_job_order.ID");
        
        $condition = array();
        $likeCondition = array();
        
        ($ID)    ? $condition["pm_production_job_order.ID"] =  $ID : null;
        ($receiverID)    ? $condition["pm_production_job_order.created_by"] =  $receiverID : null;
        if($approvedBy == ">"){
            $condition["pm_production_job_order.approved_by > "] = 0;
        }else{
            ($approvedBy)    ? $condition["pm_production_job_order.approved_by"] =  ($approvedBy == "zero") ? 0:$approvedBy : null;
        }
        ($remarks)    ? $condition["pm_production_job_order.remarks"] =  $remarks : null;
        ($dateAssigned)    ? $likeCondition["pm_production_job_order.date_assigned"] =  $dateAssigned : null;
        ($assignedTo)    ? $likeCondition["pm_production_job_order.assigned_to"] =  $assignedTo : null;
        (count($likeCondition) > 0) ? $this->db->like($likeCondition)   : null;
        (count($condition) > 0)     ? $this->db->where($condition)      : null;
        $result = $this->db->get();
        $this->db->flush_cache();
        
        if($result->num_rows() > 0){
            $result = $result->result_array();
            $IDs = $this->makeIDList($result);
            $this->db->flush_cache();
            $this->db->stop_cache();
            $this->db->flush_cache();
            $this->db->select("*");
            $this->db->select("pm_production_job_order_product.product_ID,pm_inventory_product.description, pm_inventory_unit.description as unit_description");
            $this->db->join("pm_inventory_product", " pm_inventory_product.ID = pm_production_job_order_product.product_ID");
            $this->db->join("pm_inventory_unit", " pm_inventory_unit.ID = pm_inventory_product.fk_unit_id");
                
            $this->db->where_in("pm_production_job_order_product.fk_job_order_ID",$IDs);
            $products =  $this->db->get("pm_production_job_order_product");
            if($products->num_rows() > 0){
                $products = $products->result_array();
                $this->db->flush_cache();
                $this->db->stop_cache();
                $this->db->flush_cache();
                $this->db->select("*");
                $this->db->select("pm_inventory_product.description, pm_inventory_unit.description as unit_description");
                $this->db->join("pm_inventory_product", " pm_inventory_product.ID = pm_production_job_order_product_material.product_ID");
                $this->db->join("pm_inventory_unit", " pm_inventory_unit.ID = pm_inventory_product.fk_unit_id");
                $this->db->where_in("pm_production_job_order_product_material.fk_job_order_product_ID",$this->makeIDList($products));
                $productMaterials =  $this->db->get("pm_production_job_order_product_material");
                $productMaterials = (empty($productMaterials)) ? false: $productMaterials->result_array();
                foreach($result as $resultKey => $resultValue){
                    if(!empty($products)){
                        $result[$resultKey]["products"] = array();
                        foreach($products as $productsKey => $productsValue){
                            if($resultValue["ID"] == $productsValue["fk_job_order_ID"]){
                                $productsValue["material"] = array();
                                $result[$resultKey]["products"][] = $productsValue;
                                if($productMaterials){
                                    foreach($productMaterials as $productMaterialKey => $productMaterialValue){
                                        if($productMaterialValue["fk_job_order_product_ID"] == $productsValue["ID"]){
                                            $result[$resultKey]["products"][sizeof($result[$resultKey]["products"]) -1]["material"][] = $productMaterialValue;
                                            unset($productMaterials[$productMaterialKey]);
                                        }
                                    }
                                }
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
    public function updateJobOrder($ID, $assignedTo = false, $remarks = false, $createdBy = false, $approvedBy = false, $dateApproved = false, $approvalRemarks = false){
        $this->db->start_cache();
        $this->db->flush_cache();
        $newData = array();
        ($assignedTo) ? $newData["assigned_to"] = $assignedTo : null;
        ($remarks) ? $newData["remarks"] = $remarks : null;
        ($createdBy) ? $newData["created_by"] = $createdBy : null;
        ($approvedBy) ? $newData["approved_by"] = $approvedBy : null;
        ($dateApproved) ? $newData["date_approved"] = $dateApproved : null;
        ($approvalRemarks) ? $newData["approval_remarks"] = $approvalRemarks : null;
        if(sizeof($newData) > 0){
            $this->db->where("ID", $ID);
            $this->db->update("pm_production_job_order", $newData);
            $this->db->flush_cache();
            $this->db->stop_cache();
            return true;
        }else{
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
<?php

class M_Formulation extends CI_Model {

    const TABLE_NAME_GENERAL = 'production_formulation';
    const TABLE_NAME_DETAIL = 'production_formulation_detail';

    public function add($general_data, $details) {
        $this->db->trans_begin();

        $this->db->insert(self::TABLE_NAME_GENERAL, $general_data);
        $id = $this->db->insert_id();
        foreach ($details as &$detail) {
            $detail['fk_production_formulation_id'] = $id;
        }
        $this->db->insert_batch(self::TABLE_NAME_DETAIL, $details);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit(); //uncomment after debug
            return $id; //uncomment after debug
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

}

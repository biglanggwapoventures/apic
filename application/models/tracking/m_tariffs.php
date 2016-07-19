<?php

class M_Tariffs extends CI_Model {

    protected $table = 'tracking_tariff';
    protected $newTable = 'tracking_tariff_details';

    function create($data)
    {

        $this->db->trans_start();

        $this->db->insert('tracking_tariff', $data['tariff']);

        $id = $this->db->insert_id();

        if(!empty($data['tariff_details'])){
            foreach($data['tariff_details'] AS &$less){
                $less['fk_tariff_id'] = $id;
            }
            $this->db->insert_batch('tracking_tariff_details', $data['tariff_details']);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $id : FALSE;
    }

    function update($id, $data)
    {

        $this->db->trans_start();

        $this->db->update('tracking_tariff', $data['tariff'], ['id' => $id]);

        foreach($data['tariff_details'] AS &$less){
                $less['fk_tariff_id'] = $id;
            }

        if(!empty($data['tariff_details'])){
        $this->sync('tracking_tariff_details', $data['tariff_details'], 'id', 'fk_tariff_id', $id);
            // $this->db->update_batch('tracking_tariff_details', $data['tariff_details'],'rate');

        }else{
            $this->db->update('tracking_tariff_details', ['deleted_at' => NULL], ['fk_tariff_id' => $id]);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $id : FALSE;

    }
function sync($table, $value_array, $table_pk_column_name, $table_fk_column_name, $table_fk_column_value)
    {
        $temp = ['new' => [], 'updated' => [], 'updated_ids' => []];

        foreach($value_array AS &$row){
            if(isset($row[$table_pk_column_name])){
                $temp['updated'][] = $row;
                $temp['updated_ids'][] = $row[$table_pk_column_name];
            }else{
                $row[$table_fk_column_name] = $table_fk_column_value;
                $temp['new'][] = $row;
            }
        }

        unset($value_array);

        if(empty($temp['updated_ids'])){
            $this->db->where([
                $table_fk_column_name => $table_fk_column_value
            ]);
            $this->db->delete($table);
            // $this->db->delete($table, ['deleted_at' => NULL], [
            //     $table_fk_column_name => $table_fk_column_value,
            //     'deleted_at' => 0
            // ]);
        }else{
            $this->db->where([
                    $table_fk_column_name => $table_fk_column_value
                ])
                ->where_not_in($table_pk_column_name, $temp['updated_ids'])
                ->delete($table);

            $this->db->update_batch($table, $temp['updated'], $table_pk_column_name);
        }

        if(!empty($temp['new'])){
            $this->db->insert_batch($table, $temp['new']);
        }
    }
    function delete($ID) {
        if ($ID) {
            $this->db->where('id', $ID);
            return $this->db->delete('tracking_tariff');
        }
        return FALSE;
    }

    function all($search=[] ,$wildcards = [])
    {
        $data = [];
        $this->db->select('DISTINCT p.*, u.name AS location_tariff, p.option , p.code AS code', FALSE);
        $this->db->from('tracking_tariff AS p');
        $this->db->join('tracking_location AS u', 'u.id = p.fk_location_id');
        if(!empty($search)){
           $this->db->where($search);
        }
        if(!empty($wildcards)){
            $this->db->like($wildcards);
        }

        $this->db->order_by('p.id', 'DESC');

        $data = $this->db->get($this->table)->result_array();

        return $data;
    }

    public function find($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    function get($id)
    {
        $data = $this->db->get_where('tracking_tariff', ['id' => $id])->row_array();

        if(!$data){
            return NULL;
        }
        $this->db->select('tt.*, tl.name AS location');
        $this->db->from('tracking_tariff AS tt');
        $this->db->join('tracking_location AS tl', 'tl.id = tt.fk_location_id');
        $data['tariff'] = $this->db->get()->row_array();
        $data['less'] = $this->db->get_where('tracking_tariff_details', ['fk_tariff_id' => $id])->result_array();

        return $data;
    }

    function exists($id)
    {
        $this->db->where([
            'id' => $id
        ]);

        return $this->db->get('tracking_tariff')->num_rows() > 0;
    }


    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('code')->from($this->table)->where('code', $code)->get()->num_rows() === 0;
    }

   
}
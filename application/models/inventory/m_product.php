<?php

class M_Product extends CI_Model {

    protected $table = 'inventory_product';
    
    const CATEGORY_FRESH_CHILLED_DRESSED_CHICKEN = 7;
    const CATEGORY_CHICKEN_BYPRODUCTS = 2;
    const CATEGORY_CHICKEN_CUTUPS = 5;
    const CATEGORY_LIVE_CHICKEN = 6;

    const TABLE_NAME_GENERAL = 'inventory_product';

    private $column_names = array(
        'class' => 'product.fk_class_id',
        'type' => 'product.fk_type_id',
        'unit' => 'product.fk_unit_id',
        'category' => 'product.fk_category_id',
        'id' => 'product.id',
        'status' => 'product.status'
    );

    const PRODUCT_ID = 'id';
    const PRODUCT_TYPE = 'type';
    const PRODUCT_CLASS = 'class';
    const CLASS_RAW = 1;
    const CLASS_FINISHED = 2;
    const PRODUCT_UNIT = 'unit';
    const PRODUCT_CATEGORY = 'type';
    const PRODUCT_STATUS = 'status';

    function get($search_token = FALSE, $filter = array(), $limit = 999, $offset = 0) {
        $this->db->select('product.status, product.id, product.description, product.code');
        $this->db->select('unit.id as unit, unit.description as unit_description, unit.quantity AS unit_q');
        // $this->db->select('type.id as type, type.description as type_description');
        $this->db->select('category.id as category, category.description as category_description');
        // $this->db->select('class.id as class, class.description as class_description');
        // $this->db->select('pricing_method.id as pricing_method, pricing_method.description as pricing_method_description');
        $this->db->from('inventory_product as product');
        // $this->db->join('production_formulation AS f', 'f.id = product.fk_production_formulation_id', 'left');
        $this->db->join('inventory_unit as unit', 'unit.id = product.fk_unit_id', 'left');
        $this->db->join('inventory_category as category', 'category.id = product.fk_category_id', 'left');
        // $this->db->join('inventory_class as class', 'class.id = product.fk_class_id', 'left');
        // $this->db->join('inventory_type as type', 'type.id = product.fk_type_id', 'left');
        // $this->db->join('inventory_pricing_method as pricing_method', 'pricing_method.id = product.fk_pricing_method_id', 'left');
        // $this->db->where_in('fk_class_id', [self::CLASS_FINISHED, self::CLASS_RAW]);
        $this->db->group_by('product.id');
        if ($search_token) {
            $this->db->like('product.description', $search_token, 'both');
        }
        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $this->db->where($this->column_names[$key], $value);
            }
        }
        $result = $this->db->limit($limit, $offset)->order_by('product.description', 'ASC')->get('inventory_unit')->result_array();
        $stocks = $this->get_stocks();
        foreach($result as &$row)
        {
            $row['stock'] = isset($stocks[$row['id']]) ? $stocks[$row['id']] : 0;   
        }
        return $result;
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data) ? $this->db->insert_id() : FALSE;
    }

    public function update($id, $data) 
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function find($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    function add($data) {
        $inserted = $this->db->insert('inventory_product', $data);
        return $inserted ? $this->db->insert_id() : FALSE;
    }

    

    function delete($ID) {
        if ($ID) {
            $this->db->where('id', $ID);
            return $this->db->delete('inventory_product');
        }
        return FALSE;
    }

    function get_pricing_method() {
        
    }

    function list_pricing_method() {
        return $this->db->get('inventory_pricing_method')->result_array();
    }

    public function is_valid($id, $options = FALSE) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        if($options !== FALSE)
        {
            $this->db->where($options);
        }
        if (is_array($id)) {
            $ids = array_unique($id);
            $this->db->where_in('id', $ids);
            return count($ids) === $this->db->get()->num_rows();
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function is_valid_finished($id) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        $this->db->where('fk_class_id', self::CLASS_FINISHED);
        if (is_array($id)) {
            $this->db->where_in('id', $id);
            return count($id) === (int) $this->db->get()->num_rows();
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    public function is_valid_raw($id) {
        $this->db->select('id')->from(self::TABLE_NAME_GENERAL);
        $this->db->where('fk_class_id', self::CLASS_RAW);
        if (is_array($id)) {
            $this->db->where_in('id', $id);
            return count($id) === (int) $this->db->get()->num_rows();
        } else {
            $this->db->where('id', $id);
            $count = $this->db->get()->num_rows();
            return $count > 0;
        }
    }

    /* =====================
      NEW FUNCTIONS 01-14-15
      ===================== */
      
   public function category_in($category_ids)
   {
       $this->db->where_in('fk_category_id', $category_ids);
       return $this;
   }

    public function get_list($params = FALSE) {
        $this->db->select('product.id, product.code, product.description, category.description AS category_description, fk_category_id');
        $this->db->from(self::TABLE_NAME_GENERAL . ' as product');
        if($params !== FALSE){
            $this->db->where($params);
        }
        $this->db->join('inventory_category AS category', 'category.id = product.fk_category_id');
        return $this->db->order_by('description', 'ASC')->get()->result_array();
    }

    public function with_formulation($params = FALSE)
    {
        $this->db->select('p.id, p.description, p.code, f.formulation_code');
        $this->db->from('inventory_product AS p');
        $this->db->join('production_formulation AS f', 'f.id = p.fk_production_formulation_id');
        if($params !== FALSE)
        {
            $this->db->where($params);
        }
        return $this->db->get()->result_array();
    }

    public function get_stocks($id = FALSE)
    {
        if($id !== FALSE){
            if(is_array($id)){
                $this->db->where_in('product_id', $id);
            }else{
                $this->db->where('product_id', $id);
            }
        }
        $this->db->select('product_id, SUM(IFNULL(`in`, 0)) - SUM(IFNULL(`out`, 0)) AS available_units, SUM(IF(`in` IS NOT NULL, `pieces`, 0)) - SUM(IF(`out` IS NOT NULL, `pieces`, 0)) AS available_pieces', FALSE);
        $this->db->from('running_inventory');
        $this->db->where('is_counted', 1);
        $result = $this->db->group_by('product_id')->get()->result_array();
        return array_column($result, NULL, 'product_id');
    }

    public function get_logs($product_id, $page = 1)
    {
        $offset = $page === 1 ? 0 : $page*30;
        $this->db->select('in,out,date, inv.unit_price, inv.pieces,
            dr.fk_sales_delivery_id AS pl_no, 
            sa.adjustment_id AS sa_no, 
            pur.fk_purchase_receiving_id AS rr_no, 
            pur.fk_purchase_receiving_id as pur,
            yf.fk_yielding_id AS yieldf_no,
            ytf.fk_yielding_id AS yieldt_no');
        $this->db->from('running_inventory AS inv');
        $this->db->join('inventory_stock_adjustment_detail AS sa', 'sa.id = inv.stock_adjustment_detail_id', 'left');
        $this->db->join('sales_delivery_detail AS dr', 'dr.id = inv.delivery_detail_id', 'left');
        $this->db->join('purchase_receiving_detail AS pur', 'pur.id = inv.purchase_receiving_detail_id', 'left');

        $this->db->join('yieldings_from AS yf', 'yf.id = inv.fk_yieldings_from_id', 'left');
        $this->db->join('yieldings_to AS yt', 'yt.id = inv.fk_yieldings_to_id', 'left');
        $this->db->join('yieldings_from AS ytf', 'ytf.id = yt.fk_yieldings_from_id', 'left');

        $this->db->where(['inv.product_id' => $product_id, 'inv.is_counted' => 1]);
        $this->db->or_having(['inv.in >' => 0, 'inv.out >' => 0]);
        return $this->db->limit(100, $offset)->order_by('inv.id', 'DESC')->get()->result_array();
    }

    public function get_formulation_id($id)
    {
        if(!is_array($id)){
            $id = (array)$id;
        }
        $this->db->select('fk_production_formulation_id AS formulation_id, id')->from('inventory_product');
        $this->db->where_in('id', $id);
        return array_column($this->db->get()->result_array(), 'formulation_id', 'id');
    }

    public function identify($ids)
    {
        $this->db->select('p.id, p.description, p.code, u.description AS unit_description')->from('inventory_product AS p');
        $this->db->join('inventory_unit AS u', 'u.id = p.fk_unit_id');
        $this->db->where_in('p.id', $ids);
        return array_column($this->db->get()->result_array(), NULL, 'id');
    }

    public function all($search = [], $wildcards = [])
    {
        $this->db->select('DISTINCT p.*, u.description AS unit, c.description AS category', FALSE);
        $this->db->from($this->table.' AS p');
        $this->db->join('inventory_unit AS u', 'u.id = p.fk_unit_id');
        $this->db->join('inventory_category AS c', 'c.id = p.fk_category_id');
        if(!empty($search)){
           $this->db->where($search);
        }
        if(!empty($wildcards)){
            $this->db->like($wildcards);
        }
        $this->db->order_by('p.description', 'ASC');
        return $this->db->get($this->table)->result_array();
    }

    public function has_unique_code($code, $id = FALSE)
    {
        if($id !== FALSE){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('code')->from($this->table)->where('code', $code)->get()->num_rows() === 0;
    }



}

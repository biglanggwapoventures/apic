<?php

$config = array(
    'finished_product' => array(
        array(
            'field' => 'class',
            'label' => 'Product Class',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Product Description',
            'rules' => 'required'
        ),
        array(
            'field' => 'code',
            'label' => 'Product Code',
            'rules' => 'required'
        ),
        array(
            'field' => 'category',
            'label' => 'Product Category',
            'rules' => 'required'
        ),
        array(
            'field' => 'type',
            'label' => 'Product Type',
            'rules' => 'required'
        ),
        array(
            'field' => 'unit',
            'label' => 'Product Unit',
            'rules' => 'required'
        ),
        array(
            'field' => 'formulation_code',
            'label' => 'Formulation code',
            'rules' => 'required'
        ),
        array(
            'field' => 'reorder_level',
            'label' => 'Reorder level',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'cost_method',
            'label' => 'Cost method',
            'rules' => 'callback_validate_cost_method'
        ),
        array(
            'field' => 'status',
            'label' => 'Status',
            'rules' => 'callback_validate_status'
        )
    ), 
    'raw_product' => array(
        array(
            'field' => 'class',
            'label' => 'Product Class',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Product Description',
            'rules' => 'required'
        ),
        array(
            'field' => 'code',
            'label' => 'Product Code',
            'rules' => 'required'
        ),
        array(
            'field' => 'unit',
            'label' => 'Product Unit',
            'rules' => 'required'
        ),
        array(
            'field' => 'reorder_level',
            'label' => 'Reorder level',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'cost_method',
            'label' => 'Cost method',
            'rules' => 'callback_validate_cost_method'
        ),
        array(
            'field' => 'status',
            'label' => 'Status',
            'rules' => 'callback_validate_status'
        )
    ),
    'customer' => array(
        array(
            'field' => 'company_name',
            'label' => 'Company Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'customer_code',
            'label' => 'Customer Code',
            'rules' => 'required'
        ),
        array(
            'field' => 'contact_person',
            'label' => 'Contact Person',
            'rules' => 'required'
        ),
        array(
            'field' => 'contact_number',
            'label' => 'Contact Number',
            'rules' => 'required'
        ),
        array(
            'field' => 'address',
            'label' => 'Address',
            'rules' => 'required'
        ),
        array(
            'field' => 'credit_limit',
            'label' => 'Credit Limit',
            'rules' => 'required|callback_credit_limit_check'
        )
    )
);

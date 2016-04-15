<?php

function generate_customer_dropdown($dropdown_name, $default_val = FALSE, $attrs = FALSE, $first_option_text = '') {
    $CI = &get_instance();
    $CI->load->helper('form');
    $CI->load->model('sales/m_customer');
    $customers = $CI->m_customer->all(['status' => 'a']);
    $dropdown_format = array('' => $first_option_text);
    array_map(function($var) USE(&$dropdown_format) {
        $dropdown_format[$var['id']] = "[{$var['customer_code']}] {$var['company_name']}";
    }, $customers);
    unset($customers);
    return form_dropdown($dropdown_name, $dropdown_format, $default_val, $attrs);
}

function format_customer_editable($return_as_json = FALSE) {
    $CI = &get_instance();
    $CI->load->model('sales/m_customer');
    $customers = $CI->m_customer->all();
    $format = [];
    array_map(function($var) USE(&$format) {
        $format[$var['id']] = array(
            'name' => $var['company_name'],
            'credit_term' => $var['credit_term'],
            'credit_limit' => $var['credit_limit'],
            'customer_code' => $var['customer_code']
        );
    }, $customers);
    unset($customers);
    return $return_as_json ? json_encode($format) : $format;
}

//function generate_customer_product_dropdown($customer_id, $select_name, $select_attr = '', $remove_select = FALSE){
//    $CI = &get_instance();
//    $CI->load->model('sales/m_customer');
//    $select = array("<select {$select_attr} name='{$select_name}'>",'</select>');
//    $product_list = $CI->m_customer->get_customer_products($customer_id);
//    if(!$product_list){
//        return implode('<option>There are no registered products for this customer.</option>', $select);
//    }
//    $options = array('<option data-unit="units"></option>');
//    array_map(function($var) USE (&$options){
//        $options[] = "<option value='{$var['product_id']}' data-unit='{$var['prod_unit']}' data-price='{$var['price']}' data-discount='{$var['discount']}'>{$var['description']} ({$var['formulation_code']})</option>";
//    }, $product_list);
//    if($remove_select){
//        return implode('', $options);
//    }
//    return implode(implode('', $options), $select);
//    
//}

function generate_customer_product_dropdown($name, $options_array, $value_key, $text_key, $selected, $grouping_key = FALSE, $attrs = '') {
    $groups = [];
    $options = [];
    foreach ($options_array as $option) {
        $default = $option->$value_key === $selected ? 'selected="selected"' : '';
        $option_tag = "<option data-price='{$option->price}' data-discount='{$option->discount}' data-unit='{$option->prod_unit}' value='{$option->$value_key}' {$default}>{$option->$text_key}</option>";
        if ($grouping_key !== FALSE) {
            $groups[$option->$grouping_key][] = $option_tag;
        } else {
            $options[] = $option_tag;
        }
    }
    $options_html = '';
    if (!empty($groups)) {
        foreach ($groups as $g => $opt) {
            $options_html.= "<optgroup label='{$g}'>" . implode($opt, '') . "</optgroup>";
        }
    } else {
        $options_html = implode($options, '');
    }
    return "<select name='{$name}' {$attrs}><option value=''></option>" . $options_html . "</select>";
}

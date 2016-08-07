<?php

function generate_customer_dropdown($dropdown_name, $default_val = FALSE, $attrs = FALSE, $first_option_text = '') {
    $CI = &get_instance();
    $CI->load->helper('form');
    $CI->load->model('sales/m_customer');
    $customers = $CI->m_customer->all(['status' => 'a']);
    $dropdown_format = array('' => $first_option_text);
    array_map(function($var) USE(&$dropdown_format) {
        $dropdown_format[$var['id']] = "{$var['company_name']} [{$var['customer_code']}]";
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


function trucking_customer_dropdown($dropdown_name, $default_val = FALSE, $attrs = FALSE, $first_option_text = '') {
    $CI = &get_instance();
    $CI->load->helper('form');
    $CI->load->model('sales/m_customer');
    $customers = $CI->m_customer->all(['status' => 'a','for_trucking' => 1]);
    $dropdown_format = array('' => $first_option_text);
    array_map(function($var) USE(&$dropdown_format) {
        $dropdown_format[$var['id']] = "{$var['company_name']} [{$var['customer_code']}]";
    }, $customers);
    unset($customers);
    return form_dropdown($dropdown_name, $dropdown_format, $default_val, $attrs);
}


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

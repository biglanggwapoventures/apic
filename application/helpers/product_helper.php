<?php

function generate_product_dropdown($dropdown_name, $default_val = FALSE, $attrs = FALSE, $first_option_text = '') {
    $CI = &get_instance();
    $CI->load->helper('form');
    $CI->load->model('inventory/m_product');
    $products = $CI->m_product->get_list();
    $dropdown_format = array('' => $first_option_text);
    array_map(function($var) USE(&$dropdown_format) {
        $dropdown_format[$var['id']] = "{$var['description']} [{$var['code']}]";
    }, $products);
    unset($products);
    return form_dropdown($dropdown_name, $dropdown_format, $default_val, $attrs);
}


<?php

function include_css($items = array()) {
    $CI = &get_instance();
    if (is_array($items)) {
        foreach ($items as $item) {
            $path = $CI->config->base_url('assets/css/' . $item);
            echo "<link rel='stylesheet' href='{$path}'/>";
        }
    } else {
        $path = $CI->config->base_url('assets/css/' . $items);
        echo "<link rel='stylesheet' href='{$path}'/>";
    }
    return;
}

function include_js($items = array()) {
    $CI = &get_instance();
    if (is_array($items)) {
        foreach ($items as $item) {
            $path = $CI->config->base_url('assets/js/' . $item);
            echo "<script type='text/javascript' src='{$path}'></script>";
        }
    } else {
        $path = $CI->config->base_url('assets/js/' . $items);
        echo "<script type='text/javascript' src='{$path}'></script>";
    }
    return;
}


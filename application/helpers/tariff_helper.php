<?php

function generate_tariff_dropdown($name, $options_array, $value_key, $text_key, $selected, $grouping_key = FALSE, $attrs = '') {
    $groups = [];
    $options = [];
    foreach ($options_array as $option) {
        $default = $option->$value_key === $selected ? 'selected="selected"' : '';
        $option_tag = "<option data-rate='{$option->rate}' value='{$option->$value_key}' {$default}>{$option->$text_key}</option>";
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

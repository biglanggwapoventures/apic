<?php

function dropdown_format($item_array, $value_key, $text_key = array(), $first_entry = FALSE, $prefix = '[', $suffix = ']')
{
    if (!is_array($item_array)) return ['' => ''];
    $formatted = [];
    if ($first_entry !== FALSE){
        $formatted[''] = $first_entry;
    }
    if (is_array($text_key)){
        foreach ($item_array as $item){
            $text = $item[$text_key[0]];
            for ($x = 1; $x < count($text_key); $x++){
                if ($item[$text_key[$x]]){
                    $text .= " {$prefix}{$item[$text_key[$x]]}{$suffix}";
                }
            }
            $formatted[$item[$value_key]] = $text;
        }
    }else{
        foreach ($item_array as $item){
            $formatted[$item[$value_key]] = $item[$text_key];
        }
    }
    return $formatted;
}

function PRINTR($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function generate_dropdown($name = '', $options = array(), $default_value = FALSE, $attrs = '', $subject = FALSE)
{
    $dropdown = array();
    $dropdown[] = "<select name='{$name}' {$attrs}>";
    $dropdown[] = '<option disabled selected value="">' . ($subject ? "Please select a {$subject}" : 'Please select an item from the list') . '</option>';
    foreach ($options as $value => $text)
    {
        $selected = $value == $default_value ? 'selected' : '';
        $dropdown[] = "<option value='{$value}' {$selected}>{$text}</option>";
    }
    $dropdown[] = '</select>';
    return implode("", $dropdown);
}

function convertIntegerToWords($x)
{

    $nwords = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
        'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen',
        'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
        'nineteen', 'twenty', 30 => 'thirty', 40 => 'forty',
        50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty',
        90 => 'ninety', '01' => 'one', '02' => 'two', '03' => 'three', '04' => 'four',
        '05' => 'five', '06' => 'six', '07' => 'seven', '08' => 'eight', '09' => 'nine');

    if (!is_numeric($x))
    {
        $w = '#';
    }
    else if (fmod($x, 1) != 0)
    {
        $w = '#';
    }
    else
    {
        if ($x < 0)
        {
            $w = 'minus ';
            $x = -$x;
        }
        else
        {
            $w = '';
        }
        if ($x < 21)
        {
            $w .= $nwords[$x];
        }
        else if ($x < 100)
        {
            $w .= $nwords[10 * floor($x / 10)];
            $r = fmod($x, 10);
            if ($r > 0)
            {
                $w .= '-' . $nwords[$r];
            }
        }
        else if ($x < 1000)
        {
            $w .= $nwords[floor($x / 100)] . ' hundred';
            $r = fmod($x, 100);
            if ($r > 0)
            {
                $w .= ' ' . convertIntegerToWords($r);
            }
        }
        else if ($x < 1000000)
        {
            $w .= convertIntegerToWords(floor($x / 1000)) . ' thousand';
            $r = fmod($x, 1000);
            if ($r > 0)
            {
                $w .= ' ';
                if ($r < 100)
                {
                    $w .= ' ';
                }
                $w .= convertIntegerToWords($r);
            }
        }
        else
        {
            $w .= convertIntegerToWords(floor($x / 1000000)) . ' million';
            $r = fmod($x, 1000000);
            if ($r > 0)
            {
                $w .= ' ';
                if ($r < 100)
                {
                    $word .= 'and ';
                }
                $w .= convertIntegerToWords($r);
            }
        }
    }
    return $w;
}

function convertCurrencyToWords($number)
{
    if (!is_numeric($number))
        return false;
    $nums = explode('.', $number);
    $out = convertIntegerToWords($nums[0]) . ' pesos';
    if (isset($nums[1]) && (int)$nums[1])
    {
        $out .= ' & ' . $nums[1] . ' / 100';
    }
    return ucwords($out);
}

function group_dropdown($name_attr, $options_array, $value_key, $text_key, $selected, $grouping_key = FALSE, $attrs = '')
{
    $groups = [];
    $options = [];
    foreach ($options_array as $option)
    {
        $default = $option->$value_key === $selected ? 'selected="selected"' : '';
        $option_tag = "<option value='{$option->$value_key}' {$default}>{$option->$text_key}</option>";
        if ($grouping_key !== FALSE)
        {
            $groups[$option->$grouping_key][] = $option_tag;
        }
        else
        {
            $options[] = $option_tag;
        }
    }
    $options_html = '';
    if (!empty($groups))
    {
        foreach ($groups as $name => $opt)
        {
            $options_html.= "<optgroup label='{$name}'>" . implode($opt, '') . "</optgroup>";
        }
    }
    else
    {
        $options_html = implode($options, '');
    }
    return "<select name='{$name_attr}' {$attrs}><option value=''></option>" . $options_html . "</select>";
}

function arr_group_dropdown($name_attr, $options_array, $value_key, $text_key, $selected, $grouping_key = FALSE, $attrs = '')
{
    $groups = [];
    $options = [];
    foreach ($options_array as $option)
    {
        $default = $option[$value_key] === $selected ? 'selected="selected"' : '';
        if(is_array($text_key))
        {
            $option_tag = "<option data-{$text_key['attr']['name']}='{$option[$text_key['attr']['value']]}' value='{$option[$value_key]}' {$default}>{$option[$text_key['text']]}</option>";
        }
        else
        {
            $option_tag = "<option value='{$option[$value_key]}' {$default}>{$option[$text_key]}</option>";
        }
        if ($grouping_key !== FALSE)
        {
            $groups[$option[$grouping_key]][] = $option_tag;
        }
        else
        {
            $options[] = $option_tag;
        }
    }
    $options_html = '';
    if (!empty($groups))
    {
        foreach ($groups as $name => $opt)
        {
            $options_html.= "<optgroup label='{$name}'>" . implode($opt, '') . "</optgroup>";
        }
    }
    else
    {
        $options_html = implode($options, '');
    }
    return "<select name='{$name_attr}' {$attrs}><option value=''></option>" . $options_html . "</select>";
}

if (!function_exists('cif'))
{

    function cif($test, $true, $false)
    {
        return $test ? $true : $false;
    }

}

if (!function_exists('ckey_exists'))
{

    function ckey_exists($key, $array, $default)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

}

if(!function_exists('select_cheque')){
    function select_cheque($name, $default_val = FALSE, $attr = ''){
        $options = [''=>'', 'rcbc' => 'RCBC', 'mb' => 'Metrobank', 'xrcbc' => 'RCBC (Cross check)', 'xmb' => 'Metrobank (Cross check)'];
        return form_dropdown($name, $options, $default_val, $attr);
    }
}

if(!function_exists('put_value')){
    function put_value($arr, $key, $default)
    {
        if(isset($arr[$key])){
            return $arr[$key];
        }
        return $default;
    }
}

if(!function_exists('category_types')){
    function category_types()
    {
        return ['rm' => 'Finished Goods', 'fg' => 'Finished Goods', 'l' => 'Labour', 'di' => 'Dummy Items'];
    }
}

if(!function_exists('status_dropdown')){
    function status_dropdown($name, $default = '', $attrs = '', $add_all_option = FALSE)
    {
        $options = ['ia' => 'Inactive', 'a' => 'Active'];
        if($add_all_option === TRUE){
            $options['all'] = '-All statuses-';
        }
        return form_dropdown($name, $options, $default, $attrs);
    }
}

if(!function_exists('option_dropdown')){
    function option_dropdown($name, $default = '', $attrs = '',$first_option_text = '', $add_all_option = FALSE)
    {
        $options = [null=>$first_option_text,1 => 'Origin', 2 => 'Destination'];
        if($add_all_option === TRUE){
            $options['all'] = '-All statuses-';
        }
        return form_dropdown($name, $options, $default, $attrs);
    }
}

if(!function_exists('category_type_dropdown')){
    function category_type_dropdown($name, $default = '', $attrs = '', $add_all_option = FALSE)
    {
        $options = category_types();
        if($add_all_option === TRUE){
            $options['all'] = '-All types-';
        }
        return form_dropdown($name, $options, $default, $attrs);
    }
}

if(!function_exists('category_type')){
    function category_type($type)
    {
        $types = category_types();
        return in_array($type, array_keys($types)) ? $types[$type] : NULL;
    }
}

if(!function_exists('status')){
    function status($status)
    {
        switch($status){
            case 'a': return ['text' => 'Active', 'class' => 'label-success'];
            case 'ia': return ['text' => 'Inactive', 'class' => 'label-warning'];
        }
    }
}

if(!function_exists('get_status')){
    function get_status($status)
    {
        return $status ? ['text' => 'Active', 'class' => 'label-success'] :  ['text' => 'Inactive', 'class' => 'label-warning'];
    }
}

if(function_exists('approved')){
    function approved($resource)
    { 
        if(isset($resouce['status']) && $resouce['status'] === 'a' ){
            return TRUE;
        }
        return FALSE;
    }
}

if(!function_exists('input_group')){
    function input_group($name, $val, $type='text', $attrs = '', $add_on = '')
    {
        echo "<div class='input-group'>
                <input type='{$type}' name='{$name}' value='{$val}' {$attrs}>
                <span class='input-group-addon'>{$add_on}</span>
              </div>";
    }
}



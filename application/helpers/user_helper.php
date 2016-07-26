<?php

function is_admin($ID = FALSE) {
    $CI = &get_instance();
    $CI->load->model('m_account');
    return $CI->m_account->is_admin();
}

function has_access($mod_request) {
    $CI = &get_instance();
    $modules = $CI->session->userdata('module_access');
    $is_admin = (int) $CI->session->userdata('type_id') === (int) M_Account::TYPE_ADMIN;
    if ($is_admin) {
        return TRUE;
    }
    return (int) $modules[$mod_request] === 1;
}

function is_adm() {
    $CI = &get_instance();
    return $CI->session->userdata('type_id') == M_Account::TYPE_ADMIN;
}


if(!function_exists('user_id')){
    
    function user_id()
    {
        $CI = &get_instance();
        return $CI->session->userdata('user_id');
    }
}

if(!function_exists('role')){
    
    function role($r = FALSE)
    {
        $CI = &get_instance();
        $role = $CI->session->userdata('role');
        return $r !== FALSE ? $role === $r : $role;
    }
}

if(!function_exists('can_set_status')){
    
    function can_set_status()
    {
        $CI = &get_instance();
        $role = $CI->session->userdata('role');
        return in_array($role, ['su', 'a']);
    }
}

if(!function_exists('can_update')){
    
    function can_update($resource, $status_key = FALSE)
    {
        $CI = &get_instance();
        $role = $CI->session->userdata('role');
        $status_key = is_string($status_key) ? $status_key : 'status';
        return !isset($resource[$status_key]) ||(isset($resource[$status_key]) && $resource[$status_key] === 'ia') || in_array($role, ['su', 'a']);
    }
}

if(!function_exists('can_delete')){
    
    function can_delete($resource, $status_key = FALSE)
    {
        $CI = &get_instance();
        $status_key = is_string($status_key) ? $status_key : 'status';
        if(isset($resource[$status_key]) && ($resource[$status_key] === 'a' || $resource[$status_key] !== NULL)){
            return in_array(role(), ['su', 'a']);
        }
        return TRUE;
    }
}

if(!function_exists('is_approved')){
    
    function is_approved($resource, $status_key = FALSE)
    {
        $CI = &get_instance();
        // !empty($resource['approved_by']
        // $status_key = is_string($status_key) ? $status_key : 'status';
        if(!empty($resource['approved_by'])){
            return FALSE;
        }
        return TRUE;
    }
}

if(!function_exists('check_access')){
    function check_access($module)
    {
        if(!has_access($module)) 
            show_error('You do not have any access to this module. Please contact administraotr', 401);
    }
}




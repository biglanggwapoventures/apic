<?php

if(!function_exists('insert_prop'))
{
	function insert_prop(&$item, $index, $data)
	{
		$item[$data['name']] = $data['value'];
	}

}
if(!function_exists('extract_prop'))
{
	function extract_prop($prop, $data)
	{
		
	}
}

if(!function_exists('')){
	function prep_for_dropdown(){

	}
}
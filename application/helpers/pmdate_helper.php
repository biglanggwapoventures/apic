<?php

function is_valid_date($date, $format = 'Y-m-d') {
    if (!is_array($date)) {
        $date = (array) $date;
    }
    return array_filter($date, function($var) use($format) {
                $d = DateTime::createFromFormat($format, $var);
                return $d && $d->format($format) == $var;
            }) === $date;
}


function formatDate($date, $toFormat, $originalFormat = FALSE)
{
	$dateInstance = $originalFormat ? date_create_from_format($originalFormat, $date) : date_create($date);
	return $dateInstance->format($toFormat);
}
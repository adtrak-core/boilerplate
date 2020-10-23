<?php

$findin = site_url();
$findthis = array(".adtrak.agency", ".vm", ".test", "localhost", ".localdev");

function strpos_arr($haystack, $arr_needle){
    $valid = false;
    foreach($arr_needle as $key => $needle)
    {
        if(stripos($haystack, $needle) !== false)
        {
            add_filter("updraftplus_boot_backup", "__return_false");
            break;
        }
    }
}

strpos_arr($findin, $findthis);

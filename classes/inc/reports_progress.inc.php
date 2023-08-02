<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminreport']){
    $array = $lib->get_courses_array();
    $return = "
        <h3>".get_string('completion_p', $p)."</h3>
        <h4>".get_string('select_ac', $p)."</h4>
        <div class='d-flex'>
            <select id='progress_select'>
    ";
    foreach($array as $arr){
        $return .= "<option value='$arr[1]'>$arr[0]</option>";
    }
    $return .= "
                <option disabled hidden value='' selected>".get_string('choose_ac', $p)."</option>
            </select>
            <button class='btn btn-primary mr-1 ml-1' type='button' onclick='progress_select();'>".get_string('filter', $p)."</button>
            <button class='btn btn-primary mr-1 ml-1' type='button' onclick='progress_all();'>".get_string('show_all', $p)."</button>
        </div>
        <h4 style='display: none;' class='text-error' id='progress_error'></h4>
        <div id='progress_content_div' style='display: none;'></div>
    ";
    $return = str_replace("  ","",$return);
    $returnText->return = $return;
    \local_offthejobadmin\event\viewed_reports_progress_menu::create(array('context' => \context_system::instance()))->trigger();
} else {
    $returnText->return = get_string('error_ld', $p);
}
echo(json_encode($returnText));
<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminreport']){
    $array = $lib->get_courses_array();
    $return = "
        <h3>Completion Progress</h3>
        <h4>Select a course to filter by or select show all</h4>
        <div class='d-flex'>
            <select id='progress_select'>
    ";
    foreach($array as $arr){
        $return .= "<option value='$arr[1]'>$arr[0]</option>";
    }
    $return .= "
                <option disabled hidden value='' selected>Choose a course</option>
            </select>
            <button class='btn btn-primary mr-1 ml-1' type='button' onclick='progress_select();'>Filter</button>
            <button class='btn btn-primary mr-1 ml-1' type='button' onclick='progress_all();'>Show All</button>
        </div>
        <h4 style='display: none;' class='text-error' id='progress_error'></h4>
        <div id='progress_content_div' style='display: none;'></div>
    ";
    $return = str_replace("  ","",$return);
    $returnText->return = $return;
} else {
    $returnText->return = 'Error Loading data.';
}
echo(json_encode($returnText));
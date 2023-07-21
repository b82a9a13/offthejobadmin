<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
$returnText = new stdClass();
if($_SESSION['otj_adminreport']){
    $return = "
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`ac`)' id='ac_btn'>Show <b>All Apprenticeship Courses</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lwis`)' id='lwis_btn'>Show <b>Learners With Incomplete Setup</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lwcs`)' id='lwcs_btn'>Show <b>Learners With Complete Setup</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lbt`)' id='lbt_btn'>Show <b>Learners Behind Target</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`ldms`)' id='ldms_btn'>Show <b>Learners Documents Missing Signatures</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lwap`)' id='lwap_btn'>Show <b>Learners Without A Plan</b></button>
        <h4 class='text-error' id='table_error' style='display:none;'></h4>
        <div id='table_div' class='inside-div' style='display:none;'></div>
    ";
    $return = str_replace("  ","",$return);
    $returnText->return = $return;
} else {
    $returnText->return = 'Error Loading data.';
}
echo(json_encode($returnText));

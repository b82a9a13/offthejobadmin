<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
$returnText = new stdClass();
if($_SESSION['otj_adminreport']){
    $return = "
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`hlt`)' id='hlt_btn'>Show <b>Hours Log Target</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`cct`)' id='cct_btn'>Show <b>Course Completion Target</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`sc`)' id='sc_btn'>Show <b>Setup Completion</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`pu`)' id='pu_btn'>Show <b>Plan Utilization</b></button>
        <h4 class='text-error' id='chart_error' style='display:none;'></h4>
        <div id='chart_div' class='inside-div' style='display:none;'></div>
    ";
    $return = str_replace("  ","",$return);
    $returnText->return = $return;
} else {
    $returnText->return = 'Error Loading data.';
}
echo(json_encode($returnText));

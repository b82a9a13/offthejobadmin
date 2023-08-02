<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminreport']){
    $showTxt = get_string('show', $p);
    $return = "
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`hlt`)' id='hlt_btn'>$showTxt <b>".get_string('hours_lt', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`cct`)' id='cct_btn'>$showTxt <b>".get_string('course_ct', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`sc`)' id='sc_btn'>$showTxt <b>".get_string('setup_c', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='charts_btn_clicked(`pu`)' id='pu_btn'>$showTxt <b>".get_string('plan_u', $p)."</b></button>
        <h4 class='text-error' id='chart_error' style='display:none;'></h4>
        <div id='chart_div' class='inside-div' style='display:none;'></div>
    ";
    $return = str_replace("  ","",$return);
    $returnText->return = $return;
    \local_offthejobadmin\event\viewed_reports_charts::create(array('context' => \context_system::instance()))->trigger();
} else {
    $returnText->return = get_string('error_ld', $p);
}
echo(json_encode($returnText));

<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminreport']){
    $showTxt = get_string('show', $p);
    $return = "
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`ac`)' id='ac_btn'>$showTxt <b>".get_string('all_ac', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lwis`)' id='lwis_btn'>$showTxt <b>".get_string('learners_wis', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lwcs`)' id='lwcs_btn'>$showTxt <b>".get_string('learners_wcs', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lbt`)' id='lbt_btn'>$showTxt <b>".get_string('learners_bt', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`ldms`)' id='ldms_btn'>$showTxt <b>".get_string('learner_dms', $p)."</b></button>
        <button class='btn btn-primary mb-2 mr-2 p-2' onclick='table_btn_clicked(`lwap`)' id='lwap_btn'>$showTxt <b>".get_string('learners_wap', $p)."</b></button>
        <h4 class='text-error' id='table_error' style='display:none;'></h4>
        <div id='table_div' class='inside-div' style='display:none;'></div>
    ";
    $return = str_replace("  ","",$return);
    $returnText->return = $return;
    \local_offthejobadmin\event\viewed_reports_tables::create(array('context' => \context_system::instance()))->trigger();
} else {
    $returnText->return = get_string('error_ld', $p);
}
echo(json_encode($returnText));

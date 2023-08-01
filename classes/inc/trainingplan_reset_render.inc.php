<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    $array = $lib->get_partial_trainingplan_data($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid']);
    $html = "
        <div class='modal_content'>
            <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
            <table class='table table-bordered table-striped table-hover'>
                <tr>
                    <th>Name</th>
                    <th>Employer</th>
                    <th>Start Date</th>
                    <th>Planned End Date</th>
                </tr>
                <tr>
                    <td>$array[0]</td>
                    <td>$array[1]</td>
                    <td>$array[2]</td>
                    <td>$array[3]</td>
                </tr>
                <tr>
                    <th>Length of Programme</th>
                    <th>OTJH</th>
                    <th>EPAO</th>
                    <th>Funding Source</th>
                </tr>
                <tr>
                    <td>$array[4]</td>
                    <td>$array[5]</td>
                    <td>$array[6]</td>
                    <td>$array[7]</td>
                </tr>
            </table>
            <h2 class='text-error'>Are you sure you want to reset this training plan?</h2>
            <div class='d-flex'>
                <button class='btn btn-danger mb-2 mr-2 p-2' onclick='plan_reset_clicked()'>Yes</button>
                <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>No</button>
                <h4 class='text-error' id='reset_error' style='display:none;'></h4>
            </div>
        </div>
    ";
    $returnText->return = str_replace("  ","",$html);
    \local_offthejobadmin\event\viewed_user_trainingplan_reset::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid']))->trigger();
} else {
    $returnText->error = 'Error Loading data.';
}
echo(json_encode($returnText));
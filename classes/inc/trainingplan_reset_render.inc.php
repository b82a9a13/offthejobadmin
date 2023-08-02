<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    $array = $lib->get_partial_trainingplan_data($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid']);
    $html = "
        <div class='modal_content'>
            <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
            <table class='table table-bordered table-striped table-hover'>
                <tr>
                    <th>".get_string('name', $p)."</th>
                    <th>".get_string('employer', $p)."</th>
                    <th>".get_string('start_date', $p)."</th>
                    <th>".get_string('planned_ed', $p)."</th>
                </tr>
                <tr>
                    <td>$array[0]</td>
                    <td>$array[1]</td>
                    <td>$array[2]</td>
                    <td>$array[3]</td>
                </tr>
                <tr>
                    <th>".get_string('length_op', $p)."</th>
                    <th>".get_string('otjh', $p)."</th>
                    <th>".get_string('epao', $p)."</th>
                    <th>".get_string('funding_s', $p)."</th>
                </tr>
                <tr>
                    <td>$array[4]</td>
                    <td>$array[5]</td>
                    <td>$array[6]</td>
                    <td>$array[7]</td>
                </tr>
            </table>
            <h2 class='text-error'>".get_string('reset_plan_text', $p)."</h2>
            <div class='d-flex'>
                <button class='btn btn-danger mb-2 mr-2 p-2' onclick='plan_reset_clicked()'>".get_string('yes', $p)."</button>
                <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>".get_string('no', $p)."</button>
                <h4 class='text-error' id='reset_error' style='display:none;'></h4>
            </div>
        </div>
    ";
    $returnText->return = str_replace("  ","",$html);
    \local_offthejobadmin\event\viewed_user_trainingplan_reset::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid']))->trigger();
} else {
    $returnText->error = get_string('error_ld', $p);
}
echo(json_encode($returnText));
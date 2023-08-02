<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    $array = $lib->get_setup_data($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid']);
    $html = "
        <div class='modal_content'>
            <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
            <table class='table table-bordered table-striped table-hover'>
                <tr>
                    <th>".get_string('total_m', $p)."</th>
                    <th>".get_string('total_otjh', $p)."</th>
                    <th>".get_string('employer_os', $p)."</th>
                    <th>".get_string('coach', $p)."</th>
                    <th>".get_string('manager_om', $p)."</th>
                </tr>
                <tr>
                    <td>$array[0]</td>
                    <td>$array[1]</td>
                    <td>$array[2]</td>
                    <td>$array[3]</td>
                    <td>$array[4]</td>
                </tr>
            </table>
            <table class='table table-bordered table-striped table-hover'>
                <tr>
                    <th>".get_string('start_date', $p)."</th>
                    <th>".get_string('contracted_hpw', $p)."</th>
                    <th>".get_string('annual_lw', $p)."</th>
                    <th>".get_string('training_plan', $p)."</th>
                </tr>
                <tr>
                    <td>$array[5]</td>
                    <td>$array[6]</td>
                    <td>$array[7]</td>
                    <td>$array[8]</td>
                </tr>
            </table>
    ";
    $html .= ($array[9]) ? "<div class='d-flex'><div><p>".get_string('learner_s', $p)."</p><img src='$array[9]' class='sign-img'></div>" : "<div class='d-flex'>";
    $html .= ($array[10]) ? "<div><p>".get_string('coach_s', $p)."</p><img src='$array[10]' class='sign-img'></div></div>" : "</div>";
    $html .= "
            <h2 class='text-error'>".get_string('reset_setup_text', $p)."</h2>
            <div class='d-flex'>
                <button class='btn btn-danger mb-2 mr-2 p-2' onclick='setup_reset_clicked()'>".get_string('yes', $p)."</button>
                <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>".get_string('no', $p)."</button>
                <h4 class='text-error' id='reset_error' style='display:none;'></h4>
            </div>
        </div>
    ";
    $returnText->return = str_replace("  ","",$html);
    \local_offthejobadmin\event\viewed_user_setup_reset::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid']))->trigger();
} else {
    $returnText->error = get_string('error_ld', $p);
}
echo(json_encode($returnText));
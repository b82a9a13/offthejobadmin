<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    if($_POST['type']){
        $type = $_POST['type'];
        if(!in_array($type, ['coach','learn'])){
            $returnText->error = get_string('invalid_tp', $p);
        } else {
            if($_POST['action']){
                $action = $_POST['action'];
                if(!in_array($action, ['reset', 'view'])){
                    $returnText->error = get_string('invalid_ap', $p);
                } else {
                    $sign = $lib->sign_render($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid'], $type);
                    if($sign === ''){
                        $returnText->error = get_string('no_da', $p);
                    } else {
                        $html = '';
                        if($action === 'view'){
                            $html .= "    
                                <div class='modal_content'>
                                    <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
                                    <img id='modal_img' src='$sign'>
                                    <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>".get_string('close', $p)."</button>
                                </div>
                            ";
                            $type = ($type == 'learn') ? 'learner' : $type;
                            \local_offthejobadmin\event\viewed_user_signature::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid'], 'other' => $type))->trigger();
                        } elseif($action === 'reset'){
                            $html .= "    
                                <div class='modal_content'>
                                    <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
                                    <img id='modal_img' src='$sign'>
                                    <h2 class='text-error'>".get_string('sign_reset_text', $p)."</h2>
                                    <div class='d-flex'>
                                        <button class='btn btn-danger mb-2 mr-2 p-2' onclick='reset_sign(`$type`)'>".get_string('yes', $p)."</button>
                                        <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>".get_string('no', $p)."</button>
                                        <h4 class='text-error' id='modal_error' style='display:none;'></h4>
                                    <div>
                                </div>
                            ";
                            $type = ($type == 'learn') ? 'learner' : $type;
                            \local_offthejobadmin\event\viewed_user_signature_reset::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid'], 'other' => $type))->trigger();
                        }
                        $returnText->return = str_replace("  ","",$html);
                    }
                }
            } else {
                $returnText->error = get_string('no_ap', $p);
            }
        }
    } else {
        $returnText->error = get_string('no_tp', $p);
    }
} else {
    $returnText->error = get_string('error_ld', $p);
}
echo(json_encode($returnText));
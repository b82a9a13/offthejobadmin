<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    if($_POST['type']){
        $type = $_POST['type'];
        if(!in_array($type, ['coach','learn'])){
            $returnText->error = 'Invalid type provided.';
        } else {
            if($_POST['action']){
                $action = $_POST['action'];
                if(!in_array($action, ['reset', 'view'])){
                    $returnText->error = 'Invalid action provided.';
                } else {
                    $sign = $lib->sign_render($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid'], $type);
                    if($sign === ''){
                        $returnText->error = 'No data available.';
                    } else {
                        $html = '';
                        $type = ($type == 'learn') ? 'learner' : $type;
                        if($action === 'view'){
                            $html .= "    
                                <div class='modal_content'>
                                    <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
                                    <img id='modal_img' src='$sign'>
                                    <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>Close</button>
                                </div>
                            ";
                            \local_offthejobadmin\event\viewed_user_signature::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid'], 'other' => $type))->trigger();
                        } elseif($action === 'reset'){
                            $html .= "    
                                <div class='modal_content'>
                                    <span class='modal_close' id='modal_span_close' onclick='close_modal_div()'>&times;</span>
                                    <img id='modal_img' src='$sign'>
                                    <h2 class='text-error'>Are you sure you want to reset this signature?</h2>
                                    <div class='d-flex'>
                                        <button class='btn btn-danger mb-2 mr-2 p-2' onclick='reset_sign(`$type`)'>Yes</button>
                                        <button class='btn btn-primary mb-2 mr-2 p-2' id='modal_btn_close' onclick='close_modal_div()'>No</button>
                                        <h4 class='text-error' id='modal_error' style='display:none;'></h4>
                                    <div>
                                </div>
                            ";
                            \local_offthejobadmin\event\viewed_user_signature_reset::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid'], 'other' => $type))->trigger();
                        }
                        $returnText->return = str_replace("  ","",$html);
                    }
                }
            } else {
                $returnText->error = 'No action provided.';
            }
        }
    } else {
        $returnText->error = 'No type provided.';
    }
} else {
    $returnText->error = 'Error Loading data.';
}
echo(json_encode($returnText));
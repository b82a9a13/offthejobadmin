<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_actrec'] && $_SESSION['otj_actrec_cid'] && $_SESSION['otj_actrec_uid']){
    $id = $_POST['id'];
    if(empty($id) || !preg_match("/^[0-9]*$/", $id)){
        $returnText->error = get_string('invalid_np', $p);
    } else {
        if($lib->delete_activityrecord($_SESSION['otj_actrec_cid'], $_SESSION['otj_actrec_uid'], $id)){
            $returnText->return = true;
            \local_offthejobadmin\event\deleted_user_activityrecord::create(array('context' => \context_course::instance($_SESSION['otj_actrec_cid']), 'courseid' => $_SESSION['otj_actrec_cid'], 'relateduserid' => $_SESSION['otj_actrec_uid'], 'other' => $id))->trigger();
        } else {
            $returnText->return = false;
        }
    }
} else {
    $returnText->error = get_string('error_r', $p);
}
echo(json_encode($returnText));
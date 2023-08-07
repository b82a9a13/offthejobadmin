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
        if(!in_array($type, ['learn', 'coach'])){
            $returnText->error = get_string('invalid_tp', $p);
        } else {
            if($lib->sign_reset($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid'], $type)){
                $returnText->return = true;
                if($type === 'learn'){
                    \local_offthejobadmin\event\deleted_user_sign_learn::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid']))->trigger();
                } elseif($type === 'coach'){
                    \local_offthejobadmin\event\deleted_user_sign_coach::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid']))->trigger();
                }
            } else {
                $returnText->return = false;
            }
        }
    } else {
        $returnText->error = get_string('no_tp', $p);
    }
} else {
    $returnText->error = get_string('error_rd', $p);
}
echo(json_encode($returnText));
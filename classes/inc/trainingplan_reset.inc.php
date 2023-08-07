<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    if($lib->reset_trainplan($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid'])){
        $returnText->return = true;
        \local_offthejobadmin\event\deleted_user_trainingplan::create(array('context' => \context_course::instance($_SESSION['otj_adminuser_cid']), 'courseid' => $_SESSION['otj_adminuser_cid'], 'relateduserid' => $_SESSION['otj_adminuser_uid']))->trigger();
    } else {
        $returnText->return = false;
    }
} else {
    $returnText->error = get_string('error_rd', 'local_offthejobadmin');
}
echo(json_encode($returnText));
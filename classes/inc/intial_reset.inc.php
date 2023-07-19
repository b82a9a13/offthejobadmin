<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    if($lib->reset_user_course_data($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid'])){
        $returnText->return = true;
    } else {
        $returnText->return = false;
    }
} else {
    $returnText->error = 'Error resetting.';
}
echo(json_encode($returnText));
<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_actrec'] && $_SESSION['otj_actrec_cid'] && $_SESSION['otj_actrec_uid']){
    $id = $_POST['id'];
    if(empty($id) || !preg_match("/^[0-9]*$/", $id)){
        $returnText->error = 'Invalid number provided.';
    } else {
        if($lib->delete_activityrecord($_SESSION['otj_actrec_cid'], $_SESSION['otj_actrec_uid'], $id)){
            $returnText->return = true;
        } else {
            $returnText->return = false;
        }
    }
} else {
    $returnText->error = 'Error resetting.';
}
echo(json_encode($returnText));
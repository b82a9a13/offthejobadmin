<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminuser'] && $_SESSION['otj_adminuser_cid'] && $_SESSION['otj_adminuser_uid']){
    if($_POST['type']){
        $type = $_POST['type'];
        if(!in_array($type, ['learn', 'coach'])){
            $returnText->error = 'Invalid type provided.';
        } else {
            if($lib->sign_reset($_SESSION['otj_adminuser_cid'], $_SESSION['otj_adminuser_uid'], $type)){
                $returnText->return = true;
            } else {
                $returnText->return = false;
            }
        }
    } else {
        $returnText->error = 'No type provided.';
    }
} else {
    $returnText->error = 'Error resetting data.';
}
echo(json_encode($returnText));
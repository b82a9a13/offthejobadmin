<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();

if(!isset($_SESSION['otj_hourslog']) || !isset($_SESSION['otj_hourslog_cid']) || !isset($_SESSION['otj_hourslog_uid'])){
    $returnText->return = false;
} else {
    if(!isset($_POST['id'])){
        $returnText->error = 'No id provided.';
    } else {
        $id = $_POST['id'];
        if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
            $returnText->error = 'Invalid id provided.';
        } else {
            $returnText->return = $lib->delete_hourslog($_SESSION['otj_hourslog_cid'], $_SESSION['otj_hourslog_uid'], $id);
        }
    }
}

echo(json_encode($returnText));
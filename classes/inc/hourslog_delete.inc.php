<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';

if(!isset($_SESSION['otj_hourslog']) || !isset($_SESSION['otj_hourslog_cid']) || !isset($_SESSION['otj_hourslog_uid'])){
    $returnText->return = false;
} else {
    if(!isset($_POST['id'])){
        $returnText->error = get_string('no_ip', $p);
    } else {
        $id = $_POST['id'];
        if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
            $returnText->error = get_string('invalid_ip', $p);
        } else {
            $returnText->return = $lib->delete_hourslog($_SESSION['otj_hourslog_cid'], $_SESSION['otj_hourslog_uid'], $id);
        }
    }
}

echo(json_encode($returnText));
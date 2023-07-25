<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();

if(!isset($_SESSION['otj_hourslog']) || !isset($_SESSION['otj_hourslog_cid']) || !isset($_SESSION['otj_hourslog_uid'])){
    $returnText->return = false;
} else {
    $returnText->return = $lib->get_hours_logs($_SESSION['otj_hourslog_cid'], $_SESSION['otj_hourslog_uid']);
}

echo(json_encode($returnText));
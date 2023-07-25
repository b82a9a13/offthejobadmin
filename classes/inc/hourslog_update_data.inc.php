<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();

if(!isset($_SESSION['otj_hourslog']) || !isset($_SESSION['otj_hourslog_cid']) || !isset($_SESSION['otj_hourslog_uid']) || !isset($_SESSION['otj_hourslog_rid'])){
    $returnText->return = false;
} else {
    //Validation regex
    $textarea = "/^[a-zA-Z0-9 ,.!'():;\s\-#]*$/";
    $textareaReplace = "/[a-zA-Z0-9 ,.!'():;\s\-#]/";

    $errorarray = [];
    $date = $_POST['date'];
    if($date != null && !empty($date)){
        if(!preg_match("/^[0-9-]*$/", $date) || empty($date)){
            array_push($errorarray, ['date', 'Date']);
        } else {
            $date = (new DateTime($date))->format('U');
        }
    } else {
        array_push($errorarray, ['date', 'Date']);
    }
    $activity = $_POST['activity'];
    if(!preg_match($textarea, $activity) || empty($activity)){
        array_push($errorarray, ['activity', 'Activity:'.preg_replace($textareaReplace, '', $activity)]);
    }
    $whatlink = $_POST['whatlink'];
    if(!preg_match("/^[a-z A-Z0-9]*$/", $whatlink) || empty($whatlink)){
        array_push($errorarray, ['whatlink', 'What does this link to:'.preg_replace("/[a-z A-Z]/", '', $whatlink)]);
    }
    $impact = $_POST['impact'];
    if(!preg_match($textarea, $impact) || empty($impact)){
        array_push($errorarray, ['impact', 'What impact will this have:'.preg_replace($textareaReplace, '', $impact)]);
    }
    $duration = $_POST['duration'];
    if(!preg_match("/^[0-9.]*$/", $duration) || empty($duration)){
        array_push($errorarray, ['duration', 'Duration:'.preg_replace("/[0-9.]/", '', $duration)]);
    }

    if($errorarray != []){
        $returnText->error = $returnText;
    } else {
        $returnText->return = $lib->update_hourslog($_SESSION['otj_hourslog_cid'], $_SESSION['otj_hourslog_uid'], $_SESSION['otj_hourslog_rid'], [
            $date,
            $activity,
            $whatlink,
            $impact,
            $duration
        ]);
    }
}

echo(json_encode($returnText));
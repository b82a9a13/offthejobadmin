<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminsetup'] && $_SESSION['otj_adminsetup_cid'] && $_SESSION['otj_adminsetup_uid']){
    //Get posted values
    $totalmonths = $_POST['totalmonths'];
    $totalhours = $_POST['totalhours'];
    $eors = $_POST['eors'];
    $coach = $_POST['coach'];
    $morm = $_POST['morm'];
    $startdate = (new DateTime($_POST['startdate']))->format('U');
    $hpw = $_POST['hpw'];
    $alw = $_POST['alw'];
    $trainplan = $_POST['trainplan'];
    $option = $_POST['option'];
    $errorarray = [];
    //Validate posted values
    if(!preg_match("/^[0-9]*$/", $totalmonths) || empty($totalmonths)){
        array_push($errorarray, get_string('total_m', $p).':'.preg_replace('/[0-9]/','',$totalmonths));
    }
    if(!preg_match("/^[0-9]*$/", $totalhours) || empty($totalhours)){
        array_push($errorarray, get_string('total_h', $p).':'.preg_replace('/[0-9]/','',$totalhours));
    }
    if(!preg_match("/^[a-z A-Z'\-()0-9]*$/", $eors) || empty($eors)){
        array_push($errorarray, get_string('employer_os', $p).':'.preg_replace("/[a-z A-Z'\-()0-9]/","",$eors));
    }
    if(!preg_match("/^[A-Za-z '\-]*$/", $coach) || empty($coach)){
        array_push($errorarray, get_string('coach', $p).':'.preg_replace("/[a-zA-Z '\-]/","",$coach));
    }
    if(!preg_match("/^[a-z A-Z'\-]*$/", $morm) || empty($morm)){
        array_push($errorarray, get_string('manager_om', $p).':'.preg_replace("/[a-z A-Z'\-]/","",$morm));
    }
    if(!preg_match("/^[0-9]*$/", $startdate) || empty($startdate)){
        array_push($errorarray, get_string('start_date', $p).':'.preg_replace('/[0-9]/','',$startdate));
    }
    if(!preg_match("/^[0-9.]*$/", $hpw) || empty($hpw)){
        array_push($errorarray, get_string('hours_pw', $p).':'.preg_replace("/[0-9.]/","",$hpw));
    }
    if(!preg_match("/^[0-9.]*$/", $alw) || empty($alw)){
        array_push($errorarray, get_string('annual_lw', $p).':'.preg_replace("/[0-9.]/","",$alw));
    }
    $plans = $lib->get_training_plans_names();
    if(!in_array($trainplan, $plans) || empty($trainplan)){
        array_push($errorarray, get_string('training_plan', $p));
    }
    if(!preg_match("/^[0-9]*$/", $option)){
        array_push($errorarray, get_string('option', $p));
    }
    if($errorarray != []){
        $returnText->error = $errorarray;
    } else {
        $update = $lib->update_setup_data([$totalmonths, $totalhours, $eors, $coach, $morm, $startdate, $hpw, $alw, $trainplan, $option], $_SESSION['otj_adminsetup_cid'], $_SESSION['otj_adminsetup_uid']);
        if($update){
            $returnText->return = true;
            \local_offthejobadmin\event\updated_user_setup::create(array('context' => \context_course::instance($_SESSION['otj_adminsetup_cid']), 'courseid' => $_SESSION['otj_adminsetup_cid'], 'relateduserid' => $_SESSION['otj_adminsetup_uid']))->trigger();
        } else {
            $returnText->return = false;
        }
    }
} else {
    $returnText->error = get_string('error_ud', $p);
}
echo(json_encode($returnText));
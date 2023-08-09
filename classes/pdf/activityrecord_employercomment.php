<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__.'/../../../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/offthejobadmin:admin', $context);
use local_offthejobadmin\lib;
$lib = new lib;
$p = 'local_offthejobadmin';

$id = null;
$cid = null;
$uid = null;
$fullname = '';
$errorTxt = '';
if(!isset($_GET['id'])){
    $errorTxt = get_string('no_ip', $p);
} elseif(!isset($_GET['cid'])){
    $errorTxt = get_string('no_cip', $p);
} elseif(!isset($_GET['uid'])){
    $errorTxt = get_string('no_uip', $p);
} else {
    $id = $_GET['id'];
    $cid = $_GET['cid'];
    $uid = $_GET['uid'];
    $numMatch = "/^[0-9]*$/";
    if(!preg_match($numMatch, $id) || empty($id)){
        $errorTxt = get_string('invalid_ip', $p);
    } elseif(!preg_match($numMatch, $cid) || empty($cid)){
        $errorTxt = get_string('invalid_cip', $p);
    } elseif(!preg_match($numMatch, $uid) || empty($uid)){
        $errorTxt = get_string('invalid_uid', $p);
    } else {
        $fullname = $lib->check_learner_enrolment($cid, $uid);
        if($fullname == false){
            $errorTxt = get_string('selected_uneal', $p);
        } else {
            if(!$lib->check_setup_exists($cid, $uid)){
                $errorTxt = get_string('initial_sdne', $p);
            } else {
                if(!$lib->check_activityrecord_exists($cid, $uid)){
                    $errorTxt = get_string('no_ara', $p);
                }
            }
        }
    }
}

if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $coursename = $lib->get_course_fullname($cid);
    $data = $lib->get_employercomment_pdf($cid, $uid, $id);
    header('Content-Type: application/pdf');
    header("Content-Disposition:inline;filename=MonthlyActivityRecord-".str_replace(' ','_',$fullname)."-".str_replace(' ','_',$coursename)."-$date-EmployerComment.pdf");
    include("../../../activityrecord/classes/pdf/employercomment/$data[0]");
    \local_offthejobadmin\event\viewed_user_activityrecord_ec_pdf::create(array('context' => \context_course::instance($cid), 'courseid' => $cid, 'relateduserid' => $uid))->trigger();
}
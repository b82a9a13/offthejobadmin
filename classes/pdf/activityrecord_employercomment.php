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

$id = $_GET['id'];
$cid = $_GET['cid'];
$uid = $_GET['uid'];
$numMatch = "/^[0-9]*$/";
$fullname = '';
if(!preg_match($numMatch, $id) || empty($id)){
    $errorTxt = 'Invalid id provided.';
} elseif(!preg_match($numMatch, $cid) || empty($cid)){
    $errorTxt = 'Invalid course id provided.';
} elseif(!preg_match($numMatch, $uid) || empty($uid)){
    $errorTxt = 'Invalid user id provided.';
} else {
    $fullname = $lib->check_learner_enrolment($cid, $uid);
    if($fullname == false){
        $errorTxt = 'Selected user is not enrolled as a learner in the course selected.';
    } else {
        if(!$lib->check_setup_exists($cid, $uid)){
            $errorTxt = 'Initial setup does not exist for the user id and course id provided.';
        } else {
            if(!$lib->check_activityrecord_exists($cid, $uid)){
                $errorTxt = 'No activity records available.';
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
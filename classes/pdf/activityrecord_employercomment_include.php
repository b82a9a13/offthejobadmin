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

if(!isset($_GET['id'])){
    echo("<h2 class='text-error'>".get_string('no_ip', $p)."</h2>");
    exit();
} else {
    $id = $_GET['id'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id) || !isset($_SESSION['otj_actrec_cid']) || !isset($_SESSION['otj_actrec_cid'])){
        echo("<h2 class='text-error'>".get_string('invalid_ip', $p)."</h2>");
        exit();
    }
    $cid = $_SESSION['otj_actrec_cid'];
    $uid = $_SESSION['otj_actrec_uid'];
    $fullname = $lib->check_learner_enrolment($cid, $uid);
    if($fullname == false){
        echo('<h2 class="text-error">'.get_string('selected_uneal', $p).'</h2>');
        exit();
    } else {
        $data = $lib->get_employercomment_pdf($cid, $uid, $id);
        if($data[0] == '' || $data[0] == null){
            echo("<h2 class='text-error'>".get_string('employer_cde', $p)."</h2>");
            exit();
        } else {
            header('Content-Type: application/pdf');
            $coursename = $lib->get_course_fullname($cid);
            $date = $data[1];
            header("Content-Disposition:inline;filename=MonthlyActivityRecord-".str_replace(' ','_',$fullname)."-".str_replace(' ','_',$coursename)."-$date-EmployerComment.pdf");
            include('../../../activityrecord/classes/pdf/employercomment/'.$data[0]);
        }
    }
}
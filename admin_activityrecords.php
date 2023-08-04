<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__.'/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/offthejobadmin:admin', $context);
use local_offthejobadmin\lib;
$lib = new lib;
$p = 'local_offthejobadmin';

$errorTxt = '';
$cid = $_GET['cid'];
$uid = $_GET['uid'];
$fullname = '';
if($_GET['cid']){
    if(!preg_match("/^[0-9]*$/", $cid) || empty($cid)){
        $errorTxt = 'Invalid course id provided.';
    } else {
        if(!preg_match("/^[0-9]*$/", $uid) || empty($uid)){
            $errorTxt = 'Invalid user id provided.';
        } else {
            //Check if the user is enrolled as a learner in the course selected
            $fullname = $lib->check_learner_enrolment($cid, $uid);
            if($fullname == false){
                $errorTxt = 'Selected user is not enrolled as a learner in the course selected.';
            } else {
                //Check if the user has a initial setup complete
                if(!$lib->check_setup_exists($cid, $uid)){
                    $errorTxt = 'Initial setup does not exist for the user id and course id provided.';
                } else {
                    if(!$lib->check_activityrecord_exists($cid, $uid)){
                        $errorTxt = 'No activity records available.';
                    }
                }
            }
        }
    }
} else {
    $errorTxt = 'No course id provided';
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejobadmin/admin.php'));
$PAGE->set_title('Admin - Activity Records');
$PAGE->set_heading('Admin - Activity Records');
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $template = (Object)[
        'btmm' => get_string('btmm', $p),
        'btum' => get_string('btum', $p),
        'title' => get_string('activity_r', $p),
        'records_txt' => get_string('records', $p),
        'edit' => get_string('edit', $p),
        'delete' => get_string('delete', $p),
        'yes' => get_string('yes', $p),
        'no' => get_string('no', $p),
        'pdf' => get_string('pdf', $p),
        'apprentice' => get_string('apprentice', $p),
        'review_date' => get_string('review_date', $p),
        'standard' => get_string('standard', $p),
        'employer_os' => get_string('employer_os', $p),
        'coach' => get_string('coach', $p),
        'manager_om' => get_string('manager_om', $p),
        'summary_op' => get_string('summary_op', $p),
        'course_ptd' => get_string('course_ptd', $p),
        'course_epattp' => get_string('course_epattp', $p),
        'comments' => get_string('comments', $p),
        'otjh_c' => get_string('otjh_c', $p),
        'expected_otjh_aptp' => get_string('expected_otjh_aptp', $p),
        'safeguarding_title' => get_string('safeguarding_title', $p),
        'recap_title' => get_string('recap_title', $p),
        'impact_title' => get_string('impact_title', $p),
        'details_title' => get_string('details_title', $p),
        'modules_askb' => get_string('modules_askb', $p),
        'functional_sp' => get_string('functional_sp', $p),
        'learning_t' => get_string('learning_t', $p),
        'target_title' => get_string('target_title', $p),
        'math' => get_string('math', $p),
        'english' => get_string('english', $p),
        'aln_title' => get_string('aln_title', $p),
        'agreed_title' => get_string('agreed_title', $p),
        'coach_feedback' => get_string('coach_feedback', $p),
        'apprentice_ct' => get_string('apprentice_ct', $p),
        'employer_comment' => get_string('employer_comment', $p),
        'date_onpr' => get_string('date_onpr', $p),
        'remote_ftf' => get_string('remote_ftf', $p),
        'face_tf' => get_string('face_tf', $p),
        'learner_s' => get_string('learner_s', $p),
        'coach_s' => get_string('coach_s', $p),
        'submit' => get_string('submit', $p),
        'fullname' => $fullname,
        'coursename' => $lib->get_course_fullname($cid),
        'records' => array_values($lib->get_activityrecord_list($cid, $uid)),
        'cid' => $cid,
        'uid' => $uid
    ];
    echo $OUTPUT->render_from_template('local_offthejobadmin/activityrecords', $template);
    $_SESSION['otj_actrec'] = true;
    $_SESSION['otj_actrec_cid'] = $cid;
    $_SESSION['otj_actrec_uid'] = $uid;
    \local_offthejobadmin\event\viewed_user_activityrecords::create(array('context' => \context_course::instance($cid), 'relateduserid' => $uid, 'courseid' => $cid))->trigger();
}
echo $OUTPUT->footer();
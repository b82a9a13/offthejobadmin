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
                    if(!$lib->check_hourslog_exists($cid, $uid)){
                        $errorTxt = 'No hours log records available.';
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
$PAGE->set_title('Admin - Hours Log');
$PAGE->set_heading('Admin - Hours Log');
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $template = (Object)[
        'fullname' => $fullname,
        'coursename' => $lib->get_course_fullname($cid),
        'cid' => $cid,
        'uid' => $uid,
        'info_array' => array_values([$lib->get_hourslog_info_table_data($cid, $uid)]),
        'logs_array' => $lib->get_hours_logs($cid, $uid)
    ];
    echo $OUTPUT->render_from_template('local_offthejobadmin/hourslog', $template);
    $_SESSION['otj_hourslog'] = true;
    $_SESSION['otj_hourslog_cid'] = $cid;
    $_SESSION['otj_hourslog_uid'] = $uid;
    \local_offthejobadmin\event\viewed_user_hourslog::create(array('context' => \context_course::instance($cid), 'relateduserid' => $uid, 'courseid' => $cid))->trigger();
}
echo $OUTPUT->footer();
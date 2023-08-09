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
$cid = null;
$uid = null;
$fullname = '';
if(isset($_GET['cid'])){
    $cid = $_GET['cid'];
    if(!preg_match("/^[0-9]*$/", $cid) || empty($cid)){
        $errorTxt = get_string('invalid_cip', $p);
    } else {
        if(!isset($_GET['uid'])){
            $errorTxt = get_string('no_uip', $p);
        } else {
            $uid = $_GET['uid'];
            if(!preg_match("/^[0-9]*$/", $uid) || empty($uid)){
                $errorTxt = get_string('invalid_uid', $p);
            } else {
                //Check if the user is enrolled as a learner in the course selected
                $fullname = $lib->check_learner_enrolment($cid, $uid);
                if($fullname == false){
                    $errorTxt = get_string('selected_uneal', $p);
                } else {
                    //Check if the user has a initial setup complete
                    if(!$lib->check_setup_exists($cid, $uid)){
                        $errorTxt = get_string('initial_sdne', $p);
                    }
                }
            }
        }
    }
} else {
    $errorTxt = get_string('no_cip', $p);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejobadmin/admin.php'));
$PAGE->set_title(get_string('otj_admin', $p));
$PAGE->set_heading(get_string('otj_admin', $p));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $template = (Object)[
        'btm' => get_string('btm', $p),
        'select_wywtd' => get_string('select_wywtd', $p),
        'initial_s' => get_string('initial_s', $p),
        'edit' => get_string('edit', $p),
        'reset' => get_string('reset', $p),
        'learner_s' => get_string('learner_s', $p),
        'view' => get_string('view', $p),
        'coach_s' => get_string('coach_s', $p),
        'training_plan' => get_string('training_plan', $p),
        'activity_r' => get_string('activity_r', $p),
        'hours_l' => get_string('hours_l', $p),
        'username' => $fullname,
        'coursename' => $lib->get_course_fullname($cid),
        'cid' => $cid,
        'uid' => $uid
    ];
    $template->plan_disable = (!$lib->check_trainplan_exists($cid, $uid)) ? 'disabled' : '';
    $template->learner_disable = (!$lib->check_learn_signed($cid, $uid)) ? 'disabled' : '';
    $template->coach_disable = (!$lib->check_coach_signed($cid, $uid)) ? 'disabled' : '';
    $template->activity_disabled = (!$lib->check_activityrecord_exists($cid, $uid)) ? 'disabled' : '';
    $template->hourslog_disabled = (!$lib->check_hourslog_exists($cid, $uid)) ? 'disabled' : '';
    echo $OUTPUT->render_from_template('local_offthejobadmin/user', $template);
    $_SESSION['otj_adminuser'] = true;
    $_SESSION['otj_adminuser_uid'] = $uid;
    $_SESSION['otj_adminuser_cid'] = $cid;
    \local_offthejobadmin\event\viewed_user_menu::create(array('context' => \context_course::instance($cid), 'relateduserid' => $uid, 'courseid' => $cid))->trigger();
}
echo $OUTPUT->footer();
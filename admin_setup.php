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
$PAGE->set_title(get_string('admin_is', $p));
$PAGE->set_heading(get_string('admin_is', $p));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $data = $lib->get_setup_data($cid, $uid);
    $filesarray = $lib->get_training_plans();
    $hasoption = 'none';
    $optionsarray = [];
    for($i = 0; $i < count($filesarray); $i++){
        if($filesarray[$i][1] === $data[8]){
            $filesarray[$i][3] = 'selected';
            if($filesarray[$i][2] > 0){
                $hasoption = 'block';
                for($y = 1; $y <= $filesarray[$i][2]; $y++){
                    $selected = ($data[11] == $y) ? 'selected' : '';
                    array_push($optionsarray, ['Option '.$y, $y, $selected]);
                }
            }
        }
    }
    $template = (Object)[
        'btmm' => get_string('btmm', $p),
        'btum' => get_string('btum', $p),
        'title' => get_string('setup', $p),
        'total_m' => get_string('total_m', $p),
        'total_otjh' => get_string('total_otjh', $p),
        'employer_os' => get_string('employer_os', $p),
        'coach_txt' => get_string('coach', $p),
        'manager_om' => get_string('manager_om', $p),
        'start_date' => get_string('start_date', $p),
        'contracted_hpw' => get_string('contracted_hpw', $p),
        'annual_lw' => get_string('annual_lw', $p),
        'training_plan' => get_string('training_plan', $p),
        'option' => get_string('option', $p),
        'coach_s' => get_string('coach_s', $p),
        'success' => get_string('success', $p),
        'submit' => get_string('submit', $p),
        'fullname' => $fullname,
        'coursename' => $lib->get_course_fullname($cid),
        'cid' => $cid,
        'uid' => $uid,
        'totalmonths' => $data[0],
        'totalhours' => $data[1],
        'eors' => $data[2],
        'coach' => $data[3],
        'morm' => $data[4],
        'startdate' => date('Y-m-d', strtotime($data[5])),
        'hpw' => $data[6],
        'alw' => $data[7],
        'signature' => $data[10],
        'filesarray' => array_values($filesarray),
        'has_option' => $hasoption,
        'optionsarray' => array_values($optionsarray)
    ];
    echo $OUTPUT->render_from_template('local_offthejobadmin/setup', $template);
    $_SESSION['otj_adminsetup'] = true;
    $_SESSION['otj_adminsetup_cid'] = $cid;
    $_SESSION['otj_adminsetup_uid'] = $uid;
    \local_offthejobadmin\event\viewed_user_setup::create(array('context' => \context_course::instance($cid), 'courseid' => $cid, 'relateduserid' => $uid))->trigger();
}
echo $OUTPUT->footer();
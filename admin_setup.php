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
                }
            }
        }
    }
} else {
    $errorTxt = 'No course id provided';
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejobadmin/admin.php'));
$PAGE->set_title('Admin - Initial Setup');
$PAGE->set_heading('Admin - Initial Setup');
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
}
echo $OUTPUT->footer();
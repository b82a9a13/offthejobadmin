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
                    if(!$lib->check_trainplan_exists($cid, $uid)){
                        $errorTxt = 'Training plan does not exist.';
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
$PAGE->set_title('Admin - Training Plan');
$PAGE->set_heading('Admin - Training Plan');
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $data = $lib->get_trainplan_data($cid, $uid, 'Y-m-d');
    array_push($data[4], ['','','required']);
    $template = (Object)[
        'fullname' => $fullname,
        'coursename' => $lib->get_course_fullname($cid),
        'cid' => $cid,
        'uid' => $uid,
        'learnername' => $data[0][0],
        'employer' => $data[0][1],
        'startdate' => date('Y-m-d',$data[0][2]),
        'plannedendd' => date('Y-m-d',$data[0][3]),
        'lengthofprog' => $data[0][4],
        'otjh' => $data[0][5],
        $data[0][6] => 'selected',
        $data[0][7] => 'selected',
        'bksbrm' => $data[0][8],
        'bksbre' => $data[0][9],
        $data[0][10] => 'selected',
        'skillscanlr' => $data[0][11],
        'skillscaner' => $data[0][12],
        'hpw' => $data[0][13],
        'wop' => $data[0][14],
        'annuallw' => $data[0][15],
        'hoursperweek' => $data[0][16],
        'aostrength' => $data[0][17],
        'ltgoals' => $data[0][18],
        'stgoals' => $data[0][19],
        'iaguide' => $data[0][20],
        'recopl' => $data[0][21],
        'modarray' => $data[1][0],
        'total_mw' => $data[1][1][0],
        'total_otjh' => $data[1][1][1],
        'mathfs' => $data[2][0][1],
        'mathlevel' => $data[2][0][2],
        'mathmod' => $data[2][0][3],
        'mathsd' => $data[2][0][4],
        'mathped' => $data[2][0][5],
        'mathaed' => $data[2][0][6],
        'mathaead' => $data[2][0][7],
        'engfs' => $data[2][1][1],
        'englevel' => $data[2][1][2],
        'engmod' => $data[2][1][3],
        'engsd' => $data[2][1][4],
        'engped' => $data[2][1][5],
        'engaed' => $data[2][1][6],
        'engaead' => $data[2][1][7],
        'prarray' => $data[3],
        'addsa' => $data[0][22],
        'logarray' => array_values($data[4]),
        'jsfile' => 'editplan',
        'prbtns' => [[]]
    ];
    echo $OUTPUT->render_from_template('local_offthejobadmin/trainingplan', $template);
}
echo $OUTPUT->footer();
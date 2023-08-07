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
        $errorTxt = get_string('invalid_cip', $p);
    } else {
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
                } else {
                    if(!$lib->check_trainplan_exists($cid, $uid)){
                        $errorTxt = get_string('training_pdne', $p);
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
$PAGE->set_title(get_string('admin_tp', $p));
$PAGE->set_heading(get_string('admin_tp', $p));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
if($errorTxt != ''){
    echo("<h1 class='text-error'>$errorTxt</h1>");
} else {
    $data = $lib->get_trainplan_data($cid, $uid, 'Y-m-d');
    array_push($data[4], ['','','required']);
    $template = (Object)[
        'btmm' => get_string('btmm', $p),
        'btum' => get_string('btum', $p),
        'title' => get_string('training_plan', $p),
        'name' => get_string('name', $p),
        'employer_txt' => get_string('employer', $p),
        'start_date' => get_string('start_date', $p),
        'planned_ed' => get_string('planned_ed', $p),
        'length_op' => get_string('length_op', $p),
        'otjh_txt' => get_string('otjh', $p),
        'epao' => get_string('epao', $p),
        'funding_s' => get_string('funding_s', $p),
        'choose_epao' => get_string('choose_epao', $p),
        'fr_awards' => get_string('fr_awards', $p),
        'c_and_g' => get_string('c_and_g', $p),
        'innovate_txt' => get_string('innovate', $p),
        'dsw_txt' => get_string('dsw_txt', $p),
        'nocn_txt' => get_string('nocn', $p),
        'choose_fs' => get_string('choose_fs', $p),
        'contrib_five' => get_string('contrib_five', $p),
        'levy_txt' => get_string('levy', $p),
        'initial_a' => get_string('initial_a', $p),
        'bksb_rm' => get_string('bksb_rm', $p),
        'bksb_re' => get_string('bksb_re', $p),
        'learning_s' => get_string('learning_s', $p),
        'skill_slr' => get_string('skill_slr', $p),
        'skill_ser' => get_string('skill_ser', $p),
        'choose_ls' => get_string('choose_ls', $p),
        'visual_txt' => get_string('visual', $p),
        'auditory_txt' => get_string('auditory', $p),
        'kinaesthetic_txt' => get_string('kinaesthetic', $p),
        'otj_c' => get_string('otj_c', $p),
        'apprentice_hpw' => get_string('apprentice_hpw', $p),
        'weeks_op' => get_string('weeks_op', $p),
        'less_al' => get_string('less_al', $p),
        'hours_pw' => get_string('hours_pw', $p),
        'aspirations_acg' => get_string('aspirations_acg', $p),
        'area_os' => get_string('area_os', $p),
        'long_tg' => get_string('long_tg', $p),
        'short_tg' => get_string('short_tg', $p),
        'iag_title' => get_string('iag_title', $p),
        'recognition_title' => get_string('recognition_title', $p),
        'modules' => get_string('modules', $p),
        'planned_sd' => get_string('planned_sd', $p),
        'revised_sd' => get_string('revised_sd', $p),
        'planned_ed' => get_string('planned_ed', $p),
        'revised_ed' => get_string('revised_ed', $p),
        'module_w' => get_string('module_w', $p),
        'planned_otjh' => get_string('planned_otjh', $p),
        'method_od' => get_string('method_od', $p),
        'otj_t' => get_string('otj_t', $p),
        'actual_otjhc' => get_string('actual_otjhc', $p),
        'totals' => get_string('totals', $p),
        'required_fs' => get_string('required_fs', $p),
        'functional_sd' => get_string('functional_sd', $p),
        'functional_s' => get_string('functional_s', $p),
        'level' => get_string('level', $p),
        'actual_ed' => get_string('actual_ed', $p),
        'actual_ead' => get_string('actual_ead', $p),
        'progress_r' => get_string('progress_r', $p),
        'type_or' => get_string('type_or', $p),
        'planned_r' => get_string('planned_r', $p),
        'actual_r' => get_string('actual_r', $p),
        'learner_employer' => get_string('learner_employer', $p),
        'learner' => get_string('learner', $p),
        'add_nr' => get_string('add_nr', $p),
        'remove_r' => get_string('remove_r', $p),
        'additional_sa' => get_string('additional_sa', $p),
        'changes_log_title' => get_string('changes_log_title', $p),
        'date_oc' => get_string('date_oc', $p),
        'log' => get_string('log', $p),
        'submit' => get_string('submit', $p),
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
        'prbtns' => [[]]
    ];
    echo $OUTPUT->render_from_template('local_offthejobadmin/trainingplan', $template);
    $_SESSION['otj_adminplan'] = true;
    $_SESSION['otj_adminplan_uid'] = $uid;
    $_SESSION['otj_adminplan_cid'] = $cid;
    \local_offthejobadmin\event\viewed_user_trainingplan::create(array('context' => \context_course::instance($cid), 'courseid' => $cid, 'relateduserid' => $uid))->trigger();
}
echo $OUTPUT->footer();
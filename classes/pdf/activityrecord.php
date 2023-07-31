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
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/tcpdf/tcpdf.php');
    class MYPDF extends TCPDF {
        public function Header(){
            $this->Image('./../img/ntalogo.png', $this->GetPageWidth() - 32, $this->GetPageHeight() - 22, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        }
        public function Footer(){

        }
    }
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $coursename = $lib->get_course_fullname($cid);
    $pdf->addPage('P', 'A4');
    $pdf->Cell(0, 0, "Activity Record - $fullname - $coursename", 0, 0, 'C', 0, '', 0);
    $pdf->Ln();
    $data = $lib->get_activityrecord_data($cid, $uid, $id);
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Apprentice</b></th>
                    <th><b>Review Date</b></th>
                    <th><b>Standard</b></th>
                    <th><b>Employer or Store</b></th>
                    <th><b>Coach</b></th>
                    <th><b>Manager or Mentor</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[0].'</td>
                    <td>'.date('d/m/Y',strtotime($data[1])).'</td>
                    <td>'.$data[2].'</td>
                    <td>'.$data[3].'</td>
                    <td>'.$data[4].'</td>
                    <td>'.$data[5].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th colspan="24"><b>Summary of progress</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="3"><b>Course % Progress to date</b></th>
                    <td colspan="3">'.$data[6].'</td>
                    <th colspan="3"><b>Course % Expected Progress according to Training Plan</b></th>
                    <td colspan="3">'.$data[7].'</td>
                    <th colspan="3"><b>Comments</b></th>
                    <td colspan="9">'.$data[8].'</td>
                </tr>
                <tr>
                    <th colspan="3"><b>OTJH Completed</b></th>
                    <td colspan="3">'.$data[9].'</td>
                    <th colspan="3"><b>Expected OTJH as per Training Plan</b></th>
                    <td colspan="3">'.$data[10].'</td>
                    <th colspan="3"><b>Comments</b></th>
                    <td colspan="9">'.$data[11].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>E & D, H & S, Safeguarding & Learner Welfare (LDC to check understanding & link to a vocational context. Any issues must be actioned accordingly)</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[23].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Recap on actions from last month</b></th>
                    <th><b>What impact has this had in your current job role/situation</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[12].'</td>
                    <td>'.$data[13].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Details of Teaching & Learning activity undertaken today (include reference to modules and knowledge, skills & behaviours)</b></th>
                    <th><b>Modules and K,S,B</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[14].'</td>
                    <td>'.$data[15].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>What impact will this have in your job role/situation</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[16].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th colspan="3"><b>Functional Skills Progress</b></th>
                </tr>
                <tr>
                    <th></th>
                    <th><b>Learning today</b></th>
                    <th><b>Target for next visit</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><b>Math</b></th>
                    <td>'.$data[17].'</td>
                    <td>'.$data[18].'</td>
                </tr>
                <tr>
                    <th><b>English</b></th>
                    <td>'.$data[19].'</td>
                    <td>'.$data[20].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>ALN (Additional Learner Needs) Suppot delivered today</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[21].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Agreed actions & future skills development activity</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[24].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Coach/Tutor Feedback</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[22].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Apprentice Comments regarding their learning journey</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[25].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <tr>
                <th><b>Employer comment on progress</b></th>
                <td>MonthlyActivityRecord-'.str_replace(' ','_',$fullname).'-'.str_replace(' ','_',$coursename).'-'.$data[1].'-EmployerComment.pdf</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <tr>
                <th><b>Date & time of next planned review</b></th>
                <td>'.date('H:m d-m-Y',(new DateTime($data[31]))->format('U')).'</td>
            </tr>
            <tr>
                <th><b>Remote / Face to Face</b></th>
                <td>'.$data[32].'</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>Learner Signature</b></th>
                    <th><b>Coach Signature</b></th>
                </tr>
            </thead>
            <tbody>';
    $html .= '<tr>';
    $html .= ($data[27] != '1970-01-01' && $data[29]) ? '<td><img src="@'.preg_replace('#^data:image/[^;]+;base64,#', '', $data[29]).'"></td>' : '<td></td>';
    $html .= ($data[28] != '1970-01-01' && $data[30]) ? '<td><img src="@'.preg_replace('#^data:image/[^;]+;base64,#', '', $data[30]).'"></td>' : '<td></td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= ($data[27] != '1970-01-01') ? '<td>'.$data[27].'</td>' : '<td></td>';
    $html .= ($data[28] != '1970-01-01') ? '<td>'.$data[28].'</td>' : '<td></td>';
    $html .= '</tr>';
    $html .= '
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $pdf->Output("ActivityRecord-$fullname-$coursename-$data[1].pdf");
    \local_offthejobadmin\event\viewed_user_activityrecord_pdf::create(array('context' => \context_course::instance($cid), 'courseid' => $cid, 'relateduserid' => $uid))->trigger();
}
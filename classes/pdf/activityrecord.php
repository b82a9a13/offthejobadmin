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

$id = null;
$cid = null;
$uid = null;
$fullname = '';
$errorTxt = '';
if(!isset($_GET['id'])){
    $errorTxt = get_string('no_ip', $p);
} elseif(!isset($_GET['cid'])){
    $errorTxt = get_string('no_cip', $p);
} elseif(!isset($_GET['uid'])){
    $errorTxt = get_string('no_uip', $p);
} else {
    $id = $_GET['id'];
    $cid = $_GET['cid'];
    $uid = $_GET['uid'];
    $numMatch = "/^[0-9]*$/";
    if(!preg_match($numMatch, $id) || empty($id)){
        $errorTxt = get_string('invalid_ip', $p);
    } elseif(!preg_match($numMatch, $cid) || empty($cid)){
        $errorTxt = get_string('invalid_cip', $p);
    } elseif(!preg_match($numMatch, $uid) || empty($uid)){
        $errorTxt = get_string('invalid_uid', $p);
    } else {
        $fullname = $lib->check_learner_enrolment($cid, $uid);
        if($fullname == false){
            $errorTxt = get_string('selected_uneal', $p);
        } else {
            if(!$lib->check_setup_exists($cid, $uid)){
                $errorTxt = get_string('initial_sdne', $p);
            } else {
                if(!$lib->check_activityrecord_exists($cid, $uid)){
                    $errorTxt = get_string('no_ara', $p);
                }
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
    $pdf->Cell(0, 0, get_string('activity_rec', $p)." - $fullname - $coursename", 0, 0, 'C', 0, '', 0);
    $pdf->Ln();
    $data = $lib->get_activityrecord_data($cid, $uid, $id);
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>'.get_string('apprentice', $p).'</b></th>
                    <th><b>'.get_string('review_date', $p).'</b></th>
                    <th><b>'.get_string('standard', $p).'</b></th>
                    <th><b>'.get_string('employer_os', $p).'</b></th>
                    <th><b>'.get_string('coach', $p).'</b></th>
                    <th><b>'.get_string('manager_om', $p).'</b></th>
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
    $commentsTxt = get_string('comments', $p);
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th colspan="24"><b>'.get_string('summary_op', $p).'</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="3"><b>'.get_string('course_ptd', $p).'</b></th>
                    <td colspan="3">'.$data[6].'</td>
                    <th colspan="3"><b>'.get_string('course_epattp', $p).'</b></th>
                    <td colspan="3">'.$data[7].'</td>
                    <th colspan="3"><b>'.$commentsTxt.'</b></th>
                    <td colspan="9">'.$data[8].'</td>
                </tr>
                <tr>
                    <th colspan="3"><b>'.get_string('otjh_c', $p).'</b></th>
                    <td colspan="3">'.$data[9].'</td>
                    <th colspan="3"><b>'.get_string('expected_otjh_aptp', $p).'</b></th>
                    <td colspan="3">'.$data[10].'</td>
                    <th colspan="3"><b>'.$commentsTxt.'</b></th>
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
                    <th><b>'.get_string('safeguarding', $p).'</b></th>
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
                    <th><b>'.get_string('health_as', $p).'</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[33].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>'.get_string('equality_ad', $p).'</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[34].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>'.get_string('information_aag', $p).'</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'.$data[35].'</td>
                </tr>
            </tbody>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $impactTxt = get_string('impact_title', $p);
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>'.get_string('recap_title', $p).'</b></th>
                    <th><b>'.$impactTxt.'</b></th>
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
                    <th><b>'.get_string('details_title', $p).'</b></th>
                    <th><b>'.get_string('modules_askb', $p).'</b></th>
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
                    <th><b>'.$impactTxt.'</b></th>
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
    if($data[17] != '' || $data[18] != '' || $data[19] != '' || $data[20] != ''){
        $html = '
            <table border="1" cellpadding="2">
                <thead>
                    <tr>
                        <th colspan="3"><b>'.get_string('functional_sp', $p).'</b></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><b>'.get_string('learning_t', $p).'</b></th>
                        <th><b>'.get_string('target_title', $p).'</b></th>
                    </tr>
                </thead>
                <tbody>
        ';
        if($data[17] != '' || $data[18] != ''){
            $html .= '
                    <tr>
                        <th><b>Math</b></th>
                        <td>'.$data[17].'</td>
                        <td>'.$data[18].'</td>
                    </tr>
            ';
        }
        if($data[19] != '' || $data[20] != ''){
            $html .= '
                    <tr>
                        <th><b>English</b></th>
                        <td>'.$data[19].'</td>
                        <td>'.$data[20].'</td>
                    </tr>
            ';
        }
        $html .= '
                </tbody>
            </table>
        ';
        $pdf->writeHTML($html, true, false, false, false, '');
    }
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>'.get_string('aln_title', $p).'</b></th>
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
                    <th><b>'.get_string('agreed_title', $p).'</b></th>
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
                    <th><b>'.get_string('coach_feedback', $p).'</b></th>
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
                    <th><b>'.get_string('apprentice_ct', $p).'</b></th>
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
                <th><b>'.get_string('employer_comment', $p).'</b></th>
                <td>MonthlyActivityRecord-'.str_replace(' ','_',$fullname).'-'.str_replace(' ','_',$coursename).'-'.$data[1].'-EmployerComment.pdf</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <tr>
                <th><b>'.get_string('date_onpr', $p).'</b></th>
                <td>'.date('H:m d-m-Y',(new DateTime($data[31]))->format('U')).'</td>
            </tr>
            <tr>
                <th><b>'.get_string('remote_ftf', $p).'</b></th>
                <td>'.$data[32].'</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($html, true, false, false, false, '');
    $html = '
        <table border="1" cellpadding="2">
            <thead>
                <tr>
                    <th><b>'.get_string('learner_s', $p).'</b></th>
                    <th><b>'.get_string('coach_s', $p).'</b></th>
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
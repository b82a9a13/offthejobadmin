<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_actrec'] && $_SESSION['otj_actrec_cid'] && $_SESSION['otj_actrec_uid']){
    $id = $_POST['id'];
    if(empty($id) || !preg_match("/^[0-9]*$/", $id)){
        $returnText->error = get_string('invalid_np', $p);
    } else {
        $data = $lib->get_activityrecord_data($_SESSION['otj_actrec_cid'], $_SESSION['otj_actrec_uid'], $id);
        if($data != []){
            $_SESSION['otj_actrec_rid'] = $id;
            $array = [
                ['apprentice', $data[0]],
                ['reviewdate', $data[1]],
                ['standard', $data[2]],
                ['eors', $data[3]],
                ['coach', $data[4]],
                ['morm', $data[5]],
                ['coursep', $data[6]],
                ['courseep', $data[7]],
                ['coursecomment', $data[8]],
                ['otjhc', $data[9]],
                ['otjhe', $data[10]],
                ['otjhcomment', $data[11]],
                ['recap', $data[12]],
                ['recapimpact', $data[13]],
                ['details', $data[14]],
                ['detailsmod', $data[15]],
                ['impact', $data[16]],
                ['mathtoday', $data[17]],
                ['mathnext', $data[18]],
                ['engtoday', $data[19]],
                ['engnext', $data[20]],
                ['aln', $data[21]],
                ['coachfeed', $data[22]],
                ['safeguard', $data[23]],
                ['agreedact', $data[24]],
                ['apprencom', $data[25]],
                ['activityrecord_title', 'Edit Activity Record'],
                ['ar_form_script', './amd/min/editrecord.min.js'],
                ['ar_sign_div', 'flex'],
                ['filesrc', "./classes/pdf/activityrecord_employercomment_include.php?id=".$id],
                ['nextdate', $data[31]],
                ['remotef2f', $data[32]],
                ['hands', $data[33]],
                ['eandd', $data[34]],
                ['iaag', $data[35]]
            ];
            if($data[27] != '1970-01-01'){
                array_push($array, ['coachsigndate', $data[27]]);
                array_push($array, ['coachsignimg', $data[30]]);
            }
            if($data[28] != '1970-01-01'){
                array_push($array, ['learnsigndate', $data[28]]);
                array_push($array, ['learnsignimg', $data[29]]);
            }
            $returnText->return = $array;
            \local_offthejobadmin\event\viewed_user_activityrecord::create(array('context' => \context_course::instance($_SESSION['otj_actrec_cid']), 'courseid' => $_SESSION['otj_actrec_cid'], 'relateduserid' => $_SESSION['otj_actrec_uid'], 'other' => $id))->trigger();
        } else {
            $returnText->error = get_string('no_da', $p);
        }
    }
} else {
    $returnText->error = get_string('error_gd', $p);
}
echo(json_encode($returnText));
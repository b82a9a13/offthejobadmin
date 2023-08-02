<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_actrec'] && $_SESSION['otj_actrec_cid'] && $_SESSION['otj_actrec_uid'] && $_SESSION['otj_actrec_rid']){
    //Validation regex
    $date = "/^[0-9\-]*$/";
    $name = "/^[a-z A-Z'\-]*$/";
    $number = "/^[0-9]*$/";
    $textarea = "/^[a-zA-Z0-9 ,.!'():;\s\-#\/]*$/";
    $numberReplace = "/[0-9]/";
    $nameReplace = "/[a-z A-Z'\-]/";
    $textareaReplace = "/[a-zA-Z0-9 ,.!'():;\s\-#\/]/";

    //Validation and Error Checking
    $errorarray = [];
    $apprentice = $_POST['apprentice'];
    if(!preg_match($name, $apprentice) || empty($apprentice)){
        array_push($errorarray, ['apprentice', get_string('apprentice', $p).':'.preg_replace($nameReplace,'', $apprentice)]);
    }
    $reviewdate = $_POST['reviewdate'];
    if($reviewdate != null && !empty($reviewdate)){
        if(!preg_match($date, $reviewdate)){
            array_push($errorarray, ['reviewdate', get_string('review_date', $p)]);
        } else {
            $reviewdate = (new DateTime($reviewdate))->format('U');
        }
    } else {
        array_push($errorarray, ['reviewdate', get_string('review_date', $p)]);
    }
    $standard = $_POST['standard'];
    if(!preg_match("/^[a-z A-Z0-9()\-]*$/", $standard) || empty($standard)){
        array_push($errorarray, ['standard', get_string('standard', $p).':'.preg_replace("/[a-z A-Z0-9()\-]/",'', $standard)]);
    }
    $eors = $_POST['eors'];
    if(!preg_match("/^[a-z A-Z\-0-9]*$/", $eors) || empty($eors)){
        array_push($errorarray, ['eors', get_string('employer_os', $p).':'.preg_replace("/[a-z A-Z\-0-9]/",'', $eors)]);
    }
    $coach = $_POST['coach'];
    if(!preg_match($name, $coach) || empty($coach)){
        array_push($errorarray, ['coach', get_string('coach', $p).':'.preg_replace($nameReplace,'', $coach)]);
    }
    $morm = $_POST['morm'];
    if(!preg_match($name, $morm) || empty($morm)){
        array_push($errorarray, ['morm', get_string('manager_om', $p).':'.preg_replace($nameReplace,'', $morm)]);
    }
    $coursep = $_POST['coursep'];
    if(!preg_match($number, $coursep) || empty($coursep)){
        array_push($errorarray, ['coursep', get_string('course_ptd', $p).':'.preg_replace($numberReplace,'', $coursep)]);
    }
    $courseep = $_POST['courseep'];
    if(!preg_match($number, $courseep) || empty($courseep)){
        array_push($errorarray, ['courseep', get_string('course_epattp_short', $p).':'.preg_match($numberReplace,'', $courseep)]);
    }
    $coursecomment = $_POST['coursecomment'];
    if(!preg_match($textarea, $coursecomment) || empty($coursecomment)){
        array_push($errorarray, ['coursecomment', get_string('course_c', $p).':'.preg_replace($textareaReplace,'', $coursecomment)]);
    }
    $otjhc = $_POST['otjhc'];
    if(!preg_match($number, $otjhc) || empty($otjhc)){
        array_push($errorarray, ['otjhc', get_string('otjh_c', $p).':'.preg_replace($numberReplace,'', $otjhc)]);
    }
    $otjhe = $_POST['otjhe'];
    if(!preg_match($number, $otjhe) || empty($otjhe)){
        array_push($errorarray, ['otjhe', get_string('expected_otjh', $p).':'.preg_replace($numberReplace,'', $otjhe)]);
    }
    $otjhcomment = $_POST['otjhcomment'];
    if(!preg_match($textarea, $otjhcomment) || empty($otjhcomment)){
        array_push($errorarray, ['otjhcomment', get_string('otjh_com', $p).':'.preg_replace($textareaReplace,'', $otjhcomment)]);
    }
    $recap = $_POST['recap'];
    if(!preg_match($textarea, $recap)){
        array_push($errorarray, ['recap', get_string('recap_oa', $p).':'.preg_replace($textareaReplace,'', $recap)]);
    }
    $recapimpact = $_POST['recapimpact'];
    if(!preg_match($textarea, $recapimpact)){
        array_push($errorarray, ['recapimpact', get_string('what_i', $p).':'.preg_replace($textareaReplace,'', $recapimpact)]);
    }
    $details = $_POST['details'];
    if(!preg_match($textarea, $details) || empty($details)){
        array_push($errorarray, ['details', get_string('details_otal', $p).':'.preg_replace($textareaReplace,'', $details)]);
    }
    $detailsmod = $_POST['detailsmod'];
    if(!preg_match($textarea, $detailsmod) || empty($detailsmod)){
        array_push($errorarray, ['detailsmod', get_string('modules_askb', $p).':'.preg_replace($textareaReplace,'', $detailsmod)]);
    }
    $impact = $_POST['impact'];
    if(!preg_match($textarea, $impact) || empty($impact)){
        array_push($errorarray, ['impact', get_string('what_i', $p).':'.preg_replace($textareaReplace,'', $impact)]);
    }
    $mathtoday = $_POST['mathtoday'];
    if(!preg_match($textarea, $mathtoday) || empty($mathtoday)){
        array_push($errorarray, ['mathtoday', get_string('math_lt', $p).':'.preg_replace($textareaReplace,'', $mathtoday)]);
    }
    $mathnext = $_POST['mathnext'];
    if(!preg_match($textarea, $mathnext) || empty($mathnext)){
        array_push($errorarray, ['mathnext', get_string('math_t', $p).':'.preg_replace($textareaReplace,'', $mathnext)]);
    }
    $engtoday = $_POST['engtoday'];
    if(!preg_match($textarea, $engtoday) || empty($engtoday)){
        array_push($errorarray, ['engtoday', get_string('english_lt', $p).':'.preg_replace($textareaReplace,'', $engtoday)]);
    }
    $engnext = $_POST['engnext'];
    if(!preg_match($textarea, $engnext) || empty($engnext)){
        array_push($errorarray, ['engnext', get_string('english_t', $p).':'.preg_replace($textareaReplace,'', $engnext)]);
    }
    $aln = $_POST['aln'];
    if(!preg_match($textarea, $aln) || empty($aln)){
        array_push($errorarray, ['aln', get_string('aln', $p).':'.preg_replace($textareaReplace,'', $aln)]);
    }
    $coachfeed = $_POST['coachfeed'];
    if(!preg_match($textarea, $coachfeed) || empty($coachfeed)){
        array_push($errorarray, ['coachfeed', get_string('otj_a', $p).':'.preg_replace($textareaReplace,'', $coachfeed)]);
    }
    $safeguard = $_POST['safeguard'];
    if(!preg_match($textarea, $safeguard) || empty($safeguard)){
        array_push($errorarray, ['safeguard', get_string('safeguarding', $p).':'.preg_replace($textareaReplace,'', $safeguard)]);
    }
    $agreedact = $_POST['agreedact'];
    if(!preg_match($textarea, $agreedact) || empty($agreedact)){
        array_push($errorarray, ['agreedact', get_string('agreed_a', $p).':'.preg_replace($textareaReplace,'', $agreedact)]);
    }
    $file = $_FILES['file'];
    $fileArr = [];
    if(!empty($file['name'])){
        $filename = $file['name'];
        $filetmpname = $file['tmp_name'];
        $filesize = $file['size'];
        $fileerror = $file['error'];
        $filetype = $file['type'];
        $fileext = strtolower(end(explode('.',$filename)));
        if(in_array($fileext, ['pdf'])){
            if($fileerror === 0){
                if($filesize < 2500000){
                    $filenamenew = uniqid().''.uniqid().'.pdf';
                    $filedestination = '../../../activityrecord/classes/pdf/employercomment/'.$filenamenew;
                    $fileArr = [$filetmpname, $filedestination];
                    $file = $filenamenew;
                } else {
                    array_push($errorarray, ['file', get_string('file_ms', $p)]);
                }
            } else {
                array_push($errorarray, ['file', get_string('file_i', $p)]);
            }
        } else {
            array_push($errorarray, ['file', get_string('file_po', $p)]);
        }
    }
    $apprencom = $_POST['apprencom'];
    if(!preg_match($textarea, $apprencom)){
        array_push($errorarray, ['apprencom', get_string('apprentice_c', $p).':'.preg_replace($textareaReplace,'', $apprencom)]);
    }
    $nextdate = $_POST['nextdate'];
    if($nextdate != null && !empty($nextdate)){
        if(!preg_match("/^[0-9\-T:]*$/", $nextdate)){
            array_push($errorarray, ['nextdate', get_string('next_pr', $p)]);
        } else {
            $nextdate = (new DateTime($nextdate))->format('U');
        }
    } else {
        array_push($errorarray, ['nextdate', get_string('next_pr', $p)]);
    }
    $remotef2f = $_POST['remotef2f'];
    if($remotef2f != 'remote' && $remotef2f != 'f2f'){
        array_push($errorarray, ['remotef2f', get_string('remote_ftf', $p)]);
    }
    if($errorarray != []){
        $returnText->error = $errorarray;
    } else {
        $result = $lib->update_activityrecord($_SESSION['otj_actrec_cid'], $_SESSION['otj_actrec_uid'], $_SESSION['otj_actrec_rid'], [
            $apprentice,
            $reviewdate,
            $standard,
            $eors,
            $coach,
            $morm,
            $coursep,
            $courseep,
            $coursecomment,
            $otjhc,
            $otjhe,
            $otjhcomment,
            $recap,
            $recapimpact,
            $details,
            $detailsmod,
            $impact,
            $mathtoday,
            $mathnext,
            $engtoday,
            $engnext,
            $aln,
            $coachfeed,
            $safeguard,
            $agreedact,
            $file,
            $apprencom,
            $nextdate,
            $remotef2f
        ]);
        if($result){
            if($fileArr != []){
                move_uploaded_file($fileArr[0], $fileArr[1]);
            }
            $returnText->return = true;
        } else {
            $returnText->return = false;
        }
    }
} else {
    $returnText->return = false;
}
echo(json_encode($returnText));
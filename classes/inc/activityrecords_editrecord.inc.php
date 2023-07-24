<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
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
        array_push($errorarray, ['apprentice', 'Apprentice:'.preg_replace($nameReplace,'', $apprentice)]);
    }
    $reviewdate = $_POST['reviewdate'];
    if($reviewdate != null && !empty($reviewdate)){
        if(!preg_match($date, $reviewdate)){
            array_push($errorarray, ['reviewdate', 'Review Date']);
        } else {
            $reviewdate = (new DateTime($reviewdate))->format('U');
        }
    } else {
        array_push($errorarray, ['reviewdate', 'Review Date']);
    }
    $standard = $_POST['standard'];
    if(!preg_match("/^[a-z A-Z0-9()\-]*$/", $standard) || empty($standard)){
        array_push($errorarray, ['standard', 'Standard:'.preg_replace("/[a-z A-Z0-9()\-]/",'', $standard)]);
    }
    $eors = $_POST['eors'];
    if(!preg_match("/^[a-z A-Z\-0-9]*$/", $eors) || empty($eors)){
        array_push($errorarray, ['eors', 'Employer or Store:'.preg_replace("/[a-z A-Z\-0-9]/",'', $eors)]);
    }
    $coach = $_POST['coach'];
    if(!preg_match($name, $coach) || empty($coach)){
        array_push($errorarray, ['coach', 'Coach:'.preg_replace($nameReplace,'', $coach)]);
    }
    $morm = $_POST['morm'];
    if(!preg_match($name, $morm) || empty($morm)){
        array_push($errorarray, ['morm', 'Manager/Mentor:'.preg_replace($nameReplace,'', $morm)]);
    }
    $coursep = $_POST['coursep'];
    if(!preg_match($number, $coursep) || empty($coursep)){
        array_push($errorarray, ['coursep', 'Course % Progress to date:'.preg_replace($numberReplace,'', $coursep)]);
    }
    $courseep = $_POST['courseep'];
    if(!preg_match($number, $courseep) || empty($courseep)){
        array_push($errorarray, ['courseep', 'Course % Expected Progress:'.preg_match($numberReplace,'', $courseep)]);
    }
    $coursecomment = $_POST['coursecomment'];
    if(!preg_match($textarea, $coursecomment) || empty($coursecomment)){
        array_push($errorarray, ['coursecomment', 'Course Comments:'.preg_replace($textareaReplace,'', $coursecomment)]);
    }
    $otjhc = $_POST['otjhc'];
    if(!preg_match($number, $otjhc) || empty($otjhc)){
        array_push($errorarray, ['otjhc', 'OTJH Completed:'.preg_replace($numberReplace,'', $otjhc)]);
    }
    $otjhe = $_POST['otjhe'];
    if(!preg_match($number, $otjhe) || empty($otjhe)){
        array_push($errorarray, ['otjhe', 'Expected OTJH:'.preg_replace($numberReplace,'', $otjhe)]);
    }
    $otjhcomment = $_POST['otjhcomment'];
    if(!preg_match($textarea, $otjhcomment) || empty($otjhcomment)){
        array_push($errorarray, ['otjhcomment', 'OTJH Comments:'.preg_replace($textareaReplace,'', $otjhcomment)]);
    }
    $recap = $_POST['recap'];
    if(!preg_match($textarea, $recap)){
        array_push($errorarray, ['recap', 'Recap on actions:'.preg_replace($textareaReplace,'', $recap)]);
    }
    $recapimpact = $_POST['recapimpact'];
    if(!preg_match($textarea, $recapimpact)){
        array_push($errorarray, ['recapimpact', 'What impact:'.preg_replace($textareaReplace,'', $recapimpact)]);
    }
    $details = $_POST['details'];
    if(!preg_match($textarea, $details) || empty($details)){
        array_push($errorarray, ['details', 'Details of Teaching & Learning:'.preg_replace($textareaReplace,'', $details)]);
    }
    $detailsmod = $_POST['detailsmod'];
    if(!preg_match($textarea, $detailsmod) || empty($detailsmod)){
        array_push($errorarray, ['detailsmod', 'Modules and K,S,B:'.preg_replace($textareaReplace,'', $detailsmod)]);
    }
    $impact = $_POST['impact'];
    if(!preg_match($textarea, $impact) || empty($impact)){
        array_push($errorarray, ['impact', 'What impact:'.preg_replace($textareaReplace,'', $impact)]);
    }
    $mathtoday = $_POST['mathtoday'];
    if(!preg_match($textarea, $mathtoday) || empty($mathtoday)){
        array_push($errorarray, ['mathtoday', 'Math, Learning today:'.preg_replace($textareaReplace,'', $mathtoday)]);
    }
    $mathnext = $_POST['mathnext'];
    if(!preg_match($textarea, $mathnext) || empty($mathnext)){
        array_push($errorarray, ['mathnext', 'Math, Target:'.preg_replace($textareaReplace,'', $mathnext)]);
    }
    $engtoday = $_POST['engtoday'];
    if(!preg_match($textarea, $engtoday) || empty($engtoday)){
        array_push($errorarray, ['engtoday', 'English, Learning today:'.preg_replace($textareaReplace,'', $engtoday)]);
    }
    $engnext = $_POST['engnext'];
    if(!preg_match($textarea, $engnext) || empty($engnext)){
        array_push($errorarray, ['engnext', 'English, Target:'.preg_replace($textareaReplace,'', $engnext)]);
    }
    $aln = $_POST['aln'];
    if(!preg_match($textarea, $aln) || empty($aln)){
        array_push($errorarray, ['aln', 'ALN:'.preg_replace($textareaReplace,'', $aln)]);
    }
    $coachfeed = $_POST['coachfeed'];
    if(!preg_match($textarea, $coachfeed) || empty($coachfeed)){
        array_push($errorarray, ['coachfeed', 'Off the Job Activity:'.preg_replace($textareaReplace,'', $coachfeed)]);
    }
    $safeguard = $_POST['safeguard'];
    if(!preg_match($textarea, $safeguard) || empty($safeguard)){
        array_push($errorarray, ['safeguard', 'Safeguarding:'.preg_replace($textareaReplace,'', $safeguard)]);
    }
    $agreedact = $_POST['agreedact'];
    if(!preg_match($textarea, $agreedact) || empty($agreedact)){
        array_push($errorarray, ['agreedact', 'Agreed actions:'.preg_replace($textareaReplace,'', $agreedact)]);
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
                    array_push($errorarray, ['file', 'File:max size 2.5mb']);
                }
            } else {
                array_push($errorarray, ['file', 'File:invalid']);
            }
        } else {
            array_push($errorarray, ['file', 'File:pdf only']);
        }
    }
    $apprencom = $_POST['apprencom'];
    if(!preg_match($textarea, $apprencom)){
        array_push($errorarray, ['apprencom', 'Apprentice Comments:'.preg_replace($textareaReplace,'', $apprencom)]);
    }
    $coachsigndate = $_POST['coachsigndate'];
    if($coachsigndate != null && !empty($coachsigndate)){
        if(!preg_match($date, $coachsigndate)){
            array_push($errorarray, ['reviewdate', 'Review Date']);
        } else {
            $coachsigndate = (new DateTime($coachsigndate))->format('U');
        }
    }
    $nextdate = $_POST['nextdate'];
    if($nextdate != null && !empty($nextdate)){
        if(!preg_match("/^[0-9\-T:]*$/", $nextdate)){
            array_push($errorarray, ['nextdate', 'Next planned review']);
        } else {
            $nextdate = (new DateTime($nextdate))->format('U');
        }
    } else {
        array_push($errorarray, ['nextdate', 'Next planned review']);
    }
    $remotef2f = $_POST['remotef2f'];
    if($remotef2f != 'remote' && $remotef2f != 'f2f'){
        array_push($errorarray, ['remotef2f', 'Remote / Face to Face']);
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
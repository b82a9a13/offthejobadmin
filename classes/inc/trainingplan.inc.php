<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';

//Validation regex
$textarea = "/^[a-zA-Z0-9 ,.!'():;\s\-#]*$/";
$number = "/^[0-9]*$/";
$decimals = "/^[0-9.]*$/";
$date = "/^[0-9\-]*$/";
$numberReplace = "/[0-9]/";
$decimalsReplace = "/[0-9.]/";
$textareaReplace = "/[a-zA-Z0-9 ,.!'():;\s\-#]/";

//Validation and Error checking
$errorArray = [];
if($_SESSION['otj_adminplan'] && $_SESSION['otj_adminplan_cid'] && $_SESSION['otj_adminplan_uid']){
    $startdateTxt = get_string('start_date', $p);
    $name = $_POST['name'];
    if(!preg_match("/^[a-z A-Z'\-]*$/",$name) || empty($name)){
        array_push($errorArray, ['name', get_string('name', $p).':'.preg_replace("/[a-z A-Z'\-]/",'',$name)]);
    }
    $employer = str_replace("($)","&",$_POST['employer']);
    if(!preg_match("/^[a-z A-Z'\-&]*$/", $employer) || empty($employer)){
        array_push($errorArray, ['employer', get_string('employer', $p).':'.preg_replace("/[a-z A-Z'\-&]/",'',$employer)]);
    }
    $startdate = $_POST['startdate'];
    if($startdate != null && !empty($startdate)){
        if(!preg_match($date, $startdate)){
            array_push($errorArray, ['startdate', $startdateTxt]);
        } else {
            $startdate = (new DateTime($startdate))->format('U');
        }
    } else {
        array_push($errorArray, ['startdate', $startdateTxt]);
    }
    $planenddate = $_POST['planenddate'];
    if($planenddate != null && !empty($planenddate)){
        if(!preg_match($date, $planenddate)){
            array_push($errorArray, ['planenddate', get_string('planned_ed', $p)]);
        } else {
            $planenddate = (new DateTime($planenddate))->format('U');
        }
    } else {
        array_push($errorArray, ['planenddate', get_string('planned_ed', $p)]);
    }
    $lengthofprog = $_POST['lengthofprog'];
    if(!preg_match($number, $lengthofprog) || empty($lengthofprog)){
        array_push($errorArray, ['lengthofprog', get_string('length_op', $p).':'.preg_replace($numberReplace, '', $lengthofprog)]);
    }
    $otjh = $_POST['otjh'];
    if(!preg_match($number, $otjh) || empty($otjh)){
        array_push($errorArray, ['otjh', get_string('otjh', $p).':'.preg_replace($numberReplace, '', $otjh)]);
    }
    $epao = $_POST['epao'];
    if(!preg_match("/^[a-z A-Z]*$/", $epao) || empty($epao)){
        array_push($errorArray, ['epao', get_string('epao', $p).':'.preg_replace("/[a-z A-Z]/", '', $epao)]);
    }
    $fundsource = $_POST['fundsource'];
    if(($fundsource != 'levy' && $fundsource != 'contrib') || empty($fundsource)){
        array_push($errorArray, ['fundsource', get_string('funding_s', $p)]);
    }
    $bksbrm = $_POST['bksbrm'];
    if(!preg_match($number, $bksbrm) || empty($bksbrm)){
        array_push($errorArray, ['bksbrm', get_string('bksb_rm', $p).':'.preg_replace($numberReplace, '', $bksbrm)]);
    }
    $bksbre = $_POST['bksbre'];
    if(!preg_match($number, $bksbre) || empty($bksbre)){
        array_push($errorArray, ['bksbre', get_string('bksb_re', $p).':'.preg_replace($numberReplace, '', $bksbre)]);
    }
    $learnstyle = $_POST['learnstyle'];
    if(($learnstyle != 'visual' && $learnstyle != 'auditory' && $learnstyle != 'kinaesthetic') || empty($learnstyle)){
        array_push($errorArray, ['learnstyle', get_string('learning_s', $p)]);
    }
    $skillscanlr = $_POST['skillscanlr'];
    if(!preg_match("/^[0-9A-Za-z \/]*$/", $skillscanlr) || empty($skillscanlr)){
        array_push($errorArray, ['skillscanlr', get_string('skill_slr', $p).':'.preg_replace("/[0-9A-Za-z \/]/", '', $skillscanlr)]);
    }
    $skillscaner = $_POST['skillscaner'];
    if(!preg_match("/^[0-9A-Za-z \/]*$/", $skillscaner) || empty($skillscaner)){
        array_push($errorArray, ['skillscaner', get_string('skill_ser', $p).':'.preg_replace("/[0-9A-Za-z \/]/", '', $skillscaner)]);
    }
    $apprenhpw = $_POST['apprenhpw'];
    if(!preg_match($number, $apprenhpw) || empty($apprenhpw)){
        array_push($errorArray, ['apprenhpw', get_string('apprentice_hpw', $p).':'.preg_replace($numberReplace, '', $apprenhpw)]);
    }
    $weeksonprog = $_POST['weeksonprog'];
    if(!preg_match($number, $weeksonprog) || empty($weeksonprog)){
        array_push($errorArray, ['weeksonprog', get_string('weeks_op', $p).':'.preg_replace($numberReplace, '', $weeksonprog)]);
    }
    $annualleave = $_POST['annualleave'];
    if(!preg_match($decimals, $annualleave) || empty($annualleave)){
        array_push($errorArray, ['annualleave', get_string('less_al', $p).':'.preg_replace($decimalsReplace, '', $annualleave)]);
    }
    $hoursperweek = $_POST['hoursperweek'];
    if(!preg_match($number, $hoursperweek) || empty($hoursperweek)){
        array_push($errorArray, ['hoursperweek', get_string('hours_pw', $p).':'.preg_replace($numberReplace, '', $hoursperweek)]);
    }
    $aostrength = $_POST['aostrength'];
    if(!preg_match($textarea, $aostrength) || empty($aostrength)){
        array_push($errorArray, ['aostrength', get_string('area_os', $p).':'.preg_replace($textareaReplace, '', $aostrength)]);
    }
    $ltgoals = $_POST['ltgoals'];
    if(!preg_match($textarea, $ltgoals) || empty($ltgoals)){
        array_push($errorArray, ['ltgoals', get_string('long_tg', $p).':'.preg_replace($textareaReplace, '', $ltgoals)]);
    }
    $stgoals = $_POST['stgoals'];
    if(!preg_match($textarea, $stgoals) || empty($stgoals)){
        array_push($errorArray, ['stgoals', get_string('short_tg', $p).':'.preg_replace($textareaReplace, '', $stgoals)]);
    }
    $iaguide = $_POST['iaguide'];
    if(!preg_match($textarea, $iaguide) || empty($iaguide)){
        array_push($errorArray, ['iaguide', get_string('iag_title', $p).':'.preg_replace($textareaReplace, '', $iaguide)]);
    }
    $recopl = $_POST['recopl'];
    if(!preg_match($textarea, $recopl) || empty($recopl)){
        array_push($errorArray, ['recopl', get_string('recognition_opl', $p).':'.preg_replace($textareaReplace, '', $recopl)]);
    }
    $addsa = $_POST['addsa'];
    if(!preg_match($textarea, $addsa)){
        array_push($errorArray, ['addsa', get_string('additional_sa', $p).':'.preg_replace($textareaReplace, '', $addsa)]);
    }

    $fsArray = [
        ['mathfs', 'mathlevel', 'mathmod', 'mathsd', 'mathped', 'mathaed', 'mathaead'],
        ['engfs', 'englevel', 'engmod', 'engsd', 'engped', 'engaed', 'engaead']
    ];
    $int = 1;
    $fsValues = [];
    if(isset($_POST[$fsArray[0][0]]) && isset($_POST[$fsArray[1][0]])){
        $fsTxt = get_string('functional_s', $p);
        $lvlTxt = get_string('level', $p);
        $methodTxt = get_string('method_od', $p);
        $rowTxt = get_string('row', $p);
        $planedTxt = get_string('planned_ed', $p);
        $actedTxt = get_string('actual_ed', $p);
        $acteadTxt = get_string('actual_ead', $p);
        foreach($fsArray as $fsArr){
            $tmp = [$_POST[$fsArr[0]], $_POST[$fsArr[1]], str_replace("($)","&",$_POST[$fsArr[2]]), $_POST[$fsArr[3]], $_POST[$fsArr[4]], $_POST[$fsArr[5]], $_POST[$fsArr[6]]];
            if(($tmp[0] != 'Maths' && $tmp[0] != 'English' && $tmp[0] != 'ICT')){
                array_push($errorArray, [$fsArr[0], "$fsTxt, $fsTxt, $rowTxt $int"]);
            }
            if(!preg_match($number, $tmp[1])){
                array_push($errorArray, [$fsArr[1], "$fsTxt, $lvlTxt, $rowTxt $int:".preg_replace($numberReplace, '', $tmp[1])]);
            }
            if(!preg_match("/^[a-z A-Z&,0-9]*$/", $tmp[2])){
                array_push($errorArray, [$fsArr[2], "$fsTxt, $methodTxt, $rowTxt $int:".preg_replace("/[a-z A-Z&,0-9]/", '', $tmp[2])]);
            }
            if($tmp[3] != null && !empty($tmp[3])){
                if(!preg_match($date, $tmp[3])){
                    array_push($errorArray, [$fsArr[3], "$fsTxt, $startdateTxt, $rowTxt $int"]);
                } else {
                    $tmp[3] = (new DateTime($tmp[3]))->format('U');
                }
            } else {
                $tmp[3] = null;
            }
            if($tmp[4] != null && !empty($tmp[4])){
                if(!preg_match($date, $tmp[4])){
                    array_push($errorArray, [$fsArr[4], "$fsTxt, $planedTxt, $rowTxt $int"]);
                } else {
                    $tmp[4] = (new DateTime($tmp[4]))->format('U');
                }
            } else {
                $tmp[4] = null;
            }
            if($tmp[5] != null && !empty($tmp[5])){
                if(!preg_match($date, $tmp[5])){
                    array_push($errorArray, [$fsArr[5], "$fsTxt, $actedTxt, $rowTxt $int"]);
                } else {
                    $tmp[5] = (new DateTime($tmp[5]))->format('U');
                }
            }
            if($tmp[6] != null && !empty($tmp[6])){
                if(!preg_match($date, $tmp[6])){
                    array_push($errorArray, [$fsArr[6], "$fsTxt, $acteadTxt, $rowTxt $int"]);
                } else {
                    $tmp[6] = (new DateTime($tmp[6]))->format('U');
                }
            }
            $int++;
            array_push($fsValues, $tmp);
        }
    }

    $modTotal = $_POST['mod-total'];
    $modArray = [];
    if(!preg_match($number, $modTotal) || empty($modTotal)){
        array_push($errorArray, ['mod-total', 'Invalid Module Total']);
    } else {
        $modTxt = get_string('modules', $p);
        $rowTxt = get_string('row', $p);
        $plansdTxt = get_string('planned_sd', $p);
        $planedTxt = get_string('planned_ed', $p);
        $modwTxt = get_string('module_w', $p);
        $planotjhTxt = get_string('planned_otjh', $p);
        $methodTxt = get_string('method_od', $p);
        $otjtTxt = get_string('otj_t', $p);
        $rsdTxt = get_string('revised_sd', $p);
        $redTxt = get_string('revised_ed', $p);
        for($i = 0; $i < $modTotal; $i++){
            $tmp = [str_replace('($)','&',$_POST["mod-m-$i"]), $_POST["mod-psd-$i"], $_POST["mod-ped-$i"], $_POST["mod-mw-$i"], $_POST["mod-potjh-$i"], $_POST["mod-mod-$i"], str_replace('($)','&',$_POST["mod-otjt-$i"]), $_POST["mod-rsd-$i"], $_POST["mod-red-$i"]];
            if(!preg_match("/^[a-z A-Z&,0-9\-]*$/", $tmp[0]) || empty($tmp[0])){
                array_push($errorArray, ["mod-m", "$modTxt, $modTxt, $rowTxt ".($i+1).':'.preg_replace("/[a-z A-Z&,0-9\-]/", '', $tmp[0]), $i]);
            }
            if($tmp[1] != null && !empty($tmp[1])){
                if(!preg_match($date, $tmp[1])){
                    array_push($errorArray, ["mod-psd", "$modTxt, $plansdTxt, $rowTxt ".($i+1), $i]);
                } else {
                    $tmp[1] = (new DateTime($tmp[1]))->format('U');
                }
            } else {
                array_push($errorArray, ["mod-psd", "$modTxt, $plansdTxt, $rowTxt ".($i+1), $i]);
            }
            if($tmp[2] != null && !empty($tmp[2])){
                if(!preg_match($date, $tmp[2])){
                    array_push($errorArray, ["mod-ped", "$modTxt, $planedTxt, $rowTxt ".($i+1), $i]);
                } else {
                    $tmp[2] = (new DateTime($tmp[2]))->format('U');
                }
            } else {
                array_push($errorArray, ["mod-ped", "$modTxt, $planedTxt, $rowTxt ".($i+1), $i]);
            }
            if(!preg_match($number, $tmp[3]) || empty($tmp[3])){
                array_push($errorArray, ["mod-mw", "$modTxt, $modwTxt, $rowTxt ".($i+1).":".preg_replace($numberReplace, '', $tmp[3]), $i]);
            }
            if(!preg_match($decimals, $tmp[4]) || empty($tmp[4])){
                array_push($errorArray, ["mod-potjh", "$modTxt, $planotjhTxt, $rowTxt ".($i+1).":".preg_replace($decimalsReplace, '', $tmp[4]), $i]);
            }
            if(!preg_match("/^[a-z A-Z,0-9]*$/", $tmp[5]) || empty($tmp[5])){
                array_push($errorArray, ["mod-mod", "$modTxt, $methodTxt, $rowTxt ".($i+1).":".preg_replace("/[a-z A-Z,0-9]/", '', $tmp[5]), $i]);
            }
            if(!preg_match($textarea, $tmp[6]) || empty($tmp[6])){
                array_push($errorArray, ["mod-otjt", "$modTxt, $otjtTxt, $rowTxt ".($i+1).":".preg_replace($textareaReplace, '', $tmp[6]), $i]);
            }
            if($tmp[7] != null && !empty($tmp[7])){
                if(!preg_match($date, $tmp[7])){
                    array_push($errorArray, ['mod-rsd', "$modTxt, $rsdTxt, $rowTxt ".($i+1), $i]);
                } else {
                    $tmp[7] = (new DateTime($tmp[7]))->format('U');
                }
            }
            if($tmp[8] != null && !empty($tmp[8])){
                if(!preg_match($date, $tmp[8])){
                    array_push($errorArray, ["mod-red", "$modTxt, $redTxt, $rowTxt ".($i+1), $i]);
                } else {
                    $tmp[8] = (new DateTime($tmp[8]))->format('U');
                }
            }
            array_push($modArray, $tmp);
        }
    }

    $prTotal = $_POST['pr-total'];
    $prArray = [];
    if(!preg_match($number, $prTotal) || empty($prTotal)){
        array_push($errorArray, ['pr-total', 'Invalid Progress Review Total']);
    } else {
        $rowTxt = get_string('row', $p);
        $prTxt = get_string('progress_r_short', $p);
        $typeTxt = get_string('type_or', $p);
        $planrTxt = get_string('planned_r', $p);
        $arTxt = get_string('actual_r', $p);
        for($i = 0; $i < $prTotal; $i++){
            $tmp = [$_POST["pr-type-$i"], $_POST["pr-pr-$i"], $_POST["pr-ar-$i"]];
            if(($tmp[0] != 'Learner' && $tmp[0] != 'Employer') || empty($tmp[0])){
                array_push($errorArray, ["pr-type", "$prTxt, $typeTxt, $rowTxt ".($i+1), $i]);
            }
            if(($tmp[1] != null && !empty($tmp[1]))){
                if(!preg_match($date, $tmp[1])){
                    array_push($errorArray, ["pr-pr", "$prTxt, $planrTxt, $rowTxt ".($i+1), $i]);
                } else {
                    $tmp[1] = (new DateTime($tmp[1]))->format('U');
                }
            } else {
                array_push($errorArray, ["pr-pr", "$prTxt, $planrTxt, $rowTxt ".($i+1), $i]);
            }
            if($tmp[2] != null && !empty($tmp[2])){
                if(!preg_match($date, $tmp[2])){
                    array_push($errorArray, ['pr-ar', "$prTxts, $arTxt, $rowTxt ".($i+1), $i]);
                } else {
                    $tmp[2] = (new DateTime($tmp[2]))->format('U');
                }
            }
            array_push($prArray, $tmp);
        }
    }

    $cldate = $_POST['cl_daterequired'];
    $clTxt = get_string('changes_log', $p);
    if($cldate != null && !empty($cldate)){
        if(!preg_match($date, $cldate)){
            array_push($errorArray, ['cl_daterequired', "$clTxt, ".get_string('date_oc', $p)]);
        } else {
            $cldate = (new DateTime($cldate))->format('U');
        }
    } else {
        array_push($errorArray, ['cl_daterequired', "$clTxt, ".get_string('date_oc', $p)]);
    }
    $cllog = $_POST['cl_logrequired'];
    if(!preg_match($textarea, $cllog) && !empty($cllog)){
        array_push($errorArray, ['cl_logrequired', "$clTxt, ".get_string('log', $p).':'.preg_replace($textareaReplace, '', $cllog)]);
    }
} else {
    $returnText->return = false;
}

if($errorArray != []){
    $returnText->error = $errorArray;
} else{
    $creation = $lib->submit_trainplan($_SESSION['otj_adminplan_cid'], $_SESSION['otj_adminplan_uid'], [
        $name,
        $employer,
        $startdate,
        $planenddate,
        $lengthofprog,
        $otjh,
        $epao,
        $fundsource,
        $bksbrm,
        $bksbre,
        $learnstyle,
        $skillscanlr,
        $skillscaner,
        $apprenhpw,
        $weeksonprog,
        $annualleave,
        $hoursperweek,
        $aostrength,
        $ltgoals,
        $stgoals,
        $iaguide,
        $recopl,
        $addsa
    ], $modArray, $fsValues, $prArray, [$cldate, $cllog]);
    if($creation){
        $returnText->return = true;
    } else{
        $returnText->return = false;
    }
}
echo(json_encode($returnText));
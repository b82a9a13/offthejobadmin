<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminreport']){
    if(isset($_POST['cid'])){
        $cid = $_POST['cid'];
        if(!preg_match("/^[0-9]*$/", $cid) || empty($cid)){
            $returnText->error = get_string('invalid_cip', $p);
        } else {
            //Get all learners for a specific course and create html
            $array = $lib->get_otj_progress_data($cid);
            if($array === []){
                $returnText->error = get_string('no_da', $p);
            } else {
                $return = "";
                $script = "";
                $progressTxt = get_string('progress', $p);
                $expectedTxt = get_string('expected', $p);
                $hoursLTxt = get_string('hours_l', $p);
                $modulesTxt = get_string('modules', $p);
                foreach($array as $arr){
                    $return .= "
                        <div class='inside-div w-100 mt-1 mb-1'>
                            <h4>$arr[0] - $arr[1]</h4>
                            <div class='d-flex'>
                                <div class='w-50'>
                                    <h5>$hoursLTxt</h5>
                                    <div class='d-flex'>
                                        <canvas id='prog_canvas_hour_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                        <div>
                                            <p>$progressTxt: ".$arr[2][0]."%</p>
                                            <p>$expectedTxt: ".$arr[2][1]."%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class='w-50'>
                                    <h5>$modulesTxt</h5>
                                    <div class='d-flex'>
                                        <canvas id='prog_canvas_mod_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                        <div>
                                            <p>$progressTxt: ".$arr[3][0]."%</p>
                                            <p>$expectedTxt: ".$arr[3][1]."%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";
                    $script .= "create_prog_circle('hour_$arr[4]-$arr[5]',".$arr[2][0].",".$arr[2][1].");create_prog_circle('mod_$arr[4]-$arr[5]',".$arr[3][0].",".$arr[3][1].");";
                }
                $returnText->return = str_replace("  ","",$return);
                $returnText->script = $script;
                \local_offthejobadmin\event\viewed_reports_progress_course::create(array('context' => \context_course::instance($cid), 'courseid' => $cid))->trigger();
            }
        }
    } else {
        //Get all the courses and learners
        $array = $lib->get_otj_progress_data_all();
        if($array === []){
            $returnText->error = get_string('no_da', $p);
        } else {
            $return = "";
            $script = "";
            $progressTxt = get_string('progress', $p);
            $expectedTxt = get_string('expected', $p);
            $hoursLTxt = get_string('hours_l', $p);
            $modulesTxt = get_string('modules', $p);
            foreach($array as $arra){
                foreach($arra as $ar){
                    if(is_array($ar)){
                        foreach($ar as $arr){
                            $return .= "
                                <div class='inside-div w-100 mt-1 mb-1'>
                                    <h4>$arr[0] - $arr[1]</h4>
                                    <div class='d-flex'>
                                        <div class='w-50'>
                                            <h5>$hoursLTxt</h5>
                                            <div class='d-flex'>
                                                <canvas id='prog_canvas_hour_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                                <div>
                                                    <p>$progressTxt: ".$arr[2][0]."%</p>
                                                    <p>$expectedTxt: ".$arr[2][1]."%</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='w-50'>
                                            <h5>$modulesTxt</h5>
                                            <div class='d-flex'>
                                                <canvas id='prog_canvas_mod_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                                <div>
                                                    <p>$progressTxt: ".$arr[3][0]."%</p>
                                                    <p>$expectedTxt: ".$arr[3][1]."%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ";
                            $script .= "create_prog_circle('hour_$arr[4]-$arr[5]',".$arr[2][0].",".$arr[2][1].");create_prog_circle('mod_$arr[4]-$arr[5]',".$arr[3][0].",".$arr[3][1].");";
                        }
                    }
                }
            }
            $returnText->return = str_replace("  ","",$return);
            $returnText->script = $script;
            \local_offthejobadmin\event\viewed_reports_progress_all::create(array('context' => \context_system::instance()))->trigger();
        }
    }
} else {
    $returnText->error = get_string('error_ld', $p);
}
echo(json_encode($returnText));
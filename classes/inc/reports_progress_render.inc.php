<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminreport']){
    if($_POST['cid']){
        $cid = $_POST['cid'];
        if(!preg_match("/^[0-9]*$/", $cid) || empty($cid)){
            $returnText->error = 'Invalid course id provided.';
        } else {
            //Get all learners for a specific course and create html
            $array = $lib->get_otj_progress_data($cid);
            if($array === []){
                $returnText->error = 'No data available.';
            } else {
                $return = "";
                $script = "";
                foreach($array as $arr){
                    $return .= "
                        <div class='inside-div w-100 mt-1 mb-1'>
                            <h4>$arr[0] - $arr[1]</h4>
                            <div class='d-flex'>
                                <div class='w-50'>
                                    <h5>Hours Log</h5>
                                    <div class='d-flex'>
                                        <canvas id='prog_canvas_hour_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                        <div>
                                            <p>Progress: ".$arr[2][0]."%</p>
                                            <p>Expected: ".$arr[2][1]."%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class='w-50'>
                                    <h5>Modules</h5>
                                    <div class='d-flex'>
                                        <canvas id='prog_canvas_mod_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                        <div>
                                            <p>Progress: ".$arr[3][0]."%</p>
                                            <p>Expected: ".$arr[3][1]."%</p>
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
            }
        }
    } else {
        //Get all the courses and learners
        $array = $lib->get_otj_progress_data_all();
        if($array === []){
            $returnText->error = 'No data available.';
        } else {
            $return = "";
            $script = "";
            foreach($array as $arra){
                foreach($arra as $ar){
                    foreach($ar as $arr){
                        $return .= "
                            <div class='inside-div w-100 mt-1 mb-1'>
                                <h4>$arr[0] - $arr[1]</h4>
                                <div class='d-flex'>
                                    <div class='w-50'>
                                        <h5>Hours Log</h5>
                                        <div class='d-flex'>
                                            <canvas id='prog_canvas_hour_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                            <div>
                                                <p>Progress: ".$arr[2][0]."%</p>
                                                <p>Expected: ".$arr[2][1]."%</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='w-50'>
                                        <h5>Modules</h5>
                                        <div class='d-flex'>
                                            <canvas id='prog_canvas_mod_$arr[4]-$arr[5]' class='prog-canvas' width='120px' height='120px'></canvas>
                                            <div>
                                                <p>Progress: ".$arr[3][0]."%</p>
                                                <p>Expected: ".$arr[3][1]."%</p>
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
            $returnText->return = str_replace("  ","",$return);
            $returnText->script = $script;
        }
    }
} else {
    $returnText->error = 'Error Loading data.';
}
echo(json_encode($returnText));
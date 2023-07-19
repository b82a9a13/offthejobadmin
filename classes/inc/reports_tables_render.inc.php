<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminreport']){
    if($_POST['type']){
        $type = $_POST['type'];
        if(!in_array($type, ['ac', 'lwis', 'lwcs', 'lbt', 'ldms', 'lwap'])){
            $returnText->error = 'Invalid type provided.';
        } else {
            $return = '';
            $tableclass = 'class="table table-bordered table-striped table-hover"';
            if($type === 'ac'){
                $array = $lib->get_all_apprentice_courses();
                $return .= '<h4>All Apprenticeship Courses</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Total Enrolled Learners</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                asort($array[1]);
                foreach($array[1] as $arr){
                    $return .= "
                        <tr>
                            <td>$arr</td>
                            <td>".$array[0][$arr]."</td>
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'lwis'){
                $array = $lib->get_users_incomplete_setup();
                $return .= '<h4>Learners With Incomplete Setup</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'lwcs'){
                $array = $lib->get_users_complete_setup();
                $return .= '<h4>Learners With Complete Setup</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Course</th>
                            <th>Training Plan Used</th>
                            <th>Activity Reports Used</th>
                            <th>Hours Log Used</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                    ";
                    $return .= ($arr[2]) ? '<td class="bg-green">Yes</td>' : '<td class="bg-red">No</td>';
                    $return .= ($arr[3]) ? '<td class="bg-green">Yes</td>' : '<td class="bg-red">No</td>';
                    $return .= ($arr[4]) ? '<td class="bg-green">Yes</td>' : '<td class="bg-red">No</td>';
                    $return .= "
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'lbt'){
                $array = $lib->get_users_behind_target();
                $return .= '<h4>Learners Behind Target</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Course</th>
                            <th>Hours Log</th>
                            <th>Course Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                    ";
                    $return .= ($arr[2]) ? '<td class="bg-green">On Target</td>' : '<td class="bg-red">Behind Target</td>';
                    $return .= ($arr[3]) ? '<td class="bg-green">On Target</td>' : '<td class="bg-red">Behind Target</td>';
                    $return .= "
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'ldms'){
                $array = $lib->get_nosign_ar();
                $return .= '<h4>Learner Documents Missing Signature(s)</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Course</th>
                            <th>Review Date</th>
                            <th>Coach Signed</th>
                            <th>Learner Signed</th>
                        </tr>
                    </thead>
                    <tbody>  
                ';
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                            <td>$arr[2]</td>
                    ";
                    $return .= ($arr[3]) ? '<td class="bg-green">Signed</td>' : '<td class="bg-red">Not Signed</td>';
                    $return .= ($arr[4]) ? '<td class="bg-green">Signed</td>' : '<td class="bg-red">Not Signed</td>';
                    $return .= "
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'lwap'){
                $array = $lib->get_noplan_learners();
                $return .= '<h4>Learners Without A Plan</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            }
            $returnText->return = str_replace("  ","",$return);
        }
    } else {
        $returnText->error = 'No type specified.';
    }
} else {
    $returnText->error = 'Error Loading data.';
}
echo(json_encode($returnText));

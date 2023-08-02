<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
$p = 'local_offthejobadmin';
if($_SESSION['otj_adminreport']){
    if($_POST['type']){
        $type = $_POST['type'];
        if(!in_array($type, ['ac', 'lwis', 'lwcs', 'lbt', 'ldms', 'lwap'])){
            $returnText->error = get_string('invalid_tp', $p);
        } else {
            $return = '';
            $tableclass = 'class="table table-bordered table-striped table-hover"';
            $title = '';
            if($type === 'ac'){
                $title = get_string('all_ac', $p);
                $array = $lib->get_all_apprentice_courses();
                $return .= '<h4>'.$title.'</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>'.get_string('course', $p).'</th>
                            <th>'.get_string('total_el', $p).'</th>
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
                $title = get_string('learners_wis', $p);
                $return .= '<h4>'.$title.'</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>'.get_string('learner', $p).'</th>
                            <th>'.get_string('course', $p).'</th>
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
                $title = get_string('learners_wcs', $p);
                $return .= '<h4>'.$title.'</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>'.get_string('learner', $p).'</th>
                            <th>'.get_string('course', $p).'</th>
                            <th>'.get_string('training_pu', $p).'</th>
                            <th>'.get_string('activity_ru', $p).'</th>
                            <th>'.get_string('hours_lu', $p).'</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                $noTxt = get_string('no', $p);
                $yesTxt = get_string('yes', $p);
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                    ";
                    $return .= ($arr[2]) ? '<td class="bg-green">'.$yesTxt.'</td>' : '<td class="bg-red">'.$noTxt.'</td>';
                    $return .= ($arr[3]) ? '<td class="bg-green">'.$yesTxt.'</td>' : '<td class="bg-red">'.$noTxt.'</td>';
                    $return .= ($arr[4]) ? '<td class="bg-green">'.$yesTxt.'</td>' : '<td class="bg-red">'.$noTxt.'</td>';
                    $return .= "
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'lbt'){
                $array = $lib->get_users_behind_target();
                $title = get_string('learners_bt', $p);
                $return .= '<h4>'.$title.'</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>'.get_string('learner', $p).'</th>
                            <th>'.get_string('course', $p).'</th>
                            <th>'.get_string('hours_l', $p).'</th>
                            <th>'.get_string('course_comp', $p).'</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                $ontTxt = get_string('on_t', $p);
                $behindtTxt = get_string('behind_t', $p);
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                    ";
                    $return .= ($arr[2]) ? '<td class="bg-green">'.$ontTxt.'</td>' : '<td class="bg-red">'.$behindtTxt.'</td>';
                    $return .= ($arr[3]) ? '<td class="bg-green">'.$ontTxt.'</td>' : '<td class="bg-red">'.$behindtTxt.'</td>';
                    $return .= "
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'ldms'){
                $array = $lib->get_nosign_ar();
                $title = get_string('learner_dms', $p);
                $return .= '<h4>'.$title.'</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>'.get_string('learner', $p).'</th>
                            <th>'.get_string('course', $p).'</th>
                            <th>'.get_string('review_date', $p).'</th>
                            <th>'.get_string('coach_sd', $p).'</th>
                            <th>'.get_string('learner_sd', $p).'</th>
                        </tr>
                    </thead>
                    <tbody>  
                ';
                $signTxt = get_string('signed', $p);
                $notTxt = get_string('not_s', $p);
                foreach($array as $arr){
                    $return .= "
                        <tr>
                            <td>$arr[0]</td>
                            <td>$arr[1]</td>
                            <td>$arr[2]</td>
                    ";
                    $return .= ($arr[3]) ? '<td class="bg-green">'.$signTxt.'</td>' : '<td class="bg-red">'.$notTxt.'</td>';
                    $return .= ($arr[4]) ? '<td class="bg-green">'.$signTxt.'</td>' : '<td class="bg-red">'.$notTxt.'</td>';
                    $return .= "
                        </tr>
                    ";
                }
                $return .= '</tbody></table>';
            } elseif($type === 'lwap'){
                $array = $lib->get_noplan_learners();
                $title = get_string('learners_wap', $p);
                $return .= '<h4>'.$title.'</h4>
                    <table '.$tableclass.'>
                    <thead>
                        <tr>
                            <th>'.get_string('learner', $p).'</th>
                            <th>'.get_string('course', $p).'</th>
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
            \local_offthejobadmin\event\viewed_reports_table::create(array('context' => \context_system::instance(), 'other' => strtolower($title)))->trigger();
            $returnText->return = str_replace("  ","",$return);
        }
    } else {
        $returnText->error = get_string('no_tp', $p);
    }
} else {
    $returnText->error = get_string('error_ld', $p);
}
echo(json_encode($returnText));

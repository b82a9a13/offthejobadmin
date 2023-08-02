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
        if(!in_array($type, ['hlt', 'cct', 'sc', 'pu'])){
            $returnText->error = get_string('invalid_tp', $p);
        } else {
            $return = '';
            $tableclass = 'class="table table-bordered table-striped table-hover"';
            $title = '';
            $percent = 0;
            $behind = 0;
            $array = [];
            $text = [];
            if($type === 'hlt'){
                $array = $lib->get_hourslog_target_totals();
                $text = [get_string('on_t', $p), get_string('behind_t', $p)];
                $title = get_string('hours_lt', $p);
            } elseif($type === 'cct'){
                $array = $lib->get_coursecomp_target_totals();
                $text = [get_string('complete', $p), get_string('incomplete', $p)];
                $title = get_string('course_ct', $p);
            } elseif($type === 'sc'){
                $array = $lib->get_setupcomp_totals();
                $text = [get_string('complete', $p), get_string('incomplete', $p)];
                $title = get_string('setup_c', $p);
            } elseif($type === 'pu'){
                $array = $lib->get_tplan_totals();
                $text = [get_string('used', $p), get_string('unused', $p)];
                $title = get_string('plan_u', $p);
            }
            $return = "<h4>$title</h4>";
            if($array === []){
                $return .= '<p>'.get_string('no_da', $p).'</p>';
            } else {
                $percent = ($array[0] / ($array[0]+$array[1])) * 100;
                $behind = 100 - $percent;
                $return .="
                    <div>
                        <div class='d-flex mb-1'><div class='mr-1 bg-green div-key'></div>$text[0]</div>
                        <div class='d-flex mb-1'><div class='mr-1 bg-red div-key'></div>$text[1]</div>
                    </div>
                    <div class='d-flex w-100 mb-1'>
                        <div style='width: $percent%;' class='bar-height bg-green'></div>
                        <div style='width: $behind%;' class='bar-height bg-red'></div>
                    </div>
                    <table $tableclass><thead>
                        <tr>
                            <th></th>
                            <th>".get_string('total', $p)."</th>
                        </tr>
                    <thead><tbody>
                        <tr>
                            <th>$text[0]</th>
                            <td>$array[0]</td>
                        </tr>
                        <tr>
                            <th>$text[1]</th>
                            <td>$array[1]</td>
                        </tr>
                    </tbody></table>
                ";
            }
            $returnText->return = str_replace("  ","",$return);
            \local_offthejobadmin\event\viewed_reports_chart::create(array('context' => \context_system::instance(), 'other' => strtolower($title)))->trigger();
        }
    } else {
        $returnText->error = get_string('no_tp', $p);
    }
} else {
    $returnText->error = get_string('error_ld', $p);
}
echo(json_encode($returnText));
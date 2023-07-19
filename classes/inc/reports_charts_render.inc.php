<?php 
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_offthejobadmin\lib;
$lib = new lib;
$returnText = new stdClass();
if($_SESSION['otj_adminreport']){
    if($_POST['type']){
        $type = $_POST['type'];
        if(!in_array($type, ['hlt', 'cct', 'sc', 'pu'])){
            $returnText->error = 'Invalid type provided.';
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
                $text = ['On target', 'Behind target'];
                $title = "Hours Log Target";
            } elseif($type === 'cct'){
                $array = $lib->get_coursecomp_target_totals();
                $text = ['Complete', 'Incomplete'];
                $title = 'Course Completion Target';
            } elseif($type === 'sc'){
                $array = $lib->get_setupcomp_totals();
                $text = ['Complete', 'Incomplete'];
                $title = 'Setup Completion';
            } elseif($type === 'pu'){
                $array = $lib->get_tplan_totals();
                $text = ['Used', 'Unused'];
                $title = 'Plan Utilization';
            }
            $return = "<h4>$title</h4>";
            if($array === []){
                $return .= '<p>No data available.</p>';
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
                            <th>Total</th>
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
        }
    } else {
        $returnText->error = 'No type specified.';
    }
} else {
    $returnText->error = 'Error Loading data.';
}
echo(json_encode($returnText));
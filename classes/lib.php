<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_offthejobadmin;
use stdClass;

class lib{
    //Get user full name from a specific id
    public function get_user_fullname($id){
        global $DB;
        $record = $DB->get_record_sql('SELECT firstname, lastname FROM {user} WHERE id = ?',[$id]);
        return $record->firstname.' '.$record->lastname;
    }

    //Get the course name for a course from its course id
    public function get_course_fullname($id){
        global $DB;
        return $DB->get_record_sql('SELECT fullname FROM {course} WHERE id = ?',[$id])->fullname;
    }

    //Get the category id for apprenticeships
    private function get_category_id(){
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {course_categories} WHERE name = ?',['Apprenticeships'])->id;
    }

    //Get all users which have a intial setup
    public function get_setted_users(){
        global $DB;
        $records = $DB->get_records_sql('SELECT {trainingplan_setup}.userid as userid, {trainingplan_setup}.courseid as courseid, {course}.fullname as fullname, {user}.firstname as firstname, {user}.lastname as lastname FROM {trainingplan_setup}
            INNER JOIN {user} ON {user}.id = {trainingplan_setup}.userid
            INNER JOIN {course} ON {course}.id = {trainingplan_setup}.courseid');
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->userid, $record->firstname.' '.$record->lastname, $record->courseid, $record->fullname]);
        }
        return $array;
    }

    //Check if a userid is enrolled in a course as a learner
    public function check_learner_enrolment($cid, $uid){
        global $DB;
        $record = $DB->get_record_sql('SELECT {user}.firstname as firstname, {user}.lastname as lastname FROM {user_enrolments} 
            INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
            INNER JOIN {context} ON {context}.instanceid = {enrol}.courseid
            INNER JOIN {role_assignments} ON {role_assignments}.contextid = {context}.id
            INNER JOIN {course} ON {course}.id = {enrol}.courseid 
            INNER JOIN {user} ON {user}.id = {user_enrolments}.userid
            WHERE {enrol}.courseid = ? AND {user_enrolments}.status = 0 AND {role_assignments}.roleid = 5 AND {course}.category = ? AND {user_enrolments}.userid = {role_assignments}.userid AND {user_enrolments}.userid = ?',
        [$cid, $this->get_category_id(), $uid]);
        if($record->firstname != null){
            return $record->firstname.' '.$record->lastname;
        } else {
            return false;
        }
    }

    //Check if a setup exists for a specific userid and courseid
    public function check_setup_exists($cid, $uid){
        global $DB;
        if($DB->record_exists('trainingplan_setup', [$DB->sql_compare_text('userid') => $uid, $DB->sql_compare_text('courseid') => $cid])){
            return true;
        } else {
            return false;
        }
    }

    //Get All Apprentice Courses and the total enrolled learners
    public function get_all_apprentice_courses(){
        global $DB;
        $records = $DB->get_records_sql('SELECT {user}.id as id, {course}.fullname as fullname, {course}.id as cid FROM {user_enrolments}
            INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
            INNER JOIN {context} ON {context}.instanceid = {enrol}.courseid
            INNER JOIN {role_assignments} ON {role_assignments}.contextid = {context}.id
            INNER JOIN {course} ON {course}.id = {enrol}.courseid 
            INNER JOIN {user} ON {user}.id = {user_enrolments}.userid
            WHERE {user_enrolments}.status = 0 AND {role_assignments}.roleid = 5 AND {course}.category = ? AND {user_enrolments}.userid = {role_assignments}.userid',
        [$this->get_category_id()]);
        if(count($records) > 0){
            $array = [];
            foreach($records as $record){
                array_push($array, $record->fullname);
            }
            return [array_count_values($array), array_unique($array)];
        } else {
            return [[],[]];
        }
    }

    //Get all learners who are enrolled as a learner in the apprenticeship category and return (userid, courseid, course full name, user fistname, user lastname)
    private function get_learners_data(){
        global $DB;
        return $DB->get_records_sql('SELECT {user}.id as id, {course}.fullname as fullname, {course}.id as cid, {user}.firstname as firstname, {user}.lastname as lastname FROM {user_enrolments}
            INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
            INNER JOIN {context} ON {context}.instanceid = {enrol}.courseid
            INNER JOIN {role_assignments} ON {role_assignments}.contextid = {context}.id
            INNER JOIN {course} ON {course}.id = {enrol}.courseid 
            INNER JOIN {user} ON {user}.id = {user_enrolments}.userid
            WHERE {user_enrolments}.status = 0 AND {role_assignments}.roleid = 5 AND {course}.category = ? AND {user_enrolments}.userid = {role_assignments}.userid',
        [$this->get_category_id()]);
    }

    //Get all learners with a incomplete setup
    public function get_users_incomplete_setup(){
        global $DB;
        $records = $this->get_learners_data();
        if(count($records) > 0){
            $array = [];
            foreach($records as $record){
                if(!$this->check_setup_exists($record->cid, $record->id)){
                    array_push($array, [$record->firstname.' '.$record->lastname, $record->fullname]);
                }
            }
            asort($array);
            return $array;
        } else {
            return [];
        }
    }

    //Check if a training plan exists for a specific user id and course id
    public function check_trainplan_exists($cid, $uid){
        global $DB;
        if($DB->record_exists('trainingplan_plans', [$DB->sql_compare_text('courseid') => $cid, $DB->sql_compare_text('userid') => $uid])){
            return true;
        } else {
            return false;
        }
    }

    //Get activity record id for a specific course id and user id
    private function get_activityrecord_id($cid, $uid){
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {activityrecord_docs} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
    }

    //Check if a activity record exists for a specific user id and course id
    public function check_activityrecord_exists($cid, $uid){
        global $DB;
        $id = $this->get_activityrecord_id($cid, $uid);
        if($id != null && $id != ''){
            if($DB->record_exists('activityrecord_docs_info', [$DB->sql_compare_text('docsid') => $id])){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Get hours log id from a course id and user id
    public function get_hours_id($cid, $uid){
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {hourslog_hours} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
    }

    //Check if a hours log exists for a specific user id and course id
    public function check_hourslog_exists($cid, $uid){
        global $DB;
        $id = $this->get_hours_id($cid, $uid);
        if($id != null && $id != ''){
            if($DB->record_exists('hourslog_hours_info', [$DB->sql_compare_text('hoursid') => $id])){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Get all learners with complete setups and what plugins they've used
    public function get_users_complete_setup(){
        global $DB;
        $records = $this->get_learners_data();
        if(count($records) > 0){
            $array = [];
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    array_push($array, [
                        $record->firstname.' '.$record->lastname, 
                        $record->fullname,
                        $this->check_trainplan_exists($record->cid, $record->id),
                        $this->check_activityrecord_exists($record->cid, $record->id),
                        $this->check_hourslog_exists($record->cid, $record->id)
                    ]);
                }
            }
            asort($array);
            return $array;
        } else {
            return [];
        }
    }

    //Get the hours log current and expected progress from a specific userid and courseid
    public function get_hourslog_progexpect($cid, $uid){
        global $DB;
        $id = $this->get_hours_id($cid, $uid);
        if($id != null && $id != ''){
            $records = $DB->get_records_sql('SELECT duration FROM {hourslog_hours_info} WHERE hoursid = ?',[$id]);
            $hours = 0;
            foreach($records as $rec){
                $hours += $rec->duration;
            }
            $record = $DB->get_record_sql('SELECT otjhours, totalmonths, startdate FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
            $percent = floatval(number_format(($hours / $record->otjhours) * 100, 0, '.',' '));
            $percent = ($percent < 0) ? 0 : $percent;
            $percent = ($percent > 100) ? 100 : $percent;
            $expected = floatval(
                number_format((($record->otjhours / $record->totalmonths) / 4) *
                (round((date('U') - $record->startdate) / 604800) / $record->otjhours) * 100, 0, '.',' ')
            );
            $expected = ($expected < 0) ? 0 : $expected;
            return [$percent, $expected];
        } else {
            return false;
        }
    }

    //Check if hours log for a specific user id and course id is on target
    public function check_user_hourslog_target($cid, $uid){
        global $DB;
        $values = $this->get_hourslog_progexpect($cid, $uid);
        if($values){
            if($values[0] < $values[1]){
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //Get the current and expected course completion for a specific user id and course id
    public function get_user_coursecomp($cid, $uid){
        global $DB;
        $record = $DB->get_record_sql('SELECT totalmonths, startdate FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
        $complete = $DB->get_record_sql('SELECT count(*) as total FROM {course_modules}
            INNER JOIN {course_modules_completion} ON {course_modules_completion}.coursemoduleid = {course_modules}.id
            WHERE {course_modules}.course = ? AND {course_modules}.completion != 0 AND {course_modules_completion}.userid AND {course_modules_completion}.completionstate = 1',
        [$cid, $uid])->total;
        $total = $DB->get_record_sql('SELECT count(*) as total FROM {course_modules} WHERE course = ? AND completion != 0',[$cid])->total;
        $percent = round(($complete / $total) * 100);
        $expected = round(((
            (($total / $record->totalmonths) / 4) *
            (round((date('U') - $record->startdate) / 604800))
            ) / $total) * 100
        );
        $expected = ($expected < 0) ? 0 : $expected;
        $percent = ($percent < 0) ? 0 : $percent;
        return [$percent, $expected];
    }

    //Check if module completion for a specific user id and course id is on target
    public function check_user_coursecomp_target($cid, $uid){
        $values = $this->get_user_coursecomp($cid, $uid);
        if($values){
            if($values[0] < $values[1]){
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //Get all learners behind target
    public function get_users_behind_target(){
        $records = $this->get_learners_data();
        if(count($records) > 0){
            $array = [];
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    $logtarget = $this->check_user_hourslog_target($record->cid, $record->id);
                    $coursetarget = $this->check_user_coursecomp_target($record->cid, $record->id);
                    if($logtarget == false || $coursetarget == false){
                        array_push($array, [
                            $record->firstname.' '.$record->lastname,
                            $record->fullname,
                            $logtarget,
                            $coursetarget
                        ]);
                    }
                }
            }
            asort($array);
            return $array;
        } else {
            return [];
        }
    }

    //Get all activity records which are missing a signature or multiple signatures
    public function get_nosign_ar(){
        global $DB;
        $records = $DB->get_records_sql('SELECT docsid, ntasign, learnsign, reviewdate FROM {activityrecord_docs_info} WHERE learnsign IS NULL OR ntasign IS NULL');
        $array = [];
        foreach($records as $record){
            $coachsign = ($record->ntasign != null) ? true : false;
            $learnersign = ($record->learnsign != null) ? true : false;
            $data = $DB->get_record_sql('SELECT userid, courseid FROM {activityrecord_docs} WHERE id = ?',[$record->docsid]);
            array_push($array, [
                $this->get_user_fullname($data->userid),
                $this->get_course_fullname($data->courseid),
                date('d-m-Y',$record->reviewdate),
                $coachsign,
                $learnersign
            ]);
        }
        asort($array);
        return $array;
    }

    //Get all learners which don't have a training plan
    public function get_noplan_learners(){
        global $DB;
        $records = $DB->get_records_sql('SELECT userid, courseid FROM {trainingplan_setup}');
        $array = [];
        foreach($records as $record){
            if(!$DB->record_exists('trainingplan_plans', [$DB->sql_compare_text('userid') => $record->userid, $DB->sql_compare_text('courseid') => $record->courseid])){
                array_push($array, [
                    $this->get_user_fullname($record->userid),
                    $this->get_course_fullname($record->courseid)
                ]);
            }
        }
        asort($array);
        return $array;
    }

    //Get the total number of learners on target and behind target for their hours log
    public function get_hourslog_target_totals(){
        $records = $this->get_learners_data();
        if(count($records) > 0){
            $ontarget = 0;
            $notontarget = 0;
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    if($this->check_user_hourslog_target($record->cid, $record->id)){
                        $ontarget++;
                    } else {
                        $notontarget++;
                    }
                }
            }
            return [$ontarget, $notontarget];
        } else {
            return [];
        }
    }

    //Get the total number of learners on target and behind target for their course modules
    public function get_coursecomp_target_totals(){
        $records = $this->get_learners_data();
        if(count($records) > 0){
            $ontarget = 0;
            $notontarget = 0;
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    if($this->check_user_coursecomp_target($record->cid, $record->id)){
                        $ontarget++;
                    } else {
                        $notontarget++;
                    }
                }
            }
            return [$ontarget, $notontarget];
        } else {
            return [];
        }
    }

    //Get the total number of learners with a setup complete for their course
    public function get_setupcomp_totals(){
        $records = $this->get_learners_data();        
        if(count($records > 0)){
            $complete = 0;
            $incomplete = 0;
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    $complete++;
                } else {
                    $incomplete++;
                }
            }
            return [$complete, $incomplete];
        } else {
            return [];
        }
    }

    //Get the total number of learners which have a training plan
    public function get_tplan_totals(){
        $records = $this->get_learners_data();
        if(count($records > 0)){
            $used = 0;
            $notused = 0;
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    if($this->check_trainplan_exists($record->cid, $record->id)){
                        $used++;
                    } else {
                        $notused++;
                    }
                }
            }
            return [$used, $notused];
        } else {
            return [];
        }
    }

    //Get all apprenticeship courses
    public function get_courses_array(){
        global $DB;
        $records = $DB->get_records_sql('SELECT id, fullname FROM {course} WHERE category = ?', [$this->get_category_id()]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->fullname, $record->id]);
        }
        asort($array);
        return $array;
    }

    //Get expected progress for a sepecific user id and course id
    private function get_hours_expected($cid, $uid){
        global $DB;
        $record = $DB->get_record_sql('SELECT otjhours, totalmonths, startdate FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
        $percent = floatval(number_format(($hours / $record->otjhours) * 100, 0, '.',' '));
        $expected = floatval(
            number_format((($record->otjhours / $record->totalmonths) / 4) *
            (round((date('U') - $record->startdate) / 604800) / $record->otjhours) * 100, 0, '.',' ')
        );
        $expected = ($expected < 0) ? 0 : $expected;
        return $expected;
    }

    //Get learner progress from a specific course id
    public function get_otj_progress_data($cid){
        global $DB;
        $records = $DB->get_records_sql('SELECT {user}.id as id, {course}.fullname as fullname, {course}.id as cid, {user}.firstname as firstname, {user}.lastname as lastname FROM {user_enrolments}
            INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
            INNER JOIN {context} ON {context}.instanceid = {enrol}.courseid
            INNER JOIN {role_assignments} ON {role_assignments}.contextid = {context}.id
            INNER JOIN {course} ON {course}.id = {enrol}.courseid 
            INNER JOIN {user} ON {user}.id = {user_enrolments}.userid
            WHERE {user_enrolments}.status = 0 AND {role_assignments}.roleid = 5 AND {course}.category = ? AND {user_enrolments}.userid = {role_assignments}.userid AND {course}.id = ?',
        [$this->get_category_id(), $cid]);
        if(count($records) > 0){
            $array = [];
            foreach($records as $record){
                if($this->check_setup_exists($record->cid, $record->id)){
                    $hours = $this->get_hourslog_progexpect($record->cid, $record->id);
                    if($hours){
                        array_push($array, [
                            $record->firstname.' '.$record->lastname,
                            $record->fullname,
                            $hours,
                            $this->get_user_coursecomp($record->cid, $record->id),
                            $record->cid,
                            $record->id
                        ]);
                    } else {
                        array_push($array, [
                            $record->firstname.' '.$record->lastname,
                            $record->fullname,
                            [0, $this->get_hours_expected($record->cid, $record->id)],
                            $this->get_user_coursecomp($record->cid, $record->id),
                            $record->cid,
                            $record->id
                        ]);
                    }
                }
            }
            asort($array);
            return $array;
        } else {
            return [];
        }
    }

    //Get all learners and their progress for a specific course
    public function get_otj_progress_data_all(){
        global $DB;
        $courses = $this->get_courses_array();
        if(count($courses > 0)){
            $array = [];
            foreach($courses as $course){
                $coursearray = $this->get_otj_progress_data($course[1]);
                if($coursearray != []){
                    array_push($array, [
                        $course[0], $this->get_otj_progress_data($course[1])
                    ]);
                }
            }
            return $array;
        } else {
            return [];
        }
    }

    //Check if a learner signature exists for a specific course id and user id
    public function check_learn_signed($cid, $uid){
        global $DB;
        $signature = $DB->get_record_sql('SELECT learnersign FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid])->learnersign;
        if($signature != null && $signature != ''){
            return true;
        } else {
            return false;
        }
    }

    //Check if a coach signature exists for a specific course id and user id
    public function check_coach_signed($cid, $uid){
        global $DB;
        $signature = $DB->get_record_sql('SELECT coachsign FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid])->coachsign;
        if($signature != null && $signature != ''){
            return true;
        } else {
            return false;
        }
    }

    //Get data for the rendering of the coach/learner signature on the admin_user page for a specific course id and user id
    public function sign_render($cid, $uid, $type){
        global $DB;
        $field = '';
        if($type === 'coach'){
            $field = 'coachsign';
        } elseif($type === 'learn'){
            $field = 'learnersign';
        }
        if($field === ''){
            return '';
        } else {
            return str_replace(" ","+",$DB->get_record_sql("SELECT $field FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?",[$cid, $uid])->$field);
        }
    }

    //Reset a signature for a coach/learner for a specific course id and user id
    public function sign_reset($cid, $uid, $type){
        global $DB;
        $field = '';
        if($type === 'coach'){
            $field = 'coachsign';
        } elseif($type === 'learn'){
            $field = 'learnersign';
        }
        if($field === ''){
            return '';
        } else {
            $update = new stdClass();
            $id = $DB->get_record_sql('SELECT id FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
            $update->id = $id;
            $update->$field = null;
            if($DB->update_record('trainingplan_setup', $update, [$DB->sql_compare_text('id') => $id])){
                return true;
            } else {
                return false;
            }
        }
    }

    //Get initial setup data for a specific course id and user id
    public function get_setup_data($cid, $uid){
        global $DB;
        $record = $DB->get_record_sql('SELECT totalmonths, otjhours, employerorstore, coach, managerormentor, learnersign, coachsign, startdate, hoursperweek, annuallw, planfilename, option FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
        $array = [
            $record->totalmonths,
            $record->otjhours,
            $record->employerorstore,
            $record->coach,
            $record->managerormentor,
            date('d-m-Y',$record->startdate),
            $record->hoursperweek,
            $record->annuallw,
            $record->planfilename,
            str_replace(" ","+",$record->learnersign),
            str_replace(" ","+",$record->coachsign),
            $record->option
        ];
        return $array;
    }

    private function get_trainplan_id($cid, $uid){
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {trainingplan_plans} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
    }

    //Reset all data for a specific course id and user id
    public function reset_user_course_data($cid, $uid){
        global $DB;
        $id = $DB->get_record_sql('SELECT id FROM {hourslog_hours} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
        if($id != null && $id != ''){
            $DB->delete_records('hourslog_hours_info', [$DB->sql_compare_text('hoursid') => $id]);
            $DB->delete_records('hourslog_hours', [$DB->sql_compare_text('id') => $id]);
        }
        $id = $this->get_activityrecord_id($cid, $uid);
        if($id != null && $id != ''){
            $records = $DB->get_records_sql('SELECT employercomment FROM {activityrecord_docs_info} WHERE docsid = ?',[$id]);
            foreach($records as $record){
                unlink('../../../activityrecord/classes/pdf/employercomment/'.$record->employercomment);
            }
            $DB->delete_records('activityrecord_docs_info', [$DB->sql_compare_text('docsid') => $id]);
            $DB->delete_records('activityrecord_docs', [$DB->sql_compare_text('id') => $id]);
        }
        $id = $this->get_trainplan_id($cid, $uid);
        if($id != null && $id != ''){
            $DB->delete_records('trainingplan_plans_pr', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans_modules', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans_log', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans_fs', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans', [$DB->sql_compare_text('id') => $id]);
        }
        $id = $DB->get_record_sql('SELECT id FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
        if($id != null && $id != ''){
            $DB->delete_records('trainingplan_setup', [$DB->sql_compare_text('id') => $id]);
        }
        return true;
    }

    //Get training plan names and file name
    public function get_training_plans(){
        global $CFG;
        //Get files
        $files = scandir($CFG->dirroot.'/local/trainingplan/templates/json');
        //Remove first two elements
        unset($files[0]);
        unset($files[1]);
        //put relevant data into an array
        $files = array_values($files);
        $filesarray = [];
        foreach($files as $file){
            $json = file_get_contents($CFG->dirroot.'/local/trainingplan/templates/json/'.$file);
            $json = json_decode($json);
            $options = 0;
            foreach($json->modules as $mod){
                if(isset($mod->option1)){
                    $options++;
                } elseif(isset($mod->option2)){
                    $options++;
                }
            }
            array_push($filesarray, [$json->name, $file, $options]);
        }
        return $filesarray;
    }

    //Get training plan file names
    public function get_training_plans_names(){
        global $CFG;
        $files = scandir($CFG->dirroot.'/local/trainingplan/templates/json');
        unset($files[0]);
        unset($files[1]);
        $files = array_values($files);
        return $files;
    }

    //Update initial setup data
    public function update_setup_data($data, $cid, $uid){
        global $DB;
        $id = $DB->get_record_sql('SELECT id FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid])->id;
        if($id == null || $id == ''){
            return false;
        }
        $record = new stdClass();
        $record->id = $id;
        $record->totalmonths = $data[0];
        $record->otjhours = $data[1];
        $record->employerorstore = $data[2];
        $record->coach = $data[3];
        $record->managerormentor = $data[4];
        $record->startdate = $data[5];
        $record->hoursperweek = $data[6];
        $record->annuallw = $data[7];
        $record->planfilename = $data[8];
        $record->option = $data[9];
        if(count($DB->update_record('trainingplan_setup', $record, false)) > 0){
            return true;
        } else {
            return false;
        }
    }

    //Get activity records for a specific course id and user id
    public function get_activityrecord_list($cid, $uid){
        global $DB;
        $records = $DB->get_records_sql('SELECT reviewdate, id FROM {activityrecord_docs_info} WHERE docsid = ?',[$this->get_activityrecord_id($cid, $uid)]);
        $array = [];
        foreach($records as $record){
            array_push($array, [date('d-m-Y',$record->reviewdate), $record->id]);
        }
        asort($array);
        return $array;
    }

    //Get data for hours info table
    public function get_hourslog_info_table_data($cid, $uid){
        global $DB;
        $record = $DB->get_record_sql('SELECT otjhours, hoursperweek, totalmonths, annuallw FROM {trainingplan_setup} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
        $records = $DB->get_records_sql('SELECT {hourslog_hours_info}.duration as duration FROM {hourslog_hours}
            INNER JOIN {hourslog_hours_info} ON {hourslog_hours_info}.hoursid = {hourslog_hours}.id
            WHERE {hourslog_hours}.courseid = ? AND {hourslog_hours}.userid = ?',
        [$cid, $uid]);
        $totalHL = $record->otjhours;
        foreach($records as $rec){
            $totalHL -= $rec->duration;
        }
        $totalHL = ($totalHL < 0) ? 0 : $totalHL;
        return [
            $record->otjhours,
            $totalHL,
            $record->hoursperweek,
            $record->totalmonths,
            round($record->totalmonths*4.34),
            $record->annuallw
        ];
    }

    //Get partial training plan data from a specific course id and user id
    public function get_partial_trainingplan_data($cid, $uid){
        global $DB;
        $record = $DB->get_record_sql('SELECT employer, name, startdate, plannedendd, otjh, epao, fundsource, lengthoprog FROM {trainingplan_plans} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
        $epaoArray = [['frawards', 'FR Awards'],['candg', 'C & G'],['innovate', 'Innovate'],['dsw', 'DSW'],['nocn', 'NOCN']];
        foreach($epaoArray as $epaoArr){
            if($record->epao == $epaoArr[0]){
                $record->epao = $epaoArr[1];
            }
        }
        $fundArray = [['contrib', '5% Contribution'],['levy', 'Levy']];
        foreach($fundArray as $fundArr){
            if($record->fundsource == $fundArr[0]){
                $record->fundsource = $fundArr[1];
            }
        }
        return [
            $record->name,
            $record->employer,
            date('d-m-Y',$record->startdate),
            date('d-m-Y',$record->plannedendd),
            $record->lengthoprog,
            $record->otjh,
            $record->epao,
            $record->fundsource
        ];
    }

    //Reset training plan from a specific course id and user id
    public function reset_trainplan($cid, $uid){
        global $DB;
        $id = $this->get_trainplan_id($cid, $uid);
        if($id != null & $id != ''){
            $DB->delete_records('trainingplan_plans_pr', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans_modules', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans_log', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans_fs', [$DB->sql_compare_text('plansid') => $id]);
            $DB->delete_records('trainingplan_plans', [$DB->sql_compare_text('id') => $id]);
            return true;
        } else {
            return false;
        }
    }

    //Get all training plan data for a sepcific course id and user id
    public function get_trainplan_data($cid, $uid, $datetype){
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {trainingplan_plans} WHERE courseid = ? AND userid = ?',[$cid, $uid]);
        $planid = $record->id;
        $plansArray = [
            $record->name, 
            $record->employer, 
            $record->startdate, 
            $record->plannedendd,
            $record->lengthoprog,
            $record->otjh,
            $record->epao,
            $record->fundsource,
            $record->bksbrm,
            $record->bksbre,
            $record->learnstyle,
            $record->sslearnr,
            $record->ssemployr,
            $record->apprenhpw,
            $record->weekop,
            $record->annuall,
            $record->pdhours,
            $record->areaostren,
            $record->longtgoal,
            $record->shorttgoal,
            $record->iag,
            $record->recopl,
            $record->addsa
        ];
        $records = $DB->get_records_sql('SELECT * FROM {trainingplan_plans_modules} WHERE plansid = ?',[$planid]);
        $modArray = [];
        $total = [0,0];
        foreach($records as $rec){
            $tmp = [];
            array_push($tmp, 
                $rec->modpos,
                $rec->modname,
                date($datetype, $rec->modpsd)
            );
            $tmpVal = ($rec->modrsd == 0) ? '' : date($datetype,$rec->modrsd);
            array_push($tmp, $tmpVal);
            array_push($tmp, date($datetype,$rec->modped));
            $tmpVal = ($rec->modred == 0) ? '' : date($datetype, $rec->modred);
            array_push($tmp, $tmpVal);
            array_push($tmp, 
                $rec->modw,
                $rec->modotjh,
                $rec->modmod,
                $rec->modotjt,
                $rec->modaotjhc
            );
            array_push($modArray, $tmp);
            $total[0] += $rec->modw;
            $total[1] += $rec->modotjh;
        }
        asort($modArray);
        $modArray = [$modArray, $total];
        $records = $DB->get_records_sql('SELECT * FROM {trainingplan_plans_fs} WHERE plansid = ?',[$planid]);
        $fsArray = [];
        foreach($records as $rec){
            $tmp = [];
            array_push($tmp, 
                $rec->fspos,
                $rec->fsname
            );
            $tmpVal = ($rec->fslevel == 0) ? '' : $rec->fslevel;
            array_push($tmp, $tmpVal);
            $tmpVal = ($rec->fssd == 0) ? '' : date($datetype, $rec->fssd);
            array_push($tmp, $rec->fsmod, $tmpVal);
            $tmpVal = ($rec->fsped == 0) ? '' : date($datetype, $rec->fsped);
            array_push($tmp, $tmpVal);
            $tmpVal = ($rec->fsaed == 0) ? '' : date($datetype, $rec->fsaed);
            array_push($tmp, $tmpVal);
            $tmpVal = ($rec->fsaead == 0) ? '' : date($datetype, $rec->fsaead);
            array_push($tmp, $tmpVal);
            array_push($fsArray, $tmp);
        }
        asort($fsArray);
        $records = $DB->get_records_sql('SELECT * FROM {trainingplan_plans_pr} WHERE plansid = ?',[$planid]);
        $prArray = [];
        foreach($records as $rec){
            $tmp = [];
            array_push($tmp, $rec->prpos);
            $tmpVal = $rec->prtor;
            if($tmpVal == 'Learner'){
                $tmp[2] = 'selected';
                $tmp[6] = 'readonly disabled';
            } elseif($tmpVal == 'Employer'){
                $tmp[3] = 'selected';
                $tmp[6] = 'readonly disabled';
            } else {
                $tmp[1] = 'selected';
            }
            $tmpVal = ($rec->prpr == 0) ? '' : date($datetype, $rec->prpr);
            $tmp[4] = $tmpVal;
            $tmpVal = ($rec->prar == 0) ? '' : date($datetype, $rec->prar);
            $tmp[5] = $tmpVal;
            array_push($prArray, $tmp);
        }
        asort($prArray);
        $records = $DB->get_records_sql('SELECT * FROM {trainingplan_plans_log} WHERE plansid = ?',[$planid]);
        $logArray = [];
        foreach($records as $rec){
            array_push($logArray, [
                date($datetype, $rec->dateofc),
                $rec->log,
                'disabled'
            ]);
        }
        asort($logArray);
        return [$plansArray, $modArray, $fsArray, $prArray, $logArray];
    }

    //Submit training plan data for a specific course id and user id
    public function submit_trainplan($cid, $uid, $allArray, $modArray, $fsValues, $prArray, $logArray){
        global $DB;
        $planid = $this->get_trainplan_id($cid, $uid);
        $record = new stdClass();
        $record->id = $planid;
        $record->userid = $uid;
        $record->courseid = $cid;
        $record->name = $allArray[0];
        $record->employer = $allArray[1];
        $record->startdate = $allArray[2];
        $record->plannedendd = $allArray[3];
        $record->lengthoprog = $allArray[4];
        $record->otjh = $allArray[5];
        $record->epao = $allArray[6];
        $record->fundsource = $allArray[7];
        $record->bksbrm = $allArray[8];
        $record->bksbre = $allArray[9];
        $record->learnstyle = $allArray[10];
        $record->sslearnr = $allArray[11];
        $record->ssemployr = $allArray[12];
        $record->apprenhpw = $allArray[13];
        $record->weekop = $allArray[14];
        $record->annuall = $allArray[15];
        $record->pdhours = $allArray[16];
        $record->areaostren = $allArray[17];
        $record->longtgoal = $allArray[18];
        $record->shorttgoal = $allArray[19];
        $record->iag = $allArray[20];
        $record->recopl = $allArray[21];
        $record->addsa = $allArray[22];
        if($DB->update_record('trainingplan_plans', $record)){
            for($i = 0; $i < count($modArray); $i++){
                $record = new stdClass();
                $record->id = $DB->get_record_sql('SELECT id FROM {trainingplan_plans_modules} WHERE plansid = ? AND modpos = ?',[$planid, $i])->id;
                if($record->id != null && $record->id != ''){
                    $record->modpos = $i;
                    $record->modname = $modArray[$i][0];
                    $record->modpsd = $modArray[$i][1];
                    $record->modped = $modArray[$i][2];
                    $record->modw = $modArray[$i][3];
                    $record->modotjh = $modArray[$i][4];
                    $record->modmod = $modArray[$i][5];
                    $record->modotjt = $modArray[$i][6];
                    $record->modrsd = $modArray[$i][7];
                    $record->modred = $modArray[$i][8];
                    $DB->update_record('trainingplan_plans_modules', $record);
                }
            }
            for($i = 0; $i < count($fsArray); $i++){
                $record = new stdClass();
                $record->id = $DB->get_record_sql('SELECT id FROM {trainingplan_plans_fs} WHERE plansid = ? AND fspos = ?',[$planid, $i])->id;
                if($record->id != null && $record->id != ''){
                    $record->fspos = $i;
                    $record->fsname = $fsArray[$i][0];
                    $record->fslevel = $fsArray[$i][1];
                    $record->fsmod = $fsArray[$i][2];
                    $record->fssd = $fsArray[$i][3];
                    $record->fsped = $fsArray[$i][4];
                    $record->fsaed = $fsArray[$i][5];
                    $record->fsaead = $fsArray[$i][6];
                    $DB->update_record('trainingplan_plans_fs', $record);
                }
            }
            for($i = 0; $i < count($prArray); $i++){
                $record = new stdClass();
                $record->id = $DB->get_record_sql('SELECT id FROM {trainingplan_plans_pr} WHERE plansid = ? AND prpos = ?',[$planid, $i])->id;
                if($record->id != null && $record->id != ''){
                    $record->prpos = $i;
                    $record->prtor = $prArray[$i][0];
                    $record->prpr = $prArray[$i][1];
                    $record->prar = $prArray[$i][2];
                    $DB->update_record('trainingplan_plans_pr', $record);
                } else {
                    unset($record->id);
                    $record->plansid = $planid;
                    $record->prpos = $i;
                    $record->prtor = $prArray[$i][0];
                    $record->prpr = $prArray[$i][1];
                    $record->prar = $prArray[$i][2];
                    $DB->insert_record('trainingplan_plans_pr', $record, true);
                }
            }
            $record = new stdClass();
            $record->plansid = $planid;
            $record->dateofc = $logArray[0];
            $record->log = $logArray[1];
            $DB->insert_record('trainingplan_plans_log', $record, true);
            return true;
        } else {
            return false;
        }
    }

    //delete specfic activity record for a specific course id and user id
    public function delete_activityrecord($cid, $uid, $id){
        global $DB;
        $docsid = $this->get_activityrecord_id($cid, $uid);
        $file = $DB->get_record_sql('SELECT employercomment FROM {activityrecord_docs_info} WHERE docsid = ? AND id = ?',[$docsid, $id])->employercomment;
        if(file_exists('../../../activityrecord/classes/pdf/employercomment/'.$file)){
            unlink('../../../activityrecord/classes/pdf/employercomment/'.$file);
        }
        $DB->delete_records('activityrecord_docs_info', [$DB->sql_compare_text('docsid') => $docsid, $DB->sql_compare_text('id') => $id]);
        return true;
    }

    //get activity record data for a spefici course id and user id
    public function get_activityrecord_data($cid, $uid, $id){
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {activityrecord_docs_info} WHERE id = ? AND docsid = ?',[$id, $this->get_activityrecord_id($cid, $uid)]);
        $array = [
            $record->apprentice,
            date('Y-m-d',$record->reviewdate),
            $record->standard,
            $record->employerandstore,
            $record->coach,
            $record->managerormentor,
            $record->progress,
            $record->expectprogress,
            $record->progresscom,
            $record->hours,
            $record->expecthours,
            $record->otjhcom,
            $record->recap,
            $record->impact,
            $record->details,
            $record->detailsksb,
            $record->detailimpact,
            $record->todaymath,
            $record->nextmath,
            $record->todayeng,
            $record->nexteng,
            $record->alnsupport,
            $record->coachfeedback,
            $record->safeguarding,
            $record->agreedaction,
            $record->apprenticecomment,
            $record->employercomment,
            date('Y-m-d',$record->ntasigndate),
            date('Y-m-d',$record->learnsigndate),
            str_replace(" ","+",$record->learnsign),
            str_replace(" ","+",$record->ntasign),
            date('Y-m-d H:m',$record->nextdate),
            $record->nexttype
        ];
        return $array;
    }

    //get employer comment pdf file for a specific record id, user id and course id
    public function get_employercomment_pdf($cid, $uid, $id){
        global $DB;
        $record = $DB->get_record_sql('SELECT employercomment, reviewdate FROM {activityrecord_docs_info} WHERE id = ? AND docsid = ?',[$id, $this->get_activityrecord_id($cid, $uid)]);
        return [$record->employercomment, date('Y-m-d',$record->reviewdate)];
    }

    //update a activity record for a sepcific record id, user id and course id
    public function update_activityrecord($cid, $uid, $id, $data){
        global $DB;
        $info = $DB->get_record_sql('SELECT docsid, employercomment FROM {activityrecord_docs_info} WHERE id = ? AND docsid = ?',[$id, $this->get_activityrecord_id($cid, $uid)]);
        $docsid = $info->docsid;
        $file = $info->employercomment;
        if($data[25] != $file && $file != null && $data[25] != null && !empty($data[25]) && !empty($file)){
            if(file_exists('../../../activityrecord/classes/pdf/employercomment/'.$file)){
                unlink('../../../activityrecord/classes/pdf/employercomment/'.$file);
            }
        } elseif($file != null){
            $data[25] = $file;
        }
        $record = new stdClass();
        $record->id = $id;
        $record->docsid = $docsid;
        $record->apprentice = $data[0];
        $record->reviewdate = $data[1];
        $record->standard = $data[2];
        $record->employerandstore = $data[3];
        $record->coach = $data[4];
        $record->managerormentor = $data[5];
        $record->progress = $data[6];
        $record->expectprogress = $data[7];
        $record->progresscom = $data[8];
        $record->hours = $data[9];
        $record->expecthours = $data[10];
        $record->otjhcom = $data[11];
        $record->recap = $data[12];
        $record->impact = $data[13];
        $record->details = $data[14];
        $record->detailsksb = $data[15];
        $record->detailimpact = $data[16];
        $record->todaymath = $data[17];
        $record->nextmath = $data[18];
        $record->todayeng = $data[19];
        $record->nexteng = $data[20];
        $record->alnsupport = $data[21];
        $record->coachfeedback = $data[22];
        $record->safeguarding = $data[23];
        $record->agreedaction = $data[24];
        $record->employercomment = $data[25];
        $record->apprenticecomment = $data[26];
        $record->nextdate = $data[27];
        $record->nexttype = $data[28];
        if($DB->update_record('activityrecord_docs_info', $record, true)){
            return true;
        } else {
            return false;
        }
    }
}
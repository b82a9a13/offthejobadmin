<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

namespace local_offthejobadmin\event;
use core\event\base;
defined('MOODLE_INTERNAL') || die();

class viewed_reports_chart extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return "Reports chart viewed";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed the reports chart '".$this->other."'";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejobadmin/admin_reports.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}
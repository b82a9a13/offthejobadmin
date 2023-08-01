<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

namespace local_offthejobadmin\event;
use core\event\base;
defined('MOODLE_INTERNAL') || die();

class viewed_user_setup extends base {
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return "User setup viewed";
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed the user's setup for the user with id '".$this->relateduserid."' and for the course with id '".$this->courseid."'.";
    }
    public function get_url(){
        return new \moodle_url('/local/offthejobadmin/admin_setup.php?uid='.$this->relateduserid.'&cid='.$this->courseid);
    }
    public function get_id(){
        return $this->objectid;
    }
}
<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
defined('MOODLE_INTERNAL') || die();

if($hassiteconfig){
    $ADMIN->add('localplugins', new admin_category('local_offthejobadmin', 'Off The Job Admin'));
    $ADMIN->add('local_offthejobadmin', new admin_externalpage('local_offthejobadmin', 'Menu', $CFG->wwwroot.'/local/offthejobadmin/admin.php'));
    $ADMIN->add('local_offthejobadmin', new admin_externalpage('local_offthejobadmin', 'Reports', $CFG->wwwroot.'/local/offthejobadmin/admin_reports.php'));
}
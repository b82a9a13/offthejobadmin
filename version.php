<?php
// This file is part of the offthejobadmin plugin
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_offthejobadmin';
$plugin->version = 21;
$plugin->requires = 2016052314;
$plugin->dependencies = [
    'local_trainingplan' => 23,
    'local_hourslog' => 15,
    'local_modulecompletion' => 7,
    'local_activityrecord' => 17
];

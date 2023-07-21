<?php
// This file is part of the offthejobadmin plugin
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_offthejobadmin';
$plugin->version = 8;
$plugin->requires = 2016052314;
$plugin->dependencies = [
    'local_trainingplan' => 12,
    'local_hourslog' => 9,
    'local_modulecompletion' => 4,
    'local_activityrecord' => 10
];

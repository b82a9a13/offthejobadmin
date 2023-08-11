<?php
// This file is part of the offthejobadmin plugin
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_offthejobadmin';
$plugin->version = 20;
$plugin->requires = 2016052314;
$plugin->dependencies = [
    'local_trainingplan' => 20,
    'local_hourslog' => 12,
    'local_modulecompletion' => 6,
    'local_activityrecord' => 12
];

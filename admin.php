<?php
/**
 * @package     local_offthejobadmin
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__.'/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/offthejobadmin:admin', $context);
use local_offthejobadmin\lib;
$lib = new lib;
$p = 'local_offthejobadmin';

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejobadmin/admin.php'));
$PAGE->set_title('Off The Job - Admin');
$PAGE->set_heading('Off The Job - Admin');
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
$template = (Object)[
    'pick_alac' => get_string('pick_alac', $p),
    'submit' => get_string('submit', $p),
    'click_tifr' => get_string('click_tifr', $p),
    'users_array' => $lib->get_setted_users()
];
echo $OUTPUT->render_from_template('local_offthejobadmin/menu', $template);

echo $OUTPUT->footer();

\local_offthejobadmin\event\viewed_main_menu::create(array('context' => \context_system::instance()))->trigger();
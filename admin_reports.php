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
$PAGE->set_url(new moodle_url('/local/offthejobadmin/admin_reports.php'));
$PAGE->set_title(get_string('otj_admin_r', $p));
$PAGE->set_heading(get_string('otj_admin_r', $p));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'btm' => get_string('btm', $p),
    'reset_p' => get_string('reset_p', $p),
    'otj_r' => get_string('otj_r', $p),
    'tables' => get_string('tables', $p),
    'charts' => get_string('charts', $p),
    'progress' => get_string('progress', $p)
];
echo $OUTPUT->render_from_template('local_offthejobadmin/reports', $template);

echo $OUTPUT->footer();
$_SESSION['otj_adminreport'] = true;
\local_offthejobadmin\event\viewed_reports_menu::create(array('context' => \context_system::instance()))->trigger();
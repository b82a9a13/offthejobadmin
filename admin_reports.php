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

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/offthejobadmin/admin.php'));
$PAGE->set_title('Off The Job - Admin Reports');
$PAGE->set_heading('Off The Job - Admin Reports');
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[];
echo $OUTPUT->render_from_template('local_offthejobadmin/reports', $template);

echo $OUTPUT->footer();
$_SESSION['otj_adminreport'] = true;
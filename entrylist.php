<?php 
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once("./classes/deluserentrytable.php");

$url = new moodle_url('/local/deluser_aftertime/entrylist.php');
//admin_externalpage_setup('test_db');


require_login(); 
$title = get_string('pluginname', 'local_deluser_aftertime');
//$pagetitle = $title;
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);

require_capability('local/deluser_aftertime:edit', $systemcontext);

$PAGE->set_pagelayout('report');

$PAGE->set_title($title);
$PAGE->set_heading($title);
 
 
echo $OUTPUT->header();
echo $OUTPUT->heading($title." ".get_string('entrylist', 'local_deluser_aftertime'));

	
$editurl = new moodle_url('/local/deluser_aftertime/edit.php');
echo html_writer::link($editurl, get_string('newentry', 'local_deluser_aftertime'));


$table = new \local_deluser_aftertime\deluserentrytable();

$table->out(50,false);
echo html_writer::link($editurl, get_string('newentry', 'local_deluser_aftertime'));


echo $OUTPUT->footer();
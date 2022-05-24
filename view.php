<?php 
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once("./classes/deluserentryuserstable.php");


$entryid = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/local/deluser_aftertime/view.php', array('id'=>$entryid));
$returnurl = new moodle_url('/local/deluser_aftertime/entrylist.php');

require_login(); 
$title = get_string('pluginname', 'local_deluser_aftertime');

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
require_capability('local/deluser_aftertime:read', $systemcontext);

$PAGE->set_pagelayout('report');

$PAGE->set_title($title);
$PAGE->set_heading($title);

global $DB;

$dbtable = "local_deluser_aftertime";
$dbenrol = "enrol";
$dbuser_enrolments = "user_enrolments";


$entry = $DB->get_record($dbtable,['id'=>$entryid]);
$allenrolments = $DB->get_records($dbenrol,array('courseid'=>$entry->courseid),'','id'); 
$allusersincousedb =  array();
foreach($allenrolments as $index=>$value){

	$allusersincousedb[$index] = $DB->get_records($dbuser_enrolments,array('enrolid'=>$index),'','userid');
}

$allusersIDs = array();
foreach($allusersincousedb as $index=>$idsarray){
		foreach($idsarray as $ids){
			//var_dump($ids->userid);
			$allusersIDs[] = $ids->userid;
		}
}

$allusersIDs = array_unique($allusersIDs);
$myrows = Array();

foreach($allusersIDs as $userID){
	$user = $DB->get_record('user',array('id'=>$userID));
	//$user->load($userID);
	//var_dump($user);
	if(!is_siteadmin($user)){
		if($entry->filter == 'email_manual' || $entry->filter == $user->auth){
			$newrow = Array();
			$newrow['name'] = $user->firstname.' '.$user->lastname;
			$newrow['created'] = date( "d-m-Y", $user->timecreated);
			$newrow['deletedate'] = date( "d-m-Y",strtotime('+'.$entry->amount.' '.$entry->timespan, $user->timecreated));
			//$newrow['countdown'] = date_diff(time(),strtotime('+'.$entry->amount.' '.$entry->timespan, $user->timecreated));
			$newrow['countdown'] = strtotime('+'.$entry->amount.' '.$entry->timespan, $user->timecreated) - time();
			$day = floor($newrow['countdown'] / (24*3600));
			$newrow['countdown'] = $day." Days";
			$newrow['active'] = $entry->active;
			$myrows[]=$newrow;
		}
	}
}




$course = get_course($entry->courseid);
	

$testtable = new flexible_table('my_flexible_table2');
$tablecolumns = array('name','created' ,'deletedate', 'countdown', 'active');
$testtable->define_columns($tablecolumns);
$tableheaders = array('name', 'created','deletedate', 'countdown', 'active');
$testtable->define_headers($tableheaders);
$testtable->sortable(false);
$testtable->define_baseurl($url);
$testtable->setup();
$row = array("name"=>"35","created"=>"??", "deletedate"=>"37", "countdown"=>"43", "active"=>"active");
//$row = array('Ivan Gula', '6.6.2021', 'in 7 Tagen', 'active');


echo $OUTPUT->header();
echo $OUTPUT->heading($title." - ".get_string('course').": ".$course->fullname); 	

//var_dump($entry);
//echo html_writer::empty_tag('br');
//echo html_writer::div('/////////////////////////////////////////////////////////////');
//echo html_writer::empty_tag('br');
//var_dump($myrows);

foreach($myrows as $row){
	$testtable->add_data($row);
}
//$testtable->add_data($entryusers);
/*$mytable = new \local_deluser_aftertime\deluserentryuserstable('my_flexible_table1');
$mytable->define_baseurl($url);
$mytable->setup();
$mytable->add_data($entryusers, $tablecolumns);
echo html_writer::div($mytable->finish_output());*/

$testtable->finish_output();

echo $OUTPUT->footer();

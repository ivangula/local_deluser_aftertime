<?php 
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once("./classes/deluserentryform.php");

$entryid         = optional_param('id', 0, PARAM_INT);
$deleteentry     = optional_param('delete', 0, PARAM_INT);


$url = new moodle_url('/local/deluser_aftertime/edit.php', array('id'=>$entryid));
$returnurl = new moodle_url('/local/deluser_aftertime/entrylist.php');


require_login(); 
$title = get_string('pluginname', 'local_deluser_aftertime');

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
require_capability('local/deluser_aftertime:edit', $systemcontext);

$PAGE->set_pagelayout('report');

$PAGE->set_title($title);
$PAGE->set_heading($title);

global $DB;
$table = 'local_deluser_aftertime';

//var_dump($entryid);



if($deleteentry != 0){
	$entry = $DB->get_record($table,['id'=>$deleteentry],'*', MUST_EXIST);
	if($entry->active == 0){
		$DB->delete_records($table, ['id' => $deleteentry]);
		redirect($returnurl, get_string('deleted','local_deluser_aftertime'), null,\core\output\notification::NOTIFY_SUCCESS);
	}else{
		redirect($returnurl, get_string('cannotdeleted','local_deluser_aftertime'),null,\core\output\notification::NOTIFY_ERROR);
	}
}else{

	$mform = new \local_deluser_aftertime\deluserentryform();

	//Form processing and displaying is done here
	if ($mform->is_cancelled()) {
		redirect($returnurl);
		//Handle form cancel operation, if cancel button is present on form
	} else if ($fromform = $mform->get_data()) {
		$dataobject = new stdClass;
	  //In this case you process validated data. $mform->get_data() returns data posted in form.
		if($fromform->id == 0){
			$dataobject->courseid = $fromform->courseid;
			$dataobject->name = $fromform->name;
			$dataobject->timespan=$fromform->timespan;
			$dataobject->amount=$fromform->amount;
			$dataobject->filter=$fromform->filter;
			$dataobject->active=$fromform->active;
			$dataobject->timecreated=time();
			$DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
			redirect($returnurl, get_string('new_saved','local_deluser_aftertime'), null,\core\output\notification::NOTIFY_SUCCESS);
		}else{
			if($DB->get_record($table,['id'=>$fromform->id],'id', MUST_EXIST)){
				//var_dump($fromform);
				$dataobject->id = $fromform->id;
				$dataobject->name = $fromform->name;
				$dataobject->courseid = $fromform->courseid;
				$dataobject->timespan=$fromform->timespan;
				$dataobject->amount=$fromform->amount;
				$dataobject->filter=$fromform->filter;
				if(isset($fromform->active)){
					$dataobject->active=$fromform->active;
				}else{
					$dataobject->active=0;
				}
				$dataobject->timemodified=time();
				$DB->update_record($table, $dataobject, $bulk=false);
				redirect($returnurl,  get_string('update_saved','local_deluser_aftertime'), null,\core\output\notification::NOTIFY_SUCCESS);
			}
		}
		
		
	} else {
	  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	  // or on the first display of the form.
		if($entryid>0){
	  //Set default data (if any)
		
			if($toform =$DB->get_record($table,['id'=>$entryid],'*', MUST_EXIST)){
				//var_dump($toform);
				$mform->set_data($toform);
			}
		}
	  echo $OUTPUT->header();
	  echo $OUTPUT->heading($title."- EDIT!"); 	
	  //displays the form
	  $mform->display();
	}
}


echo $OUTPUT->footer();
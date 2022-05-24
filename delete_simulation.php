<?php 
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once("./lib.php");

$url = new moodle_url('/local/deluser_aftertime/delete_simulation.php');

require_login(); 
$title = get_string('pluginname', 'local_deluser_aftertime');

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
require_capability('local/deluser_aftertime:read', $systemcontext);

$PAGE->set_pagelayout('report');

$PAGE->set_title($title);
$PAGE->set_heading($title.' - '.'delete_simulation');

global $DB, $CFG;	
		$myString="NIX";
	if($CFG->deluser_aftertime_on_off||$CFG->deluser_aftertime_entry_on_off){
		$myString="";
		$entries = $DB->get_records('local_deluser_aftertime');
		$rs = $DB->get_recordset('user',array('deleted'=>0)); 
		foreach ($rs as $user) {
			if(!is_siteadmin($user)&&$user->id!=1){
				$userhasentry = false;
				if($CFG->deluser_aftertime_entry_on_off){
					if(!(($user_entries = ispartofentries($user,$entries))=== false)){
						
						$userhasentry = true;
						$longest_entry = getlongestentry($user_entries);
						$delete_date = strtotime('+'.$longest_entry->amount.' '.$longest_entry->timespan, $user->timecreated);
						if(time()>$delete_date){
							$myString.= "User: ".$user->firstname." ".$user->lastname." will be <b>deleted</b> because expire of ".$longest_entry->name."<br>";
							// Userdaten werden überschrieben
							// Userdaten werden gelöscht
							$myString.='<b>'.deleteuser($user,$longest_entry->name).'</b></br>';
						}else{
							$myString.= "User: ".$user->firstname." ".$user->lastname." <b>stays</b> until: ".date( "d-m-Y", $delete_date)." becaouse of ".$longest_entry->name."<br>";
						}
					}
				}
				if($CFG->deluser_aftertime_on_off&&$userhasentry==false&&strstr($CFG->deluser_aftertime_filter, $user->auth)!==false){
					$delete_date = strtotime('+'.$CFG->deluser_aftertime_count.' '.$CFG->deluser_aftertime_time, $user->timecreated);
					if(time()>$delete_date){
						$myString.= "User:".$user->firstname." ".$user->lastname." no entry - global timespan, will be <b>deleted</b> <br>";
						// Userdaten werden überschrieben
						// Userdaten werden gelöscht
						$myString.='<b>'.deleteuser($user).'</b></br>';
					}else{
						$myString.= "User:".$user->firstname." ".$user->lastname." no entry - global timespan, <b>stays</b> until: ".date( "d-m-Y", $delete_date)." <br>";
					}
				}
			}			
		}
		
		$rs->close();
	}
/*	
function ispartofentries($user,$entries){
	global $DB;
	$return = array();
	$flag = false;
	$user_enrollments = $DB->get_records('user_enrolments',array('userid'=>$user->id),'','enrolid');
	
	foreach($entries as $entry){
		$enrols = $DB->get_records('enrol',array('courseid'=>$entry->courseid),'','id');
		foreach ($user_enrollments as $user_enrollment){
			foreach($enrols as $enrol){
				if($enrol->id == $user_enrollment->enrolid && strstr($entry->filter, $user->auth)!==false){
					$flag = true;
					$return[] = $entry;
				}
			}
		}
	}
	if($flag){
		return $return;
	}else{
		return false;
	}
	
}

function getlongestentry($user_entries){
	if(count($user_entries)>1){
		$myflag = 0;
		$tempTime = time();
		foreach($user_entries as $index => $entry){
			$entryTimespan = strtotime('+'.$entry->amount.' '.$entry->timespan, time());
			if($tempTime < $entryTimespan){
				$myflag = $index;
 				$tempTime = $entryTimespan;
			}	
		}
		return $user_entries[$myflag];
	}else{
		return $user_entries[0];
	}
}

function deleteuser($user, $method='global'){
	
	$s = 'u'.time().'_'.$user->id.'_'.$method;
	$user->firstname = $s;
	$user->lastname = $s;
	$user->email = $s;
	$user->password = 'ResetPass21!';
	//user_update_user($user);
	//user_delete_user($user);
	return $s;
}*/

echo $OUTPUT->header();
echo $OUTPUT->heading($title.' - '.'delete_simulation'); 
echo html_writer::div($myString);
echo $OUTPUT->footer();

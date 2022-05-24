<?php

/**
 * Setting up navigation in sideadministration.
 * 3 Links to report, entry and simulation.
 */

function local_deluser_aftertime_extend_settings_navigation(settings_navigation $nav){
	$url = new moodle_url('/local/deluser_aftertime/index.php');
	$url_list = new moodle_url('/local/deluser_aftertime/entrylist.php');
	$url_simulation = new moodle_url('/local/deluser_aftertime/delete_simulation.php');
	$simulation_string = get_string('pluginname', 'local_deluser_aftertime')." - ".get_string('simulation', 'local_deluser_aftertime');
	$pluginreports = get_string('pluginname', 'local_deluser_aftertime')." - ".get_string('reports');
	$url_list_string = get_string('pluginname', 'local_deluser_aftertime')." - ".get_string('entrylist', 'local_deluser_aftertime');
	
	$node = $nav->get('root');
    if ($node) {
        $node = $node->get('modules');
		$node = $node->get('localplugins');
		$node = $node->get('mycategory_deluser_aftertime');
		
    }
	
	if ($node) {
        $condrole = $node->add($pluginreports, $url, navigation_node::TYPE_SETTING,
                                $pluginreports, null);
		$condrole = $node->add($url_list_string, $url_list, navigation_node::TYPE_SETTING,
                                $url_list_string, null);
		$condrole = $node->add($simulation_string, $url_simulation, navigation_node::TYPE_SETTING,
                                $simulation_string, null);
        
    }
	
	
}
/**
 * Function search all entries connected to the user and returns False or all connected entries as array.
 *
 * @param stdClass  $user 
 * @param stdClass  $entries 
 * @return bool or array False or array of entries relevant for user 
 */
function ispartofentries($user,$entries){
	global $DB;
	$return = array();
	$flag = false;
	$user_enrollments = $DB->get_records('user_enrolments',array('userid'=>$user->id),'','enrolid'); // All enrolments of the user.
	
	// Getting through all active entries 
	foreach($entries as $entry){
		if($entry->active==1){
			$enrols = $DB->get_records('enrol',array('courseid'=>$entry->courseid),'','id');
			foreach ($user_enrollments as $user_enrollment){
				foreach($enrols as $enrol){
					// If user is enrolled in course of entry and the authentification maches the entry filter.
					if($enrol->id == $user_enrollment->enrolid && strstr($entry->filter, $user->auth)!==false){
						$flag = true;
						$return[] = $entry;
					}
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

/**
 * Function search the entry with the longest term of delete date.
 *
 * @param array  $user_entries 
 * @return array Longest entry
 */
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
	
	$s = 'u'.time().'_'.$user->id.'_'.preg_replace('/[^A-Za-z0-9\_]/', '', $method);
	$user->firstname = $s;
	$user->lastname = $s;
	$user->email = $s;
	$user->password = 'ResetPass21!';
	//user_update_user($user);
	//user_delete_user($user);
	return $s;
}
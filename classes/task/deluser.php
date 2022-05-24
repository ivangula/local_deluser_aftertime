<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Klass for the task for Delete User After Time.
 *
 *
 * @package     local_deluser_aftertime
 * @category    string
 * @copyright   2019 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 namespace local_deluser_aftertime\task;
 
 use \core\task\scheduled_task;
 
 require_once($CFG->dirroot.'/user/lib.php');
 require_once($CFG->libdir.'/adminlib.php');
 
/**
 * Task deletes all users connented to entries or global setting.
 */
class deluser extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('deluser', 'local_deluser_aftertime');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
		global $DB, $CFG;	
			$myString = 'NIX';
		if($CFG->deluser_aftertime_on_off||$CFG->deluser_aftertime_entry_on_off){
			$myString="";
			$entries = $DB->get_records('local_deluser_aftertime');
			$rs = $DB->get_recordset('user',array('deleted'=>0)); 
			foreach ($rs as $user) {
				if(!is_siteadmin($user)&&$user->id!=1){
					// User is no admin and no guest
					$userhasentry = false;
					if($CFG->deluser_aftertime_entry_on_off){
						if(!(($user_entries = $this->ispartofentries($user,$entries))=== false)){
							//user has entry
							$userhasentry = true;
							$longest_entry = $this->getlongestentry($user_entries);
							$delete_date = strtotime('+'.$longest_entry->amount.' '.$longest_entry->timespan, $user->timecreated);
							if(time()>$delete_date){
								$myString.= "User: ".$user->firstname." ".$user->lastname." will be <b>deleted</b> because expire of ".$longest_entry->name."<br>";
								// User data will be updated and deleted
								$myString.='<b>'.$this->deleteuser($user,$longest_entry->name).'</b></br>';
							}else{
								$myString.= "User: ".$user->firstname." ".$user->lastname." <b>stays</b> until: ".date( "d-m-Y", $delete_date)." becaouse of ".$longest_entry->name."<br>";
							}
						}
					}
					if($CFG->deluser_aftertime_on_off&&$userhasentry==false&&strstr($CFG->deluser_aftertime_filter, $user->auth)!==false){
						$delete_date = strtotime('+'.$CFG->deluser_aftertime_count.' '.$CFG->deluser_aftertime_time, $user->timecreated);
						if(time()>$delete_date){
							$myString.= "User:".$user->firstname." ".$user->lastname." no entry - global timespan, will be <b>deleted</b> <br>";
							// User data will be updated and deleted
							$myString.='<b>'.$this->deleteuser($user).'</b></br>';
						}else{
							$myString.= "User:".$user->firstname." ".$user->lastname." no entry - global timespan, <b>stays</b> until: ".date( "d-m-Y", $delete_date)." <br>";
						}
					}
				}			
			}
			
			$rs->close();
		}
		echo $myString;
	}
	/**
	 * Function search all entries connected to the user and returns False or all connected entries as array.
	 * 
	 * @access private
	 * @param stdClass  $user 
	 * @param stdClass  $entries 
	 * @return bool or array False or array of entries relevant for user 
	 */
	private function ispartofentries($user,$entries){
		global $DB;
		$return = array();
		$flag = false;
		$user_enrollments = $DB->get_records('user_enrolments',array('userid'=>$user->id),'','enrolid');// All enrolments of the user.
		
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
	private function getlongestentry($user_entries){
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
	
	/**
	 * Updates user name, email and password and delete the user.
	 *
	 * @param stdClass  $user
	 * @param String  $method 
	 * @return String Message of the acomplished delete task
	 */

	private function deleteuser($user, $method='global'){
		$s = 'u'.time().'_'.$user->id.'_'.preg_replace('/[^A-Za-z0-9\_]/', '', $method);
		$user->firstname = $s;
		$user->lastname = $s;
		$user->email = $s;
		$user->password = 'ResetPass21!';
		user_update_user($user);
		user_delete_user($user);
		return $s;
	}
}

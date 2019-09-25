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
 
/**
 * An example of a scheduled task.
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
		
		if($CFG->deluser_aftertime_on_off){
			
			if($CFG->deluser_aftertime_filter=='email_manual'){
				$filter = "'email', 'manual'";
			}else{
				$filter = "'".$CFG->deluser_aftertime_filter."'";
			}
			
			$select = 'auth in('.$filter.') and deleted = 0 and suspended = 0 and timecreated < unix_timestamp(DATE_SUB(curdate(),INTERVAL '.$CFG->deluser_aftertime_count.' '.$CFG->deluser_aftertime_time.'))';
			
			$us = $DB->get_records_select('user',$select,[''],'id');	
			
			if($us!=NULL){
				
				foreach($us as $key_uid => $u){
					if(!is_siteadmin($u)){
						
						$u = (object)$u;
						$s = 'u'.time().'_'.$key_uid;
						$u->firstname = $s;
						$u->lastname = $s;
						$u->email = $s;
						user_update_user($u);
						user_delete_user($u);
					}
				}
			}
    	}
	}
}
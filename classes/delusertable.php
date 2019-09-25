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
 * The class for the table. Table view of Users be deleted in 24h.
 *
 * @package     local_deluser_aftertime
 * @copyright   2019 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
namespace local_deluser_aftertime;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/classes/user.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
 
use table_sql;
use html_writer;
use confirm_action;
use moodle_url;
use pix_icon;
use action_link;
use core_user;

class delusertable extends table_sql {
	
	/**
     * Sets up the table.
     *
     * 
     * @throws coding_exception
     */
    public function __construct() {
        global $PAGE, $CFG;

        parent::__construct('delusertable');
        $this->define_baseurl($PAGE->url->out(false));

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs();

        // TODO die folgenden Variablen!
		$from = '{user}';
		$fields = ['id','firstname','lastname','username', 'auth', 'timecreated'];
		
		if($CFG->deluser_aftertime_filter=='email_manual'){
				$filter = "'email', 'manual'";
			}else{
				$filter = "'".$CFG->deluser_aftertime_filter."'";
			}
			
		$select = 'auth in('.$filter.') and deleted = 0 and suspended = 0 and timecreated < unix_timestamp(DATE_SUB(DATE_SUB(curdate(),INTERVAL 1 DAY),INTERVAL '.$CFG->deluser_aftertime_count.' '.$CFG->deluser_aftertime_time.'))';
		
		
        $this->set_sql(implode(', ', $fields), $from, $select);
    }
    
	  /**
     * Column id.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_id($row) {
        return $row->id;
    }
	
	/**
     * Column firstname.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_firstname($row) {
        return $row->firstname;
    }
	
	/**
     * Column lastname.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_lastname($row) {
        return $row->lastname;
    }
	
	/**
     * Column username.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_username($row) {
        return $row->username;
    }
	
	/**
     * Column auth.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_auth($row) {
        return $row->auth;
    }
	
	/**
     * Column timecreated.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_timecreated($row) {
        return date("d.m.Y",$row->timecreated);
    }
	
	
	/**
     * Column admin.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_admin($row) {
			
		$u =array('id'=>$row->id);
		$u = (object)$u;
		if(is_siteadmin($u)){
			return 'Admin';
		}else{
			return 'No Admin';
		}
    }

	/**
     * Returns all columns shown in this table!
     *
     * @return array
     * @throws coding_exception
     */
    protected function get_cols() {
        return [
			'id' => get_string('id', 'local_deluser_aftertime'),
            'firstname' => get_string('firstname'),
            'lastname' => get_string('lastname'),
            'username' => get_string('username'),
			'auth' => get_string('auth','mod_organizer'),
			'timecreated' => get_string('timecreated','local_deluser_aftertime'),
            'admin' => get_string('admin')
        ];
    }
	
    /**
     * Setup the headers for the table.
     *
     * @throws coding_exception
     */
    protected function define_table_columns() {
        // Define headers and columns.
        $cols = $this->get_cols();

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /**
     * Define table configs.
     */
    protected function define_table_configs() {
        $this->collapsible(true);
        $this->sortable(false, 'id', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('sel');
    }
}
 
 
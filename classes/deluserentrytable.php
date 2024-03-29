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
 * The class for the table. Table view of all entries.
 *
 * @package     local_test_db
 * @copyright   2021 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
namespace local_deluser_aftertime;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/adminlib.php');

use table_sql;
use moodle_url;
use html_writer;

class deluserentrytable extends table_sql {
	public function __construct() {
        global $PAGE, $CFG;

        parent::__construct('deluserentrytable');
        $this->define_baseurl($PAGE->url->out(false));

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs();

		$from = '{local_deluser_aftertime}';
		
		$fields = ['id', 'name', 'courseid', 'timespan', 'amount', 'filter', 'active'];
		
			
		$select = '1=1';
		
		
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
     * Column course id.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_courseid($row) {
		$cours_db = get_course($row->courseid);
        return $cours_db->fullname;
    }
	
	/**
     * Column name.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_name($row) {
		$entry_user_url = new moodle_url('/local/deluser_aftertime/view.php', array('id'=>$row->id));
		$html = html_writer::link($entry_user_url, $row->name);
        return $html;
    }
	
	
	/**
     * Column timestamp.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_timespan($row) {
		$timespan = get_string($row->timespan,'local_deluser_aftertime');
        return $timespan;
    }
	
	/**
     * Column amount.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_amount($row) {
        return $row->amount;
    }
	
	/**
     * Column filter.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_filter($row) {
		$optionAuth = array(
			'email' =>  get_string('pluginname','auth_email'),
			'manual' =>  get_string('pluginname','auth_manual'),
			'email_manual' => get_string('all','local_deluser_aftertime')
		);
        return $optionAuth[$row->filter];
    }
	
	/**
     * Column activ.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function col_active($row) {
		if($row->active<1){
			$active = get_string('off','local_deluser_aftertime');
		}else{
			$active = get_string('on','local_deluser_aftertime');
		}
        return $active;
    }
	
	 protected function col_edit($row) {
		global $OUTPUT;
		$stredit   = get_string('edit');
		$strdelete = get_string('delete');
		
		$returnurl = new moodle_url('/local/deluser_aftertime/edit.php');
		 
		$url = new moodle_url($returnurl, array('delete'=>$row->id));
        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
		
		$url = new moodle_url($returnurl, array('id'=>$row->id));
        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
		
		 return implode(' ', $buttons);
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
			'name' => get_string('name', 'local_deluser_aftertime'),
            'courseid' => get_string('coursename','local_deluser_aftertime'),
            'timespan' => get_string('timespan','local_deluser_aftertime'),
            'amount' => get_string('amount','local_deluser_aftertime'),
			'filter' => get_string('filter','local_deluser_aftertime'),
			'active' => get_string('on_off','local_deluser_aftertime'),
			'edit' => get_string('edit','local_deluser_aftertime')
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

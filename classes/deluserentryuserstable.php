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
 * The class for the table. Table view of Users connected to one entry.
 *
 * @package     local_test_db
 * @copyright   2021 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
namespace local_deluser_aftertime;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/adminlib.php');

use flexible_table;
use moodle_url;
use html_writer;

class deluserentryuserstable extends  flexible_table {
	 public function __construct() {
        global $PAGE, $CFG; 

        parent::__construct('deluserentryuserstable');
        $this->define_baseurl($PAGE->url->out(false));
		// Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs();
		$this->setup();
   
    }
	/**
     * Returns all columns shown in this table!
     *
     * @return array
     * @throws coding_exception
     */
    protected function get_cols() {
        return [
			'name' => get_string('name', 'local_deluser_aftertime'),
            'deletedate' => get_string('timespan','local_deluser_aftertime'),
            'countdown' => get_string('amount','local_deluser_aftertime'),
			'active' => get_string('on_off','local_deluser_aftertime')
        ];
    }
	
	/**
     * Column id.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     
    protected function col_id($row) {
        return $row->id;
    }
	

	
	/**
     * Column name.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     
    protected function col_name($row) {
		
        return $name;
    }
	
	
	/**
     * Column deletedate.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     
    protected function col_deletedate($row) {
        return $deletedate;
    }
	
	/**
     * Column countdown.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     
    protected function col_countdown($row) {
        return $row->countdown;
    }
	
	
	/**
     * Column activ.
     *
     * @param  object $row
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     
    protected function col_active($row) {
		if($row->active<1){
			$active = get_string('off','local_deluser_aftertime');
		}else{
			$active = get_string('on','local_deluser_aftertime');
		}
        return $active;
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

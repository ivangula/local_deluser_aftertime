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
 * @copyright   2021 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
namespace local_deluser_aftertime;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

use moodleform;

 
class deluserentryform extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;
		
		
		
		$courses_option = array();
		$coures = get_courses();
		
		foreach($coures as $id => $course){
			
			if($id!=1){
				$courses_option[$course->id] = $course->fullname;
			}
		}
		
		
		$timespan_option = array(
			'DAY' => get_string('day'),
			'WEEK' => get_string('week'),
			'MONTH' => get_string('month'),
			'YEAR' => get_string('year')
		);
		
		$optionAuth = array(
			'email' =>  get_string('pluginname','auth_email'),
			'manual' =>  get_string('pluginname','auth_manual'),
			'email_manual' => get_string('all','local_deluser_aftertime')
		);
		
        $mform = $this->_form; // Don't forget the underscore! 
		
		
		$mform->addElement('hidden', 'id', 0); //PARAM_INT 
		$mform->setType('id', PARAM_INT);
		
		$mform->addElement('text', 'name', get_string('name','local_deluser_aftertime'));
		$mform->setType('name', PARAM_TEXT);
		
		$mform->addElement('select', 'courseid', get_string('course'), $courses_option);
		$mform->addElement('select', 'timespan', get_string('timespan','local_deluser_aftertime'),  $timespan_option);
		
        $mform->addElement('text', 'amount', get_string('amount','local_deluser_aftertime')); // Add elements to your form
   
		$mform->addElement('select', 'filter', get_string('filter','local_deluser_aftertime'),  $optionAuth);
		
		$mform->addElement('advcheckbox', 'active', get_string('on_off', 'local_deluser_aftertime'),'',array(),array(0,1));
		
		$mform->setType('amount', PARAM_INT);                   //Set type of element
        $mform->setDefault('amount', '20');        //Default value
        
		$this->add_action_buttons();
		
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
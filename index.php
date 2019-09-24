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
 * Plugin user interface.
 *
 * @package     local_deluser_aftertime
 * @copyright   2019 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
require_once("../../config.php");
#require_once("./classes/crontesttable.php");
require_once("./classes/delusertable.php");
#require_once($CFG->libdir.'/gdlib.php');
#require_once($CFG->dirroot.'/user/edit_form.php');
#require_once($CFG->dirroot.'/user/editlib.php');
#require_once($CFG->dirroot.'/user/profile/lib.php');
#require_once($CFG->dirroot.'/user/lib.php');
#require_once($CFG->libdir . '/adminlib.php');

//admin_externalpage_setup('condrole', '', null, '');

//$models = \core_analytics\manager::get_all_models();
$PAGE->set_heading(get_string('pluginname', 'local_deluser_aftertime'));

echo $OUTPUT->header();

#global $DB, $CFG, $USER;	
		
		

$table = new \local_deluser_aftertime\delusertable();
#$table = new delusertable();
$table->out(50,false);


/*global $OUTPUTHEADING;
echo "<div><p>".get_string('pluginname_desc', 'local_edaktik_condrole')."</p></div>";
$addNewRuleUrl = new moodle_url('/local/edaktik_condrole/edit.php');
echo "<span style='background-color:#def2f8; padding: 4px 6px; border-radius: 4px'><a href='".$addNewRuleUrl."'>".get_string('rule_add', 'local_edaktik_condrole')."</a></span>";

$table = new \local_edaktik_condrole\rulestable();
$table->out(50,false);
*/


//$templatable = new \tool_analytics\output\models_list($models);
//echo $PAGE->get_renderer('tool_analytics')->render($templatable);

echo $OUTPUT->footer();

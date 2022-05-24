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
 * Plugin delete user after time.
 *
 * @package     local_deluser_aftertime
 * @copyright   2019 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
require_once("../../config.php");

require_once("./classes/delusertable.php");
$url = new moodle_url('/local/deluser_aftertime/index.php');

require_login();

$title = get_string('pluginname', 'local_deluser_aftertime');

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);

require_capability('local/deluser_aftertime:read', $systemcontext);

$PAGE->set_pagelayout('report');

$PAGE->set_title($title);
$PAGE->set_heading($title);


echo $OUTPUT->header();
echo $OUTPUT->heading($title." - ".get_string('reports'));
		

$table = new \local_deluser_aftertime\delusertable(); // Create table of all User that will be deleted.
$table->out(50,false);	//Show table.


echo $OUTPUT->footer();

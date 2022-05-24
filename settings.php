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
 * Plugin administration pages are defined here.
 *
 * @package     local_deluser_aftertime
 * @category    admin
 * @copyright   2019 Ivan Gula <ivan.gula.wien@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ( $hassiteconfig ){
	
	// Create the new settings page
	// - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
	// $settings will be NULL
	$ADMIN->add('localplugins', new admin_category('mycategory_deluser_aftertime', get_string('pluginname', 'local_deluser_aftertime')));
	
	$settings = new admin_settingpage( 'deluser_aftertime',  get_string('settings'));
 
	// Create 
	$ADMIN->add( 'mycategory_deluser_aftertime', $settings );
	
   // TODO: Define the plugin settings page.
   // https://docs.moodle.org/dev/Admin_settings
   	$settings->add(new admin_setting_configcheckbox('deluser_aftertime_on_off', get_string('on_off', 'local_deluser_aftertime'),
                       get_string('on_off_desc', 'local_deluser_aftertime'), 0));
	
	$settings->add(new admin_setting_configcheckbox('deluser_aftertime_entry_on_off', get_string('on_off_entry', 'local_deluser_aftertime'),
                       get_string('on_off_entry_desc', 'local_deluser_aftertime'), 0));
	
	$optionAuth = array(
		'email' =>  get_string('pluginname','auth_email'),
		'manual' =>  get_string('pluginname','auth_manual'),
		'email_manual' => get_string('all','local_deluser_aftertime')
	);
	
	$settings->add(new admin_setting_configselect('deluser_aftertime_filter', get_string('filter', 'local_deluser_aftertime'),
                       get_string('filter_desc', 'local_deluser_aftertime'), 'email', $optionAuth));
	
	$settings->add(new admin_setting_configtext('deluser_aftertime_count', get_string('count', 'local_deluser_aftertime'),
                       get_string('count_desc', 'local_deluser_aftertime'), 20, PARAM_INT));
					   
	$option = array(
		'DAY' => get_string('day'),
		'WEEK' => get_string('week'),
		'MONTH' => get_string('month'),
		'YEAR' => get_string('year')
 	);
	
	$settings->add(new admin_setting_configselect('deluser_aftertime_time', get_string('time', 'local_deluser_aftertime'),
                       get_string('time_desc', 'local_deluser_aftertime'), 'DAY', $option));

	//$settings->add(new admin_setting_configtext('crontext_index', 'crontext_index','crontext_index2', 'Die indexseite wurde Aufgerufen', PARAM_TEXT));
	
	//	$settings->add(new admin_setting_configtext('crontext_cron', 'crontext_cron', 'crontext_cron2', 'Der Cron wurde ausgeführt!', PARAM_TEXT));
	//$settings->add(new admin_setting_configtext('crontext_cron', get_string('shortpost', 'forum'), get_string('configshortpost', 'forum'), 300, PARAM_INT));
}



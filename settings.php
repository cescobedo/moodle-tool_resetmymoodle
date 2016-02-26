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
 * Settings Tool Reset MyMoodle
 *
 * @package    tool_resetmymoodle
 * @author     Carlos Escobedo <http://www.twitter.com/carlosagile>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('development', new admin_category('toolresetmymoodle', 
    	get_string('pluginname', 'tool_resetmymoodle')));

	$settings = new admin_settingpage('toolresetmymoodlesettings', 
		get_string('toolresetmymoodlesettings', 'tool_resetmymoodle'));
    $settings->add(new admin_setting_configcheckbox('toolresetmymoodle/deletedusers',
        get_string('deletedusers', 'tool_resetmymoodle'), 
        get_string('deletedusersdesc', 'tool_resetmymoodle'), 0,1));
    $settings->add(new admin_setting_configcheckbox('toolresetmymoodle/includeguests',
        get_string('includeguests', 'tool_resetmymoodle'), 
        get_string('includeguestsdesc', 'tool_resetmymoodle'), 0,1));
    $settings->add(new admin_setting_configcheckbox('toolresetmymoodle/showlog',
        get_string('showlog', 'tool_resetmymoodle'), 
        get_string('showlogdesc', 'tool_resetmymoodle'), 0,1));
	$settings->add(new admin_setting_configselect('toolresetmymoodle/memoryextra', 
		get_string('memoryextra', 'tool_resetmymoodle'), 
	get_string('memoryextradesc', 'tool_resetmymoodle'), MEMORY_STANDARD, array(MEMORY_STANDARD => 'MEMORY_STANDARD', 
		MEMORY_EXTRA => 'MEMORY_EXTRA', MEMORY_HUGE => 'MEMORY_HUGE')));
	
    $ADMIN->add('toolresetmymoodle', $settings);
       $ADMIN->add('toolresetmymoodle', new admin_externalpage('toolresetmymoodlelaunch', get_string('pluginname', 'tool_resetmymoodle'),
        "$CFG->wwwroot/$CFG->admin/tool/resetmymoodle/index.php"));

}
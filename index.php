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
 * Main Reset MyMoodle
 *
 * @package    tool_resetmymoodle
 * @author     Carlos Escobedo <http://www.twitter.com/carlosagile>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once( '../../../my/lib.php' );
require_once($CFG->dirroot.'/lib/tablelib.php');

global $DB;

admin_externalpage_setup('toolresetmymoodlelaunch');

require_login();
require_capability('moodle/site:config', context_system::instance());

$reset      = optional_param('reset', 0, PARAM_INT);
$download   = optional_param('download', '', PARAM_ALPHA);
$settings   = get_config('toolresetmymoodle');

$table = new flexible_table('toolresetmymoodleid');
$exportfilename = 'logresetmy_' . userdate(time(),
    get_string('backupnameformat', 'langconfig'), 99, false);

// Check is download LOG.
if (!$table->is_downloading($download, $exportfilename)) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pageheader', 'tool_resetmymoodle'));
    if (!$reset or !confirm_sesskey()) {
        echo $OUTPUT->notification(get_string('warning', 'tool_resetmymoodle'));

        echo $OUTPUT->box_start();
        echo $OUTPUT->continue_button(new moodle_url('/admin/tool/resetmymoodle/index.php',
            array('reset' => 1, 'sesskey' => sesskey())));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        die;
    }
    echo $OUTPUT->box_start();
}

// GET Main admins.
$siteadmins = explode(',', $CFG->siteadmins);
$myadminid = $siteadmins[0];

// Set params.
$params = array();
// Include deleted users into reset MyMoodle.
if (!$settings->deletedusers) {
    $params['deleted'] = 0;
}

// Raise more memory if its necessary.
if ($settings->memoryextra <> MEMORY_STANDARD) {
    raise_memory_limit($settings->memoryextra);
}

if ($settings->showlog || $table->is_downloading($download, $exportfilename)) {
    // Setup Table to show log.
    $table->define_baseurl($PAGE->url);
    $table->define_columns(array('id', 'fullname', 'email'));
    $table->define_headers(array('id',  get_string('fullname'), get_string('email')));
    $table->set_attribute('class', 'generaltable generalbox boxaligncenter ');
    $table->is_downloadable(true);
    $table->show_download_buttons_at(array(TABLE_P_BOTTOM));
    $table->setup();
}

if ($users = $DB->get_records('user', $params)) {
    $countusers = 0;
    $datatable = array();
    foreach ($users as $user) {
        $row = array();
        $condition = ($user->id != $myadminid);
        // Incude guest users into reset MyMoodle.
        if (!$settings->includeguests) {
            $condition = ($condition && !isguestuser($user->id));
        }
        if ($condition) {
            if (!$table->is_downloading($download, $exportfilename)) {
                // Call internal Moodle function to reset the user.
                my_reset_page($user->id);
                $countusers++;
            }
            
            if ($settings->showlog || $table->is_downloading($download, $exportfilename)) {
                $row[] = $user->id;
                $row[] = fullname($user);
                $row[] = $user->email;
                $table->add_data($row);
            }   
        }
    }
    if ($settings->showlog) {
        $table->finish_output();
    }    
    if (!$table->is_downloading($download, $exportfilename)) {
        echo $OUTPUT->notification(get_string('resetok', 'tool_resetmymoodle', $countusers), 'notifysuccess');
    }
} else {
    if (!$table->is_downloading($download, $exportfilename)) {
        echo $OUTPUT->notification(get_string('nopages', 'tool_resetmymoodle'), 'notifysuccess');
    }
}
if (!$table->is_downloading($download, $exportfilename)) {
    echo $OUTPUT->box_end();

    echo $OUTPUT->continue_button(new moodle_url('/admin/tool/resetmymoodle/index.php', array('reset' => 0)));
    echo $OUTPUT->footer();
}
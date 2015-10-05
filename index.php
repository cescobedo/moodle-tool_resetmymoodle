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

global $DB;

admin_externalpage_setup('toolresetmymoodle');

require_login();
require_capability('moodle/site:config', context_system::instance());

$reset = optional_param('reset', 0, PARAM_INT);

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

// GET Main admin.
$siteadmins = explode(',', $CFG->siteadmins);
$myadminid = $siteadmins[0];

if ( $users = $DB->get_records( 'user' ) ) {
    $countusers = 0;
    foreach ($users as $user) {
        if ($user->id != $myadminid) {
            // Call internal Moodle function to reset the user.
            my_reset_page( $user->id );
            $countusers++;
        }
    }
    echo $OUTPUT->notification(get_string('resetok', 'tool_resetmymoodle', $countusers), 'notifysuccess');
} else {
    echo $OUTPUT->notification(get_string('nopages', 'tool_resetmymoodle'), 'notifysuccess');
}
echo $OUTPUT->box_end();

echo $OUTPUT->continue_button(new moodle_url('/admin/tool/resetmymoodle/index.php', array('reset' => 0)));
echo $OUTPUT->footer();

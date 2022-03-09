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
 * Global Settings
 *
 * @package   local_course_templates
 * @copyright 2017 onwards, emeneo (www.emeneo.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $ADMIN, $USER, $DB, $PAGE;

$contextuser = context_user::instance($USER->id);

$viewcoursetemplates = has_capability('local/course_templates:view', $contextuser);

$capabilities = array(
    'moodle/backup:backupcourse',
    'moodle/backup:userinfo',
    'moodle/restore:restorecourse',
    'moodle/restore:userinfo',
    'moodle/course:create',
    'moodle/site:approvecourse',
);

$systemcontext = context_system::instance();

if (has_capability('local/course_templates:view', $contextuser)
        && has_all_capabilities($capabilities, $systemcontext)
) {
    $ADMIN->add(
        'courses',
        new admin_externalpage(
            'local_course_templates',
            get_string('addcourse', 'local_course_templates'),
            new moodle_url('/local/course_templates/index.php'),
            $capabilities
        )
    );
}

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_course_templates_settings', 'Course templates');

    $ADMIN->add('localplugins', $settings);

    if ($ADMIN->fulltree) {
        $default = get_config('local_course_templates', 'namecategory');

        if ($default === false) {
            $templatecategory = $DB->get_record('course_categories', array('name' => 'Course templates'));

            // Set the new default administrator setting to 'Course templates' if it exists, if not default to 'Miscellaneous'.
            if ($templatecategory !== false) {
                $default = $templatecategory->id;
            } else {
                $default = 1;
            }
        }

        $settings->add(
            new admin_settings_coursecat_select(
                'local_course_templates/namecategory',
                get_string('namecategory', 'local_course_templates'),
                get_string('namecategorydescription', 'local_course_templates'),
                $default
            )
        );

        $options = array(
            1 => get_string('jumpto_coursepage', 'local_course_templates'),
            2 => get_string('jumpto_coursesettingspage', 'local_course_templates')
        );

        $settings->add(
            new admin_setting_configselect(
                'local_course_templates/jump_to',
                get_string('jumpto', 'local_course_templates'),
                '',
                1,
                $options
            )
        );
    }
}

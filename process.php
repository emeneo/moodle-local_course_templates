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
 * Process file.
 *
 * @package   local_course_templates
 * @copyright 2017 onwards, emeneo (www.emeneo.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('../../course/externallib.php');

global $CFG, $DB;

require_login();
require_sesskey();

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

$context = context_user::instance($USER->id);
require_capability('local/course_templates:view', $context);

// TODO: check option users further.
$fullname = optional_param('course_name', '', PARAM_RAW);
$shortname = optional_param('course_short_name', '', PARAM_RAW);
$categoryid = optional_param('cateid', 0, PARAM_INT);
$courseid = optional_param('cid', 0, PARAM_INT);
$options = array(
    array('name' => 'blocks', 'value' => 1),
    array('name' => 'activities', 'value' => 1),
    array('name' => 'filters', 'value' => 1),
    array('name' => 'users', 'value' => 1)
);
$visible = 1;

$startdatetime = optional_param('start_datetime', '', PARAM_RAW);
$enddatetime = optional_param('end_datetime', '', PARAM_RAW);
$location = optional_param('location', '', PARAM_RAW);
$coursedate = optional_param('course_date', '', PARAM_RAW);

if (!empty($startdatetime)) {
    $startdatetime = strtotime($coursedate.' '.$startdatetime);
}

if (!empty($enddatetime)) {
    $enddatetime = strtotime($coursedate.' '.$enddatetime);
}

if (!$fullname || !$shortname || !$categoryid || !$courseid) {
    exit(json_encode(array('status' => 2, 'id' => $courseid, 'cateid' => $categoryid)));
}

$externalobj = new core_course_external();
$res = $externalobj->duplicate_course($courseid, $fullname, $shortname, $categoryid, $visible, $options);

if (@isset($res['id'])) {
    $course = $DB->get_record('course', array('id' => $res['id']));

    if (!empty($startdatetime)) {
        $course->startdate = $startdatetime;
        $course->enddate = $enddatetime;
        $DB->update_record('course', $course);
    }

    if (!empty($location)) {
        $eventoption = $DB->get_record(
            'course_format_options',
            array(
                'courseid' => $course->id,
                'format' => 'event',
                'name' => 'location'
            )
        );
        $eventoption->value = $location;
        $DB->update_record('course_format_options', $eventoption);
    }

    exit(json_encode(array('status' => 1, 'id' => $res['id'], 'shortname' => $res['shortname'])));
} else {
    exit(json_encode(array('status' => 0)));
}

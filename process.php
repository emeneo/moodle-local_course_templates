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

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once(dirname(__FILE__) . '/../../config.php');
require_once('../../course/externallib.php');
require_login();
require_sesskey();

$context = context_user::instance($USER->id);
require_capability('local/course_templates:edit', $context);

$fullname = $_REQUEST['course_short_name'];
$shortname = $_REQUEST['course_short_name'];
$categoryid = $_REQUEST['cateid'];
$courseid = $_REQUEST['cid'];
$options = array(array('name' => 'blocks', 'value' => 1),
                 array('name' => 'activities', 'value' => 1),
                 array('name' => 'filters', 'value' => 1),
                 array('name' => 'users', 'value' => 1));
$visible = 1;

if (!$fullname || !$shortname || !$categoryid || !$courseid) {
    exit(json_encode(array('status' => 2, 'id' => $courseid, 'cateid' => $categoryid)));
}

$externalObj = new core_course_external();
$res = $externalObj->duplicate_course($courseid, $fullname, $shortname, $categoryid, $visible, $options);

if (@isset($res['id'])) {
    exit(json_encode(array('status' => 1, 'id' => $res['id'], 'shortname' => $res['shortname'])));
} else {
    exit(json_encode(array('status' => 0)));
}

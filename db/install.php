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
 * The installation script.
 *
 * @package     local_course_templates
 * @copyright   2021 Andrew Caya <andrewscaya@yahoo.ca>
 * @author      Andrew Caya
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sets some database values upon plugin installation.
 *
 * @return bool
 * @throws dml_exception
 * @throws moodle_exception
 */
function xmldb_local_course_templates_install() {
    global $CFG, $DB;

    require_once($CFG->dirroot . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . 'lib.php');

    // Check if the 'Course templates' category exists and if not, create it.
    $templatecategory = $DB->get_record('course_categories', array('name' => 'Course templates'));

    if ($templatecategory === false) {
        $dataobject = new stdClass();
        $dataobject->name = 'Course templates';
        $dataobject->idnumber = '';
        $dataobject->description = 'Category containing course templates';
        $dataobject->descriptionformat = 0;
        $dataobject->parent = 0;
        $dataobject->sortorder = 20000;
        $dataobject->coursecount = 0;
        $dataobject->visible = 1;
        $dataobject->visibleold = 1;
        $dataobject->timemodified = time();

        // Refreshing caches through the Event API.
        core_course_category::create($dataobject);
    }

    return true;
}

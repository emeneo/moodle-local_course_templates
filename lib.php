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
 * Main library file.
 *
 * @package   local_course_templates
 * @copyright 2017 onwards, emeneo (www.emeneo.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_course_templates\coursecategorieslistform;
use local_course_templates\coursetemplateslistform;

/**
 * Loads category specific settings in the navigation
 *
 * @param navigation_node $parentnode
 * @param context_coursecat $context
 *
 * @return navigation_node
 */
function local_course_templates_extend_navigation_category_settings($parentnode, $context) {
    $capabilities = array(
        'moodle/backup:backupcourse',
        'moodle/backup:userinfo',
        'moodle/restore:restorecourse',
        'moodle/restore:userinfo',
        'moodle/course:create',
        'moodle/site:approvecourse',
    );

    if (has_capability('local/course_templates:view', $context)
            && has_all_capabilities($capabilities, $context)
    ) {
        $parentnode->add(
            get_string('addcourse', 'local_course_templates'),
            new moodle_url('/local/course_templates/index.php', array('cateid' => $context->instanceid)),
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('t/add', get_string('addcourse', 'local_course_templates'))
        );
    };
}

/**
 * Gets the list of templates.
 *
 * @return array
 *
 * @throws dml_exception
 */
function get_template_list() {
    global $DB;

    $namecategoryid = get_config('local_course_templates', 'namecategory');

    $sql = "select id, fullname from {course} where category = (select id from {course_categories} where id='$namecategoryid')";

    return $DB->get_records_sql($sql);
}

/**
 * Gets the form for the template list.
 *
 * @param int $cateid
 *
 * @return string
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function get_template_list_form($cateid = null) {
    global $CFG, $USER;

    if (isset($cateid) && !empty($cateid)) {
        $cateid = (int) $cateid;

        $context = context_coursecat::instance($cateid);
    } else {
        $context = context_user::instance($USER->id);
    }

    $redirecturl = $CFG->wwwroot.'/local/course_templates/index.php?cateid=' . $cateid . '&step=2';

    $rows = get_template_list();

    $table = new html_table();
    $table->align = array('left');

    foreach ($rows as $row) {
        $data = array();

        $data[] = format_string($row->fullname, true, ['context' => $context]);

        $mform = new coursetemplateslistform($redirecturl . '&cid=' . $row->id, array('buttonid' => $row->id));

        // Turn on output buffering because MoodleQuickForm writes to the buffer directly.
        ob_start();
        $mform->display();
        $data[] = ob_get_clean();

        $table->data[] = $data;
    }

    return html_writer::table($table);
}

/**
 * Gets the form for the course categories.
 *
 * @param int $cid
 * @param int $cateid
 *
 * @return string
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function get_template_categories_form($cid, $cateid = null) {
    global $CFG, $USER;

    $capabilities = array(
        'moodle/backup:backupcourse',
        'moodle/backup:userinfo',
        'moodle/restore:restorecourse',
        'moodle/restore:userinfo',
        'moodle/course:create',
        'moodle/site:approvecourse',
        'local/course_templates:view',
    );

    $cateid = (int) $cateid ?? 1;

    $redirecturl = $CFG->wwwroot . '/local/course_templates/index.php?cateid=' . $cateid . '&step=3&cid=' . $cid;

    $rowsarray = \core_course_category::make_categories_list($capabilities);

    $categoriesarray = array();

    foreach ($rowsarray as $key => $row) {
        $categoriesarray[$key] = $row;
    }

    $mform = new coursecategorieslistform(
        $redirecturl,
        array(
            'categoriesarray' => $categoriesarray,
            'defaultcategory' => $cateid,
        )
    );

    // Turn on output buffering because MoodleQuickForm writes to the buffer directly.
    ob_start();
    $mform->display();
    $data[] = ob_get_clean();

    $output = '';
    $table = new html_table();
    $table->align = array('left');

    $table->data[] = $data;

    $output .= html_writer::table($table);

    return $output;
}

/**
 * Gets the form for the template setting.
 *
 * @param int $cid
 * @param int $categoryid
 * @param int $cateid
 *
 * @return string
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function get_template_setting_form($cid, $categoryid, $cateid = null) {
    global $CFG, $DB, $OUTPUT;

    if (isset($cateid) && !empty($cateid)) {
        $cateid = (int) $cateid;
    }

    $course = $DB->get_record('course', array('id' => $cid));

    $redirecturl = $CFG->wwwroot.'/local/course_templates/process.php?cateid=' . $cateid . '&cid='
        . $cid
        . '&sesskey='
        . sesskey();

    $returnurl   = $CFG->wwwroot.'/local/course_templates/index.php?cateid=' . $cateid . '&step=4';

    $output = '';
    $output .= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/jquery-1.8.3.min.js"></script>';
    $output .= '<link rel="stylesheet" href="'.$CFG->wwwroot.'/local/course_templates/css/bootstrap-datetimepicker.css">';
    $output .= '<link rel="stylesheet" href="'.$CFG->wwwroot.'/local/course_templates/css/throbber.css">';
    $output .= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/bootstrap-datetimepicker.js"></script>';
    $output .= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/process.js"></script>';
    $output .= '<div id="course_templates_validation_error_message" data-validation-message="'
        . get_string('requiredelement', 'form')
        . '"></div>';

    $output .= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'process_request_url', 'value' => $redirecturl));
    $output .= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'process_returnurl', 'value' => $returnurl));
    $output .= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'success_returnurl', 'value' => $CFG->wwwroot));
    $table = new html_table();
    $table->align = array('left');

    $table->data[] = array(
        html_writer::nonempty_tag(
            'label',
            get_string('coursename', 'local_course_templates'),
            array(
                'for' => 'course_name',
                'id' => 'course_name_label'
            )
        ),
        $OUTPUT->pix_icon('req', get_string('requiredelement', 'form')),
        html_writer::empty_tag(
           'input',
           array(
               'type' => 'text',
               'id' => 'course_name',
               'required' => 'required'
           )
        )
    );
    $table->data[] = array(
        html_writer::nonempty_tag(
            'label',
            get_string('courseshortname', 'local_course_templates'),
            array(
                'for' => 'course_short_name',
                'id' => 'course_short_name_label'
            )
        ),
        $OUTPUT->pix_icon('req', get_string('requiredelement', 'form')),
        html_writer::empty_tag(
            'input',
            array(
               'type' => 'text',
               'id' => 'course_short_name',
               'required' => 'required'
            )
        )
    );

    $table->data[] = array(
        '',
        '',
        '<div class="fdescription required">'
        . get_string('somefieldsrequired', 'form', $OUTPUT->pix_icon('req', get_string('requiredelement', 'form')))
        . '</div>'
    );

    if ($course->format == 'event') {
        $optionshour = $optionsmin = '';
        for ($i = 0; $i < 24; $i++) {
            $hour = $i;

            if ($hour < 10) {
                $hour = '0'.$hour;
            }

            $optionshour .= '<option value="' . $hour . '">' . $hour . '</option>';
        }

        for ($i = 0; $i < 60; $i++) {
            $min = $i;

            if ($min < 10) {
                $min = '0'.$min;
            }

            $optionsmin .= '<option value="' . $min . '">' . $min . '</option>';
        }

        $startdatetimeh = '<select id="start_datetime_h" style="margin-right:3px;">'
            . $optionshour
            . '</select>';
        $startdatetimem = '<select id="start_datetime_m" style="margin:0 20px 0 3px;">'
            . $optionsmin
            . '</select>';

        $enddatetimeh = '<select id="end_datetime_h" style="margin-right:3px;">'
            . $optionshour
            . '</select>';
        $enddatetimem = '<select id="end_datetime_m" style="margin:0 20px 0 3px;">'
            . $optionsmin
            . '</select>';

        $table->data[] = array(
            get_string('datetime', 'local_course_templates'),
            html_writer::empty_tag(
                'input',
                array(
                    'type' => 'text',
                    'id' => 'course_date',
                    'class' => "form_datetime",
                    'style' => 'margin-right:10px;'
                )
            ) . $startdatetimeh . ":" . $startdatetimem . $enddatetimeh . ":" . $enddatetimem);

        $config = get_config('format_event');
        $locations = $config->locations;
        $arrlocations = explode(";", $locations);
        $options = "<select id='location'>";
        foreach ($arrlocations as $location) {
            if (empty($location)) {
                continue;
            }
            $options .= "<option value='" . $location . "'>" . $location . "</option>";
        }
        $options .= "</select>";
        $table->data[] = array(get_string('location', 'local_course_templates'), $options);
    }

    $output .= html_writer::table($table);
    $output .= html_writer::tag(
        'p',
        html_writer::empty_tag(
            'input',
            array(
                'type' => 'button',
                'value' => get_string('back', 'local_course_templates'),
                'onclick' => 'javascript :history.back(-1)',
                'class' => 'btn btn-secondary',
                'style' => 'margin-right:20px;'
            )
        ) . html_writer::empty_tag(
            'input',
            array(
                'type' => 'button',
                'value' => get_string('continue', 'local_course_templates'),
                'id' => 'btnProcess',
                'class' => 'btn btn-primary'
            )
        )
    );

    $output .= '<script>$("#course_date") . datetimepicker({minView: "month",format: "yyyy-mm-dd", autoclose:true});</script>';

    return $output;
}

// Make sure that we are compatible with Moodle 3.6+.
if (!function_exists('require_all_capabilities')) {
    /**
     * A convenience function that tests has_capability for a list of capabilities, and displays an error if
     * the user does not have that capability.
     *
     * This is just a utility method that calls has_capability in a loop. Try to put
     * the capabilities that fewest users are likely to have first in the list for best
     * performance.
     *
     * @category access
     * @see has_capability()
     *
     * @param array $capabilities an array of capability names.
     * @param context $context the context to check the capability in. You normally get this with context_xxxx::instance().
     * @param int $userid A user id. By default (null) checks the permissions of the current user.
     * @param bool $doanything If false, ignore effect of admin role assignment
     * @param string $errormessage The error string to to user. Defaults to 'nopermissions'.
     * @param string $stringfile The language file to load the error string from. Defaults to 'error'.
     * @return void terminates with an error if the user does not have the given capability.
     */
    function require_all_capabilities(
        array $capabilities,
        context $context,
        $userid = null,
        $doanything = true,
        $errormessage = 'nopermissions',
        $stringfile = '')
    : void {
        foreach ($capabilities as $capability) {
            if (!has_capability($capability, $context, $userid, $doanything)) {
                throw new required_capability_exception($context, $capability, $errormessage, $stringfile);
            }
        }
    }
}

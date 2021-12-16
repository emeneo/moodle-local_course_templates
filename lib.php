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

defined('MOODLE_INTERNAL') || die;

/**
 * Gets the list of templates.
 *
 * @return array
 * @throws dml_exception
 */
function get_template_list() {
    global $CFG, $USER, $DB;

    $namecategoryid = get_config('local_course_templates', 'namecategory');

    $sql = "select id, fullname from {course} where category = (select id from {course_categories} where id='$namecategoryid')";

    return $DB->get_records_sql($sql);
}

/**
 * Gets the form for the template list.
 *
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function get_template_list_form() {
    global $CFG, $USER, $DB;

    $context = context_user::instance($USER->id);
    $redirecturl = $CFG->wwwroot.'/local/course_templates/index.php?step=2';
    $rows = get_template_list();

    $table = new html_table();
    $table->align = array('left');

    foreach ($rows as $row) {
        $data = array();

        $data[] = format_string($row->fullname, true, ['context' => $context]);
        $data[] = html_writer::empty_tag(
            'input',
            array(
                'type' => 'button',
                'value' => get_string('useastemplate', 'local_course_templates'),
                'onclick' => 'window.location.href="' . $redirecturl . '&cid=' . $row->id . '"',
                'class' => 'btn btn-primary'
            )
        );

        $table->data[] = $data;
    }

    return html_writer::table($table);
}

/**
 * Gets the course categories.
 *
 * @param int $visible
 * @return array
 * @throws dml_exception
 */
function get_template_categories($visible = 1) {
    global $CFG, $USER, $DB;

    $sql = "select id,name,description from {course_categories} where visible=$visible";

    return $DB->get_records_sql($sql);
}

/**
 * Gets the form for the course categories.
 *
 * @param string $cid
 *
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function get_template_categories_form($cid) {
    global $CFG, $USER, $DB;

    $context = context_user::instance($USER->id);
    $redirecturl = $CFG->wwwroot.'/local/course_templates/index.php?step=3&cid=' . $cid;
    $rows = get_template_categories(1);

    $output = '';
    $action = $redirecturl;
    $output .= html_writer::start_tag('form', array('action' => $action, 'method' => 'post'));
    $table = new html_table();
    $table->align = array('left');

    foreach ($rows as $row) {
        $data = array();

        $data[] = html_writer::empty_tag('input', array('type' => 'radio', 'value' => $row->id, 'name' => 'sel_cate'));
        $data[] = format_string($row->name, true, ['context' => $context]);
        $data[] = strip_tags(format_text($row->description, FORMAT_HTML, ['context' => $context]));

        $table->data[] = $data;
    }

    $rows = get_template_categories(0);
    $hiddentable = new html_table();

    $hiddentable->align = array('left');

    foreach ($rows as $row) {
        $data = array();

        $data[] = html_writer::empty_tag('input', array('type' => 'radio', 'value' => $row->id, 'name' => 'sel_cate'));
        $data[] = format_string($row->name, true, ['context' => $context]);
        $data[] = strip_tags(format_text($row->description, FORMAT_HTML, ['context' => $context]));

        $hiddentable->data[] = $data;
    }

    $output .= html_writer::table($table);
    $output .= html_writer::tag('p', html_writer::tag('strong', get_string('hiddencategories',  'local_course_templates')));
    $output .= html_writer::table($hiddentable);
    $output .= html_writer::tag(
        'p',
        html_writer::empty_tag(
            'input',
            array(
                'type' => 'button',
                'value' => 'Back',
                'onclick' => 'javascript :history.back(-1)',
                'class' => 'btn btn-primary',
                'style' => 'margin-right:20px;'
            )
        ) . html_writer::empty_tag(
            'input',
            array(
                'type' => 'submit',
                'value' => get_string('continue', 'local_course_templates'),
                'class' => 'btn btn-primary'
            )
        )
    );

    $output .= html_writer::end_tag('form');

    return $output;
}

/**
 * Gets the form for the template setting.
 *
 * @param string $cid
 * @param string $categoryid
 *
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function get_template_setting_form($cid, $categoryid) {
    global $CFG, $USER, $DB;

    $course = $DB->get_record('course', array('id' => $cid));

    $redirecturl = $CFG->wwwroot.'/local/course_templates/process.php?cid='
        . $cid
        . '&cateid='
        . $categoryid
        . '&sesskey='
        . sesskey();
    $returnurl   = $CFG->wwwroot.'/local/course_templates/index.php?step=4';
    $output = '';
    $output .= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/jquery-1.8.3.min.js"></script>';
    $output .= '<link rel="stylesheet" href="'.$CFG->wwwroot.'/local/course_templates/js/bootstrap-datetimepicker.css">';
    $output .= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/bootstrap-datetimepicker.js"></script>';
    $output .= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/process.js"></script>';

    $output .= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'process_request_url', 'value' => $redirecturl));
    $output .= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'process_returnurl', 'value' => $returnurl));
    $output .= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'success_returnurl', 'value' => $CFG->wwwroot));
    $table = new html_table();
    $table->align = array('left');

    $table->data[] = array(get_string('coursename', 'local_course_templates'),
                           html_writer::empty_tag('input', array('type' => 'text', 'id' => 'course_name')));
    $table->data[] = array(get_string('courseshortname', 'local_course_templates'),
                           html_writer::empty_tag('input', array('type' => 'text', 'id' => 'course_short_name')));
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

        $startdatetimeh = '<select id="start_datetime_h" style="margin-right:3px;">'.$optionshour.'</select>';
        $startdatetimem = '<select id="start_datetime_m" style="margin:0 20px 0 3px;">'.$optionsmin.'</select>';

        $enddatetimeh = '<select id="end_datetime_h" style="margin-right:3px;">'.$optionshour.'</select>';
        $enddatetimem = '<select id="end_datetime_m" style="margin:0 20px 0 3px;">'.$optionsmin.'</select>';

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
                'value' => 'Back',
                'onclick' => 'javascript :history.back(-1)',
                'class' => 'btn btn-primary',
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

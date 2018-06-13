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
 * @package   local_course_templates
 * @copyright 2017 onwards, emeneo (www.emeneo.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die;

function get_template_list() {
    global $CFG, $USER, $DB;

    $sql = "select id,fullname from {course} where category = (select id from {course_categories} where name='Course templates')";
    return $DB->get_records_sql($sql);
}

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
        $data[] = html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('useastemplate', 'local_course_templates'), 'onclick' => 'window.location.href="'.$redirecturl.'&cid='.$row->id.'"'));

        $table->data[] = $data;
    }

    return html_writer::table($table);
}

function get_template_categories($visible=1) {
    global $CFG, $USER, $DB;

    $sql = "select id,name,description from {course_categories} where visible=".$visible;
    return $DB->get_records_sql($sql);
}

function get_template_categories_form($cid) {
    global $CFG, $USER, $DB;

    $context = context_user::instance($USER->id);
    $redirecturl = $CFG->wwwroot.'/local/course_templates/index.php?step=3&cid='.$cid;
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
    $output .= html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('continue', 'local_course_templates'))));
    $output .= html_writer::end_tag('form');
    return $output;
}

function get_template_setting_form($cid, $categoryid) {
    global $CFG, $USER, $DB;

    $redirecturl = $CFG->wwwroot.'/local/course_templates/process.php?cid='.$cid.'&cateid='.$categoryid.'&sesskey='.sesskey();
    $returnurl   = $CFG->wwwroot.'/local/course_templates/index.php?step=4';
    $output = '';
    // ... $output.= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/jquery.js"></script>';
    // ... $output.= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/require.js"></script>';
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
    $output .= html_writer::table($table);
    $output .= html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'id' => 'btnProcess')));
    return $output;
}

function local_course_templates_extend_navigation(global_navigation $navigation) {
    global $PAGE, $COURSE;
    // ... echo "<pre>";print_r($navigation);exit;
    $branch = $navigation->find('admin', navigation_node::TYPE_SITE_ADMIN);
    // ... $branch = $PAGE->navigation;
    $node = navigation_node::create(get_string('addcourse', 'local_course_templates'), new moodle_url('/local/course_templates/index.php'));
    // ... $branch->add_node($node);
}

function local_course_templates_extends_navigation(global_navigation $navigation) {
    local_course_templates_extend_navigation($navigation);
}
/*
function local_course_templates_extend_settings_navigation(settings_navigation $settingsnav, context $context){
    global $CFG, $PAGE;

    $settingnode = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);
    var_dump($settingnode);exit;
    if($settingnode){
        $setMotdMenuLbl = get_string('addcourse', 'local_course_templates');
        $setMotdUrl = new moodle_url('/local/course_templates/index.php');
        $setMotdnode = navigation_node::create(
            $setMotdMenuLbl,
            $setMotdUrl,
            navigation_node::NODETYPE_LEAF);

        $settingnode->add_node($setMotdnode);
    }
}

function local_course_templates_extend_navigation(global_navigation $nav){
    global $CFG, $PAGE;

    $previewnode = $PAGE->navigation->add(get_string('preview'), new moodle_url('/a/link/if/you/want/one.php'), navigation_node::TYPE_CONTAINER);
    $thingnode = $previewnode->add(get_string('name of thing'), new moodle_url('/a/link/if/you/want/one.php'));
    $thingnode->make_active();
}
*/

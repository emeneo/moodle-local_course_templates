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

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

require_login();

$step = optional_param('step', 0, PARAM_INT);
$cid = optional_param('cid', 0, PARAM_INT);
$cateid = optional_param('cateid', 0, PARAM_INT);

$params = array();
$userid = $USER->id;  // Owner of the page.
$context = context_user::instance($USER->id);
$header = fullname($USER);
$pagetitle = get_string('pluginname', 'local_course_templates');

$PAGE->set_context($context);
$PAGE->set_url('/local/course_templates/index.php', $params);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($header);

$sel_cate = optional_param('sel_cate', 0, PARAM_INT); 
$course_status = optional_param('status', 0, PARAM_INT);
$course_id = optional_param('courseid', 0, PARAM_INT);
$cate_id = optional_param('cateid', 0, PARAM_INT); 

require_capability('local/course_templates:view', $context);

$redirecturl = $CFG->wwwroot.'/local/course_templates/index.php';

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
//echo '<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js">';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">';

$theme_cfg = get_config('theme_boost');

if (!$step) {
    $html_stepper = '';
    $html_stepper.= '<div class="bs-stepper">';
    $html_stepper.= '<div class="bs-stepper-header" role="tablist">';
    $html_stepper.= '<div class="step active" data-target="#logins-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="logins-part" id="logins-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle" style="background-color:'.$theme_cfg->brandcolor.';">1</span>';
    $html_stepper.= '<span class="bs-stepper-label"><strong style="color:'.$theme_cfg->brandcolor.';">Choose Template</strong></span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '<div class="line"></div>';
    $html_stepper.= '<div class="step" data-target="#information-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle">2</span>';
    $html_stepper.= '<span class="bs-stepper-label">Select Category</span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '<div class="line"></div>';
    $html_stepper.= '<div class="step" data-target="#information-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle">3</span>';
    $html_stepper.= '<span class="bs-stepper-label">Define Settings</span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '</div>';
    echo $html_stepper;

    echo html_writer::tag('p', html_writer::tag('strong', get_string('choosetemplate', 'local_course_templates')));
    echo get_template_list_form();
} else if ($step == 2) {
    $html_stepper = '';
    $html_stepper.= '<div class="bs-stepper">';
    $html_stepper.= '<div class="bs-stepper-header" role="tablist">';
    $html_stepper.= '<div class="step" data-target="#logins-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="logins-part" id="logins-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle">1</span>';
    $html_stepper.= '<span class="bs-stepper-label">Choose Template</span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '<div class="line"></div>';
    $html_stepper.= '<div class="step active" data-target="#information-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle" style="background-color:'.$theme_cfg->brandcolor.';">2</span>';
    $html_stepper.= '<span class="bs-stepper-label"><strong style="color:'.$theme_cfg->brandcolor.';">Select Category</strong></span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '<div class="line"></div>';
    $html_stepper.= '<div class="step" data-target="#information-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle">3</span>';
    $html_stepper.= '<span class="bs-stepper-label">Define Settings</span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '</div>';
    echo $html_stepper;
    if (!$cid) {
        echo $OUTPUT->notification(get_string('choosetemplate', 'local_course_templates'));
        echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Back', 'onclick' => 'javascript :history.back(-1)', 'class' => 'btn btn-primary', 'style' => 'margin-right:20px;')).html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'onclick' => 'window.location.href="'.$redirecturl.'"', 'class' => 'btn btn-primary')));
    } else {
        echo html_writer::tag('p', html_writer::tag('strong', get_string('choosecategory', 'local_course_templates')));
        echo get_template_categories_form($cid);
    }
} else if ($step == 3) {
    $course_templates_config = get_config('local_course_templates');
    $html_stepper = '';
    $html_stepper.= '<div class="bs-stepper">';
    $html_stepper.= '<div class="bs-stepper-header" role="tablist">';
    $html_stepper.= '<div class="step" data-target="#logins-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="logins-part" id="logins-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle">1</span>';
    $html_stepper.= '<span class="bs-stepper-label">Choose Template</span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '<div class="line"></div>';
    $html_stepper.= '<div class="step" data-target="#information-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle">2</span>';
    $html_stepper.= '<span class="bs-stepper-label">Select Category</span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '<div class="line"></div>';
    $html_stepper.= '<div class="step active" data-target="#information-part">';
    $html_stepper.= '<button type="button" class="step-trigger" role="tab" aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $html_stepper.= '<span class="bs-stepper-circle" style="background-color:'.$theme_cfg->brandcolor.';">3</span>';
    $html_stepper.= '<span class="bs-stepper-label"><strong style="color:'.$theme_cfg->brandcolor.';">Define Settings</strong></span>';
    $html_stepper.= '</button>';
    $html_stepper.= '</div>';
    $html_stepper.= '</div>';
    $html_stepper.= '<input type="hidden" id="jump_to" value="'.$course_templates_config->jump_to.'">';
    echo $html_stepper;
    if (!$sel_cate) {
        echo $OUTPUT->notification(get_string('choosecategory', 'local_course_templates'));
        echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Back', 'onclick' => 'javascript :history.back(-1)', 'class' => 'btn btn-primary', 'style' => 'margin-right:20px;')).html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'onclick' => 'window.location.href="'.$redirecturl.'?step=2&cid='.$cid.'"', 'class' => 'btn btn-primary')));
    } else {
        $categoryid = $sel_cate;
        echo html_writer::tag('p', html_writer::tag('strong', get_string('inputinfo', 'local_course_templates')));
        echo get_template_setting_form($cid, $categoryid);
    }
} else if ($step == 4) {
    $status = $course_status;
    $courseid = $course_id;
    if ($status == 1) {
        $redirecturl = $CFG->wwwroot.'/course/view.php?id='.$courseid;
        
        echo html_writer::tag('p', get_string('createsuccess', 'local_course_templates'));
        echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Back', 'onclick' => 'javascript :history.back(-1)', 'class' => 'btn btn-primary', 'style' => 'margin-right:20px;')).html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'onclick' => 'window.location.href="'.$redirecturl.'"', 'class' => 'btn btn-primary')));
    } else if ($status == 2) {
        $cateid = $cate_id;
        echo $OUTPUT->notification(get_string('inputinfotip', 'local_course_templates'));
        echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Back', 'onclick' => 'javascript :history.back(-1)', 'class' => 'btn btn-primary', 'style' => 'margin-right:20px;')).html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'onclick' => 'window.location.href="'.$redirecturl.'?step=3&cid='.$courseid.'&sel_cate='.$cateid.'"', 'class' => 'btn btn-primary')));
    } else {
        echo $OUTPUT->notification(get_string('createfailed', 'local_course_templates'));
        echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => 'Back', 'onclick' => 'javascript :history.back(-1)', 'class' => 'btn btn-primary', 'style' => 'margin-right:20px;')).html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'onclick' => 'window.location.href="'.$redirecturl.'"', 'class' => 'btn btn-primary')));
    }
}

echo $OUTPUT->footer();
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
 * Index file.
 *
 * @package   local_course_templates
 * @copyright 2017 onwards, emeneo (www.emeneo.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

use local_course_templates\coursecategorieslistform;

require_login();

global $CFG, $DB, $USER, $PAGE, $OUTPUT;

$step = optional_param('step', 0, PARAM_INT);
$cid = optional_param('cid', 0, PARAM_INT);
$coursestatus = optional_param('status', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$cateid = optional_param('cateid', $CFG->defaultrequestcategory, PARAM_INT);

$params = array();
$userid = $USER->id;  // Owner of the page.

if (isset($cateid) && !empty($cateid)) {
    $context = context_coursecat::instance($cateid);
} else {
    $context = context_user::instance($userid);
}

$capabilities = array(
    'moodle/backup:backupcourse',
    'moodle/backup:userinfo',
    'moodle/restore:restorecourse',
    'moodle/restore:userinfo',
    'moodle/course:create',
    'moodle/site:approvecourse',
);

require_capability('local/course_templates:view', $context);
require_all_capabilities($capabilities, $context);

$header = fullname($USER);
$pagetitle = get_string('pluginname', 'local_course_templates');

$PAGE->set_context($context);
$PAGE->set_url('/local/course_templates/index.php', $params);
$PAGE->requires->css(new moodle_url('/local/course_templates/styles/bs-stepper.min.css'));
$PAGE->requires->css(new moodle_url('/local/course_templates/styles.css'));
$PAGE->set_pagelayout('standard');

switch ($step) {
    case 2:
        $banner = get_string('selectcategorybanner', 'local_course_templates');
        break;

    case 3:
        $banner = get_string('definesettingsbanner', 'local_course_templates');
        break;

    default:
        $banner = get_string('choosetemplatebanner', 'local_course_templates');
        break;
}

$pageheading = get_string('createcoursefromtemplate', 'local_course_templates');
$pagetitle = $banner.' - '.$pageheading;

$PAGE->set_title($pagetitle);
$PAGE->set_heading($header);

$redirecturl = $CFG->wwwroot . '/local/course_templates/index.php';

$courseidarray = array();
$categoryidarray = array();

if ($step === 3 && isset($cateid)) {
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

    if ($fromform = $mform->get_data()) {
        $selcate = $fromform->sel_cate;
        $cateid = $selcate;
    }

    $redirecturl = $CFG->wwwroot . '/local/course_templates/index.php?step=3&cateid=' . $cateid . 'cid=' . $cid;
}

$courseidarray['cid'] = isset($cid) ? $cid : '';
$courseidarray['courseid'] = isset($courseid) ? $courseid : '';

foreach ($courseidarray as $courseidentifier) {
    if (isset($courseidentifier) && !empty($courseidentifier)) {
        if (!$DB->get_record('course', array('id' => $courseidentifier), 'id')) {
            redirect(new moodle_url('/local/course_templates/index.php'), array());
        }
    }
}

$categoryidarray['selcate'] = isset($selcate) ? $selcate : '';
$categoryidarray['cateid'] = isset($cateid) ? $cateid : 1;

foreach ($categoryidarray as $categoryidentifier) {
    if (isset($categoryidentifier) && !empty($categoryidentifier)) {
        if (!$DB->get_record('course_categories', array('id' => $categoryidentifier), 'id')) {
            redirect(new moodle_url('/local/course_templates/index.php'), array());
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($pageheading);

$themecfg = get_config('theme_boost');

if (!$step) {
    $htmlstepper = '';
    $htmlstepper .= '<div class="bs-stepper">';
    $htmlstepper .= '<div class="bs-stepper-header" role="tablist">';
    $htmlstepper .= '<div class="step active" data-target="#logins-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="logins-part" id="logins-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle" style="background-color:'
        . $themecfg->brandcolor
        . ';">1</span>';
    $htmlstepper .= '<span class="bs-stepper-label"><strong style="color:'
        . $themecfg->brandcolor
        . ';">'
        . get_string('choosetemplatebanner', 'local_course_templates')
        . '</strong></span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<div class="line"></div>';
    $htmlstepper .= '<div class="step" data-target="#information-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle">2</span>';
    $htmlstepper .= '<span class="bs-stepper-label">'
        . get_string('selectcategorybanner', 'local_course_templates')
        . '</span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<div class="line"></div>';
    $htmlstepper .= '<div class="step" data-target="#information-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle">3</span>';
    $htmlstepper .= '<span class="bs-stepper-label">'
        . get_string('definesettingsbanner', 'local_course_templates')
        . '</span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '</div>';
    echo $htmlstepper;

    echo html_writer::tag('p', html_writer::tag('strong', get_string('choosetemplate', 'local_course_templates')));
    echo get_template_list_form($cateid);
} else if ($step == 2) {
    $htmlstepper = '';
    $htmlstepper .= '<div class="bs-stepper">';
    $htmlstepper .= '<div class="bs-stepper-header" role="tablist">';
    $htmlstepper .= '<div class="step" data-target="#logins-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="logins-part" id="logins-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle">1</span>';
    $htmlstepper .= '<span class="bs-stepper-label">'
        . get_string('choosetemplatebanner', 'local_course_templates')
        . '</span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<div class="line"></div>';
    $htmlstepper .= '<div class="step active" data-target="#information-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle" style="background-color:'
        . $themecfg->brandcolor
        . ';">2</span>';
    $htmlstepper .= '<span class="bs-stepper-label"><strong style="color:'
        . $themecfg->brandcolor
        . ';">'
        . get_string('selectcategorybanner', 'local_course_templates')
        . '</strong></span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<div class="line"></div>';
    $htmlstepper .= '<div class="step" data-target="#information-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle">3</span>';
    $htmlstepper .= '<span class="bs-stepper-label">'
        . get_string('definesettingsbanner', 'local_course_templates')
        . '</span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '</div>';
    echo $htmlstepper;

    if (!$cid) {
        echo $OUTPUT->notification(get_string('choosetemplate', 'local_course_templates'));
        echo html_writer::tag(
            'p',
            html_writer::empty_tag(
                'input',
                array(
                    'type' => 'button',
                    'value' => get_string('back', 'local_course_templates'),
                    'onclick' => 'javascript :history.back(-1)',
                    'class' => 'btn btn-primary',
                    'style' => 'margin-right:20px;'
                )
            ) . html_writer::empty_tag(
                'input',
                array(
                    'type' => 'button',
                    'value' => get_string('continue', 'local_course_templates'),
                    'onclick' => 'window.location.href="' . $redirecturl . '"',
                    'class' => 'btn btn-primary'
                )
            )
        );
    } else {
        echo html_writer::tag(
            'p',
            html_writer::tag(
                'strong',
                get_string('choosecategory', 'local_course_templates')
            )
        );

        echo get_template_categories_form($cid, $cateid);
    }
} else if ($step == 3) {
    $coursetemplatesconfig = get_config('local_course_templates');
    $htmlstepper = '';
    $htmlstepper .= '<div class="bs-stepper">';
    $htmlstepper .= '<div class="bs-stepper-header" role="tablist">';
    $htmlstepper .= '<div class="step" data-target="#logins-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="logins-part" id="logins-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle">1</span>';
    $htmlstepper .= '<span class="bs-stepper-label">'
        . get_string('choosetemplatebanner', 'local_course_templates')
        . '</span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<div class="line"></div>';
    $htmlstepper .= '<div class="step" data-target="#information-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle">2</span>';
    $htmlstepper .= '<span class="bs-stepper-label">'
        . get_string('selectcategorybanner', 'local_course_templates')
        . '</span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<div class="line"></div>';
    $htmlstepper .= '<div class="step active" data-target="#information-part">';
    $htmlstepper .= '<button type="button" class="step-trigger" role="tab" '
        . 'aria-controls="information-part" id="information-part-trigger" disabled="disabled">';
    $htmlstepper .= '<span class="bs-stepper-circle" style="background-color:'
        . $themecfg->brandcolor
        . ';">3</span>';
    $htmlstepper .= '<span class="bs-stepper-label"><strong style="color:'
        . $themecfg->brandcolor
        . ';">'
        . get_string('definesettingsbanner', 'local_course_templates')
        . '</strong></span>';
    $htmlstepper .= '</button>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '</div>';
    $htmlstepper .= '<input type="hidden" id="jump_to" value="'
        . $coursetemplatesconfig->jump_to
        . '">';
    echo $htmlstepper;

    if (!$selcate) {
        echo $OUTPUT->notification(get_string('choosecategory', 'local_course_templates'));
        echo html_writer::tag(
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
                array('type' => 'button',
                    'value' => get_string('continue', 'local_course_templates'),
                    'onclick' => 'window.location.href="' . $redirecturl . '?step=2&cid=' . $cid . '"',
                    'class' => 'btn btn-primary'
                )
            )
        );
    } else {
        $categoryid = $selcate;
        echo html_writer::tag('p', html_writer::tag('strong', get_string('inputinfo', 'local_course_templates')));
        echo get_template_setting_form($cid, $categoryid, $cateid);
    }
} else if ($step == 4) {
    $status = $coursestatus;

    if ($status == 1) {
        $redirecturl = $CFG->wwwroot.'/course/view.php?id=' . $courseid;

        echo html_writer::tag('p', get_string('createsuccess', 'local_course_templates'));
        echo html_writer::tag(
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
                    'onclick' => 'window.location.href="' . $redirecturl . '"',
                    'class' => 'btn btn-primary'
                )
            )
        );
    } else if ($status == 2) {
        echo $OUTPUT->notification(get_string('inputinfotip', 'local_course_templates'));
        echo html_writer::tag(
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
                array('type' => 'button',
                    'value' => get_string('continue', 'local_course_templates'),
                    'onclick' => 'window.location.href="'
                        . $redirecturl
                        . '?step=3&cid='
                        . $courseid
                        . '&sel_cate='
                        . $cateid
                        . '"',
                    'class' => 'btn btn-primary'
                )
            )
        );
    } else {
        echo $OUTPUT->notification(get_string('createfailed', 'local_course_templates'));
        echo html_writer::tag(
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
                array('type' => 'button',
                    'value' => get_string('continue', 'local_course_templates'),
                    'onclick' => 'window.location.href="' . $redirecturl . '"',
                    'class' => 'btn btn-primary'
                )
            )
        );
    }
}

echo $OUTPUT->footer();

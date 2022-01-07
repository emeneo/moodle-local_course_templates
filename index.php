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

$selcate = optional_param('sel_cate', 0, PARAM_INT);
$coursestatus = optional_param('status', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$cateid = optional_param('cateid', 0, PARAM_INT);

require_capability('local/course_templates:view', $context);

$redirecturl = $CFG->wwwroot.'/local/course_templates/index.php';

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">';

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
    echo get_template_list_form();
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

        echo get_template_categories_form($cid);
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
                    'class' => 'btn btn-primary',
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
        echo get_template_setting_form($cid, $categoryid);
    }
} else if ($step == 4) {
    $status = $coursestatus;
    $courseid = $courseid;
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
    } else if ($status == 2) {
        $cateid = $cateid;
        echo $OUTPUT->notification(get_string('inputinfotip', 'local_course_templates'));
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
                    'class' => 'btn btn-primary',
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

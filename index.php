<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

require_login();

$step = optional_param('step', 0, PARAM_INT);
$cid = optional_param('cid', 0, PARAM_INT);
$cateid = optional_param('cateid', 0, PARAM_INT);

$params = array();
$userid = $USER->id;  // Owner of the page
$context = context_user::instance($USER->id);
$header = fullname($USER);
$pagetitle = get_string('pluginname', 'local_course_template');

$PAGE->set_context($context);
$PAGE->set_url('/local/course_template/index.php', $params);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($header);

require_capability('local/course_template:edit', $context);
/*
if(!$auth){
	print_error('error:incorrectcoursemoduleid', 'facetoface');
}
*/
$redirect_url = $CFG->wwwroot.'/local/course_template/index.php';

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
if(!$step){
	echo html_writer::tag('p',html_writer::tag('strong',get_string('choosetemplate', 'local_course_template')));
	echo get_template_list_form();
}else if($step == 2){
	if(!$cid){
        echo $OUTPUT->notification(get_string('choosetemplate', 'local_course_template'));
        echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_template'), 'onclick'=>'window.location.href="'.$redirect_url.'"')));
    }else{
    	echo html_writer::tag('p',html_writer::tag('strong',get_string('choosecategory', 'local_course_template')));
    	echo get_template_categories_form($cid);
    }
}else if($step == 3){
	if(!$_REQUEST['sel_cate']){
		echo $OUTPUT->notification(get_string('choosecategory', 'local_course_template'));
		echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_template'), 'onclick'=>'window.location.href="'.$redirect_url.'?step=2&cid='.$cid.'"')));
	}else{
		$categoryid = $_REQUEST['sel_cate'];
		echo html_writer::tag('p',html_writer::tag('strong',get_string('inputinfo', 'local_course_template')));
		echo get_template_setting_form($cid,$categoryid);
	}
}else if($step == 4){
	$status = $_REQUEST['status'];
	$courseid = $_REQUEST['courseid'];
	if($status == 1){
		$redirect_url = $CFG->wwwroot.'/course/view.php?id='.$courseid;
		echo html_writer::tag('p',get_string('createsuccess', 'local_course_template'));
		echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_template'), 'onclick'=>'window.location.href="'.$redirect_url.'"')));
	}else if($status == 2){
		$cateid = $_REQUEST['cateid'];
		echo $OUTPUT->notification(get_string('inputinfotip', 'local_course_template'));
		echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_template'), 'onclick'=>'window.location.href="'.$redirect_url.'?step=3&cid='.$courseid.'&sel_cate='.$cateid.'"')));
	}else{
		echo $OUTPUT->notification(get_string('createfailed', 'local_course_template'));
		echo html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_template'), 'onclick'=>'window.location.href="'.$redirect_url.'"')));
	}
}

echo $OUTPUT->footer();
<?php
defined('MOODLE_INTERNAL') || die;

function get_template_list(){
	global $CFG, $USER, $DB;

	$sql = "select id,fullname from {course} where category = (select id from {course_categories} where name='Course templates')";
	return $DB->get_records_sql($sql);
}

function get_template_list_form(){
	global $CFG, $USER, $DB;

    $redirect_url = $CFG->wwwroot.'/local/course_templates/index.php?step=2';
	$rows = get_template_list();

	$table = new html_table();
	$table->align = array('left');

	foreach ($rows as $row) {
		$data = array();

		$data[] = $row->fullname;
		$data[] = html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('useastemplate', 'local_course_templates'),'onclick'=>'window.location.href="'.$redirect_url.'&cid='.$row->id.'"'));

		$table->data[] = $data;
	}

	return html_writer::table($table);
}

function get_template_categories($visible=1){
	global $CFG, $USER, $DB;

	$sql = "select id,name,description from {course_categories} where visible=".$visible;
	return $DB->get_records_sql($sql);
}

function get_template_categories_form($cid){
    global $CFG, $USER, $DB;

    $redirect_url = $CFG->wwwroot.'/local/course_templates/index.php?step=3&cid='.$cid;
    $rows = get_template_categories(1);

    $output = '';
    $action = $redirect_url;
    $output.= html_writer::start_tag('form', array('action' => $action, 'method' => 'post'));
    $table = new html_table();
    $table->align = array('left');

    foreach ($rows as $row) {
        $data = array();

        $data[] = html_writer::empty_tag('input', array('type' => 'radio', 'value' => $row->id,'name'=> 'sel_cate'));
        $data[] = $row->name;
        $data[] = strip_tags($row->description);

        $table->data[] = $data;
    }

    $rows = get_template_categories(0);
    $hidden_table = new html_table();

    $hidden_table->align = array('left');

    foreach ($rows as $row) {
        $data = array();

        $data[] = html_writer::empty_tag('input', array('type' => 'radio', 'value' => $row->id,'name'=> 'sel_cate'));
        $data[] = $row->name;
        $data[] = strip_tags($row->description);

        $hidden_table->data[] = $data;
    }

    $output.= html_writer::table($table);
    $output.= html_writer::tag('p',html_writer::tag('strong',get_string('hiddencategories', 'local_course_templates')));
    $output.= html_writer::table($hidden_table);
    $output.= html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('continue', 'local_course_templates'))));
    $output.= html_writer::end_tag('form');
    return $output;
}

function get_template_setting_form($cid,$categoryid){
    global $CFG, $USER, $DB;

    $redirect_url = $CFG->wwwroot.'/local/course_templates/process.php?cid='.$cid.'&cateid='.$categoryid;
    $return_url   = $CFG->wwwroot.'/local/course_templates/index.php?step=4';
    $output = '';
    $output.= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/jquery.js"></script>';
    $output.= '<script src="'.$CFG->wwwroot.'/local/course_templates/js/process.js"></script>';
    $output.= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'process_request_url', 'value' => $redirect_url));
    $output.= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'process_return_url', 'value' => $return_url));
    $output.= html_writer::start_tag('input', array('type' => 'hidden', 'id' => 'success_return_url', 'value' => $CFG->wwwroot));
    $table = new html_table();
    $table->align = array('left');

    $table->data[] = array(get_string('coursename', 'local_course_templates'),
                           html_writer::empty_tag('input', array('type' => 'text', 'id'=> 'course_name')));
    $table->data[] = array(get_string('courseshortname', 'local_course_templates'),
                           html_writer::empty_tag('input', array('type' => 'text', 'id'=> 'course_short_name')));
    $output.= html_writer::table($table);
    $output.= html_writer::tag('p', html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('continue', 'local_course_templates'), 'id'=> 'btnProcess')));
    return $output;
}

function local_course_templates_extend_navigation(global_navigation $navigation) {
    global $PAGE, $COURSE;
    //echo "<pre>";print_r($navigation);exit;
    $branch = $navigation->find('admin', navigation_node::TYPE_SITE_ADMIN);
    //$branch = $PAGE->navigation;
    $node = navigation_node::create(get_string('addcourse', 'local_course_templates'),new moodle_url('/local/course_templates/index.php'));
    //$branch->add_node($node);
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
<?php
error_reporting(E_ALL);//打开所有错误报告
ini_set('display_errors', 'On');

defined('MOODLE_INTERNAL') || die;

global $PAGE,$CFG;

if ($hassiteconfig) {
    $ADMIN->add('courses', new admin_externalpage('local_course_template',
           		get_string('addcourse', 'local_course_template'),
            	new moodle_url('/local/course_template/index.php')));
}
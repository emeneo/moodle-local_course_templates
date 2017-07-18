<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once('../../course/externallib.php');

$fullname = $_REQUEST['course_short_name'];
$shortname = $_REQUEST['course_short_name'];
$categoryid = $_REQUEST['cateid'];
$courseid = $_REQUEST['cid'];
$options = array(array('name'=>'blocks', 'value'=>1), 
				 array('name'=>'activities', 'value'=>1), 
				 array('name'=>'filters', 'value'=>1),
				 array('name'=>'users', 'value'=>1));
$visible = 1;

if(!$fullname || !$shortname || !$categoryid || !$courseid){
	exit(json_encode(array('status'=>2,'id'=>$courseid,'cateid'=>$categoryid)));
}

$externalObj = new core_course_external();
$res = $externalObj->duplicate_course($courseid, $fullname, $shortname, $categoryid, $visible, $options);

if(@isset($res['id'])){
	exit(json_encode(array('status'=>1,'id'=>$res['id'],'shortname'=>$res['shortname'])));
}else{
	exit(json_encode(array('status'=>0)));
}
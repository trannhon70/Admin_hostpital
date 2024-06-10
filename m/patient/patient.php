<?php
/*
// - 功能说明 : 病人列表
// - 创建作者 : 爱医战队 
// - 创建时间 : 2013-05-01 05:09
*/
require "../../core/core.php";
$mod = "patient";
$table = "patient_".$user_hospital_id;

if ($user_hospital_id == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

// 颜色定义 2010-07-31
$line_color = array('black', 'red', 'silver', '#8AC6C6', '#8000FF');
$line_color_tip = array("等待", "已到", "未到", "过期", "回访");
$area_id_name = array(0 => "未知", 1 => "本市", 2 => "外地");

// 操作的处理:
if ($op = $_GET["op"]) {
	include "patient.op.php";
}

include "patient.list.php";

?>
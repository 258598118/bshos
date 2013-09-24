<?php
/*
// - 功能说明 : 病人列表
// - 创建作者 : fangyang (278294861)
// - 创建时间 : 2013-03-19 08:09
*/
require "../../core/core.php";
$mod = "patient";
$table = "patient_".$user_hospital_id;

if ($user_hospital_id == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

// 颜色定义 2010-07-31
//$line_color = array('label label-info', 'label label-important', 'label', 'label label-success', 'label  label-warning');
$line_color = array('color:#3a87ad', 'color:#b94a48', 'color:#51a351', 'color:#468847', 'color:#f89406');
$line_color_tip = array("等待", "已到", "未到", "过期", "回访");
$area_id_name = array(0 => "未知", 1 => "本市", 2 => "外地");
$re_arrive_arr = array('','复','查','再');
$re_arrive_full = array('初诊','复诊','复查','再消费');

 // 操作的处理:
if ($op = $_REQUEST["op"])
{
    include "patient.op.php";
}


if ($_POST["action"] == "notpl")
{
    exit();
}else {
    include "patient.list.php";
}



?>
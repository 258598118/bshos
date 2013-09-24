<?php
/*
// - 功能说明 : 消费财务相关
// - 创建作者 : fangyang (278294861)
// - 创建时间 : 2013-05-19 08:09
*/
require_once "../../core/core.php";
$mod = "fee";
$table = "patient_fee";
$patient_table = "patient_".$user_hospital_id;

if ($user_hospital_id == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

$line_color = array('color:#3a87ad', 'color:#b94a48', 'color:#51a351', 'color:#468847', 'color:#f89406');
$line_color_tip = array("等待", "已到", "未到", "过期", "回访");
$area_id_name = array(0 => "未知", 1 => "本市", 2 => "外地");
$re_arrive_arr = array('','复','查','再');
$re_arrive_full = array('初诊','复诊','复查','再消费');
$cj_status = array(
		'<span style="color:#468847"><i class="icon-flag"></i></span>',
		'<span style="color:#b94a48"><i class="icon-flag"></i></span>',
		'<span style="color:#3a87ad"><i class="icon-flag"></i></span>'
		);

 // 操作的处理:
if ($op = $_REQUEST["op"])
{
    include $mod.".op.php";
}


include $mod.".list.php";




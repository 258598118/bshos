<?php
/*
// ˵��: ����
// ����: ���� (weelia@126.com)
// ʱ��: 2011-11-24
*/
require "../../core/core.php";

// �������Ķ���:
include "rp.core.php";

$tongji_tips = " - ҽ��ͳ�� - ".$type_tips;
?>
<html>
<head>
<title>ҽ������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
body {margin-top:6px; }
#rp_condition_form {text-align:center; }
.head, .head a {font-family:"΢���ź�","Verdana"; }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }
.date_tips {padding:15px 0 15px 0px; font-weight:bold; text-align:center; font-size:15px; font-family:"΢���ź�","Verdana"; }
form {display:inline; }
.red {color:red !important;  }
</style>
</head>

<body>

<?php include_once "rp.condition_form.php"; ?>

<?php if ($_GET["op"] == "report") { ?>
<?php

// ��ȡҽ�������15��
$doctor_arr = $db->query("select doctor,count(doctor) as c from $table where $where doctor!='' and {$timetype}>=$max_tb and {$timetype}<=$max_te group by doctor order by c desc limit 15", "doctor", "c");
if (count($doctor_arr) == 0) {
	exit_html("<center>�Բ��𣬸�ҽԺδʹ��ҽ�����ܣ��޷�ͳ�ơ�</center>");
}

if (in_array($type, array(1,2,3,4))) {
	// ����ͳ������:
	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = $db->query("select count(*) as c from $table where $where {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");

		foreach ($doctor_arr as $me => $num) {
			$data[$k][$me] = $db->query("select count(*) as c from $table where $where doctor='{$me}' and {$timetype}>=".$v[0]." and {$timetype}<=".$v[1]." ", 1, "c");
		}
	}
} else if ($type == 5) {
	$arr = array();
	$arr["��"] = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");

	foreach ($doctor_arr as $me => $num) {
		$arr[$me] = $db->query("select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where doctor='{$me}' and $where {$timetype}>=".$tb." and {$timetype}<=".$te." group by from_unixtime({$timetype},'%k')", "sd", "c");
	}

	$data = array();
	foreach ($final_dt_arr as $k => $v) {
		$data[$k]["��"] = intval($arr["��"][$v]);
		foreach ($doctor_arr as $me => $num) {
			$data[$k][$me] = intval($arr[$me][$v]);
		}
	}
}


?>
<div class="date_tips"><?php echo $h_name.$tongji_tips.$tips; ?></div>
<table width="100%" align="center" class="table table-striped table-bordered table-condensed">
    <thead>
		<tr>
			<th class="head" align="center">ʱ��</th>
			<th class="head red" align="center">�ܼ�</th>
	        <?php foreach ($doctor_arr as $me => $num) { ?>
			<th class="head" align="center"><?php echo $me; ?></th>
	        <?php } ?>
		</tr>
	</thead>

<?php foreach ($final_dt_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
		<td class="item" align="center"><?php echo $data[$k]["��"]; ?></td>
<?php   foreach ($doctor_arr as $me => $num) { ?>
		<td class="item" align="center"><?php echo $data[$k][$me]; ?></td>
<?php   } ?>
	</tr>
<?php } ?>
</table>

<br>
<?php } ?>


</body>
</html>
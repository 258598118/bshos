<?php
/*
// 说明: op
// 作者: fangyang (278294861)
// 时间: 2013-05-21 19:24
*/
if ($op == "search") {
	include $mod.".search.php";
	exit;
}

if ($op == "add") {
	include $mod.".edit.php";
	exit;
}

if ($op == "edit") {
	include $mod.".edit.php";
	exit;
}

if ($op == "view") {
	include $mod.".view.php";
	exit;
}


if ($op == "delete") {
	$ids = explode(",", $_GET["id"]);
	$del_ok = $del_bad = 0; $op_data = array();
	$del_name = array();
	foreach ($ids as $opid) {
		if (($opid = intval($opid)) > 0) {
			$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
			if ($db->query("delete from $table where id='$opid' limit 1")) {
				$del_ok++;
				$op_data[] = $tmp_data;
				$del_name[] = $tmp_data["name"];
			} else {
				$del_bad++;
			}
		}
	}

	if ($del_ok > 0) {
		$log->add("delete", "删除病人消费记录: ".implode("、", $del_name), $op_data, $table);
	}

	if ($del_bad > 0) {
		msg_box("删除成功 $del_ok 条资料，删除失败 $del_bad 条资料。", history(2, $id));
	} else {
		msg_box("删除成功", history(2, $id));
	}
}


if ($op == "setshow") {
	$isshow_value = intval($_GET["value"]) > 0 ? 1 : 0;
	$ids = explode(",", $_GET["id"]);
	$set_ok = $set_bad = 0;
	foreach ($ids as $opid) {
		if (($opid = intval($opid)) > 0) {
			if ($db->query("update $table set isshow='$isshow_value' where id='$opid' limit 1")) {
				$set_ok++;
			} else {
				$set_bad++;
			}
		}
	}

	if ($set_bad > 0) {
		msg_box("操作成功完成 $set_ok 条，失败 $del_bad 条。", "back", 1);
	} else {
		msg_box("设置成功！", "back", 1);
	}
}
?>
<?php
defined("ROOT") or exit;

if ($_POST) {
	$r = array();
	$r["name"] = $_POST["name"];
	$r["intro"] = $_POST["intro"];
	$r["sort"] = $_POST["sort"];

	// 本数组已经加载（定义）过，其他值不会丢失
	$line["config"]["门诊收费项目"] = str_replace("\n", "|", str_replace("\r", "", $_POST["menzhen_fei"]));
	$line["config"]["住院收费项目"] = str_replace("\n", "|", str_replace("\r", "", $_POST["zhuyuan_fei"]));

	$line["config"][date("Ym")]["jiangli_jishu"] = intval($_POST["jiangli_jishu"]);
	$line["config"][date("Ym")]["jiangli_zhibiao"] = intval($_POST["jiangli_zhibiao"]);
	$line["config"][date("Ym")]["jiuzhen_mubiao"] = intval($_POST["jiuzhen_mubiao"]);

	$r["config"] = serialize($line["config"]);

	if ($op == "add") {
		$r["addtime"] = time();
		$r["author"] = $username;
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "edit") {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	if ($hid = $db->query($sql)) {
		$hid = ($op == "edit" ? $id : $hid);
		create_patient_table($hid);
		msg_box("资料提交成功", "?", 1);
	} else {
		msg_box("资料提交失败，系统繁忙，请稍后再试。", "back", 1, 5);
	}
}

?>
<html>
<head>
<title><?php echo $pinfo["title"]." - ".($op == "add" ? "新增" : "修改"); ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("请输入“医院名称”！");
		oForm.name.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><span class="tips"><?php echo $pinfo["title"]." - ".($op == "add" ? "新增" : "修改"); ?></span></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">提示：</div>
	<div class="d_item">1.输入医院名称，点击提交即可</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">医院资料</td>
	</tr>
	<tr>
		<td class="left">医院名称：</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">名称必须填写</span></td>
	</tr>
	<tr>
		<td class="left">医院简介：</td>
		<td class="right"><textarea class="input" name="intro" style="width:60%; height:80px; vertical-align:middle;"><?php echo $line["intro"]; ?></textarea> <span class="intro">医院简介，选填</span></td>
	</tr>
	<tr>
		<td class="left">优先度：</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" style="width:80px"> <span class="intro">优先度越大,排序越靠前</span></td>
	</tr>
</table>

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">医院设置</td>
	</tr>
	<tr>
		<td class="left">门诊收费项目：</td>
		<td class="right"><textarea class="input" name="menzhen_fei" style="width:200px; height:90px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $line["config"]["门诊收费项目"]); ?></textarea> <span class="intro">门诊收费项目，每行一个</span></td>
	</tr>
	<tr>
		<td class="left">住院收费项目：</td>
		<td class="right"><textarea class="input" name="zhuyuan_fei" style="width:200px; height:90px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $line["config"]["住院收费项目"]); ?></textarea> <span class="intro">住院收费项目，每行一个</span></td>
	</tr>
	<tr>
		<td class="left">本月奖励基数：</td>
		<td class="right"><input name="jiangli_jishu" value="<?php echo $line["config"][date("Ym")]["jiangli_jishu"]; ?>" class="input" style="width:80px"> <span class="intro">本月奖励基数(只能在本月修改)</span> &nbsp; <a href="?op=set_zhibiao&id=<?php echo $id; ?>"><b>【设置其他月份指标】</b></a></td>
	</tr>
	<tr>
		<td class="left">本月奖励指标：</td>
		<td class="right"><input name="jiangli_zhibiao" value="<?php echo $line["config"][date("Ym")]["jiangli_zhibiao"]; ?>" class="input" style="width:80px"> <span class="intro">本月奖励指标(只能在本月修改)</span></td>
	</tr>
	<tr>
		<td class="left">本月目标就诊：</td>
		<td class="right"><input name="jiuzhen_mubiao" value="<?php echo $line["config"][date("Ym")]["jiuzhen_mubiao"]; ?>" class="input" style="width:80px"> <span class="intro">本月目标就诊(只能在本月修改)</span></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="linkinfo" value="<?php echo $linkinfo; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>
<?php
/*
// - 功能说明 : 修改我的资料
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2007-07-19 09:59
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$uid) {
	exit_html("不能编辑资料...");
}

$uline = $db->query("select * from $table where id='$uid'", 1);

if ($_POST) {

	$detail = array();
	if ($uline["detail"]) {
		$detail = @unserialize($uline["detail"]);
	}

	$detail["电话"] = $_POST["电话"];
	$detail["手机"] = $_POST["手机"];
	$detail["QQ"] = $_POST["QQ"];
	$detail["电子邮箱"] = $_POST["电子邮箱"];
	$detail["个人简介"] = $_POST["个人简介"];

	$s = serialize($detail);

	$sql = "update $table set detail='$s' where id='$uid' limit 1";

	if ($db->query($sql)) {
		msg_box("个人资料修改成功", "back", 1, 2);
	} else {
		msg_box("资料提交失败，系统繁忙，请稍后再试", "back", 1, 5);
	}
}


if ($uline && $uline["detail"]) {
	$tm = @unserialize($uline["detail"]);
	$uline = array_merge($uline, $tm);
} else {
	//exit_html("无此资料...");
}

$title = "修改我的资料";
?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
</head>

<body>
    <header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
             <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
             <li class="active"><?php echo $title; ?></li>
        </ul>
	</header>


<div class="space"></div>

<div class="alert alert-info">
	<h4 class="d_title alert-heading">修改提示：</h4>
	<li class="d_item">由于需要保持后台安全性和一致性，帐户名称一旦确定就不允许修改</li>
	<li class="d_item">为了方便别人和您取得联系，建议认真如实填写您的个人资料和联系方式</li>
	<li class="d_item">个人资料除真实姓名以外，未被授权“后台管理”->“管理员管理”->“查看”权限的人将不能查看</li>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="table table-striped table-condensed">
	<thread><tr>
		<th colspan="2" class="head">修改我的资料：</th>
	</tr></thread>
	<tr>
		<td class="left">登录名：</td>
		<td class="right"><b><?php echo $uline["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left"><font color='#b94a48'>真实姓名：</font></td>
		<td class="right"><input name="realname" value="<?php echo $uline["realname"]; ?>" class="input-xlarge disabled" style="width:120px" disabled="true"> <span class="intro">真实姓名不能修改</span></td>
	</tr>
	<tr>
		<td class="left">电话：</td>
		<td class="right"><input name="电话" value="<?php echo $uline["电话"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">手机：</td>
		<td class="right"><input name="手机" value="<?php echo $uline["手机"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">QQ：</td>
		<td class="right"><input name="QQ" value="<?php echo $uline["QQ"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">电子邮箱：</td>
		<td class="right"><input name="电子邮箱" value="<?php echo $uline["电子邮箱"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">个人简介：</td>
		<td class="right"><textarea class="input-xlarge" name="个人简介" style="width:400px;height:80px"><?php echo $uline["个人简介"]; ?></textarea></td>
	</tr>
</table>

<div class="button_line"><button type="submit" class="btn btn-small btn-primary" value="提交资料">提交资料</button></div>
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
</form>

<div class="space"></div>
</body>
</html>
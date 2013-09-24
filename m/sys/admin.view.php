<?php defined("ROOT") or exit("Error."); ?><!DOCTYPE html><html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?><?php foreach ($easydialog as $x){echo $x;}?>
</head>

<body>
<table width="100%" class="edit">	<tr>		<td class="head" colspan="2">详细资料</td>	</tr>	<tr>
		<td class="left">登录名：</td>
		<td class="right"><b><?php echo $user["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left">真实姓名：</td>
		<td class="right"><?php echo $user["realname"]; ?></td>
	</tr>
	<tr>
		<td class="left">管理站点：</td>
		<td class="right"><?php echo $user["hospitals_str"]; ?></td>
	</tr>
	<tr>
		<td class="left">授权方式：</td>
		<td class="right"><?php
if ($user["powermode"] == 1) {
	echo "直接授权";
} else if ($user["powermode"] == 2) {
	echo "通过角色授权";
} else {
	echo "(未授权)";
}
?>
		</td>
	</tr>

	<tr>
		<td class="left">角色名称：</td>
		<td class="right"><?php
if ($user["powermode"] == 2) {
	$ch_data = $db->query_first("select * from sys_character where id='".$user["character_id"]."' limit 1");
	echo $ch_data["name"];
} else {
	echo "<font color='gray'>(未使用角色系统)</font>";
}
?>
		</td>
	</tr>
	<tr>
		<td class="head" colspan="2">个人资料</td>
	</tr>
	<tr>
		<td class="left">电话：</td>
		<td class="right"><?php echo $user["phone"]; ?></td>
	</tr>
	<tr>
		<td class="left">手机：</td>
		<td class="right"><?php echo $user["mobile"]; ?></td>
	</tr>
	<tr>
		<td class="left">QQ：</td>
		<td class="right"><?php echo $user["qq"]; ?></td>
	</tr>
	<tr>
		<td class="left">E-Mail：</td>
		<td class="right"><?php echo $user["email"]; ?></td>
	</tr>
	<tr>
		<td class="left">个人简介：</td>
		<td class="right"><?php echo $user["intro"]; ?></td>
	</tr>
</table>
<div class="button_line"><button onclick="history.back()" class="buttonb">返回</button></div>
<div class="space"></div>
</body>
</html>
<?php defined("ROOT") or exit("Error."); ?><!DOCTYPE html><html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?><?php foreach ($easydialog as $x){echo $x;}?>
</head>

<body>
<table width="100%" class="edit">	<tr>		<td class="head" colspan="2">��ϸ����</td>	</tr>	<tr>
		<td class="left">��¼����</td>
		<td class="right"><b><?php echo $user["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left">��ʵ������</td>
		<td class="right"><?php echo $user["realname"]; ?></td>
	</tr>
	<tr>
		<td class="left">����վ�㣺</td>
		<td class="right"><?php echo $user["hospitals_str"]; ?></td>
	</tr>
	<tr>
		<td class="left">��Ȩ��ʽ��</td>
		<td class="right"><?php
if ($user["powermode"] == 1) {
	echo "ֱ����Ȩ";
} else if ($user["powermode"] == 2) {
	echo "ͨ����ɫ��Ȩ";
} else {
	echo "(δ��Ȩ)";
}
?>
		</td>
	</tr>

	<tr>
		<td class="left">��ɫ���ƣ�</td>
		<td class="right"><?php
if ($user["powermode"] == 2) {
	$ch_data = $db->query_first("select * from sys_character where id='".$user["character_id"]."' limit 1");
	echo $ch_data["name"];
} else {
	echo "<font color='gray'>(δʹ�ý�ɫϵͳ)</font>";
}
?>
		</td>
	</tr>
	<tr>
		<td class="head" colspan="2">��������</td>
	</tr>
	<tr>
		<td class="left">�绰��</td>
		<td class="right"><?php echo $user["phone"]; ?></td>
	</tr>
	<tr>
		<td class="left">�ֻ���</td>
		<td class="right"><?php echo $user["mobile"]; ?></td>
	</tr>
	<tr>
		<td class="left">QQ��</td>
		<td class="right"><?php echo $user["qq"]; ?></td>
	</tr>
	<tr>
		<td class="left">E-Mail��</td>
		<td class="right"><?php echo $user["email"]; ?></td>
	</tr>
	<tr>
		<td class="left">���˼�飺</td>
		<td class="right"><?php echo $user["intro"]; ?></td>
	</tr>
</table>
<div class="button_line"><button onclick="history.back()" class="buttonb">����</button></div>
<div class="space"></div>
</body>
</html>
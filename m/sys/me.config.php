<?php
/*
// - ����˵�� : ѡ������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2007-07-19 09:46
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$uid) {
	exit_html("�����޸���������...");
}

if ($_POST) {
	$new_config = array();
	if ($uinfo["config"] != '') {
		$new_config = @unserialize($uinfo["config"]);
	}

	$new_config["jsmenu"] = intval($_POST["jsmenu"]);
	$new_config["shortcut"] = intval($_POST["shortcut"]);
	$new_config["submenu_pos"] = intval($_POST["submenu_pos"]);

	$new_str = serialize($new_config);

	if ($uid > 0) {
		$sql = "update $table set config='$new_str' where id=$uid limit 1";

		if ($db->query($sql)) {
			//msg_box("ѡ���޸ĳɹ�", "", 0);
			update_main_frame();
			exit;
		} else {
			msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
		}
	}
}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
</head>

<body>    <header class="jumbotron subhead"  style="margin-bottom: 20px;">		<ul class="breadcrumb">             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>             <li class="active"><?php echo $pinfo["title"]; ?></li>        </ul>	</header>
<div class="space"></div>

<div class="alert alert-info">
	<h4 class="d_title alert-heading">��ʾ��</h4>
	<li class="d_item">���������ĸ���ϲ�����ý��棻��ҳѡ���ύ�󽫻�����������ʾ</li>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="table table-striped table-condensed">
	<tr>
		<td colspan="2" class="head">����ѡ��</td>
	</tr>
	<tr>
		<td class="left" style="width:25%">��̬�����˵�: </td>
		<td class="right">
			<input type="checkbox" name="jsmenu" value="1" <?php echo $config["jsmenu"] ? "checked" : ""; ?> id="showjsmenu" class="check" style="float:left"><label for="showjsmenu">��ʾ��̬�����˵�</label>
		</td>
	</tr>
	<tr>
		<td class="left">��ݷ�ʽ: </td>
		<td class="right">
			<input type="checkbox" name="shortcut" value="1" <?php echo $config["shortcut"] ? "checked" : ""; ?> id="showshortcut" class="check" style="float:left"><label for="showshortcut">��ʾ����ݷ�ʽ����</label>
		</td>
	</tr>
	<tr>
		<td class="left">������ʾλ�ã�</td>
		<td class="right">
			<select name="submenu_pos" class="controls">
				<option value="1" <?php if (intval($config["submenu_pos"]) === 1) echo "selected"; ?>>���<?php if (intval($config["submenu_pos"]) === 1) echo " *"; ?></option>
				<option value="0" <?php if ($config["submenu_pos"] == 0) echo "selected"; ?>>����ʾ<?php if ($config["submenu_pos"] == 0) echo " *"; ?></option>
			</select>
		</td>
	</tr>
</table>

<div class="button_line"><button type="submit" class="btn btn-small btn-primary" value="�ύ����">�ύ����</button></div>
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
</form>

<div class="space"></div>
</body>
</html>
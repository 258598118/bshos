<?php
/*
// - ����˵�� : �޸ĵ�ǰ��¼�û�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2007-01-06 20:52
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$uid) {
	exit_html("�����޸�����...");
}

if ($_POST) {
	$OldPass = $_POST["oldpass"];
	$NewPass = $_POST["newpass"];
	$NewPass1 = $_POST["newpass1"];

	if ($NewPass != $NewPass1) {
		msg_box("�����������벻һ�£����������룡", "back", 1);
	}
	if (strlen($NewPass) < 3) {
		msg_box("�����볤������Ҫ�趨��λ�����ϣ��������趨��", "back", 1);
	}

	$EnPass = md5($NewPass);

	if ($old = $db->query_first("select * from $table where name='$username' limit 1")) {
		if (md5($OldPass) == $old["pass"]) {
			if ($db->query("update $table set pass='$EnPass' where name='$username' limit 1")) {
				msg_box("�����޸ĳɹ����´ε�¼��ʹ�������룡", "back", 1);
			} else {
				msg_box("�����޸�ʧ�ܣ����Ժ�����", "back", 1, 5);
			}
		} else {
			msg_box("ԭ�������벻��ȷ������������ԭ���룡", "back", 1);
		}
	}
}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>�޸�����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<script language="javascript">
function check_data(f) {
	if (f.oldpass.value == "") {
		msg_box("���������ĵ�ǰ���룡",2); f.oldpass.focus(); return false;
	}
	if (f.newpass.value == "") {
		msg_box("���������������룡",2); f.newpass.focus(); return false;
	}
	if (f.newpass.value.length < 3) {
		msg_box("�����볤������Ҫ��3λ��",2); f.newpass.focus(); return false;
	}
	if (f.newpass1.value == "") {
		msg_box("���ٴ��������������룡",2); f.newpass1.focus(); return false;
	}
	if (f.newpass.value != f.newpass1.value) {
		msg_box("�����������벻һ�£�",2); f.newpass1.focus(); return false;
	}
	return true;
}
</script>
</head>

<body>     <header class="jumbotron subhead"  style="margin-bottom: 20px;">		<ul class="breadcrumb">             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>             <li class="active">�޸�����</li>        </ul>	</header>

<div class="space"></div>
<div class="alert alert-info">
	<h4 class="d_title alert-heading">�޸���ʾ��</h4>
	<li class="d_item">����������ȷ��ԭ���룬����������6λ��������</li>
	<li class="d_item">�ɹ��޸ĺ�������뼴����Ч�����κ���Ҫʹ�����ĸ�������ĵط���Ӧʹ�ô�������</li>
</div>

<div class="space"></div>
<form method='POST' onsubmit="return check_data(this);">
<table width="100%" class="table table-striped table-condensed">
	<tr>
		<td colspan="2" class="head">�޸ĵ�¼���룺</td>
	</tr>
	<tr>
		<td class="left">ԭ���룺</td>
		<td class="right"><input name='oldpass' type='password' style='width:120' > <span class="intro">���ĵ�ǰ����</span></td>
	</tr>
	<tr>
		<td class="left">�����룺</td>
		<td class="right"><input name='newpass' type='password' style='width:120' > <span class="intro">�µ����룬����6λ</span></td>
	</tr>
	<tr>
		<td class="left">ȷ�������룺</td>
		<td class="right"><input name='newpass1' type='password' style='width:120' > <span class="intro">������һ����ȷ��������</span></td>
	</tr>
</table>

<div class="button_line"><button type="submit" value="�޸�����" class="btn btn-small btn-primary">�޸�����</button></div>
</form>

<div class="space"></div>
</body>
</html>
<?php
/*
// - ����˵�� : �޸��ҵ�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2007-07-19 09:59
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$uid) {
	exit_html("���ܱ༭����...");
}

$uline = $db->query("select * from $table where id='$uid'", 1);

if ($_POST) {

	$detail = array();
	if ($uline["detail"]) {
		$detail = @unserialize($uline["detail"]);
	}

	$detail["�绰"] = $_POST["�绰"];
	$detail["�ֻ�"] = $_POST["�ֻ�"];
	$detail["QQ"] = $_POST["QQ"];
	$detail["��������"] = $_POST["��������"];
	$detail["���˼��"] = $_POST["���˼��"];

	$s = serialize($detail);

	$sql = "update $table set detail='$s' where id='$uid' limit 1";

	if ($db->query($sql)) {
		msg_box("���������޸ĳɹ�", "back", 1, 2);
	} else {
		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ�����", "back", 1, 5);
	}
}


if ($uline && $uline["detail"]) {
	$tm = @unserialize($uline["detail"]);
	$uline = array_merge($uline, $tm);
} else {
	//exit_html("�޴�����...");
}

$title = "�޸��ҵ�����";
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
             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
             <li class="active"><?php echo $title; ?></li>
        </ul>
	</header>


<div class="space"></div>

<div class="alert alert-info">
	<h4 class="d_title alert-heading">�޸���ʾ��</h4>
	<li class="d_item">������Ҫ���ֺ�̨��ȫ�Ժ�һ���ԣ��ʻ�����һ��ȷ���Ͳ������޸�</li>
	<li class="d_item">Ϊ�˷�����˺���ȡ����ϵ������������ʵ��д���ĸ������Ϻ���ϵ��ʽ</li>
	<li class="d_item">�������ϳ���ʵ�������⣬δ����Ȩ����̨����->������Ա����->���鿴��Ȩ�޵��˽����ܲ鿴</li>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="table table-striped table-condensed">
	<thread><tr>
		<th colspan="2" class="head">�޸��ҵ����ϣ�</th>
	</tr></thread>
	<tr>
		<td class="left">��¼����</td>
		<td class="right"><b><?php echo $uline["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left"><font color='#b94a48'>��ʵ������</font></td>
		<td class="right"><input name="realname" value="<?php echo $uline["realname"]; ?>" class="input-xlarge disabled" style="width:120px" disabled="true"> <span class="intro">��ʵ���������޸�</span></td>
	</tr>
	<tr>
		<td class="left">�绰��</td>
		<td class="right"><input name="�绰" value="<?php echo $uline["�绰"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">�ֻ���</td>
		<td class="right"><input name="�ֻ�" value="<?php echo $uline["�ֻ�"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">QQ��</td>
		<td class="right"><input name="QQ" value="<?php echo $uline["QQ"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">�������䣺</td>
		<td class="right"><input name="��������" value="<?php echo $uline["��������"]; ?>"  class="span3"></td>
	</tr>
	<tr>
		<td class="left">���˼�飺</td>
		<td class="right"><textarea class="input-xlarge" name="���˼��" style="width:400px;height:80px"><?php echo $uline["���˼��"]; ?></textarea></td>
	</tr>
</table>

<div class="button_line"><button type="submit" class="btn btn-small btn-primary" value="�ύ����">�ύ����</button></div>
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
</form>

<div class="space"></div>
</body>
</html>
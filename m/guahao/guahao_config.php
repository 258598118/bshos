<?php
/*
// - ����˵�� : �޸ĹҺ�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-03-31 11:23
*/
require "../../core/core.php";
$table = "guahao_config";

if ($_POST) {
	$record = array();
	$record["config"] = $_POST["config"];

	$sqldata = $db->sqljoin($record);
	$sql = "update $table set $sqldata where name='filter' limit 1";

	if ($db->query($sql)) {
		msg_box("�����ύ�ɹ�", "?self", 1);
	} else {
		msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
	}
}

$line = $db->query("select * from $table where name='filter' limit 1", 1);
$title = "�޸�����";
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	return true;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->

    <header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
             <li class="active"><?php echo $title; ?></li>
        </ul>
	</header>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">1. ��ע�⣬�����öԱ�ϵͳ�ڵ�����ҽԺ����Ч����ȫ�ֵġ�</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="head">����</td>
	</tr>
	<tr>
		<td class="left">���˴ʻ㣺</td>
		<td class="right"><textarea name="config" style="width:60%; height:80px;"><?php echo $line["config"]; ?></textarea> <span class="intro">�����Զ���(,)����</span></td>
	</tr>
</table>

<div class="button_line"><input type="submit" class="btn" value="�ύ����"></div>
</form>
</body>
</html>
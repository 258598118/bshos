<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><button class="btn btn-success"><i class="icon-home icon-white"></i>�û�����������¼</button></div>
	<div class="header_center" style="padding-top:2px;">
		<?php if ($debug_mode) { ?><a href="?op=clear" onclick="return confirm('ȷ��Ҫ������м�¼��')">���</a>&nbsp;<?php } ?>
		<?php if ($debug_mode || $username=="admin") { ?><a href="?op=del_week" onclick="return confirm('ȷ��Ҫɾ��һ��֮ǰ�ļ�¼��')">ɾ��һ��֮ǰ�ļ�¼</a>&nbsp;<?php } ?>
	</div>
	<div class="headers_oprate">
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="table table-striped table-condensed">
<?php
echo $table_header."\r\n";
if (count($table_items) > 0) {
	echo implode("\r\n", $table_items);
} else {
?>
	<tr>
		<td colspan="<?php echo count($list_heads); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php
}
?>
</table>
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left">
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- ��ҳ���� end -->

<div class="space"></div>
</body>
</html>
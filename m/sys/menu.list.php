<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
</head>

<body>
<!-- ͷ�� begin -->
<header class="jumbotron subhead"  style="margin-bottom: 10px;">
<!-- ͷ�� end -->
<div class="space"></div>
<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="table table-hover table-condensed">
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
	<div class="footer_op_left"><button onclick="select_all()" class="btn">ȫѡ</button>&nbsp;<button onclick="unselect()" class="btn">��ѡ</button>&nbsp;<?php echo $power->show_button("close,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

<div class="space"></div>

</body>
</html>
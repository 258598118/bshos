<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<style></style>
<script language="javascript"></script>
</head>

<body>    <header class="jumbotron subhead"  style="margin-bottom: 20px;">		<ul class="breadcrumb">             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>             <li class="active">Ȩ���б�</li>        </ul>	</header>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="header_center"><?php echo $power->show_button("add"); ?></div>
	<div class="headers_oprate">	    <form name="topform" method="GET">	        ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" size="8">&nbsp;	   <button type="submit" class="btn btn-small" value="����" style="font-weight:bold" title="�������">����</button>&nbsp;	   <button value="����" onclick="location='?'" class="btn btn-small" title="�˳�������ѯ">����</button>&nbsp;&nbsp;	   </form> </div>
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
	<div class="footer_op_left">	    <button onclick="select_all()" class="btn">ȫѡ</button>&nbsp;	    <button onclick="unselect()" class="btn">��ѡ</button>&nbsp;
	<?php
		if ($username == "admin" || $debug_mode) {
			echo $power->show_button("close,delete");
		}
	?>
	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

<div class="space"></div>
</body>
</html>
<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
</head>

<body>    <header class="jumbotron subhead"  style="margin-bottom: 20px;">		<ul class="breadcrumb">             <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>             <li class="active"><?php echo $pinfo["title"]; ?></li>        </ul>	</header>	
<!-- 头部 begin -->
<div class="headers">
	<div class="header_center"><?php echo $power->show_button("add"); ?></div>
	<div class="headers_oprate">	    <form name="topform" method="GET">模糊搜索：	    <input name="key" value="<?php echo $_GET["key"]; ?>"  size="8">&nbsp;	    <button type="submit" class="btn btn-small" value="搜索" style="font-weight:bold" title="点击搜索">搜索</button>&nbsp;	    <button value="重置" onclick="location='?'" class="btn btn-small" title="退出条件查询">重置</button>&nbsp;&nbsp;	     </form></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<table width="100%" align="center" class="table table-striped table-condensed">
<?php
echo $table_header."\r\n";
if (count($table_items) > 0) {
	echo implode("\r\n", $table_items);
} else {
?>
	<tr>
		<td colspan="<?php echo count($list_heads); ?>" align="center" class="nodata">(没有数据...)</td>
	</tr>
<?php
}
?>
</table>
</form>
<!-- 数据列表 end -->

<div class="space"></div>

<!-- 分页链接 begin -->
<div class="footer_op">
	<div class="footer_op_left">	    <button onclick="select_all()" class="btn btn-small">全选</button>&nbsp;	    <button onclick="unselect()" class="btn btn-small">反选</button>&nbsp;	    <?php echo $power->show_button("close,delete"); ?>	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

<div class="space"></div>

</body>
</html>
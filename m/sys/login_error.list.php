<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><button class="btn btn-success"><i class="icon-home icon-white"></i>用户名密码错误记录</button></div>
	<div class="header_center" style="padding-top:2px;">
		<?php if ($debug_mode) { ?><a href="?op=clear" onclick="return confirm('确定要清除所有记录吗？')">清空</a>&nbsp;<?php } ?>
		<?php if ($debug_mode || $username=="admin") { ?><a href="?op=del_week" onclick="return confirm('确定要删除一周之前的记录吗？')">删除一周之前的记录</a>&nbsp;<?php } ?>
	</div>
	<div class="headers_oprate">	    <form name="topform" method="GET">	    <?php echo $power->show_button("add"); ?>&nbsp;&nbsp;&nbsp;&nbsp;模糊搜索：<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="span2">&nbsp;	    <button type="submit" class="btn btn-small" value="搜索" style="font-weight:bold" title="点击搜索">搜索</button>&nbsp;	    <button value="重置" onclick="location='?'" class="btn btn-small" title="退出条件查询">重置</button>&nbsp;&nbsp;	    <button value="返回" onclick="history.back()" class="btn btn-small btn-info" title="返回上一页">返回</button>	    </form>	 </div>
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
	<div class="footer_op_left">	    <button onclick="select_all()" class="btn">全选</button>&nbsp;	    <button onclick="unselect()" class="btn">反选</button>&nbsp;	    <?php echo $power->show_button("close,delete"); ?></div>
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- 分页链接 end -->

<div class="space"></div>
</body>
</html>
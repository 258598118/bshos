<?php defined("ROOT") or exit("Error."); ?><!DOCTYPE html>
<html>
<head><title><?php echo $pinfo["title"]; ?></title><meta http-equiv="Content-Type" content="text/html;charset=gb2312"><?php foreach ($common_bootstrap as $z){echo $z;}?><?php foreach ($easydialog as $x){echo $x;}?>
</head>

<body>
<!-- 头部 begin -->
<header class="jumbotron subhead"  style="margin-bottom: 10px;">    <div class="breadcrumb">        <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>	    <li class="active"><span style="color:#0088cc;font-weight:bolder">菜单模块列表</li>    </div></header><div class="header_center"><?php echo $power->show_button("add"); ?></div><div class="headers_oprate"><form name="topform" method="GET"><input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input-medium search-query" placeholder="模糊搜索" >&nbsp;<input type="submit" class="btn" value="搜索" style="font-weight:bold" title="点击搜索"></form></div>
<!-- 头部 end -->
<div class="space"></div>
<!-- 数据列表 begin -->
<form name="mainform">
<table width="100%" align="center" class="table table-hover table-condensed">
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
	<div class="footer_op_left"><button onclick="select_all()" class="btn">全选</button>&nbsp;<button onclick="unselect()" class="btn">反选</button>&nbsp;<?php echo $power->show_button("close,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

<div class="space"></div>

</body>
</html>
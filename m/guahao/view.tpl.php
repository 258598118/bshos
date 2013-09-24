<?php
// --------------------------------------------------------
// - 功能 : view 模板
// - 作者 : zhuwenya (weelia@126.com)
// - 时间 : 2008-08-20 11:34
// --------------------------------------------------------

// 检查是否包含调用
if (!$username) {
	exit("This page can not directly opened from browser...");
}

// 检查数据
if (!$viewdata) {
	exit("Wrong viewdata...");
}

if ($title == "") {
	$title = "查看";
}
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
</head>

<body>
<!-- 头部 begin -->
    <header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
             <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
             <li class="active"><?php echo $title; ?></li>
        </ul>
	</header>
<div class="headers">
	<div class="headers_title">
	    <button class="btn btn-success">
	    <i class="icon-home icon-white"></i>
	    <?php echo $title; ?></button>
    </div>
	<div class="headers_oprate">
	    <button onClick="history.back()" class="btn btn-small btn-info">
	    <i class="icon-arrow-left icon-white"></i>返回</button>
	</div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<table width="100%" class="table table-striped table-condensed">
<?php foreach ($viewdata as $k => $v) { ?>
	<tr>
		<td class="left" style="font-weight:bolder;width:20%"><?php echo $v[0]; ?>：</td>
		<td class="right"><?php echo $v[1]; ?></td>
	</tr>
<?php } ?>
</table>

<div class="button_line">
	<button onClick="history.back()" class="btn btn-small btn-info"><i class="icon-arrow-left icon-white"></i>返回</button>
</div>
</body>
</html>
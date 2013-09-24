<?php
/*
// - 功能说明 : 显示所有在线人员
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-04-25 22:42
*/
require "../../core/core.php";
$table = "sys_admin";

$where = array();
// 搜索:
if ($key = $_GET["key"]) {
	$where[] = "(name like '%{$key}%' or realname like '%{$key}%')";
}

$sqlwhere = '';
if (count($where) > 0) {
	$sqlwhere = "and ".implode(" and ", $where);
}

$list = $db->query("select id,name,realname from $table where online=1 $sqlwhere order by realname asc", "id");

// ------------- 页面开始 ---------------
?>
<html>
<head>
<title>所有在线人员</title>
<meta http-equiv="refresh" content="180">
<meta http-equiv="Content-Type" content="text/html;charset=gbk">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
.admin_list {margin-left:10px; margin-top:10px; }
#rec_part, #rec_user {margin-top:6px; }
.rub {width:180px; float:left; }
.rub input {float:left; }
.rub a {display:block; float:left; padding-top:2px; }
.rgp {clear:both; margin:10px 0 5px 0; font-weight:bold; }
.group_select {margin-top:10px; margin-bottom:0px; text-align:center; }
.breadcrumb ul{margin:0}
</style>

<script language="javascript">
function ld(id) {
	parent.load_box(1, "src", "m/sys/talk.php?to="+id);
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<header class="jumbotron subhead"  style="margin-bottom: 10px;">
	<div class="breadcrumb">
        <ul style="float:left">
             <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
             <li class="active"><span style="color:#0088cc;font-weight:bolder"> 所有在线人员</li>
		</ul>
		<div class="clear"></div>
    </div>
</header>
<!-- 头部 end -->

<div class="space"></div>
<div class="group_select">
<?php if ($_GET["key"]) { ?>
	<b>共搜索到 <?php echo count($list); ?> 人</b>&nbsp;
<?php } else { ?>
	<b>共有 <?php echo count($list); ?> 人在线</b>&nbsp;
<?php } ?>
	(按拼音排序) &nbsp;&nbsp;
	<b>搜索名字：</b>
	<form method="GET" style="display:inline;">
		<input name="key" value="<?php echo $_GET["key"]; ?>" class="span2" size="12">
		<input type="submit" class="btn" value="搜索" style="font-weight:bold;">
		<input type="submit" class="btn" onclick="this.form.key.value=''" value="重置">
	</form> &nbsp;
	(本页面每<b>3</b>分钟自动刷新)
</div>

<div class="space"></div>
<div class="admin_list">
	<div id="rec_user">
<?php
foreach ($list as $a => $b) {
	echo "\t\t".'<div class="rub"><a href="#" onclick="return ld('.$a.')" title="点击交谈">·'.$b["realname"]." (".$b["name"].") ".'</a></div>'."\r\n";
}
?>
		<div class="clear"></div>
	</div>
</div>

</body>
</html>
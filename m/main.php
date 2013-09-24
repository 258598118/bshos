<?php
/**
 * 功能说明 : main.php
 * author : fangyang (funyung@163.com)
 * 2008-05-13 12:28
 */
require "../core/core.php";
include "../core/function.lunar.php";

// -------------------- 2009-05-01 23:39
if ($_GET["do"] == 'change')
{
    $_SESSION[$cfgSessionName]["hospital_id"] = $_GET["hospital_id"];
    $user_hospital_id = $_SESSION[$cfgSessionName]["hospital_id"];
}
$hospital_list = $db->query(
        "select id,name from hospital where id in (" .
                 implode(',', $hospital_ids) . ") order by sort desc,id asc", 
                'id');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
// --------------------

// 时间界限定义:
$today_tb = mktime(0, 0, 0);
$today_te = $today_tb + 24 * 3600;
$tomorrow_tb = $today_tb + 24 * 7200;
$yesterday_tb = $today_tb - 24 * 3600;
$month_tb = mktime(0, 0, 0, date("m"), 1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);

// 同比日期定义(2010-11-27):
$tb_tb = strtotime("-1 month", $month_tb);
$tb_te = strtotime("-1 month", time());

// 月比:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb))
{
    $yuebi_tb = $yuebi_te = - 1;
} else
{
    $yuebi_te = $yuebi_tb + 24 * 3600;
}

// 周比:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24 * 3600;

// 同比:
$tb_tb = strtotime("-1 month", $month_tb); // 同比时间开始
$tb_te = strtotime("-1 month", time()); // 同比时间结束
                                        
// 带有缓存的查询结果:
function wee ($tb, $te, $time_type = 'order_date', $condition = '', $condition2 = '')
{
    global $table, $db;
    $time_type = $time_type == "addtime" ? "addtime" : "order_date";
    $where = array();
    if ($tb > 0)
        $where[] = $time_type . ">=" . intval($tb);
    if ($te > 0)
        $where[] = $time_type . "<" . intval($te);
    if ($condition)
        $where[] = $condition;
    if ($condition2)
        $where[] = $condition2;
    $sqlwhere = implode(" and ", $where);
    $sql = "select count(*) as c from $table where $sqlwhere limit 1";
    $sql_md5 = md5($sql);
    
    // 缓存结果:
    $timeout = 60; // 缓存超时时间
    $sql_result = - 1;
    $cache_file = "cache/" . $table;
    if (file_exists($cache_file))
    {
        $tm = @explode("\n", 
                str_replace("\r", "", file_get_contents($cache_file)));
        foreach ($tm as $tml)
        {
            list ($a, $b, $c) = explode("|", trim($tml));
            if ($a == $sql_md5)
            {
                if (time() - $b < $timeout)
                {
                    $sql_result = $c;
                    break;
                }
            }
        }
    }
    
    if ($sql_result != - 1)
    {
        return $sql_result;
    } else
    {
        $sql_result = $db->query($sql, 1, "c");
        
        // 更新缓存文件:
        $tm = array();
        $find = 0;
        $time = time();
        if (file_exists($cache_file))
        {
            $tm = @explode("\n", 
                    str_replace("\r", "", file_get_contents($cache_file)));
            foreach ($tm as $k => $tml)
            {
                list ($a, $b, $c) = explode("|", trim($tml));
                if ($a == $sql_md5)
                {
                    $tm[$k] = $sql_md5 . "|" . $time . "|" . intval($sql_result);
                    $find = 1;
                } else
                {
                    if ($time - $b > $timeout)
                    {
                        unset($tm[$k]); // 删去过时的
                    }
                }
            }
        }
        if ($find == 0)
        {
            $tm[] = $sql_md5 . "|" . $time . "|" . intval($sql_result);
        }
        @file_put_contents($cache_file, implode("\r\n", $tm));
        // 更新结束:
        
        return $sql_result;
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
<title>后台首页</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.table>thead>tr>th{background:#f7f7f7;border-bottom: 1px solid #428bca;}
.thinfo {background-color: #d9edf7;}
</style>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<script language="javascript">
function hgo(dir) {
	var obj = byid("hospital_id");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
		} else {
			parent.msg_box("已经是最上一家医院了", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
		} else {
			parent.msg_box("已经是最下一家医院了", 3);
		}
	}
}


//回访ajax 20130228 fangyang
function huifangInfo(type) {
	//获取接受返回信息层
	var msg = document.getElementById("huifang");
	var pernum = document.getElementById("pernum");
	//获取表单对象和用户信息值

	$('#loading',window.parent.document).css('display','block')
    
	switch (type)　　 {　
	case 1:
		var f = document.hform;
		var type = "yesterday";　　
		break　
	case 2:
		var f = document.hform;
		var type = "today";　
		break　
	case 3:
		var f = document.hform;
		var type = "tomorrow";
		break　
	}

	//接收表单的URL地址
	var url = "/m/patient/patient.php";

	//需要POST的值，把每个变量都通过&来联接
	var postStr = "action=hfdate&type=" + type;

	//实例化Ajax
	//var ajax = InitAjax(); 
	var ajax = false;
	//开始初始化XMLHttpRequest对象
	if (window.XMLHttpRequest) { //Mozilla 浏览器
		ajax = new XMLHttpRequest();
		if (ajax.overrideMimeType) { //设置MiME类别
			ajax.overrideMimeType("text/xml");
		}
	} else if (window.ActiveXObject) { // IE浏览器
		try {
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e) {}
		}
	}
	if (!ajax) { // 异常，创建对象实例失败
		window.alert("不能创建XMLHttpRequest对象实例.");
		return false;
	}
	//通过Post方式打开连接
	ajax.open("POST", url, true);

	//定义传输的文件HTTP头信息
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	//发送POST数据
	ajax.send(postStr);

	//获取执行状态
	ajax.onreadystatechange = function() {
		//如果执行状态成功，那么就把返回信息写到指定的层里
		if (ajax.readyState == 4 && ajax.status == 200) {
			msg.innerHTML = ajax.responseText;
			var str = ajax.responseText;
			var patt = new RegExp("/td", "g");
			var matchs = str.match(patt);
			pernum.innerHTML = (matchs==null)?0:matchs.length / 11
			$('#loading',window.parent.document).css('display','none') 
		}
	}
}

//$('#main_home a[href="#main_home"]').tab('show');

</script>
</head>

<body>
	<div class=""
		style="margin: 15px 25px; font-weight: bolder; font-size: 14px">
		<!--<div style="line-height: 24px">
<?php
$str = '您好，<font color="#FF0000"><b>' . $realname . '</b></font>';

if ($uinfo["hospitals"] || $uinfo["part_id"] > 0)
{
    if ($uinfo["part_id"] > 0)
    {
        $str .= '　(身份：' . $part_id_name[$uinfo["part_id"]] . ")";
    }
}

$onlines = $db->query("select count(*) as count from sys_admin where online=1", 
        1, "count");
$str .= '　在线人数 <font color="red"><b>' . $onlines . '</b></font> 人&nbsp;&nbsp;';

$str .= '当前时间：<span id=localtime></span>';
if ($uinfo["part_id"] == 12)
{
    // $str .= '<br><a href="#"
// onclick="parent.load_box(1,\'src\',\'patient_huifang_list_all.php\')">[查看列表]</a>';
}

echo $str;
?>
	</div>
-->
<?php if (count($hospital_ids) > 1) { ?>
	<div style="margin-top: 15px;">
			<legend style="padding-bottom: 20px">
				<b>切换医院：</b> 
				<select name="hospital_id" id="hospital_id" class="span3" onchange="location='?do=change&hospital_id='+this.value" style="margin-bottom: 0">
					<option value="" style="color: gray">--请选择--</option>
			        <?php echo list_option($hospital_list, 'id', 'name', $_SESSION[$cfgSessionName]["hospital_id"]); ?>
		        </select>&nbsp;
				<button class="btn" onclick="hgo('up');">上</button>
				&nbsp;
				<button class="btn" onclick="hgo('down');">下</button>&nbsp;
	            <?php if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 3) { ?>
		        <button class="btn" onclick="self.location='/m/patient/patient.php?list_huifang=1'" title="查看我最近回访过的病人">我的回访</button>&nbsp;
	            <?php } ?>

                <?php if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 1|| $uinfo["part_id"] == 9) { ?>
		        <button class="btn" onclick="self.location='/m/report/rp_huifang.php'" title="客服回访记录">客服回访记录</button>&nbsp;
	            <?php } ?>

</legend>
		</div>
<?php } else if ($user_hospital_id > 0) { ?>
	<div style="margin-top: 20px;">
			<legend style="padding-bottom: 20px">
				当前医院：<b><?php echo $hospital_list[$user_hospital_id]["name"]; ?></b>
			</legend>
		</div>
<?php } else { ?>
	  <div style="margin-top: 20px;">
			<legend style="padding-bottom: 20px">没有为您分配医院，请联系上级管理人员处理。</legend>
	</div>
<?php }?>
</div>


<!-- 选择医院后 -->
<?php if ($user_hospital_id > 0): ?>

<!-- 预约管理权限 -->
<?php
    $table = "patient_" . $user_hospital_id;
    $table_fee = 'patient_fee';
    $where = array();
    $where[] = '1';
    if (! $debug_mode)
    {
        $read_parts = get_manage_part(); // 所有子部门（连同其自身部门)
        $manage_parts = explode(",", $read_parts);
        if ($uinfo["part_admin"] || $uinfo["part_manage"])
        { // 部门管理员或数据管理员
            $where[] = "(part_id in (" . $read_parts . ") or binary author='" .
                     $realname . "')";
        } else
        { // 普通用户只显示自己的数据
            $where[] = "binary author='" . $realname . "'";
        }
    }
    
    // 电话回访只显示已到病人:
    if ($uinfo["part_id"] == 12)
    {
        // $where[] = "status='1'";
    }
    
    $sqlwhere = implode(" and ", $where);
    
    //今日
    $today_all = $db->query(
            "select count(*) as count from $table where $sqlwhere and order_date>=$today_tb and order_date<$today_te", 
            1, "count");
    if ($_GET["show"] == "sql")
    {
        echo $db->sql . "<br>";
    }
    $today_come = $db->query( "select count(*) as count from $table where $sqlwhere  and order_date>=$today_tb and order_date<$today_te and status='1'", 1, "count");
    $today_not = $today_all - $today_come;
    
    $today_chu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '0' and order_date>=$today_tb and order_date<$today_te",1, "count");
    
    $today_fu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '1' and order_date>=$today_tb and order_date<$today_te",1, "count");
    
    $today_cha = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and  re_arrive = '2' and order_date>=$today_tb and order_date<$today_te",1, "count");
    
    $today_zai = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '3' and order_date>=$today_tb and order_date<$today_te",1, "count");
    
    $today_cj = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and chengjiao = '1' and order_date>=$today_tb and order_date<$today_te",1, "count");
    
    $today_ls = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and chengjiao != '1' and order_date>=$today_tb and order_date<$today_te",1, "count");

    $today_cj_money = $db->query("select sum(s_charge) as count from $table_fee where hid = $user_hospital_id and cj_time>=$today_tb and cj_time<$today_te",1, "count");
    
    $today_cj_chu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.hid = $user_hospital_id and $table_fee.cj_time>=$today_tb and $table_fee.cj_time<$today_te and $table.re_arrive = '0'",1, "count");
    
    $today_cj_fu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.hid = $user_hospital_id and $table_fee.cj_time>=$today_tb and $table_fee.cj_time<$today_te and $table.re_arrive = '1'",1, "count");
    
    $today_cj_cha_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.hid = $user_hospital_id and $table_fee.cj_time>=$today_tb and $table_fee.cj_time<$today_te and $table.re_arrive = '2'",1, "count");
    
    $today_cj_zai_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.hid = $user_hospital_id and $table_fee.cj_time>=$today_tb and $table_fee.cj_time<$today_te and $table.re_arrive = '3'",1, "count");
    $uncertain = $db->query(
            "select count(*) as count from $table where $sqlwhere and order_date>=$today_tb and order_date<$today_te and status in (0,2) and tel='' and qq=''", 
            1, "count");
    
    //昨天
    $yesterday_all = $db->query( "select count(*) as count from $table where $sqlwhere and re_arrive = '0' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    $yesterday_come = $db->query("select count(*) as count from $table where $sqlwhere and order_date>=$yesterday_tb and order_date<$today_tb and status='1'", 1, "count");
    $yesterday_not = $yesterday_all - $yesterday_come;
    
    $yesterday_chu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '0' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    
    $yesterday_fu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '1' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    
    $yesterday_cha = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '2' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    
    $yesterday_zai = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '3' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    
    $yesterday_cj = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and chengjiao = '1' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    
    $yesterday_ls = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and chengjiao != '1' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
    
    $yesterday_cj_money = $db->query("select sum(s_charge) as count from $table_fee where  cj_time>=$yesterday_tb and cj_time<$today_tb",1, "count");
    
    $yesterday_cj_chu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$yesterday_tb and $table_fee.cj_time<$today_tb and $table.re_arrive = '0'",1, "count");
    
    $yesterday_cj_fu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$yesterday_tb and $table_fee.cj_time<$today_tb and $table.re_arrive = '1'",1, "count");
    
    $yesterday_cj_cha_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$yesterday_tb and $table_fee.cj_time<$today_tb and $table.re_arrive = '2'",1, "count");
    
    $yesterday_cj_zai_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$yesterday_tb and $table_fee.cj_time<$today_tb and $table.re_arrive = '3'",1, "count");
    //本月
    $this_month_all = $db->query( "select count(*) as count from $table where $sqlwhere and re_arrive = '0' and order_date>=$month_tb and order_date<$month_te", 1, "count");
    $this_month_come = $db->query( "select count(*) as count from $table where $sqlwhere and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    $this_month_not = $this_month_all - $this_month_come;
    
    $this_month_chu = $db->query(  "select count(*) as count from $table where $sqlwhere and re_arrive = '0' and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    
    $this_month_fu = $db->query(  "select count(*) as count from $table where $sqlwhere and re_arrive = '1' and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    
    $this_month_cha = $db->query(  "select count(*) as count from $table where $sqlwhere and re_arrive = '2' and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    
    $this_month_zai = $db->query(  "select count(*) as count from $table where $sqlwhere and re_arrive = '3' and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    
    $this_month_cj = $db->query(  "select count(*) as count from $table where $sqlwhere and chengjiao = '1' and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    
    $this_month_ls = $db->query(  "select count(*) as count from $table where $sqlwhere and chengjiao != '1' and order_date>=$month_tb and order_date<$month_te and status='1'",  1, "count");
    
    $this_month_cj_money = $db->query("select sum(s_charge) as count from $table_fee where  hid = $user_hospital_id and cj_time>=$month_tb and cj_time<$month_te",1, "count");
    
    $this_month_cj_chu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$month_tb and $table_fee.cj_time<$month_te and $table.re_arrive = '0'",1, "count");
    
    $this_month_cj_fu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$month_tb and $table_fee.cj_time<$month_te and $table.re_arrive = '1'",1, "count");
    
    $this_month_cj_cha_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$month_tb and $table_fee.cj_time<$month_te and $table.re_arrive = '2'",1, "count");
    
    $this_month_cj_zai_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$month_tb and $table_fee.cj_time<$month_te and $table.re_arrive = '3'",1, "count");
    //上月
    $last_month_all = $db->query( "select count(*) as count from $table where $sqlwhere and re_arrive = '0' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    $last_month_come = $db->query( "select count(*) as count from $table where $sqlwhere and order_date>=$lastmonth_tb and order_date<$month_tb and status='1'", 1, "count");
    $last_month_not = $last_month_all - $last_month_come;
    
    $last_month_chu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and  re_arrive = '0' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    
    $last_month_fu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '1' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    
    $last_month_cha = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '2' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    
    $last_month_zai = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '3' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    
    $last_month_cj = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and chengjiao = '1' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    
    $last_month_ls = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and chengjiao != '1' and order_date>=$lastmonth_tb and order_date<$month_tb", 1, "count");
    
    $last_month_cj_money = $db->query("select sum(s_charge) as count from $table_fee where  hid = $user_hospital_id and cj_time>=$lastmonth_tb and cj_time<$month_tb",1, "count");
    
    $last_month_cj_chu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$lastmonth_tb and $table_fee.cj_time<$month_tb and $table.re_arrive = '0'",1, "count");
    
    $last_month_cj_fu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$lastmonth_tb and $table_fee.cj_time<$month_tb and $table.re_arrive = '1'",1, "count");

    $last_month_cj_cha_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$lastmonth_tb and $table_fee.cj_time<$month_tb and $table.re_arrive = '2'",1, "count");
    
    $last_month_cj_zai_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$lastmonth_tb and $table_fee.cj_time<$month_tb and $table.re_arrive = '3'",1, "count");
    // 同比:
    $tb_all = $db->query( "select count(*) as count from $table where $sqlwhere and order_date>=$tb_tb and order_date<$tb_te", 1, "count");
    $tb_come = $db->query( "select count(*) as count from $table where $sqlwhere and order_date>=$tb_tb and order_date<$tb_te and status='1'", 1, "count");
    $tb_not = $zhoubi_all - $zhoubi_come;
    
    $tb_chu = $db->query("select count(*) as count from $table where $sqlwhere  and status='1' and re_arrive = '0' and order_date>=$tb_tb and order_date<$tb_te",1, "count");
    
    $tb_fu = $db->query("select count(*) as count from $table where $sqlwhere and status='1' and re_arrive = '1' and order_date>=$tb_tb and order_date<$tb_te",1, "count");
    
    $tb_cha = $db->query("select count(*) as count from $table where $sqlwhere  and status='1' and re_arrive = '2' and order_date>=$tb_tb and order_date<$tb_te",1, "count");
    
    $tb_zai = $db->query("select count(*) as count from $table where $sqlwhere  and status='1' and re_arrive = '3' and order_date>=$tb_tb and order_date<$tb_te",1, "count");
    
    $tb_cj = $db->query("select count(*) as count from $table where $sqlwhere  and status='1' and chengjiao = '1' and order_date>=$tb_tb and order_date<$tb_te",1, "count");
    
    $tb_ls = $db->query("select count(*) as count from $table where $sqlwhere  and status='1' and chengjiao != '1' and order_date>=$tb_tb and order_date<$tb_te",1, "count");
    
    $tb_cj_money = $db->query("select sum(s_charge) as count from $table_fee where hid = $user_hospital_id and  cj_time>=$tb_tb and cj_time<$tb_te",1, "count");
    
    $tb_cj_chu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$tb_tb and $table_fee.cj_time<$tb_te and $table.re_arrive = '0'",1, "count");
    
    $tb_cj_fu_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$tb_tb and $table_fee.cj_time<$tb_te and $table.re_arrive = '1'",1, "count");
    
    $tb_cj_cha_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$tb_tb and $table_fee.cj_time<$tb_te and $table.re_arrive = '2'",1, "count");
    
    $tb_cj_zai_money = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$tb_tb and $table_fee.cj_time<$tb_te and $table.re_arrive = '3'",1, "count");
    
    //挂号总数据 预计
    //明天
    $tomorrow_all = $db->query( "select count(*) as count from $table where $sqlwhere and order_date>=$today_te and order_date<$tomorrow_tb",1, "count");
      
    ?>
<div>

		<div id="main_home" class="container-fluid" style="margin-top: 30px">
			<ul id="tab" class="nav nav-tabs">
				<li class="active"><a href="#home" data-toggle="tab">摘要</a></li>
				<?php if(!isset($purview['show_chengjiao'])||@$purview['show_chengjiao']!=0):?><li><a href="#cjiao" data-toggle="tab">成交详情</a></li><?php endif?>
				<?php if(!isset($purview['show_huifang'])||@$purview['show_huifang']!=0):?><li><a href="#remind" data-toggle="tab">回访提醒</a></li><?php endif?>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="home">
					<div class="row-fluid">
						<div class="span10">
								<table class="table table-hover">
								    <thead>
								        <tr>
								            <th width=10%><span class="text-error">总表</span></th>
								            <th width=10%>预诊</th>
								            <th width=10%>已到</th>
								            <th width=10%>未到</th>
								            <th width=10% class="text-info">初诊</th>
								            <th width=10% class="text-info">复诊</th>
								            <th width=10% class="text-info">复查</th>
								            <th width=10% class="text-info">再消费</th>
								            <th width=10%>成交</th>
								            <th width=10%>流失</th>
								        </tr>
								    </thead>
									<tr>
										<td><b class="">今日</b></td>
										<td><a href="/m/patient/patient.php?show=today&re_arrive=0"><?=$today_all?></a></td>
										<td><a href="/m/patient/patient.php?show=today&come=1"><?=$today_come?></a></td>
										<td><a href="/m/patient/patient.php?show=today&come=0&re_arrive=0"><?=$today_not?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=today&come=1&re_arrive=0"><?=$today_chu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=today&come=1&re_arrive=1"><?=$today_fu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=today&come=1&re_arrive=2"><?=$today_cha?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=today&come=1&re_arrive=3"><?=$today_zai?></a></td>
										<td><a href=""><b><?=$today_cj?></b></a></td>
										<td><a href=""><b><?=$today_ls?></b></a></td>
									</tr>
									<tr>
										<td><b class="">昨日</b></td>
										<td><a href="/m/patient/patient.php?show=yesterday&re_arrive=0"><?=$yesterday_all?></a></td>
										<td><a href="/m/patient/patient.php?show=yesterday&come=1"><?=$yesterday_come?></a></td>
										<td><a href="/m/patient/patient.php?show=yesterday&come=0&re_arrive=0"><?=$yesterday_not?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=yesterday&come=1&re_arrive=0"><?=$yesterday_chu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=yesterday&come=1&re_arrive=1"><?=$yesterday_fu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=yesterday&come=1&re_arrive=2"><?=$yesterday_cha?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=yesterday&come=1&re_arrive=3"><?=$yesterday_zai?></a></td>
										<td><a href=""><?=$yesterday_cj?></a></td>
										<td><a href=""><?=$yesterday_ls?></a></td>
									</tr>
									<tr>
										<td><b class="">明天</b></td>
										<td><a href="/m/patient/patient.php?show=tomorrow&re_arrive=0"><?=$tomorrow_all?></a></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									    <td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td><b>本月</b></td>
										<td><a href="/m/patient/patient.php?show=thismonth&re_arrive=0"><?=$this_month_all?></a></td>
										<td><a href="/m/patient/patient.php?show=thismonth&come=1"><?=$this_month_come?></a></td>
										<td><a href="/m/patient/patient.php?show=thismonth&come=0&re_arrive=0"><?=$this_month_not?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=thismonth&come=1&re_arrive=0"><?=$this_month_chu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=thismonth&come=1&re_arrive=1"><?=$this_month_fu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=thismonth&come=1&re_arrive=2"><?=$this_month_cha?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=thismonth&come=1&re_arrive=3"><?=$this_month_zai?></a></td>
										<td><a href=""><?=$this_month_cj?></a></td>
										<td><a href=""><?=$this_month_ls?></a></td>
									</tr>
									<tr>
										<td>上月</td>
										<td><a href="/m/patient/patient.php?show=lastmonth&re_arrive=0"><?=$last_month_all?></a></td>
										<td><a href="/m/patient/patient.php?show=lastmonth&come=1"><?=$last_month_come?></a></td>
										<td><a href="/m/patient/patient.php?show=lastmonth&come=0&re_arrive=0"><?=$last_month_not?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=lastmonth&come=1&re_arrive=0"><?=$last_month_chu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=lastmonth&come=1&re_arrive=1"><?=$last_month_fu?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=lastmonth&come=1&re_arrive=2"><?=$last_month_cha?></a></td>
										<td><a class="text-info" href="/m/patient/patient.php?show=lastmonth&come=1&re_arrive=3"><?=$last_month_zai?></a></td>
										<td><a href=""><?=$last_month_cj?></a></td>
										<td><a href=""><?=$last_month_ls?></a></td>
									</tr>
									<tr>
										<td><b>同比</b></td>
										<td><?=$tb_all?></td>
										<td><?=$tb_come?></td>
										<td><?=$tb_not?></td>
										<td class="text-info"><?=$tb_chu?></td>
										<td class="text-info"><?=$tb_fu?></td>
										<td class="text-info"><?=$tb_cha?></td>
										<td class="text-info"><?=$tb_zai?></td>
										<td><a href=""><?=$tb_cj?></a></td>
										<td><a href=""><?=$tb_ls?></a></td>
									</tr>
								</table>
						</div>
					</div>
                    <hr class="text-error"/>
					<div class="row-fluid">
						<!-- 管理员汇总统计数据 -->
<?php if ($user_hospital_id ==4||$username == "admin" || $debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(2,$manage_parts)) ) { ?>
<?php

        $table = "patient_" . $user_hospital_id;
        $web_1 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and addtime>=$today_tb and addtime<$today_te", 
                1, "count");
        $web_2 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and addtime>=$yesterday_tb and addtime<$today_tb", 
                1, "count");
        $web_3 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and addtime>=$month_tb and addtime<$month_te", 
                1, "count");
        
        $web_4 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and status='1' and order_date>=$today_tb and order_date<$today_te", 
                1, "count");
        $web_5 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and status='1' and order_date>=$yesterday_tb and order_date<$today_tb", 
                1, "count");
        $web_6 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and status='1' and order_date>=$month_tb and order_date<$month_te", 
                1, "count");
        
        $web_7 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and order_date>=$today_tb and order_date<$today_te", 
                1, "count");
        $web_8 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and order_date>=$yesterday_tb and order_date<$today_tb", 
                1, "count");
        $web_9 = $db->query(
                "select count(*) as count from $table where part_id=2 and re_arrive = '0' and order_date>=$month_tb and order_date<$month_te", 
                1, "count");
        

        // 同比
        $web_tb1 = $db->query(
                "select count(*) as count from $table where part_id=2 and addtime>=$tb_tb and addtime<$tb_te", 
                1, "count");
        $web_tb2 = $db->query(
                "select count(*) as count from $table where part_id=2 and order_date>=$tb_tb and order_date<$tb_te", 
                1, "count");
        $web_tb3 = $db->query(
                "select count(*) as count from $table where part_id=2 and order_date>=$tb_tb and order_date<$tb_te and status='1'", 
                1, "count");
        
        ?>
                   <div class="span5">
						<table class="table">
						    <thead>
						        <tr>
						            <th><span class="text-error">网络</span></th>
						            <th>预约</th>
						            <th>预计</th>
						            <th>到诊</th>
						        </tr>
						    </thead>
							<tr>
								<td><b>今日</b今日></td>
								<td><span title="今日客服预约人数"><a
										href="/m/patient/patient.php?show=today&time_type=addtime&part_id=2&re_arrive=0"><?php echo $web_1; ?>
									</a></span></td>
								<td><span title="今日预计到院人数"><a
										href="/m/patient/patient.php?show=today&part_id=2&re_arrive=0"><?php echo $web_7; ?>
									</a></span></td>
								<td><span title="今日已经到院人数"><a
										href="/m/patient/patient.php?show=today&part_id=2&come=1&re_arrive=0"><?php echo $web_4; ?>
									</a></span></td>
							</tr>
							<tr>
								<td><b>昨日</b></td>
								<td><span title="昨日客服预约人数"><a
										href="/m/patient/patient.php?show=yesterday&time_type=addtime&part_id=2&re_arrive=0"><?php echo $web_2; ?>
									</a></span></td>
								<td><span title="昨日预计到院人数"><a
										href="/m/patient/patient.php?show=yesterday&part_id=2&re_arrive=0"><?php echo $web_8; ?>
									</a></span></td>
								<td><span title="昨日已经到院人数"><a href="/m/patient/patient.php?show=yesterday&part_id=2&come=1&re_arrive=0"><?php echo $web_5; ?>
									</a></span></td>
							</tr>
							<tr>
								<td><b>本月</b></td>
								<td><span title="本月客服预约人数"><a href="/m/patient/patient.php?show=thismonth&time_type=addtime&part_id=2&re_arrive=0"><?php echo $web_3; ?>
									</a></span></td>
								<td><span title="本月预计到院人数"><a href="/m/patient/patient.php?show=thismonth&part_id=2&re_arrive=0"><?php echo $web_9; ?>
									</a></span></td>
								<td><span title="本月已经到院人数"><a href="/m/patient/patient.php?show=thismonth&part_id=2&come=1&re_arrive=0"><?php echo $web_6; ?> </a></span></td>
							</tr>
							<tr>
								<td><b>同比</b></td>
								<td><?=$web_tb1?></td>
								<td><?=$web_tb2?></td>
								<td><?=$web_tb3?></td>
							</tr>
						</table>
				  </div>	
<?php } ?>

<?php if ($username == "admin" || $user_hospital_id ==4||$debug_mode || in_array($uinfo["part_id"], array(1,9)) || ($uinfo["part_admin"] && in_array(3,$manage_parts)) ) { ?>
<?php

        $table = "patient_" . $user_hospital_id;
        $tel_1 = $db->query(
                "select count(*) as count from $table where part_id=3 and addtime>=$today_tb and addtime<$today_te", 
                1, "count");
        $tel_2 = $db->query(
                "select count(*) as count from $table where part_id=3 and addtime>=$yesterday_tb and addtime<$today_tb", 
                1, "count");
        $tel_3 = $db->query(
                "select count(*) as count from $table where part_id=3 and addtime>=$month_tb and addtime<$month_te", 
                1, "count");
        
        $tel_4 = $db->query(
                "select count(*) as count from $table where part_id=3 and status='1' and order_date>=$today_tb and order_date<$today_te", 
                1, "count");
        $tel_5 = $db->query(
                "select count(*) as count from $table where part_id=3 and status='1' and order_date>=$yesterday_tb and order_date<$today_tb", 
                1, "count");
        $tel_6 = $db->query(
                "select count(*) as count from $table where part_id=3 and status='1' and order_date>=$month_tb and order_date<$month_te", 
                1, "count");
        
        $tel_7 = $db->query(
                "select count(*) as count from $table where part_id=3 and order_date>=$today_tb and order_date<$today_te", 
                1, "count");
        $tel_8 = $db->query(
                "select count(*) as count from $table where part_id=3 and order_date>=$yesterday_tb and order_date<$today_tb", 
                1, "count");
        $tel_9 = $db->query(
                "select count(*) as count from $table where part_id=3 and order_date>=$month_tb and order_date<$month_te", 
                1, "count");
        
        // 同比
        $tel_tb1 = $db->query(
                "select count(*) as count from $table where part_id=3 and addtime>=$tb_tb and addtime<$tb_te", 
                1, "count");
        $tel_tb2 = $db->query(
                "select count(*) as count from $table where part_id=3 and order_date>=$tb_tb and order_date<$tb_te", 
                1, "count");
        $tel_tb3 = $db->query(
                "select count(*) as count from $table where part_id=3 and order_date>=$tb_tb and order_date<$tb_te and status='1'", 
                1, "count");
        
        ?>
              <div class="span5">
					<table class="table">
					    <thead>
					        <tr>
					            <th><span class="text-error">电话</span></th>
					            <th>预约</th>
					            <th>预计</th>
					            <th>到诊</th>
					        </tr>
					    </thead>
						<tr>
							<td><b>今日</b></td>
							<td><a href="/m/patient/patient.php?show=today&time_type=addtime&part_id=3"><?php echo $tel_1; ?></a></td>
							<td><a href="/m/patient/patient.php?show=today&part_id=3"><?php echo $tel_7; ?></a></td>
							<td><a href="/m/patient/patient.php?show=today&part_id=3&come=1"><?php echo $tel_4; ?></a></td>
						</tr>
						<tr>
							<td><b>昨日</b></td>
							<td><a href="/m/patient/patient.php?show=yesterday&time_type=addtime&part_id=3"><?php echo $tel_2; ?> </a></td>
							<td><a href="/m/patient/patient.php?show=yesterday&part_id=3"><?php echo $tel_8; ?> </a></td>
							<td><a href="/m/patient/patient.php?show=yesterday&part_id=3&come=1"><?php echo $tel_5; ?></a></td>
						</tr>
						<tr>
							<td><b>本月</b></td>
							<td><a href="/m/patient/patient.php?show=thismonth&time_type=addtime&part_id=3"><?php echo $tel_3; ?></a></td>
							<td><a href="/m/patient/patient.php?show=thismonth&part_id=3"><?php echo $tel_9; ?></a></td>
							<td><a href="/m/patient/patient.php?show=thismonth&part_id=3&come=1"><?php echo $tel_6; ?> </a></td>
						</tr>
						<tr>
							<td><b>同比</b></td>
							<td><?=$tel_tb1?></td>
							<td><?=$tel_tb2?></td>
							<td><?=$tel_tb3?></td>
						</tr>
					</table>
				</div>	
        <?php } ?>
			</div>
		</div>
		<?php if(!isset($purview['show_chengjiao'])||@$purview['show_chengjiao']!=0):?>				
		<div class="tab-pane" id="cjiao">
		    <div class="row-fluid">
			    <table class="table table-hover span10">
			        <thead>
			            <tr>
			                <th>#（单位:元）</th>
			                <th width=11%>总成交</th>
			                <th width=11%>初诊</th>
			                <th width=11%>复诊</th>
			                <th width=11%>复查</th>
			                <th width=11%>再消费</th>
			                <th width=11%>未交</th>
			                <th width=11%>预定</th>
			                <th width=11%>退款</th>
			            </tr>
			        </thead>
			        <tbody id="feetable" >
				        <tr id="todaydata">
				            <td><b>今天</b></td>
				            <td><?php echo format_money($today_cj_money);?></td>
				            <td><?php echo format_money($today_cj_chu_money);?></td>
				            <td><?php echo format_money($today_cj_fu_money);?></td>
				            <td><?php echo format_money($today_cj_cha_money);?></td>
				            <td><?php echo format_money($today_cj_zai_money);?></td>
				            <td></td>
				            <td></td>
				            <td></td>
				        </tr>
				        <tr id="tommorrowdata">
				            <td><b>昨天</b></td>
				            <td><?php echo format_money($yesterday_cj_money);?></td>
				            <td><?php echo format_money($yesterday_cj_chu_money);?></td>
				            <td><?php echo format_money($yesterday_cj_fu_money);?></td>
				            <td><?php echo format_money($yesterday_cj_cha_money);?></td>
				            <td><?php echo format_money($yesterday_cj_zai_money);?></td>
				            <td></td>
				            <td></td>
				            <td></td>
				        </tr>
				        <tr id="this_monthdata">
				            <td><b>本月</b></td>
				            <td><?php echo format_money($this_month_cj_money);?></td>
				            <td><?php echo format_money($this_month_cj_chu_money);?></td>
				            <td><?php echo format_money($this_month_cj_fu_moneyformat_money);?></td>
				            <td><?php echo format_money($this_month_cj_cha_money);?></td>
				            <td><?php echo format_money($this_month_cj_zai_money);?></td>
				            <td></td>
				            <td></td>
				            <td></td>
				        </tr>
				        <tr id="last_monthdata">
				            <td><b>上月</b></td>
				            <td><?php echo format_money($last_month_cj_money);?></td>
				            <td><?php echo format_money($last_month_cj_chu_money);?></td>
				            <td><?php echo format_money($last_month_cj_fu_money);?></td>
				            <td><?php echo format_money($last_month_cj_cha_money);?></td>
				            <td><?php echo format_money($last_month_cj_zai_money);?></td>
				            <td></td>
				            <td></td>
				            <td></td>
				        </tr>
				        <tr>
				            <td><b>同比</b></td>
				            <td><?php echo format_money($tb_cj_money);?></td>
				            <td><?php echo format_money($tb_cj_chu_money);?></td>
				            <td><?php echo format_money($tb_cj_fu_money);?></td>
				            <td><?php echo format_money($tb_cj_cha_money);?></td>
				            <td><?php echo format_money($tb_cj_zai_money);?></td>
				            <td></td>
				            <td></td>
				            <td></td>
				        </tr>
			        </tbody>
			    </table>
		    </div>
		    <div class="row-fluid">
		        <div id="feepart" class="span5">
		        </div>
		        <div id="feefrom" class="span5">
		        </div>
		    </div>
		</div>
		<?php endif?>
		
		<?php if(!isset($purview['show_huifang'])||@$purview['show_huifang']!=0):?>		
		    <?php
			    
			    $table = "patient_" . $user_hospital_id;
			    $begin_time = mktime(0, 0, 0);
			    $end_time = mktime(23, 59, 59);
			    $time = time();
			    $today_begin = mktime(0, 0, 0);
			    $today_end = $today_begin + 24 * 3600;
			    $where = "$table.huifang_date>= $begin_time AND $table.huifang_date<=$end_time AND $table.status=0";
			    if(!$debug_mode&&@$purview['show_huifang_all']==0)
			    {
			    	$where.= "  AND binary $table.author = '$realname'";
			    }
			    $list_data = $db->query( "select $table.*,disease.name as disease_name from $table  LEFT JOIN disease ON $table.disease_id = disease.id WHERE $where");
		    ?>
				 <div class="tab-pane row-fluid" id="remind">
					<div class="span10">
						<form name="hform" onsubmit="return false" style="margin-bottom: 0px">
						    <span style="font-weight:bold">
						        <custom id="locdate">今天</custom>
								<custom>需要回访</custom>
								<span class="text-error">
									<a href="javascrip:void(0)" class="text-error" style="padding: auto 3px;" id="pernum"><?=count($list_data)?></a>
								</span>
								 <custom>人</custom>
						    </span>
						    <span class="label label-info" ><a href="/m/patient/patient.list.huifang.php" style="color:#fff" title="详细预约列表">详细</a></span>
							<span style="float: right" class="btn-group" data-toggle="buttons-radio">
							    <button class="btn" name="yesterday" onclick="huifangInfo(1);document.getElementById('locdate').innerHTML='昨天'">昨天</button>
								<button class="btn active" name="today" onclick="huifangInfo(2);document.getElementById('locdate').innerHTML='今天'">今天</button>
								<button class="btn" name="tomorrow" onclick="huifangInfo(3);document.getElementById('locdate').innerHTML='明天'">明天</button>
						    </span>
									    
							<table class="table table-striped table-hover" style="margin-top: 10px; margin-bottom: 5px;">
							    <thead>
									<tr style="font-weight: bolder">
										<th width=5%>选</th>
										<th width=9.4%>姓名</th>
										<th width=9.4%>状态</th>
										<th width=9.4%>性别</th>
										<th width=9.4%>年龄</th>
										<th width=9.4%>项目</th>
										<th width=9.4%>媒体来源</th>
										<th width=9.4%>电话</th>
										<th width=9.4%>客服</th>
										<th width=9.4%>时间</th>
										<th width=5%>操作</th>
								     </tr>
							     </thead>
							</table>
						</form>

						<div class="" style="height: 370px; overflow: auto;" id="huifang">
							<table class="table table-hover">
						<?php
						$re_arrive_array = array (
								array (
										"id" => 0,
										"name" => '初诊'
								),
								array (
										"id" => 1,
										"name" => '复诊'
								),
								array (
										"id" => 2,
										"name" => '再消费'
								),
								array (
										"id" => 3,
										"name" => '复查'
								)
						);
						
						    foreach ($list_data as $data)
						    {
						    	
						    	$huifang_row = $db->query("select count(*) as count from $table where id = ".$data['id']." AND huifang like '%".date('Y-m-d')."%'", 1, "count");
						    	if($huifang_row == 0)
						    	{
						    		$huifang_status = '';
						    	}else
						    	{
						    		$huifang_status = 'warning';
						    	}
						    	
						    	//根据回访情况换颜色
						    	if(preg_match("/".@date("Y-m-d")."/",$data["huifang"] ))
						    	{
						    		$trsx = "style='background:#fcf8e3;font-weight:bold'";
						    	}else if(preg_match("/".@date("Y-m-d", $data["order_date"])."/",$data["huifang"] ))
						    	{
						    		$trsx = "style='background:#fcf8e3'";
						    	}else
						    	{
						    		$trsx = "";
						    	}
						    	
						        echo '<tr class="'.$huifang_status.'" id="'.$data["id"].'" '.$trsx.'>
						    		     <td width=5%><input type="checkbox"  '.$checked .'/></td>
						 		         <td width=9.4%><a href="javascript:void(0)" title="详情" onclick="patientbox(\''.$data['name'].'\',\''.$data['id'].'\',\'index\')" >' .
						                 $data['name'] . '</a></td>
								         <td width=9.4%>'. $re_arrive_array[$data['re_arrive']]['name'].'</td>
								         <td width=9.4%>' . $data['sex'].'</td>
    		                             <td width=9.4%>' . $data['age'].'</td>
								         <td width=9.4%>' . $data['disease_name'].'</td>
								         <td width=9.4%>' . $data['media_from'] . '</td>
								         <td width=9.4%>' . hide_tel($data["tel"],$data['doctor']). '</td>
								         <td width=9.4%>' . substr($data['author'],0,8) . '</td>
								         <td width=9.4%>' . date('m-d H:i', $data['huifang_date']) . '</td>
    		                             <td width=5%><a href="javascript:void(0)" onclick="parent.huifangm('.$data["id"].',\''.$hfhostory.'\')"><i class="icon-comment"></i></a></td>
								      </tr>';
						        
						        unset($huifang_status);
						       } ?>
							</table>
						</div>
					</div>
				</div>
				<?php endif?>
			</div>
		</div>
	</div>
	<div class="clear"></div>

	<!-- 回访表单 start -->
	<div id="huifangmodal" class="modal hide" data-backdrop='false'>
	    <form class="form-horizontal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"  aria-hidden="true">×</button>
			<h3>回访记录</h3>
		</div>
		<div class="modal-body">
		    <fieldset>
				<div class="control-group">
					<label class="control-label" for="appendedInput">关键词：</label>
					<div class="controls">
						<div class="input-append">
							<textarea class="input-xlarge"  name="searchword"  rows="3"></textarea>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="modal-footer">
			<a href="#" data-dismiss="modal"  class="btn">关闭</a> 
			<a href="#" type="submit" class="btn btn-primary">保存</a>
		</div>
		</form>
	</div>
	<!-- 回访表单 END -->
<?php endif;?>
<script>	
//工具提示
$("a[rel=tooltip],input[rel=tooltip],div[rel=tooltip],select[rel=tooltip],button[rel=tooltip]").tooltip();
$("a[rel=popover],button[rel=popover]").popover();
function drawChart(data,title)
{
	var partd,parttitle;
	if(typeof(data) == "undefined" ){
		partd='';
		parttitle = '请选中一行显示饼图'
	}else{
		partd=data;	
		parttitle = title+'消费组成饼图';
	}

    $('#feepart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: parttitle
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
        	percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+  Math.round(this.percentage*10)/10 +' %';
                    }
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: '份额',
            data: eval(partd)
        }]
    })
}	

$(function () {
	drawChart()
	var table  = $("#feetable");
	var trs  = table.find("tr");
	//for(var i=0; i<trs.length; i++){
	    //var tr = trs.eq(i);//循环获取每一行
	    trs.bind("click", function(){//为每一行添加click事件
	        var td = $(this).find("td");
	        var title = td.eq(0).text()
		    var chu = td.eq(1).text();
	        var fu = td.eq(2).text();
	        var cha = td.eq(3).text();
	        var zai = td.eq(4).text();
	        data = "[['初诊',"+chu.replace(/,/g,'')+"], ['复诊',"+fu.replace(/,/g,'')+"], ['复查',"+cha.replace(/,/g,'')+"], ['再消费',"+zai.replace(/,/g,'')+"]]";
	        drawChart(data,title)
	    });
	//}
});

$(function () {
    $('.tab a:last').tab('show');
})
$('#huifangtoggle').on('click',function(evt){
       $('#huifangmodal').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
     })
})	
</script>
<script src="/static/Highcharts/js/highcharts.js"></script>
<script src="/static/Highcharts/js/modules/exporting.js"></script>
<?php foreach ($common_sco as $y){echo $y;}?>	
</body>
</html>
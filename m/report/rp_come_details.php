<?php

require "../../core/core.php";
$mod = "patient";
$table = "patient_".$user_hospital_id;
$line_color = array('color:#3a87ad', 'color:#b94a48', 'color:#51a351', 'color:#468847', 'color:#f89406');
$line_color_tip = array("等待", "已到", "未到", "过期", "回访");
$area_id_name = array(0 => "未知", 1 => "本市", 2 => "外地");
$re_arrive_full = array('初诊','复诊','复查','再消费');
//通用

//月初月末时间戳
$bmonth = mktime(0, 0 , 0,date("m"),1,date("Y"))-24*3600*30;
$emonth = mktime(23,59,59,date("m"),date("t"),date("Y"))-24*3600*30;

/*
if ($_GET["date"] && strlen($_GET["date"]) == 6)
{
	$date = $_GET["date"];
} else
{
	$date = date("Ym"); //本月
	$_GET["date"] = $date;
}
*/
$date = date("Ym"); //本月
$_GET["date"] = $date;

$date_time = strtotime(substr($date, 0, 4) . "-" . substr($date, 4, 2) . "-01 0:0:0");
function my_show($arr, $default_value = '', $click = '')
{
	$s = '';
	foreach ( $arr as $v )
	{
		if ($v == $default_value)
		{
			$s .= '<li class="active"><a>' . $v . '</a></li>';
		} else
		{
			$s .= '<li><a href="#" onclick="' . $click . '">' . $v . '</a></li>';
		}
	}
	return $s;
}


// 可用 年,月 数组
$y_array = $m_array = $d_array = array ();
for($i = date ( "Y" ); $i >= (date ( "Y" ) - 2); $i --)
	$y_array [] = $i;
for($i = 1; $i <= 12; $i ++)
	$m_array [] = $i;
for($i = 1; $i <= 31; $i ++)
{
	if ($i <= 28 || checkdate ( date ( "n", $date_time ), $i, date ( "Y", $date_time ) ))
	{
		$d_array [] = $i;
	}
}


if ($_GET["btime"]) {
	$_GET["begin_time"] = strtotime($_GET["btime"]." 0:0:0");
}
if ($_GET["etime"]) {
	$_GET["end_time"] = strtotime($_GET["etime"]." 23:59:59");
}

// 定义当前页需要用到的调用参数:
$link_param = explode(" ", "page sort order key re_arrive begin_time end_time time_type pid show come kefu_23_name kefu_4_name doctor_name xiaofei disease part_id from depart names date list_huifang media");

$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);

// 定义单元格格式:
$list_heads = array(
		"选" => array("align"=>"center"),
		"姓名" => array("align"=>"center", "sort"=>"name", "order"=>"asc"),
		"状态" => array("align"=>"center","sort"=>"name","order"=>"asc"),
		"性别" => array("align"=>"center", "sort"=>"sex", "order"=>"asc"),
		"年龄" => array("align"=>"center", "sort"=>"age", "order"=>"asc"),
		"电话" => array("align"=>"center"),
		"客服" => array("align"=>"center", "sort"=>"author", "order"=>"asc"),
		"预诊时间" => array("align"=>"center", "sort"=>"order_date", "order"=>"desc"),
		"病患类型" => array("align"=>"center", "sort"=>"disease_id", "order"=>"asc"),
		"媒体来源" => array("align"=>"center", "sort"=>"media_from", "order"=>"asc"),
		"部门" => array("align"=>"center", "sort"=>"part_id", "order"=>"asc"),
		"地区" => array("align"=>"center", "sort"=>"is_local", "order"=>"asc")
);


$default_sort = "预诊时间"; 

if ($show == 'today') {
	$begin_time = mktime(0, 0, 0);
	$end_time = mktime(23, 59, 59);
} else if ($show == 'yesterday') {
	$begin_time = mktime(0, 0, 0) - 24 * 3600;
	$end_time = mktime(0, 0, 0);
} else if($show == 'tomorrow'){
	$begin_time = mktime(23, 59, 59) ;
	$end_time = strtotime("+1 day", $begin_time);
}else if ($show == "thismonth") {
	$begin_time = mktime(0,0,0,date("m"),1);
	$end_time = strtotime("+1 month", $begin_time);
} else if ($show == "lastmonth") {
	$end_time = mktime(0,0,0,date("m"),1);
	$begin_time = strtotime("-1 month", $end_time);
}
// 按日期搜索 2010-09-29:
/*
if ($_GET["date"]) {
	$begin_time = strtotime($_GET["date"]." 0:0:0");
	$end_time = strtotime($_GET["date"]." 23:59:59");
}
*/

// 列表显示类:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "table table-hover table-condensed";


// 搜索开始:
$where = array();
// 读取权限:
$today_where = '';

$time_type = empty($time_type) ? 'order_date' : $time_type;

if ($begin_time > 0) {
	$where[] = $time_type.'>='.$begin_time;
}else {
	$where[] = $time_type.'>='.$bmonth;
}
if ($end_time > 0) {
	$where[] = $time_type.'<'.$end_time;
}else
{
	$where[] = $time_type.'<'.$emonth;
}
$where[] ="status = 1";
$sqlwhere = $db->make_where($where);
$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);
// 分页数据:
$count = $db->query("select count(*) as count from $table $sqlwhere $sqlgroup", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 查询:
$time = time();
$today_begin = mktime(0,0,0);
$today_end = $today_begin + 24 * 3600;
$list_data = $db->query("select *,(order_date-$time) as remain_time, if(order_date<$today_begin, 1, if(order_date>$today_end, 2, 3)) as order_sort, if(status=1,2, if(status=2,1,0)) as status_1 from $table $sqlwhere $sqlgroup $sqlsort limit $offset,$pagesize");
$s_sql = $db->sql;

// id => name:
$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
$disease_id_name = $db->query("select id,name from disease", 'id', 'name');
$depart_id_name = $db->query("select id,name from depart where hospital_id=$user_hospital_id", 'id', 'name');

$use_depart = 1;
if (count($depart_id_name) == 0) {
	$use_depart = 0;
	unset($list_heads["科室"]); //没有科室
}


// 搜索的统计数据 2009-05-13 16:46
$res_report = $det_report = '';

$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1") : "where status=1";
$count_come = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status!=1") : "where status!=1";
$count_not = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1 and re_arrive=0") : "where status=1 and re_arrive=0";
$count_chu = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1 and re_arrive=1") : "where status=1 and re_arrive=1";
$count_fu = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1 and re_arrive=2") : "where status=1 and re_arrive=2";
$count_cha = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1 and re_arrive=3") : "where status=1 and re_arrive=3";
$count_zai = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

//echo "<br>".$db->sql;

$count_all = $count_come + $count_not;

$res_report = "总共: <b class='text-error'>".$count_all."</b> &nbsp; 已到: <b class='text-error'>".$count_come."</b> &nbsp; 未到: <b class='text-error'>".$count_not."</b>";

$det_report = "初诊: <b class='text-error'>".$count_chu."</b> &nbsp;复诊: <b class='text-error'>".$count_fu."</b>&nbsp;复查: <b class='text-error'>".$count_cha."</b>&nbsp;再消费: <b class='text-error'>".$count_zai."</b>";


// 对列表数据分组:

	$today_begin = mktime(0,0,0);
	$today_end = $today_begin + 24*3600;
	$yesterday_begin = $today_begin - 24*3600;

	$list_data_part = array();
	/*
	foreach ($list_data as $line) {
		if ($line["order_date"] < $yesterday_begin) {
			$list_data_part[1][] = $line;
		} else if ($line["order_date"] < $today_begin) {
			$list_data_part[2][] = $line;
		} else if ($line["order_date"] < $today_end) {
			if ($line["status"] == 0) {
				$list_data_part[31][] = $line;
			} else if ($line["status"] == 1) {
				$list_data_part[32][] = $line;
			} else {
				$list_data_part[33][] = $line;
			}
			$list_data_part[3][] = $line;
		} else {
			$list_data_part[4][] = $line;
		}
	}
	
	*/
        
	$i = $bmonth;

	while($i <= $emonth)
	{
		$list_data_title[] = array("id"=>0, "name"=>date('Y-m-d',$i+3600*24));
		
		foreach ($list_data as $row =>$line)
		{
			if($line["order_date"] > $i&&$line["order_date"] < $i+3600*24)
			{
				
				$list_data_part[$row][] = $line;
			}
		}
		
		$list_data_part = array_merge($list_data_title,$list_data_part);
		
		$i=$i+3600*24;
	}
	$list_data = $list_data_part;
	echo count($line);
	/*
	while($i <= $emonth)
	{
		foreach ($list_data as $row =>$line) {
			
			if($line["order_date"] > $i&&$line["order_date"] < $i+3600*24)
			{
				
				$list_data_part[$row][] = $line;
				$list_data_title[] = array("id"=>0, "name"=>date('Y-m-d',$i+3600*24));
			}
			
			$list_data = array_merge($list_data_title, $list_data_part[$row]);
		}
		
		$i=$i+3600*24;
	}
	*/
    

$back_url = make_back_url();

// 表格数据:
foreach ($list_data as $li) {
	$id = $li["id"];
	if ($id == 0) {
		$t->add_tip_line($li["name"]);
	} else {
		$r = array();
		$r["选"]  = '<input type="checkbox" value="'.$li['id'].'">';
		$r["姓名"] = $li["name"];
		$r["状态"] = $re_arrive_full[$li['re_arrive']];
		$r["性别"] = $li["sex"];
		$r["年龄"] = $li["age"] > 0 ? $li["age"] : "";
		$r["电话"] = hide_tel($li["tel"],$li['doctor']);    //针对导医设置
		$r["预诊时间"] = @date("m-d H:i", $li["order_date"]);

		$dis_text = array();
		foreach (explode(",", $li["disease_id"]) as $dis_id) {
			if ($dis_id > 0) $dis_text[] = $disease_id_name[$dis_id];
		}
		$r["病患类型"] = implode("|", $dis_text);
		$r["媒体来源"] = $li["media_from"];
		$r["关键词"] = $li["engine_key"];
		$r["部门"] = $part_id_name[$li["part_id"]];
		$r["科室"] = $li["depart"] > 0 ? $depart_id_name[$li["depart"]] : "";
		$r["地区"] = $li["is_local"] == 2 ? $li["area"] : $area_id_name[$li["is_local"]];
		$r["客服"] = $li["author"];
		$r["赴约情况"] = $status_array[$li["status"]];
		$r["_tr_"] = $_tr;

		$t->add($r);
	}
}


$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

//客服
$admin_name = $db->query("select realname from sys_admin", "", "realname");
$author_name = $db->query("select distinct author from $table order by binary author", "", "author");
$kefu_23_list = array_intersect($admin_name, $author_name);
//疾病
$disease_list = $db->query("select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id");
$depart_list = $db->query("select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pinfo["title"]; ?></title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
.breadcrumb ul {
	margin: 0
}
</style>
</head>

<body id="bodyobj">
	<!-- 头部 begin -->
	<header class="jumbotron subhead" style="margin-bottom: 10px;">
		<div class="breadcrumb">
			<ul style="float: left">
				<li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
				<li class="active">
				    <span style="color: #0088cc; font-weight: bolder">
				        <?=$hospital_id_name[$user_hospital_id];?>
				    </span>
				    - 就诊统计
				</li>
	            <?php if(isset($_GET['btime'])&&$_GET['btime']!=''){?>
	            <li>
	                <span class="divider">/</span><?=$_GET['btime']?>
	            <i class=" icon-arrow-right"></i>
	            </li>
	            <?php }?>
	            <?php if(isset($_GET['etime'])&&$_GET['etime']!=''){?>
	            <li><?=$_GET['etime']?></li>
	            <?php }?>
			</ul>

			<ul style="float: left; margin-left: 20px">
				<li width="33%">&nbsp;<b>统计数据:</b> <?php echo $res_report.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$det_report; ?></li>
			</ul>
			<div class="clear"></div>
		</div>
	</header>

	<div class="row-fluid show-grid">
		<div class="span9">
			<div class="left tb_margin_right"><?php echo $power->show_button("add"); ?></div>
			<form action="?" method="GET" style="display: inline;" id="dateform">
				<select name="kefu_23_name" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="客服/咨询">
					<option value='' style="color: gray" value="">--请选择--</option>
	                <?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>
	            </select> 
	            <select name="disease" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="项目/病种">
					<option value='' style="color: gray">--请选择--</option>
		            <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>
	            </select>
				<div class="pagination pagination-small span2" style="float: left; margin: 0">
					<ul>
					    <?php echo my_show($y_array, date("Y", $date_time)); ?>
					</ul>
				</div>

				<div class="pagination pagination-small span5" style="float: left; margin: 0">
					<ul>
					    <?php echo my_show($m_array, date("m", $date_time), "return update_date(2,this)"); ?>
					</ul>
				</div>

				<input type="hidden" name="from" value="search" />
				<input type="hidden" name="btime" value="<?=@isset($_GET['btime'])?$_GET['btime']:'' ?>" /> 
				<input type="hidden" name="etime" value="<?=@isset($_GET['etime'])?$_GET['etime']:'' ?>" /> 
				<input type="hidden" name="time_type" value="<?=@isset($_GET['time_type'])?$_GET['time_type']:'' ?>" /> 
				<input type="hidden" name="come" id="comeostatus" />
			</form>
		</div>

		<div class="pagination-right" style="float: right">
			<form name="topform" method="GET" style="margin-bottom: 0">
				<input name="key" type="text" value="<?php echo $_GET["key"]; ?>" class="input-medium search-query" placeholder="姓名、电话、QQ、备注">&nbsp; <input type="submit" class="btn" value="搜索" style="font-weight: bold" title="点击搜索">&nbsp;
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<!-- 头部 end -->

	<div class="space"></div>
	<!-- 数据列表 begin --> 
    <?php echo $t->show(); ?>
    <!-- 数据列表 end -->

	<!-- 分页链接 begin -->
	<div class="footer_op">
		<div class="footer_op_left"></div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- 分页链接 end -->

	<!-- <?php echo $s_sql; ?> -->
<?php foreach ($common_sco as $y){echo $y;}?>	
<script>
$("a[rel=tooltip],input[rel=tooltip],div[rel=tooltip],select[rel=tooltip],button[rel=tooltip]").tooltip()
$('a[rel=popover],button[rel=popover]').popover()
$('#advancedsearch').on('click',function(evt){
	 $('#advancedsearchmodel').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
     })
});
</script>
</body>
</html>
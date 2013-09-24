<?php
/**
 * 病人回访   (只显示需要回访的病人)
 * 
 * @author fangyang(278294861)
 * @since  20130731
 */
require "../../core/core.php";
$mod = "patient";
$table = "patient_" . $user_hospital_id;

// 提交回访
if (isset ( $_POST ) && isset ( $_POST ['id'] ) && $_POST ['model'] === 'hfedit')
{
	$r = array ();
	$po = &$_POST;
	$id = $po ['id'];
	$content = convert ( $po ['content'], 'utf-8', 'GB2312' );
	$huifang_date = strtotime ( $po ['huifang_date'] );
	$oldline = $db->query ( "select * from $table where id=$id limit 1", 1 );
	$r ["huifang"] = $oldline ["huifang"] . "<b>" . date ( "Y-m-d H:i" ) . " [" . $realname . "]</b>:  " . $content . "<br/>";
	$r ['huifang_date'] = $huifang_date;
	$sqldata = $db->sqljoin ( $r );
	
	$sql = "update $table set $sqldata where id='$id'";
	
	$return = $db->query ( $sql );
	
	if ($return)
	{
		echo json_encode ( array ( 
				'result' => 'success' 
		) );
	} else
	{
		echo json_encode ( array ( 
				'result' => 'failure' 
		) );
	}
	
	exit ();
}

// 字典
$hospital_id_name = $db->query ( "select id,name from hospital", 'id', 'name' );
// 客服
$admin_name = $db->query ( "select realname from sys_admin", "", "realname" );
$author_name = $db->query ( "select distinct author from $table order by binary author", "", "author" );
$kefu_23_list = array_intersect ( $admin_name, $author_name );
// 疾病
$disease_list = $db->query ( "select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id" );
$depart_list = $db->query ( "select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id" );

$link_param = explode ( " ", "page sort order key re_arrive begin_time end_time sdate edate pid show status timetype kefu_23_name kefu_4_name doctor_name xiaofei disease part_id from depart names date list_huifang media" );

$param = array ();
foreach ( $link_param as $s )
{
	$param [$s] = $_GET [$s];
}
extract ( $param );

// 定义单元格格式:
$list_heads = array ( 
		"选" => array ( 
				"width" => "1%", 
				"align" => "center" 
		), 
		"姓名" => array ( 
				"width" => "4%", 
				"align" => "center", 
				"sort" => "name", 
				"order" => "asc" 
		), 
		"性别" => array ( 
				"width" => "4%", 
				"align" => "center", 
				"sort" => "sex", 
				"order" => "asc" 
		), 
		"电话" => array ( 
				"width" => "8.05%", 
				"align" => "center", 
				"sort" => "tel", 
				"order" => "asc" 
		), 
		"咨询内容" => array ( 
				"width" => "6.25%", 
				"align" => "left", 
				"sort" => "content", 
				"order" => "asc" 
		), 
		"预诊时间" => array ( 
				"width" => "8.05%", 
				"align" => "center", 
				"sort" => "order_sort", 
				"order" => "desc" 
		), 
		"病患类型" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "disease_id", 
				"order" => "asc" 
		), 
		"媒体来源" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "media_from", 
				"order" => "asc" 
		), 
		"部门" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "part_id", 
				"order" => "asc" 
		), 
		"备注" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "memo", 
				"order" => "asc" 
		), 
		"客服" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "author", 
				"order" => "asc" 
		), 
		"回访" => array ( 
				"width" => "4.05%", 
				"align" => "center", 
				"sort" => "huifang", 
				"order" => "desc" 
		), 
		"添加时间" => array ( 
				"width" => "6.85%", 
				"align" => "center", 
				"sort" => "addtime", 
				"order" => "desc" 
		), 
		"操作" => array ( 
				"width" => "10%", 
				"align" => "center" 
		) 
)
;

// 默认排序方式:
$default_sort = "添加时间"; // 客服或管理员则关注今天新增加了多少病人
$default_order = "desc";
$end_time = mktime ( 0, 0, 0 );
if ($timetype === 'week')
{
	$begin_time = strtotime ( "-1 week", $end_time );
} else if ($timetype === 'halfmonth')
{
	$begin_time = strtotime ( "-2 week", $end_time );
} else if ($timetype === 'month')
{
	$begin_time = strtotime ( "-1 month", $end_time );
} else if ($timetype == "halfyear")
{
	$begin_time = strtotime ( "-6 month", $end_time );
}else if($timetype === 'none')
{
	if($_GET['sdate']||$_GET['edate'])
	{
	    $begin_time = $sdate;
	    $end_time = $edate;
	}
}

// 按日期搜索 2010-09-29:
if ($_GET ["date"])
{
	$begin_time = strtotime ( $_GET ["date"] . " 0:0:0" );
	$end_time = strtotime ( $_GET ["date"] . " 23:59:59" );
}

// 列表显示类:
$t = load_class ( "table" );
$t->set_head ( $list_heads, $default_sort, $default_order );
$t->set_sort ( $_GET ["sort"], $_GET ["order"] );
$t->param = $param;
$t->table_class = "table table-condensed";

// 搜索开始:
$where = array ();

if ($status != '')
{
	if ($status == 1)
	{
		$where [] = "huifang !=''";
	} else if ($status == 0)
	{
		$where [] = "huifang =''";
	}
}

if ($key = trim ( stripslashes ( $key ) ))
{
	$sk = "%{$key}%";
	$fields = explode ( " ", "name tel qq zhuanjia_num content memo" );
	$sfield = array ();
	foreach ( $fields as $_tm )
	{
		$sfield [] = "binary $_tm like '{$sk}'";
	}
	$where [] = "(" . implode ( " or ", $sfield ) . ")";
}

// 读取权限:
$today_where = '';

// 强制显示未回访的病人:
$where [] = "status = 0";
// 回访权限
if (! $debug_mode)
{
	if (@$purview ['show_huifang_all'] == 0)
	{
		$where [] = "binary author = '" . $realname . "'";
	}
}
$time_type = empty ( $time_type ) ? 'order_date' : $time_type;

if ($begin_time > 0)
{
	$where [] = $time_type . '>=' . $begin_time;
}
if ($end_time > 0)
{
	$where [] = $time_type . '<' . $end_time;
}

if ($pid != '')
{
	$where [] = "pid='$pid'";
}
if ($re_arrive != '')
{
	$where [] = "re_arrive='$re_arrive'";
}
if ($kefu_23_name != '')
{
	$where [] = "author='$kefu_23_name'";
}
if ($kefu_4_name != '')
{
	$where [] = "jiedai='$kefu_4_name'";
}
if ($doctor_name != '')
{
	$where [] = "doctor='$doctor_name'";
}
if ($disease != '')
{
	$where [] = "disease_id=$disease";
}
if ($part_id != '')
{
	$where [] = "part_id=$part_id";
}
if ($depart != '')
{
	$where [] = "depart=$depart";
}
if ($list_huifang)
{
	$where [] = "huifang like '%[" . $realname . "]%'";
}
if ($media)
{
	$where [] = "media_from='" . trim ( $media ) . "'";
}
$sqlwhere = $db->make_where ( $where );
$sqlsort = $db->make_sort ( $list_heads, $sort, $order, $default_sort, $default_order );
// 分页数据:
$count = $db->query ( "select count(*) as count from $table $sqlwhere $sqlgroup", 1, "count" );
$pagecount = max ( ceil ( $count / $pagesize ), 1 );
$page = max ( min ( $pagecount, intval ( $page ) ), 1 );
$offset = ($page - 1) * $pagesize;

// 查询:
$time = time ();
$today_begin = mktime ( 0, 0, 0 );
$today_end = $today_begin + 24 * 3600;
$list_data = $db->query ( "select *,(order_date-$time) as remain_time, if(order_date<$today_begin, 1, if(order_date>$today_end, 2, 3)) as order_sort  from $table $sqlwhere $sqlgroup $sqlsort limit $offset,$pagesize" );
$s_sql = $db->sql;
// id => name:
$hospital_id_name = $db->query ( "select id,name from hospital", 'id', 'name' );
$part_id_name = $db->query ( "select id,name from sys_part", 'id', 'name' );
$disease_id_name = $db->query ( "select id,name from disease", 'id', 'name' );
$depart_id_name = $db->query ( "select id,name from depart where hospital_id=$user_hospital_id", 'id', 'name' );

$use_depart = 1;
if (count ( $depart_id_name ) == 0)
{
	$use_depart = 0;
	unset ( $list_heads ["科室"] ); // 没有科室
}

// 搜索的统计数据 2009-05-13 16:46
$res_report = $per_report = $tod_report = '';
// 全部数据
$sqlwhere_s = $sqlwhere ? ($sqlwhere . " and huifang !=''") : "where huifang !=''";
$is_hf = $db->query ( "select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count" );
$sqlwhere_s = $sqlwhere ? ($sqlwhere . " and huifang =''") : "where huifang =''";
$is_hf_not = $db->query ( "select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count" );
$hf_all = $is_hf + $is_hf_not;

// 个人数据
$sqlwhere_p = $sqlwhere ? ($sqlwhere . " AND binary author = '" . $realname . "' AND huifang !=''") : "WHERE binary author = '" . $realname . "' AND huifang !=''";
$p_is_hf = $db->query ( "select count(*) as count from $table $sqlwhere_p $sqlgroup order by id desc", 1, "count" );
$sqlwhere_p = $sqlwhere ? ($sqlwhere . " AND binary author = '" . $realname . "' AND huifang =''") : "WHERE binary author = '" . $realname . "' AND huifang =''";
$p_is_hf_not = $db->query ( "select count(*) as count from $table $sqlwhere_p $sqlgroup order by id desc", 1, "count" );
$p_hf_all = $p_is_hf + $p_is_hf_not;

// 今日数据
$t_time_type = "order_date";
/*
 * $per_where = "huifang REGEXP '<b>".@date('Y-m-d',$v [0]).".{5,10}$realname'";
 * $today_where = ($today_where ? ($today_where." and") : "")."
 * $t_time_type>=".$today_begin; $today_where .= " and
 * $t_time_type<".$today_end; $sqlwhere_t = "where ".($today_where ?
 * ($today_where) : ""); $t_is_hf = $db->query("select count(*) as count from
 * $table $sqlwhere_t AND $per_where order by id desc", 1, "count");
 */

// echo "<br>".$db->sql;

$res_report = "总共: <b class='text-error'>" . $hf_all . "</b> &nbsp; 已回访: <b class='text-error'>" . $is_hf . "</b> &nbsp; 未回访: <b class='text-error'>" . $is_hf_not . "</b>";
$per_report = "总共: <b class='text-error'>" . $p_hf_all . "</b> &nbsp; 已回访: <b class='text-error'>" . $p_is_hf . "</b> &nbsp; 未回访: <b class='text-error'>" . $p_is_hf_not . "</b>";
// $tod_report = "已回访: <b class='text-error'>".$t_is_hf."</b>";
// 对列表数据分组:
if ($sort == "添加时间" || ($sort == "" && $default_sort == "添加时间"))
{
	if ($order == "desc" || $default_order == "desc")
	{
		$today_begin = mktime ( 0, 0, 0 );
		$today_end = $today_begin + 24 * 3600;
		$yesterday_begin = $today_begin - 24 * 3600;
		
		$list_data_part = array ();
		foreach ( $list_data as $line )
		{
			if ($line ["addtime"] < $yesterday_begin)
			{
				$list_data_part [3] [] = $line;
			} else if ($line ["addtime"] < $today_begin)
			{
				$list_data_part [2] [] = $line;
			} else if ($line ["addtime"] < $today_end)
			{
				$list_data_part [1] [] = $line;
			}
		}
		
		$list_data = array ();
		if (count ( $list_data_part [1] ) > 0)
		{ // 有今天的数据:
			$list_data [] = array ( 
					"id" => 0, 
					"name" => "今天 [" . count ( $list_data_part [1] ) . "]" 
			);
			$list_data = array_merge ( $list_data, $list_data_part [1] );
		}
		if (count ( $list_data_part [2] ) > 0)
		{ // 有昨天的数据:
			$list_data [] = array ( 
					"id" => 0, 
					"name" => "昨天 [" . count ( $list_data_part [2] ) . "]" 
			);
			$list_data = array_merge ( $list_data, $list_data_part [2] );
		}
		if (count ( $list_data_part [3] ) > 0)
		{ // 有前天的数据:
			$list_data [] = array ( 
					"id" => 0, 
					"name" => "前天或更早 [" . count ( $list_data_part [3] ) . "]" 
			);
			$list_data = array_merge ( $list_data, $list_data_part [3] );
		}
		unset ( $list_data_part );
	}
} else if ($sort == "媒体来源" || ($sort == "" && $default_sort == "媒体来源"))
{
	$list_data_part = array ();
	foreach ( $list_data as $line )
	{
		if ($line ["media_from"] == "网络")
		{
			$list_data_part [1] [] = $line;
		} else if ($line ["media_from"] == "电话")
		{
			$list_data_part [2] [] = $line;
		} else
		{
			$list_data_part [3] [] = $line;
		}
	}
	
	$list_data = array ();
	if (count ( $list_data_part [1] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "网络 [" . count ( $list_data_part [1] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [1] );
	}
	if (count ( $list_data_part [2] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "电话 [" . count ( $list_data_part [2] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [2] );
	}
	if (count ( $list_data_part [3] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "其他 [" . count ( $list_data_part [3] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [3] );
	}
	unset ( $list_data_part );
} else if ($sort == "预诊时间" || ($sort == "" && $default_sort == "预诊时间"))
{
	$today_begin = mktime ( 0, 0, 0 );
	$today_end = $today_begin + 24 * 3600;
	$yesterday_begin = $today_begin - 24 * 3600;
	
	$list_data_part = array ();
	foreach ( $list_data as $line )
	{
		if ($line ["order_date"] < $yesterday_begin)
		{
			$list_data_part [1] [] = $line;
		} else if ($line ["order_date"] < $today_begin)
		{
			$list_data_part [2] [] = $line;
		} else if ($line ["order_date"] < $today_end)
		{
			if ($line ["status"] == 0)
			{
				$list_data_part [31] [] = $line;
			} else if ($line ["status"] == 1)
			{
				$list_data_part [32] [] = $line;
			} else
			{
				$list_data_part [33] [] = $line;
			}
			$list_data_part [3] [] = $line;
		} else
		{
			$list_data_part [4] [] = $line;
		}
	}
	
	$list_data = array ();
	if (count ( $list_data_part [31] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "今天 (等待中) [" . count ( $list_data_part [31] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [31] );
	}
	if (count ( $list_data_part [32] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "今天 (已到) [" . count ( $list_data_part [32] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [32] );
	}
	if (count ( $list_data_part [33] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "今天 (不来了) [" . count ( $list_data_part [33] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [33] );
	}
	if (count ( $list_data_part [4] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "明天或以后 (时间未到) [" . count ( $list_data_part [4] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [4] );
	}
	if (count ( $list_data_part [2] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "昨天 [" . count ( $list_data_part [2] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [2] );
	}
	if (count ( $list_data_part [1] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "前天或更早 [" . count ( $list_data_part [1] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [1] );
	}
	unset ( $list_data_part );
}

$back_url = make_back_url ();

// 表格数据:
foreach ( $list_data as $li )
{
	$id = $li ["id"];
	if ($id == 0)
	{
		$t->add_tip_line ( $li ["name"] );
	} else
	{
		$r = array ();
		$r ["选"] = '<input type="checkbox" value="' . $li ['id'] . '">';
		$r ["姓名"] = $li ["name"];
		$r ["性别"] = $li ["sex"];
		$r ["电话"] = hide_tel ( $li ["tel"], $li ['doctor'] ); // 针对导医设置
		$r ["咨询内容"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='" . cut ( $li ["content"], 250, "..." ) . "'>" . cut ( $li ["content"], 10, ".." ) . "</a>";
		$r ["预诊时间"] = @date ( "m-d H:i", $li ["order_date"] );
		
		$dis_text = array ();
		foreach ( explode ( ",", $li ["disease_id"] ) as $dis_id )
		{
			if ($dis_id > 0)
				$dis_text [] = $disease_id_name [$dis_id];
		}
		$r ["病患类型"] = implode ( "|", $dis_text );
		$r ["媒体来源"] = $li ["media_from"];
		$r ["关键词"] = $li ["engine_key"];
		$r ["部门"] = $part_id_name [$li ["part_id"]];
		$r ["科室"] = $li ["depart"] > 0 ? $depart_id_name [$li ["depart"]] : "";
		$r ["备注"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='" . cut ( $li ["memo"], 250, "..." ) . "'>" . cut ( $li ["memo"], 10, "" ) . "</a>";
		$r ["客服"] = $li ["author"];
		$r ["赴约情况"] = $status_array [$li ["status"]];
		$r ["回访"] = $li ["huifang"] != '' ? ('<a  rel="popover"  data-placement="left" data-content="' . trim ( $li ["huifang"] ) . '" data-original-title="回访记录"><i style="color:#333333" class="icon-tags"></i></a>') : '';
		$r ["添加时间"] = @date ( "m-d", $li ["addtime"] );
		
		// 操作:
		$op = array ();
		if (check_power ( "view" ))
		{
			$op [] = "<a href='javascript:void(0)' style='color:#111' onclick=\"patientbox('" . $li ['name'] . "',$id,'patient','edit')\" class='op'><i class='icon-eye-open'></i></a>";
		}
		$hfhostory = trim ( preg_replace ( '/\s/', '', $li ['huifang'] ) );
		$op [] = "<a href='javascript:void(0)' onclick='parent.huifangm(" . $li ['id'] . ",\"" . $hfhostory . "\")'><i class='icon-comment'></i></a>";
		
		$r ["操作"] = implode ( " ", $op );
		
		// 行附加属性;//如果今天回访过的，那就换个颜色
		if (preg_match ( "/" . @date ( "Y-m-d" ) . "/", $li ["huifang"] ))
		{
			$_tr = "class='line' id='" . $li ['id'] . "' style='background:#fcf8e3;font-weight:bold'";
		} else if (preg_match ( "/" . @date ( "Y-m-d", $li ["order_date"] ) . "/", $li ["huifang"] ))
		{
			$_tr = "class='line' id='" . $li ['id'] . "' style='background:#fcf8e3'";
		} else
		{
			$_tr = "class='line' id='" . $li ['id'] . "' ";
		}
		
		$color_status = $li ["status"];
		if ($color_status == 0 && date ( "Ymd", $li ["order_date"] ) < date ( "Ymd" ))
		{
			$color_status = 3;
		}
		if ($color_status == 0 && $li ["huifang"] != '')
		{
			$color_status = 4;
		}
		$color = $line_color [$color_status];
		
		// 2010-12-17 修改，两个月之后的病人，颜色变一下
		if ($li ["order_date"] > strtotime ( "+2 month" ))
		{
			$color = "#FF00FF";
		}
		$r ["_tr_"] = $_tr;
		
		$t->add ( $r );
	}
}

// 分页
$pagelink = pagelinkc ( $page, $pagecount, $count, make_link_info ( $link_param, "page" ), "button" );
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<title><?php echo $pinfo["title"]; ?></title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<script language="javascript">

function set_come(id, come_value) {
	var xm = new ajax();
	xm.connect('http/patient_set_come.php', 'GET', 'id='+id+'&come='+come_value, set_come_do);
}
function set_come_do(o) {
	var out = ajax_out(o);
	if (out["status"] == 'ok') {
		byid("come_"+out["id"]).innerHTML = ['等待', '已到', '未到'][out["come"]];
		byid("come_"+out["id"]+"_"+out["come"]).style.display = 'none';
		byid("come_"+out["id"]+"_"+(out["come"]==1 ? 2 : 1)).style.display = 'inline';
		byid("list_line_"+out["id"]).style.color = ['label label-inverse', 'label label-important', 'label'][out["come"]];
	} else {
		alert("设置失败，请稍后再试！");
	}
}

function set_xiaofei(id, value) {
	var xm = new ajax();
	xm.connect('/http/patient_set_xiaofei.php', 'GET', 'id='+id+'&xiaofei='+value, set_xiaofei_do);
}

function set_xiaofei_do(o) {
	var out = ajax_out(o);
	if (out["status"] == 'ok') {
		if (out["xiaofei"] == '0') {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',1); return false;">×</a>';
		} else {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',0); return false;">√</a>';
		}
		byid("xiaofei_"+out["id"]).innerHTML = button;
	} else {
		alert("设置失败，请稍后再试！");
	}
}
</script>
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
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?=$hospital_id_name[$user_hospital_id];?></span> - 回访列表</li>
	             <?php if(isset($_GET['sdate'])&&$_GET['sdate']!=''){?><li class="text-error"><span class="divider">/</span><?=date('Y-m-d',$_GET['sdate'])?>
	             <i class=" icon-arrow-right"></i></li><?php }?>
	             <?php if(isset($_GET['edate'])&&$_GET['edate']!=''){?><li class="text-error"><?=date('Y-m-d',$_GET['edate'])?></li><?php }?>
			</ul>

			<ul style="float: left; margin-left: 20px">
			    <?php if(!$debug_mode&&@$purview['show_huifang_all']==0){?>
			    <li width="33%">&nbsp;<b>个人数据:</b> <?php echo $per_report?></li>
			    <?php }else{?>
			    <li width="33%">&nbsp;<b>统计数据:</b> <?php echo $res_report?></li> &nbsp;/&nbsp;
				<li width="33%">&nbsp;<b>个人数据:</b> <?php echo $per_report?></li>
			    <?php }?> 
			</ul>
			<!-- <ul style="float:right">
			    <li width="33%">&nbsp;<b>今日数据:</b> <?php echo $tod_report?></li>
			</ul>
			 -->
			<div class="clear"></div>
		</div>
	</header>

	<div id="headfixed" class="row-fluid show-grid">
		<div class="span9">
			<div class="left tb_margin_right"><?php echo $power->show_button("add"); ?></div>
			<form action="?" method="GET" style="display: inline;" id="sform">
				<div class="btn-group left tb_margin_right" data-toggle="buttons-radio" data-original-title="回访状态" rel="tooltip">
					<button class="status btn <?=@$_GET['status']=='1'?'active':''?>" type="button" onclick="document.getElementById('hfstatus').value='1';this.form.submit()">已回</button>
					<button class="status btn <?=(@$_GET['status']=='2'||!isset($_GET['status'])||$_GET['status']=='')?'active':''?>" type="button" onclick="document.getElementById('hfstatus').value='2';this.form.submit()">全部</button>
					<button class="status btn <?=@$_GET['status']=='0'?'active':''?>" type="button" onclick="document.getElementById('hfstatus').value='0';this.form.submit()">未回</button>
				</div>
				<?php if($debug_mode||@$purview['show_huifang_all']!=0):?>
				<select name="kefu_23_name" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="客服/咨询">
					<option value='' style="color: gray" value="">--选择客服--</option>
	                <?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>
	            </select> 
	            <?php endif?>
	            <select name="disease" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="项目/病种">
					<option value='' style="color: gray">--选择病种--</option>
		            <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>
	            </select> 
	            <select name="timetype" class="span2 left tb_margin_right" onchange="(this.value=='custom')?customdate():this.form.submit()" rel="tooltip" data-original-title="时间段">
					<option value='none' style="color: gray">--选择时间段--</option>
					<option value='week'>最近一周</option>
					<option value='halfmonth'>最近半个月</option>
					<option value='month'>最近一个月</option>
					<option value='halfyear'>最近半年</option>
					<option value='custom'>--自定义时间段--</option>
				</select> 
				<?php if(isset($_GET['timetype'])):?>
				<script>
				$("select[name=timetype]").val("<?=$_GET['timetype']?>");   
			    </script>
				<?php endif?>
			    
				<input class="btn form_datetime span1 left tb_margin_right" type="text" rel="tooltip" data-original-title="按日显示" data-date="<?=date('Y-m-d')?>" name="date" value="<?=@$_GET['date']==''?'':$_GET['date']?>" onchange="this.form.submit()" /> 
				<input type="hidden" name="sdate" id="h_sdate" value="<?=@isset($_GET['sdate'])?$_GET['sdate']:'' ?>" />
				<input type="hidden" name="edate" id="h_edate" value="<?=@isset($_GET['edate'])?$_GET['edate']:'' ?>" /> 
				<input type="hidden" name="from" value="search" />
				<input type="hidden" name="status" id="hfstatus" value="<?php echo isset($_GET['status'])?$_GET['status']:''?>" />
				<button type="button" class="btn left tb_margin_right" onclick="location='patient.list.huifang.php'">清除搜索</button>
			</form>
		</div>

		<div class="pagination-right" style="float: right">
			<form name="topform" method="GET" style="margin-bottom: 0">
				<input name="key" type="text" value="<?php echo $_GET["key"]; ?>" class="input-medium search-query" placeholder="搜索">&nbsp; <input type="submit" class="btn" value="搜索" style="font-weight: bold" title="点击搜索">&nbsp;
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
		<div class="footer_op_left">
			<!-- <button onclick="select_all()" class="btn btn-small pull-right toggle-all">全选</button> -->
		</div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- 分页链接 end -->

	<!-- 自定义时间段 START -->
	<div id="customdatemodal" class="modal hide fade" data-backdrop='false'>
		<form class="form-vertical">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>时间段</h3>
			</div>
			<div class="modal-body" style="text-align: center">
				<fieldset>
					<div class="control-group">
						<div class="controls">
							从：<input type="text" class="span2" id="sdate" value="<?=@date('Y-m-d',$_GET['sdate'])?>"/>&nbsp;&nbsp;到：<input type="text" class="span2" id="edate" value="<?=@date('Y-m-d',$_GET['edate'])?>"/>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" value="关闭" data-dismiss="modal"/> 
				<input type="button" class="btn btn-primary date-primary" value="查找" />
			</div>
		</form>
	</div>
	<!-- 自定义时间段 END-->



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

function customdate(){
	 $('#customdatemodal').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
     });

	$("select[name=timetype]").val('none');    
}

$('#basicedit').on('click',function(evt){
	$('#basicedit').scojs_modal({
		  title: '病人基本资料修改',
		  nobackdrop:true,
		  keyboard:true,
		 // target:parent.document,
		 // onClose:function(){destroyModal()}
	});
	function destroyModal(){
	}
    
});

//jquery 提交表单
$(".date-primary").click(function(){
    $("#sform").submit();
});

//点击按钮，取消所有操作
$('#button-toggle').on('click',function(evt){
	$(':checkbox').removeAttr('checked');
	$("#button-toggle").css("background-image","")
	$("#option-toggle").css("display","none")
})

$(':checkbox').each(function(){
    $(this).click(function(){
        if($(this).attr('checked')){
             $(':checkbox').removeAttr('checked');
             $('tr').css({'background':'','font-weight':''})
             $(this).attr('checked','checked');
             var line = $(this).val();
             $('#'+line).css({'background':'#fcf8e3','font-weight':'bold'})
         }
        
         if($(this).attr("checked")=='checked')
			{
				$("#button-toggle").css("background-image","none")
				$("#option-toggle").css("display","block")
			}else
			{
				$("#button-toggle").css("background-image","")
				$("#option-toggle").css("display","none")
			}
		
  
    })
})	 


$(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd',
    todayBtn: true,
    minView:'month',
    pickerPosition: "bottom-left"
}).on('changeDate',function(ev){});
                    
$("#sdate").datetimepicker({
	format: "yyyy-mm-dd",
	autoclose: true,
	todayBtn: true,
	minuteStep: 10,
	todayBtn: true,
    minView:'month',
    maxView:'year',
	pickerPosition: "bottom-left"
}).on('changeDate',function(ev){$("#h_sdate").val(ev.date.valueOf()/1000)});

$("#edate").datetimepicker({
	format: "yyyy-mm-dd",
	autoclose: true,
	todayBtn: true,
	minuteStep: 10,
	todayBtn: true,
    minView:'month',
    maxView:'year',
	pickerPosition: "bottom-left"
}).on('changeDate',function(ev){$("#h_edate").val(ev.date.valueOf()/1000)});					
					
</script>
</body>
</html>
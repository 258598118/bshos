<?php
/**
 * ���˻ط�   (ֻ��ʾ��Ҫ�طõĲ���)
 * 
 * @author fangyang(278294861)
 * @since  20130731
 */
require "../../core/core.php";
$mod = "patient";
$table = "patient_" . $user_hospital_id;

// �ύ�ط�
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

// �ֵ�
$hospital_id_name = $db->query ( "select id,name from hospital", 'id', 'name' );
// �ͷ�
$admin_name = $db->query ( "select realname from sys_admin", "", "realname" );
$author_name = $db->query ( "select distinct author from $table order by binary author", "", "author" );
$kefu_23_list = array_intersect ( $admin_name, $author_name );
// ����
$disease_list = $db->query ( "select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id" );
$depart_list = $db->query ( "select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id" );

$link_param = explode ( " ", "page sort order key re_arrive begin_time end_time sdate edate pid show status timetype kefu_23_name kefu_4_name doctor_name xiaofei disease part_id from depart names date list_huifang media" );

$param = array ();
foreach ( $link_param as $s )
{
	$param [$s] = $_GET [$s];
}
extract ( $param );

// ���嵥Ԫ���ʽ:
$list_heads = array ( 
		"ѡ" => array ( 
				"width" => "1%", 
				"align" => "center" 
		), 
		"����" => array ( 
				"width" => "4%", 
				"align" => "center", 
				"sort" => "name", 
				"order" => "asc" 
		), 
		"�Ա�" => array ( 
				"width" => "4%", 
				"align" => "center", 
				"sort" => "sex", 
				"order" => "asc" 
		), 
		"�绰" => array ( 
				"width" => "8.05%", 
				"align" => "center", 
				"sort" => "tel", 
				"order" => "asc" 
		), 
		"��ѯ����" => array ( 
				"width" => "6.25%", 
				"align" => "left", 
				"sort" => "content", 
				"order" => "asc" 
		), 
		"Ԥ��ʱ��" => array ( 
				"width" => "8.05%", 
				"align" => "center", 
				"sort" => "order_sort", 
				"order" => "desc" 
		), 
		"��������" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "disease_id", 
				"order" => "asc" 
		), 
		"ý����Դ" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "media_from", 
				"order" => "asc" 
		), 
		"����" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "part_id", 
				"order" => "asc" 
		), 
		"��ע" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "memo", 
				"order" => "asc" 
		), 
		"�ͷ�" => array ( 
				"width" => "6.25%", 
				"align" => "center", 
				"sort" => "author", 
				"order" => "asc" 
		), 
		"�ط�" => array ( 
				"width" => "4.05%", 
				"align" => "center", 
				"sort" => "huifang", 
				"order" => "desc" 
		), 
		"���ʱ��" => array ( 
				"width" => "6.85%", 
				"align" => "center", 
				"sort" => "addtime", 
				"order" => "desc" 
		), 
		"����" => array ( 
				"width" => "10%", 
				"align" => "center" 
		) 
)
;

// Ĭ������ʽ:
$default_sort = "���ʱ��"; // �ͷ������Ա���ע�����������˶��ٲ���
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

// ���������� 2010-09-29:
if ($_GET ["date"])
{
	$begin_time = strtotime ( $_GET ["date"] . " 0:0:0" );
	$end_time = strtotime ( $_GET ["date"] . " 23:59:59" );
}

// �б���ʾ��:
$t = load_class ( "table" );
$t->set_head ( $list_heads, $default_sort, $default_order );
$t->set_sort ( $_GET ["sort"], $_GET ["order"] );
$t->param = $param;
$t->table_class = "table table-condensed";

// ������ʼ:
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

// ��ȡȨ��:
$today_where = '';

// ǿ����ʾδ�طõĲ���:
$where [] = "status = 0";
// �ط�Ȩ��
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
// ��ҳ����:
$count = $db->query ( "select count(*) as count from $table $sqlwhere $sqlgroup", 1, "count" );
$pagecount = max ( ceil ( $count / $pagesize ), 1 );
$page = max ( min ( $pagecount, intval ( $page ) ), 1 );
$offset = ($page - 1) * $pagesize;

// ��ѯ:
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
	unset ( $list_heads ["����"] ); // û�п���
}

// ������ͳ������ 2009-05-13 16:46
$res_report = $per_report = $tod_report = '';
// ȫ������
$sqlwhere_s = $sqlwhere ? ($sqlwhere . " and huifang !=''") : "where huifang !=''";
$is_hf = $db->query ( "select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count" );
$sqlwhere_s = $sqlwhere ? ($sqlwhere . " and huifang =''") : "where huifang =''";
$is_hf_not = $db->query ( "select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count" );
$hf_all = $is_hf + $is_hf_not;

// ��������
$sqlwhere_p = $sqlwhere ? ($sqlwhere . " AND binary author = '" . $realname . "' AND huifang !=''") : "WHERE binary author = '" . $realname . "' AND huifang !=''";
$p_is_hf = $db->query ( "select count(*) as count from $table $sqlwhere_p $sqlgroup order by id desc", 1, "count" );
$sqlwhere_p = $sqlwhere ? ($sqlwhere . " AND binary author = '" . $realname . "' AND huifang =''") : "WHERE binary author = '" . $realname . "' AND huifang =''";
$p_is_hf_not = $db->query ( "select count(*) as count from $table $sqlwhere_p $sqlgroup order by id desc", 1, "count" );
$p_hf_all = $p_is_hf + $p_is_hf_not;

// ��������
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

$res_report = "�ܹ�: <b class='text-error'>" . $hf_all . "</b> &nbsp; �ѻط�: <b class='text-error'>" . $is_hf . "</b> &nbsp; δ�ط�: <b class='text-error'>" . $is_hf_not . "</b>";
$per_report = "�ܹ�: <b class='text-error'>" . $p_hf_all . "</b> &nbsp; �ѻط�: <b class='text-error'>" . $p_is_hf . "</b> &nbsp; δ�ط�: <b class='text-error'>" . $p_is_hf_not . "</b>";
// $tod_report = "�ѻط�: <b class='text-error'>".$t_is_hf."</b>";
// ���б����ݷ���:
if ($sort == "���ʱ��" || ($sort == "" && $default_sort == "���ʱ��"))
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
		{ // �н��������:
			$list_data [] = array ( 
					"id" => 0, 
					"name" => "���� [" . count ( $list_data_part [1] ) . "]" 
			);
			$list_data = array_merge ( $list_data, $list_data_part [1] );
		}
		if (count ( $list_data_part [2] ) > 0)
		{ // �����������:
			$list_data [] = array ( 
					"id" => 0, 
					"name" => "���� [" . count ( $list_data_part [2] ) . "]" 
			);
			$list_data = array_merge ( $list_data, $list_data_part [2] );
		}
		if (count ( $list_data_part [3] ) > 0)
		{ // ��ǰ�������:
			$list_data [] = array ( 
					"id" => 0, 
					"name" => "ǰ������ [" . count ( $list_data_part [3] ) . "]" 
			);
			$list_data = array_merge ( $list_data, $list_data_part [3] );
		}
		unset ( $list_data_part );
	}
} else if ($sort == "ý����Դ" || ($sort == "" && $default_sort == "ý����Դ"))
{
	$list_data_part = array ();
	foreach ( $list_data as $line )
	{
		if ($line ["media_from"] == "����")
		{
			$list_data_part [1] [] = $line;
		} else if ($line ["media_from"] == "�绰")
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
				"name" => "���� [" . count ( $list_data_part [1] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [1] );
	}
	if (count ( $list_data_part [2] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "�绰 [" . count ( $list_data_part [2] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [2] );
	}
	if (count ( $list_data_part [3] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "���� [" . count ( $list_data_part [3] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [3] );
	}
	unset ( $list_data_part );
} else if ($sort == "Ԥ��ʱ��" || ($sort == "" && $default_sort == "Ԥ��ʱ��"))
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
				"name" => "���� (�ȴ���) [" . count ( $list_data_part [31] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [31] );
	}
	if (count ( $list_data_part [32] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "���� (�ѵ�) [" . count ( $list_data_part [32] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [32] );
	}
	if (count ( $list_data_part [33] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "���� (������) [" . count ( $list_data_part [33] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [33] );
	}
	if (count ( $list_data_part [4] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "������Ժ� (ʱ��δ��) [" . count ( $list_data_part [4] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [4] );
	}
	if (count ( $list_data_part [2] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "���� [" . count ( $list_data_part [2] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [2] );
	}
	if (count ( $list_data_part [1] ) > 0)
	{
		$list_data [] = array ( 
				"id" => 0, 
				"name" => "ǰ������ [" . count ( $list_data_part [1] ) . "]" 
		);
		$list_data = array_merge ( $list_data, $list_data_part [1] );
	}
	unset ( $list_data_part );
}

$back_url = make_back_url ();

// �������:
foreach ( $list_data as $li )
{
	$id = $li ["id"];
	if ($id == 0)
	{
		$t->add_tip_line ( $li ["name"] );
	} else
	{
		$r = array ();
		$r ["ѡ"] = '<input type="checkbox" value="' . $li ['id'] . '">';
		$r ["����"] = $li ["name"];
		$r ["�Ա�"] = $li ["sex"];
		$r ["�绰"] = hide_tel ( $li ["tel"], $li ['doctor'] ); // ��Ե�ҽ����
		$r ["��ѯ����"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='" . cut ( $li ["content"], 250, "..." ) . "'>" . cut ( $li ["content"], 10, ".." ) . "</a>";
		$r ["Ԥ��ʱ��"] = @date ( "m-d H:i", $li ["order_date"] );
		
		$dis_text = array ();
		foreach ( explode ( ",", $li ["disease_id"] ) as $dis_id )
		{
			if ($dis_id > 0)
				$dis_text [] = $disease_id_name [$dis_id];
		}
		$r ["��������"] = implode ( "|", $dis_text );
		$r ["ý����Դ"] = $li ["media_from"];
		$r ["�ؼ���"] = $li ["engine_key"];
		$r ["����"] = $part_id_name [$li ["part_id"]];
		$r ["����"] = $li ["depart"] > 0 ? $depart_id_name [$li ["depart"]] : "";
		$r ["��ע"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='" . cut ( $li ["memo"], 250, "..." ) . "'>" . cut ( $li ["memo"], 10, "" ) . "</a>";
		$r ["�ͷ�"] = $li ["author"];
		$r ["��Լ���"] = $status_array [$li ["status"]];
		$r ["�ط�"] = $li ["huifang"] != '' ? ('<a  rel="popover"  data-placement="left" data-content="' . trim ( $li ["huifang"] ) . '" data-original-title="�طü�¼"><i style="color:#333333" class="icon-tags"></i></a>') : '';
		$r ["���ʱ��"] = @date ( "m-d", $li ["addtime"] );
		
		// ����:
		$op = array ();
		if (check_power ( "view" ))
		{
			$op [] = "<a href='javascript:void(0)' style='color:#111' onclick=\"patientbox('" . $li ['name'] . "',$id,'patient','edit')\" class='op'><i class='icon-eye-open'></i></a>";
		}
		$hfhostory = trim ( preg_replace ( '/\s/', '', $li ['huifang'] ) );
		$op [] = "<a href='javascript:void(0)' onclick='parent.huifangm(" . $li ['id'] . ",\"" . $hfhostory . "\")'><i class='icon-comment'></i></a>";
		
		$r ["����"] = implode ( " ", $op );
		
		// �и�������;//�������طù��ģ��Ǿͻ�����ɫ
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
		
		// 2010-12-17 �޸ģ�������֮��Ĳ��ˣ���ɫ��һ��
		if ($li ["order_date"] > strtotime ( "+2 month" ))
		{
			$color = "#FF00FF";
		}
		$r ["_tr_"] = $_tr;
		
		$t->add ( $r );
	}
}

// ��ҳ
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
		byid("come_"+out["id"]).innerHTML = ['�ȴ�', '�ѵ�', 'δ��'][out["come"]];
		byid("come_"+out["id"]+"_"+out["come"]).style.display = 'none';
		byid("come_"+out["id"]+"_"+(out["come"]==1 ? 2 : 1)).style.display = 'inline';
		byid("list_line_"+out["id"]).style.color = ['label label-inverse', 'label label-important', 'label'][out["come"]];
	} else {
		alert("����ʧ�ܣ����Ժ����ԣ�");
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
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',1); return false;">��</a>';
		} else {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',0); return false;">��</a>';
		}
		byid("xiaofei_"+out["id"]).innerHTML = button;
	} else {
		alert("����ʧ�ܣ����Ժ����ԣ�");
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
	<!-- ͷ�� begin -->
	<header class="jumbotron subhead" style="margin-bottom: 10px;">
		<div class="breadcrumb">
			<ul style="float: left">
				<li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?=$hospital_id_name[$user_hospital_id];?></span> - �ط��б�</li>
	             <?php if(isset($_GET['sdate'])&&$_GET['sdate']!=''){?><li class="text-error"><span class="divider">/</span><?=date('Y-m-d',$_GET['sdate'])?>
	             <i class=" icon-arrow-right"></i></li><?php }?>
	             <?php if(isset($_GET['edate'])&&$_GET['edate']!=''){?><li class="text-error"><?=date('Y-m-d',$_GET['edate'])?></li><?php }?>
			</ul>

			<ul style="float: left; margin-left: 20px">
			    <?php if(!$debug_mode&&@$purview['show_huifang_all']==0){?>
			    <li width="33%">&nbsp;<b>��������:</b> <?php echo $per_report?></li>
			    <?php }else{?>
			    <li width="33%">&nbsp;<b>ͳ������:</b> <?php echo $res_report?></li> &nbsp;/&nbsp;
				<li width="33%">&nbsp;<b>��������:</b> <?php echo $per_report?></li>
			    <?php }?> 
			</ul>
			<!-- <ul style="float:right">
			    <li width="33%">&nbsp;<b>��������:</b> <?php echo $tod_report?></li>
			</ul>
			 -->
			<div class="clear"></div>
		</div>
	</header>

	<div id="headfixed" class="row-fluid show-grid">
		<div class="span9">
			<div class="left tb_margin_right"><?php echo $power->show_button("add"); ?></div>
			<form action="?" method="GET" style="display: inline;" id="sform">
				<div class="btn-group left tb_margin_right" data-toggle="buttons-radio" data-original-title="�ط�״̬" rel="tooltip">
					<button class="status btn <?=@$_GET['status']=='1'?'active':''?>" type="button" onclick="document.getElementById('hfstatus').value='1';this.form.submit()">�ѻ�</button>
					<button class="status btn <?=(@$_GET['status']=='2'||!isset($_GET['status'])||$_GET['status']=='')?'active':''?>" type="button" onclick="document.getElementById('hfstatus').value='2';this.form.submit()">ȫ��</button>
					<button class="status btn <?=@$_GET['status']=='0'?'active':''?>" type="button" onclick="document.getElementById('hfstatus').value='0';this.form.submit()">δ��</button>
				</div>
				<?php if($debug_mode||@$purview['show_huifang_all']!=0):?>
				<select name="kefu_23_name" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="�ͷ�/��ѯ">
					<option value='' style="color: gray" value="">--ѡ��ͷ�--</option>
	                <?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>
	            </select> 
	            <?php endif?>
	            <select name="disease" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="��Ŀ/����">
					<option value='' style="color: gray">--ѡ����--</option>
		            <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>
	            </select> 
	            <select name="timetype" class="span2 left tb_margin_right" onchange="(this.value=='custom')?customdate():this.form.submit()" rel="tooltip" data-original-title="ʱ���">
					<option value='none' style="color: gray">--ѡ��ʱ���--</option>
					<option value='week'>���һ��</option>
					<option value='halfmonth'>��������</option>
					<option value='month'>���һ����</option>
					<option value='halfyear'>�������</option>
					<option value='custom'>--�Զ���ʱ���--</option>
				</select> 
				<?php if(isset($_GET['timetype'])):?>
				<script>
				$("select[name=timetype]").val("<?=$_GET['timetype']?>");   
			    </script>
				<?php endif?>
			    
				<input class="btn form_datetime span1 left tb_margin_right" type="text" rel="tooltip" data-original-title="������ʾ" data-date="<?=date('Y-m-d')?>" name="date" value="<?=@$_GET['date']==''?'':$_GET['date']?>" onchange="this.form.submit()" /> 
				<input type="hidden" name="sdate" id="h_sdate" value="<?=@isset($_GET['sdate'])?$_GET['sdate']:'' ?>" />
				<input type="hidden" name="edate" id="h_edate" value="<?=@isset($_GET['edate'])?$_GET['edate']:'' ?>" /> 
				<input type="hidden" name="from" value="search" />
				<input type="hidden" name="status" id="hfstatus" value="<?php echo isset($_GET['status'])?$_GET['status']:''?>" />
				<button type="button" class="btn left tb_margin_right" onclick="location='patient.list.huifang.php'">�������</button>
			</form>
		</div>

		<div class="pagination-right" style="float: right">
			<form name="topform" method="GET" style="margin-bottom: 0">
				<input name="key" type="text" value="<?php echo $_GET["key"]; ?>" class="input-medium search-query" placeholder="����">&nbsp; <input type="submit" class="btn" value="����" style="font-weight: bold" title="�������">&nbsp;
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<!-- ͷ�� end -->

	<div class="space"></div>
	<!-- �����б� begin --> 
    <?php echo $t->show(); ?>
    <!-- �����б� end -->

	<!-- ��ҳ���� begin -->
	<div class="footer_op">
		<div class="footer_op_left">
			<!-- <button onclick="select_all()" class="btn btn-small pull-right toggle-all">ȫѡ</button> -->
		</div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- ��ҳ���� end -->

	<!-- �Զ���ʱ��� START -->
	<div id="customdatemodal" class="modal hide fade" data-backdrop='false'>
		<form class="form-vertical">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">��</button>
				<h3>ʱ���</h3>
			</div>
			<div class="modal-body" style="text-align: center">
				<fieldset>
					<div class="control-group">
						<div class="controls">
							�ӣ�<input type="text" class="span2" id="sdate" value="<?=@date('Y-m-d',$_GET['sdate'])?>"/>&nbsp;&nbsp;����<input type="text" class="span2" id="edate" value="<?=@date('Y-m-d',$_GET['edate'])?>"/>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn" value="�ر�" data-dismiss="modal"/> 
				<input type="button" class="btn btn-primary date-primary" value="����" />
			</div>
		</form>
	</div>
	<!-- �Զ���ʱ��� END-->



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
		  title: '���˻��������޸�',
		  nobackdrop:true,
		  keyboard:true,
		 // target:parent.document,
		 // onClose:function(){destroyModal()}
	});
	function destroyModal(){
	}
    
});

//jquery �ύ��
$(".date-primary").click(function(){
    $("#sform").submit();
});

//�����ť��ȡ�����в���
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
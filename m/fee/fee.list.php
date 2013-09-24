<?php
/**
 * �����˵��б�
 * 
 * @author fangyang(278294861)
 * @since 2013-05-24
 */

if ($_GET["cj_btime"]) {
	$_GET["begin_time"] = strtotime($_GET["cj_btime"]." 0:0:0");
}
if ($_GET["cj_etime"]) {
	$_GET["end_time"] = strtotime($_GET["cj_etime"]." 23:59:59");
}
// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = explode(" ", "page sort order name key tel media_from type cj_btime cj_etime cj_time re_arrive is_complete bcharge spend_type echarge pid kefu_23_name kefu_4_name disease author from depart names date  media");
$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}

extract($param);

// ���嵥Ԫ���ʽ:
$list_heads = array(
	    "ѡ" => array("align"=>"center"),
		"�ɽ�" => array("align"=>"center", "sort"=>"cashier", "order"=>"asc"),
		"����" => array( "align"=>"center", "sort"=>"name", "order"=>"asc"),
	    "״̬" => array("align"=>"center","sort"=>"status","order"=>"asc"),
		"�ɽ�ʱ��" => array( "align"=>"center", "sort"=>"cj_time", "order"=>"desc"),
		"��������" => array("align"=>"center", "sort"=>"disease_id", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"part_id", "order"=>"asc"),
		"����" => array("align"=>"center", "sort"=>"media_from", "order"=>"asc"),
		"�ͷ�" => array("align"=>"center", "sort"=>"part_id", "order"=>"asc"),
		"��������" => array("align"=>"center", "sort"=>"spend_type", "order"=>"asc"),
		"Ӧ��" => array("align"=>"center", "sort"=>"y_charge", "order"=>"asc"),
		"ʵ��" => array("align"=>"s_charge", "sort"=>"s_charge", "order"=>"asc"),
		"������" => array("align"=>"center", "sort"=>"cashier", "order"=>"asc"),
		"��ע" => array("align"=>"center", "sort"=>"memo", "order"=>"asc"),
		"����" => array("width"=>"15%","align"=>"center"),
);

// Ĭ������ʽ:
$default_sort = "�ɽ�ʱ��";
$default_order = "desc";

//�漰�����������µĲ�ѯ��ʹ�����ϲ�ѯ
$leftjoin = "LEFT JOIN $patient_table ON $table.aid = $patient_table.id";

if ($show == 'today') {
	$begin_time = mktime(0, 0, 0);
	$end_time = mktime(23, 59, 59);
} else if ($show == 'yesterday') {
	$begin_time = mktime(0, 0, 0) - 24 * 3600;
	$end_time = mktime(0, 0, 0);
} else if ($show == "thismonth") {
	$begin_time = mktime(0,0,0,date("m"),1);
	$end_time = mktime("+1 month", $begin_time);
} else if ($show == "lastmonth") {
	$end_time = mktime(0,0,0,date("m"),1);
	$begin_time = strtotime("-1 month", $end_time);
}

// ���������� 2010-09-29:
if ($_GET["date"]) {
	$begin_time = strtotime($_GET["date"]." 0:0:0");
	$end_time = strtotime($_GET["date"]." 23:59:59");
}


// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "table table-hover table-condensed";


// ������ʼ:
$where = array();

if ($key = trim(stripslashes($key))) {
	$sk = "%{$key}%";
	$fields = explode(" ", "name tel qq content memo");
	$sfield = array();
	foreach ($fields as $_tm) {
		$sfield[] = "binary $patient_table . $_tm like '{$sk}'";
	}
	$where[] = "(".implode(" or ", $sfield).")";
}
// ��ȡȨ��:
$today_where = '';

//�鿴Ȩ��
if (!$debug_mode) {
	$read_parts = get_manage_part(); //�����Ӳ��ţ���ͬ��������)
	
	if(in_array($uinfo["part_id"], array(2,3))){ //��ͨ�û�ֻ��ʾ�Լ�������
		$where[] = "binary net_author='".$realname."'";
	}
}

$time_type = empty($time_type) ? 'cj_time' : $time_type;

if ($begin_time > 0) {
	$where[] = $table.'.'.$time_type.'>='.$begin_time;
}
if ($end_time > 0) {
	$where[] = $table.'.'.$time_type.'<'.$end_time;
}

if($type == 'history'&&$id != 0)
{
	$patient_id = $db->query_first("select pid from $patient_table where id=$id",'1');
	$where [] = $table.'.'.'pid ="'.$patient_id["pid"].'"';
}

if ($is_complete != '') {
	$where[] = "is_complete='$is_complete'";
}
if ($pid != '') {
	$where[] = "pid='$pid'";
}
if ($name!= '') {
	$where[] = "name='$name'";
}
if ($tel!= '') {
	$where[] = "tel='$tel'";
}
if ($bcharge!= '') {
	$where[] = "s_charge >= '$bcharge'";
}
if ($echarge!= '') {
	$where[] = "s_charge <= '$echarge'";
}
if ($re_arrive != '') {
	if($re_arrive != '5')
	{
		$where[] = $patient_table.'.'."re_arrive = '$re_arrive'";
	}
}

if ($kefu_23_name != '') {
	$where[] = $patient_table.'.'."author='$kefu_23_name'";
}
if ($doctor_name != '') {
	$where[] = "doctor='$doctor_name'";
}

if ($disease != '') {
	$where[] = "disease_id=$disease";
}
if ($depart != '') {
	$where[] = "depart=$depart";
}
$sqlwhere = $db->make_where($where);
$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);
// ��ҳ����:
$count = $db->query("select count(*) as count from $table $sqlwhere $sqlgroup", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$time = time();
$today_begin = mktime(0,0,0);
$today_end = $today_begin + 24 * 3600;
$list_data = $db->query("select $table.*,$patient_table.name,$patient_table.pid,$patient_table.tel,$patient_table.media_from,$patient_table.re_arrive,$patient_table.disease_id,$patient_table.part_id,($table.cj_time-$time) as remain_time, if($table.cj_time<$today_begin, 1, if($table.cj_time>$today_end, 2, 3)) as order_sort from $table $leftjoin $sqlwhere $sqlgroup $sqlsort limit $offset,$pagesize");
$s_sql = $db->sql;

// id => name:
$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
$disease_id_name = $db->query("select id,name from disease", 'id', 'name');
$depart_id_name = $db->query("select id,name from depart where hospital_id=$user_hospital_id", 'id', 'name');

$use_depart = 1;
if (count($depart_id_name) == 0) {
	$use_depart = 0;
	unset($list_heads["����"]); //û�п���
}


//������ͳ������ 
$res_report = '';
$sqlwhere_all = $sqlwhere ? ($sqlwhere." and $table.is_complete='1'") : "where $table.is_complete='1'";
$count_complete = $db->query("select count(*) as count FROM $table $leftjoin $sqlwhere_all $sqlgroup order by $table.id desc", 1, "count");
$sqlwhere_all = $sqlwhere ? ($sqlwhere." and $table.is_complete!='1'") : "where $table.is_complete!='1'";
$count_not = $db->query("select count(*) as count FROM $table $leftjoin $sqlwhere_all $sqlgroup order by $table.id desc", 1, "count");
$count_all = $count_complete + $count_not;
$res_report = "�ܹ�: <b class='text-error'>".$count_all."</b> &nbsp; ���: <b class='text-error'>".$count_complete."</b> &nbsp; δ���: <b class='text-error'>".$count_not."</b>";

//ͳ�ƽ�������:
$t_time_type = "cj_time";
$today_where = ($today_where ? ($today_where." and") : "")." $t_time_type>=".$today_begin;
$today_where .= " and $t_time_type<".$today_end;
$sqlwhere_today = $sqlwhere .' AND '.($today_where ? ($today_where." and $table.is_complete='1'") : "$table.is_complete='1'");
$count_today_complete = $db->query("select count(*) as count from $table $leftjoin $sqlwhere_today order by $table.id desc", 1, "count");
$sqlwhere_today = $sqlwhere .' AND '.($today_where ? ($today_where." and $table.is_complete!='1'") : "$table.is_complete!='1'");
$count_today_not = $db->query("select count(*) as count from $table $leftjoin $sqlwhere_today order by $table.id desc", 1, "count");
$count_today_all = $count_today_complete + $count_today_not;

$today_report = "�ܹ�: <b class='text-error'>".$count_today_all."</b>&nbsp;���: <b class='text-error'>".$count_today_complete."</b> &nbsp;δ���: <b class='text-error'>".$count_today_not."</b>";

//3��״̬��������
$sqlwhere_h = $sqlwhere ? $sqlwhere .' AND ' : 'WHERE';
// ���б����ݷ���:
if ($sort == "�ɽ�ʱ��" || ($sort == "" && $default_sort == "�ɽ�ʱ��")) {
	if ($order == "desc" || $default_order == "desc") {
		$today_begin = mktime(0,0,0);
		$today_end = $today_begin + 24*3600;
		$yesterday_begin = $today_begin - 24*3600;

		$list_data_part = array();
		$list_data_fee = array();
		$list_data_done = array();
		foreach ($list_data as $line) {
			if ($line["cj_time"] < $yesterday_begin) {
				$list_data_part[3][] = $line;
				$list_data_fee[3] = $db->query_first("SELECT sum($table.s_charge) as sum_charge FROM $table $leftjoin $sqlwhere_h $table.cj_time < $yesterday_begin");
				$list_data_done[3] = $db->query_first("SELECT count(*) as sum_done FROM $table $leftjoin $sqlwhere_h $table.cj_time < $yesterday_begin AND $table.is_complete = '1'");
			} else if ($line["cj_time"] < $today_begin) {
				$list_data_part[2][] = $line;
				$list_data_fee[2] = $db->query_first("SELECT sum($table.s_charge) as sum_charge FROM $table $leftjoin $sqlwhere_h $table.cj_time < $today_begin AND $table.cj_time > $yesterday_begin");
				$list_data_done[2] = $db->query_first("SELECT count(*) as sum_done FROM $table $leftjoin $sqlwhere_h $table.cj_time < $today_begin AND $table.cj_time > $yesterday_begin AND $table.is_complete = '1'");
			} else if ($line["cj_time"] < $today_end) {
				$list_data_part[1][] = $line;
				$list_data_fee[1] = $db->query_first("SELECT sum($table.s_charge) as sum_charge FROM $table $leftjoin $sqlwhere_h $table.cj_time < $today_end AND $table.cj_time > $today_begin");
				$list_data_done[1] = $db->query_first("SELECT count(*) as sum_done FROM $table $leftjoin $sqlwhere_h $table.cj_time < $today_end AND $table.cj_time > $today_begin AND $table.is_complete = '1'");
			}
		}
        
		$list_data = array();
		if (count($list_data_part[1]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [��/��ҳ��<b class='text-error'>".count($list_data_part[1])."</b>&nbsp;&nbsp;��ɣ�<b class='text-error'>".$list_data_done[1]['sum_done']."</b>&nbsp;&nbsp;�ۼƣ�<b class='text-error'>".(float)$list_data_fee[1]['sum_charge']."</b>Ԫ]");
			$list_data = array_merge($list_data, $list_data_part[1]);
		}
		if (count($list_data_part[2]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [��/��ҳ��<b class='text-error'>".count($list_data_part[2])."</b>&nbsp;&nbsp;��ɣ�<b class='text-error'>".$list_data_done[2]['sum_done']."</b>&nbsp;&nbsp;�ۼƣ�<b class='text-error'>".(float)$list_data_fee[2]['sum_charge']."</b>Ԫ]");
			$list_data = array_merge($list_data, $list_data_part[2]);
		}
		if (count($list_data_part[3]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"ǰ������ [��/��ҳ��<b class='text-error'>".count($list_data_part[3])."</b>&nbsp;&nbsp;��ɣ�<b class='text-error'>".$list_data_done[3]['sum_done']."</b>&nbsp;&nbsp;�ۼƣ�<b class='text-error'>".(float)$list_data_fee[3]['sum_charge']."</b>Ԫ]");
			$list_data = array_merge($list_data, $list_data_part[3]);
		}
		unset($list_data_part);
	}
} else if ($sort == "ý����Դ" || ($sort == "" && $default_sort == "ý����Դ")) {
	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["media_from"] == "����") {
			$list_data_part[1][] = $line;
		} else if ($line["media_from"] == "�绰") {
			$list_data_part[2][] = $line;
		} else {
			$list_data_part[3][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�绰 [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[3]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[3])."]");
		$list_data = array_merge($list_data, $list_data_part[3]);
	}
	unset($list_data_part);
} 

$back_url = make_back_url();

// �������:
foreach ($list_data as $li) {
	$id = $li["id"];
	if ($id == 0) {
		$t->add_tip_line($li["name"]);
	} else {
		$r = array();
		$r["ѡ"]  = '<input type="checkbox">'; 
		$r["�ɽ�"] = $cj_status[$li["is_complete"]];
		$r["����"] = $li["name"];
		$r["״̬"] = $re_arrive_full[$li['re_arrive']];
		$r["�ɽ�ʱ��"] = @date("m-d H:i", $li["cj_time"]);
		$dis_text = array();
		foreach (explode(",", $li["disease_id"]) as $dis_id) {
			if ($dis_id > 0) $dis_text[] = $disease_id_name[$dis_id];
		}
		$r["��������"] = implode("|", $dis_text);
		$r["����"] = $part_id_name[$li["part_id"]];
		$r["����"] = $li["media_from"];
		$r["�ͷ�"] = $li["net_author"];
		$r["��������"] = $li["fee_type"];
		$r["Ӧ��"] = hide_money((float)$li["y_charge"],$li['author']);
		$r["ʵ��"] = hide_money((float)$li["s_charge"],$li['author']);
		$r["������"] = $li["author"];
		$r["��ע"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='".cut($li["memo"],250, "...")."'>".cut($li["memo"], 10, "")."</a>";

		/*
		 * Ȩ�޲���
		 * 
		 * ����Ա�͵���Ա����Ȩ��
		 * ����˿�����һ��֮�ڶ����ݽ����޸�
		 * �������һ�� ֮�ڿ��Զ����ݽ���ɾ��
		 * ����Ա���Խ���ɾ������
		 */
		$op = array();
		$op[]='<div class="btn-group"><button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">ѡ�� <span class="caret"></span></button>
                <ul class="dropdown-menu">';
		
		$can_edit = $can_delete = 1;
		if(!in_array($uinfo["part_id"], array(1,9)))
		{
			if(floor((time() - 1369726695)/(24*60*60))>=1)
			{
				$can_edit = $can_delete= 0;
			}
		}
		
		//�޸�Ȩ��
		if ((check_power("edit") && $can_edit) || $debug_mode) {
			
			$op[] = '<li><a href="../fee/fee.php?op=edit&id='.$li["id"].'"">�޸�</a></li>';
		}
		
		//�����¼
		$op[]='<li><a href="javascript:void(0)" onclick="patientbox(\''.$li['name'].'\','.$li['aid'].',\'index\')">��������</a></li>
                  <li><a href="../patient/patient.php?pid='.$li["pid"].'">�����¼</a></li>';
		
		//ɾ��Ȩ��
		if (check_power("delete")) {
			if (in_array($uinfo["part_id"], array(1,9)) ||$debug_mode ) {
				$can_delete = 1;
			}
		}
		
		if ($can_delete == 1 || $debug_mode) {
			$op[] = '<li class="divider"></li><li><a href="../fee/fee.php?op=delete&id='.$li["id"].'"">ɾ��</a></li>';
		}
		
		$op[] = '</ul> </div>';
		$r["����"] = implode(" ", $op);

		// �и�������;
		$_tr = 'class="line" id="'.$li["id"].'"';
		$color_status = $li["status"];
		if ($color_status == 0 && date("Ymd", $li["order_date"]) < date("Ymd")) {
			$color_status = 3;
		}
		if ($color_status == 0 && $li["huifang"] != '') {
			$color_status = 4;
		}
		$color = $line_color[$color_status];

		// 2010-12-17 �޸ģ�������֮��Ĳ��ˣ���ɫ��һ��
		if ($li["order_date"] > strtotime("+2 month")) {
			$color = "#FF00FF";
		}
		/*$_tr .= 'class="'.$color.'"';*/
		//$_tr .= ' onmouseover="mi(this)" onmouseout="mo(this)"';
		$r["_tr_"] = $_tr;

		$t->add($r);
	}
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");
include $mod.".list.tpl.php";



?>
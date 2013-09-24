<?php
/**
 * �����б�
 * 
 * @author fangyang(278294861)
 * @since  2013-03-06
 */

if ($_GET["btime"]) {
	$_GET["begin_time"] = strtotime($_GET["btime"]." 0:0:0");
}
if ($_GET["etime"]) {
	$_GET["end_time"] = strtotime($_GET["etime"]." 23:59:59");
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = explode(" ", "page sort order key re_arrive begin_time end_time time_type pid show come kefu_23_name kefu_4_name doctor_name xiaofei disease part_id from depart names date list_huifang media");

$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);

// ���嵥Ԫ���ʽ:
$list_heads = array(
    "ѡ" => array("width"=>"10", "align"=>"center"),
	"����" => array("width"=>"60", "align"=>"center", "sort"=>"name", "order"=>"asc"),
    "״̬" => array("width"=>"40", "align"=>"center","sort"=>"name","order"=>"asc"),
	"�Ա�" => array("width"=>"4.25%","align"=>"center", "sort"=>"sex", "order"=>"asc"),
	"����" => array("width"=>"5.25%","align"=>"center", "sort"=>"age", "order"=>"asc"),
	"�绰" => array("width"=>"8.05%","align"=>"center", "sort"=>"tel", "order"=>"asc"),
	"��ѯ����" => array("width"=>"6.25%","align"=>"left", "sort"=>"content", "order"=>"asc"),
	"Ԥ��ʱ��" => array("width"=>"8.05%", "align"=>"center", "sort"=>"order_sort", "order"=>"desc"),
	"����" => array("width"=>"4.05%","align"=>"center", "sort"=>"remain_time", "order"=>"desc"),
	"��������" => array("width"=>"6.25%","align"=>"center", "sort"=>"disease_id", "order"=>"asc"),
	"ý����Դ" => array("width"=>"6.25%","align"=>"center", "sort"=>"media_from", "order"=>"asc"),
	"����" => array("width"=>"6.25%","align"=>"center", "sort"=>"part_id", "order"=>"asc"),
	/*"����" => array("width"=>"5.25%","align"=>"center", "sort"=>"is_local", "order"=>"asc"),*/
	"��ע" => array("width"=>"6.25%","align"=>"center", "sort"=>"memo", "order"=>"asc"),
	"�ͷ�" => array("width"=>"6.25%", "align"=>"center", "sort"=>"author", "order"=>"asc"),
	"�ط�" => array("width"=>"4.05%", "align"=>"center", "sort"=>"huifang", "order"=>"desc"),
	"���ʱ��" => array("width"=>"6.85%", "align"=>"center", "sort"=>"addtime", "order"=>"desc"),
	"����" => array("width"=>"65", "align"=>"center"),
	
	/**
	 * "QQ" => array("align"=>"center", "sort"=>"qq", "order"=>"asc"),
	 * "ר�Һ�" => array("align"=>"center", "sort"=>"zhuanjia_num", "order"=>"asc"),
	 * "�Ӵ�" => array("align"=>"center", "sort"=>"jiedai", "order"=>"asc"),
	 * "�ؼ���" => array("align"=>"center", "sort"=>"engine_key", "order"=>"asc"),
	 * "����" => array("align"=>"center", "sort"=>"depart", "order"=>"asc"),
	 * "��Լ���" => array("align"=>"center", "sort"=>"status_1", "order"=>"desc", "sort2"=>"addtime desc"),
	 */
);

// Ĭ������ʽ:
if ($uinfo["part_id"] == 4) {
	$default_sort = "Ԥ��ʱ��"; // ��ҽ�ȽϹ�ע���쵽�Ĳ���
	$default_order = "desc";
} else {
	$default_sort = "���ʱ��"; //�ͷ������Ա���ע�����������˶��ٲ���
	$default_order = "desc";
}

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
	$fields = explode(" ", "name tel qq zhuanjia_num content memo");
	$sfield = array();
	foreach ($fields as $_tm) {
		$sfield[] = "binary $_tm like '{$sk}'";
	}
	$where[] = "(".implode(" or ", $sfield).")";
}

// ��ȡȨ��:
$today_where = '';
if (!$debug_mode) {
	$read_parts = get_manage_part();                    
	if($show_patient=='1') //Ȩ���������Ƿ������ʾ�����˵Ĳ���
	{
		if ($uinfo["part_admin"] || $uinfo["part_manage"]) { //���Ź���Ա�����ݹ���Ա
			$where[] = "(part_id in (".$read_parts.") or binary author='".$realname."')";
		}
	}else{
		$where[] = "binary author='".$realname."'";
	}
}

// �绰�ط�ֻ��ʾ�ѵ�����:
if ($uinfo["part_id"] == 12) {
	$where[] = "status=1";
}

$time_type = empty($time_type) ? 'order_date' : $time_type;
if ($begin_time > 0) {
	$where[] = $time_type.'>='.$begin_time;
}
if ($end_time > 0) {
	$where[] = $time_type.'<'.$end_time;
}
if ($come != '') {
	if ($come == 1) {
		$where[] = "status=1";
	} else if($come == 0) {
		$where[] = "status in (0,2)";
	} else if($come == 2){   //δ�� 20130304
	    $where[] = "status in (0,2) and tel='' and qq=''";   
	}
}

if ($pid != '') {
	$where[] = "pid='$pid'";
}
if ($re_arrive != '') {
	$where[] = "re_arrive='$re_arrive'";
}
if ($kefu_23_name != '') {
	$where[] = "author='$kefu_23_name'";
}
if ($kefu_4_name != '') {
	$where[] = "jiedai='$kefu_4_name'";
}
if ($doctor_name != '') {
	$where[] = "doctor='$doctor_name'";
}
if ($disease != '') {
	$where[] = "disease_id=$disease";
}
if ($part_id != '') {
	$where[] = "part_id=$part_id";
}
if ($depart != '') {
	$where[] = "depart=$depart";
}
if ($list_huifang) {
	$where[] = "huifang like '%[".$realname."]%'";
}
if ($media) {
	$where[] = "media_from='".trim($media)."'";
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
	unset($list_heads["����"]); //û�п���
}


// ������ͳ������ 2009-05-13 16:46
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

$res_report = "�ܹ�: <b class='text-error'>".$count_all."</b> &nbsp; �ѵ�: <b class='text-error'>".$count_come."</b> &nbsp; δ��: <b class='text-error'>".$count_not."</b>";

$det_report = "����: <b class='text-error'>".$count_chu."</b> &nbsp;����: <b class='text-error'>".$count_fu."</b>&nbsp;����: <b class='text-error'>".$count_cha."</b>&nbsp;������: <b class='text-error'>".$count_zai."</b>";




// ͳ�ƽ�������:
$t_time_type = "order_date";

$today_where = ($today_where ? ($today_where." and") : "")." $t_time_type>=".$today_begin;
$today_where .= " and $t_time_type<".$today_end;
$sqlwhere_s = "where ".($today_where ? ($today_where." and status=1") : "status=1");
$count_today_come = $db->query("select count(*) as count from $table $sqlwhere_s order by id desc", 1, "count");

$sqlwhere_s = "where ".($today_where ? ($today_where." and status!=1") : "status!=1");
$count_today_not = $db->query("select count(*) as count from $table $sqlwhere_s order by id desc", 1, "count");

$count_today_all = $count_today_come + $count_today_not;

$today_report = "<a href='?show=today'>�ܹ�: <b class='text-error'>".$count_today_all."</b></a> &nbsp; <a href='?show=today&come=1'>�ѵ�: <b class='text-error'>".$count_today_come."</b></a> &nbsp; <a href='?show=today&come=0'>δ��: <b class='text-error'>".$count_today_not."</b></a>&nbsp;";

// ��������ͳ��(����):
if (in_array($uinfo["part_id"], array(2,3))) {
	$basewhere = "part_id=".$uinfo["part_id"];
	$part_today_all = $db->query("select count(*) as count from $table where $basewhere and order_date>=$today_begin and order_date<$today_end", 1, "count");
	$part_today_come = $db->query("select count(*) as count from $table where $basewhere and order_date>=$today_begin and order_date<$today_end and status=1", 1, "count");
	$part_today_not = $part_today_all - $part_today_come;

	$part_report = "�ܹ�: <b class='text-error'>".$part_today_all."</b>  �ѵ�: <b class='text-error'>".$part_today_come."</b>  δ��: <b class='text-error'>".$part_today_not."</b>&nbsp;";
}


// ���б����ݷ���:
if ($sort == "���ʱ��" || ($sort == "" && $default_sort == "���ʱ��")) {
	if ($order == "desc" || $default_order == "desc") {
		$today_begin = mktime(0,0,0);
		$today_end = $today_begin + 24*3600;
		$yesterday_begin = $today_begin - 24*3600;

		$list_data_part = array();
		foreach ($list_data as $line) {
			if ($line["addtime"] < $yesterday_begin) {
				$list_data_part[3][] = $line;
			} else if ($line["addtime"] < $today_begin) {
				$list_data_part[2][] = $line;
			} else if ($line["addtime"] < $today_end) {
				$list_data_part[1][] = $line;
			}
		}

		$list_data = array();
		if (count($list_data_part[1]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[1])."]");
			$list_data = array_merge($list_data, $list_data_part[1]);
		}
		if (count($list_data_part[2]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[2])."]");
			$list_data = array_merge($list_data, $list_data_part[2]);
		}
		if (count($list_data_part[3]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"ǰ������ [".count($list_data_part[3])."]");
			$list_data = array_merge($list_data, $list_data_part[3]);
		}
		unset($list_data_part);
	}
} else if ($sort == "��Լ���" || ($sort == "" && $default_sort == "��Լ���")) {
	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["status_1"] == 2) { //�ѵ�
			$list_data_part[1][] = $line;
		} else if ($line["status_1"] == 1) { //δ��
			$list_data_part[2][] = $line;
		} else if ($line["status_1"] == 0) { //�ȴ�
			$list_data_part[3][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�ѵ� (�Ѹ�Լ) [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"δ�� (ȷ�ϲ��ḰԼ) [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[3]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�ȴ� (��δ��Լ�������ܻḰԼ) [".count($list_data_part[3])."]");
		$list_data = array_merge($list_data, $list_data_part[3]);
	}
	unset($list_data_part);

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
} else if ($sort == "Ԥ��ʱ��" || ($sort == "" && $default_sort == "Ԥ��ʱ��")) {
	$today_begin = mktime(0,0,0);
	$today_end = $today_begin + 24*3600;
	$yesterday_begin = $today_begin - 24*3600;

	$list_data_part = array();
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

	$list_data = array();
	if (count($list_data_part[31]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (�ȴ���) [".count($list_data_part[31])."]");
		$list_data = array_merge($list_data, $list_data_part[31]);
	}
	if (count($list_data_part[32]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (�ѵ�) [".count($list_data_part[32])."]");
		$list_data = array_merge($list_data, $list_data_part[32]);
	}
	if (count($list_data_part[33]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (������) [".count($list_data_part[33])."]");
		$list_data = array_merge($list_data, $list_data_part[33]);
	}
	if (count($list_data_part[4]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"������Ժ� (ʱ��δ��) [".count($list_data_part[4])."]");
		$list_data = array_merge($list_data, $list_data_part[4]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"ǰ������ [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
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
		$r["ѡ"]  = '<input type="checkbox" value="'.$li['id'].'">'; 
		$r["����"] = $li["chengjiao"] == 1 ? '<a rel="tooltip" data-toggle="tooltip" data-placement="right" data-original-title="�ѳɽ�"><strong style="color:#b94a48">'.$li["name"].'</strong></a>' : $li["name"];
		$r["״̬"] = "";
		$r["�Ա�"] = $li["sex"];
		$r["����"] = $li["age"] > 0 ? $li["age"] : "";
		
	    $r["�绰"] = hide_tel($li["tel"],$li['doctor']);    //��Ե�ҽ����
		$r["��ѯ����"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='".cut($li["content"],250, "...")."'>".cut($li["content"], 10, "..")."</a>";
		$r["Ԥ��ʱ��"] = @date("m-d H:i", $li["order_date"]);
		$r["����"] = ($li["order_date"]-time() > 0 ? ceil(($li["order_date"]-time())/24/3600) : '0');

		$dis_text = array();
		foreach (explode(",", $li["disease_id"]) as $dis_id) {
			if ($dis_id > 0) $dis_text[] = $disease_id_name[$dis_id];
		}
		$r["��������"] = implode("|", $dis_text);
		$r["ý����Դ"] = $li["media_from"];
		$r["�ؼ���"] = $li["engine_key"];
		$r["����"] = $part_id_name[$li["part_id"]];
		$r["����"] = $li["depart"] > 0 ? $depart_id_name[$li["depart"]] : "";
		/*$r["����"] = $li["is_local"] == 2 ? $li["area"] : $area_id_name[$li["is_local"]];*/
		$r["��ע"] = "<a rel='tooltip' style='cursor:pointer' data-toggle='tooltip' data-placement='right' data-original-title='".cut($li["memo"],250, "...")."'>".cut($li["memo"], 10, "")."</a>";
		$r["�ͷ�"] = $li["author"]. ($li["edit_log"] ? ("<a  rel='popover' data-content='".$li["edit_log"]."' data-original-title='��������' data-placement='left' style='color:#8080C0' class='kefupointer'><i style='color:#333333' class='icon-tag'></i></a>") : '');
		$r["��Լ���"] = $status_array[$li["status"]];
		$r["�ط�"] = $li["huifang"] != '' ? ('<a  rel="popover"  data-placement="left" data-content="'.trim($li["huifang"]).'" data-original-title="�طü�¼"><i style="color:#333333" class="icon-tags"></i></a>') : '';
		$r["���ʱ��"] = @date("m-d", $li["addtime"]);

		// ����:
		$op = array();
		if (check_power("view")) {
		    $op[] = "<a href='javascript:void(0)' style='color:#111' onclick=\"patientbox('".$li['name']."',$id,'patient','edit')\" class='op'><i class='icon-eye-open'></i></a>";
		
		}

		// �ͷ�û���޸�Ȩ�ޣ���ҽ�����ϴ�����Ϻ��Ҹ���û���޸�Ȩ�ޣ�����Ա��ҽԺ����Ա���޸�Ȩ��
        // �����˶������޸�   �طã� 20130328  
		$can_edit = 1;
		if ($uinfo["part_id"] == 2) { //����ͷ�
			if ($li["author"] == $realname) {
				$can_edit = 1; //�������Լ���ӵĲ����޸�
			}
		} else if ($uinfo["part_id"] == 3) { //�绰�ͷ�
			$can_edit = 1;                   //�绰�ͷ������طã�����ʼ���ܽ����޸ģ�����Ȩ�����޸��п���
		} else if($uinfo["part_id"] ==4){    //��ҽֻ���޸Ļ���������
			$can_edit = 0;
		} else {
			$can_edit = 1;
		}
		if ((check_power("edit") && $can_edit) || $debug_mode||$uinfo["part_admin"]) {
			$op[] = "<a style='color:#111'  href='?op=edit&id=$id&go=back' class='op' alt='�޸�' title='�޸�'><i class='icon-pencil'></i></a>";
		}
		//�ж�ɾ��Ȩ��:
		$can_delete = 0;
		if (check_power("delete")) {
			// �����ύ�߱��ˣ���û���޸ĵ�����£�����ɾ��
			if ($li["author"] == $realname) {
				if ($li["status"] == 0 && $line["edit_log"] == '') {
					$can_delete = 1;
				}
			} else {
				// ���Ǳ��ˣ�����ǹ���Ա�Ļ����Ҿ���ɾ��Ȩ�ޣ�����ɾ��:
				if (in_array($uinfo["part_id"], array(1,9)) || $uinfo["part_admin"]) {
					$can_delete = 1;
				}
			}
		}
		if ($can_delete == 1 || $debug_mode) {
			$op[] = "<a style='color:#111'  href='?op=delete&id=$id' onclick='return isdel()' class='op' alt='ɾ��' title='ɾ��'><i class='icon-trash'></i></a>";
		}
		$r["����"] = implode(" ", $op);

		// �и�������;
		$_tr = "class='line' id='".$li['id']."' ondblclick=\"patientbox('".$li['name']."',$id,'patient','edit')\"";
		$color_status = $li["status"];
		if ($color_status == 0 && date("Ymd", $li["order_date"]) < date("Ymd")) {
			$color_status = 3;
		}
		if ($color_status == 0 && $li["huifang"] != '') {
			$color_status = 4;
		}
		$color = $line_color[$color_status];
        
		//2013-03-06 fangyang
		$r["״̬"] = "<span style='".$line_color[$color_status]."'><i class='icon-flag'></i></span>";
		
		//����״̬
		if(!empty($li['re_arrive']))
		{
			$r["״̬"].= "&nbsp;|&nbsp;<a href='?pid=".$li['pid']."&from=search'>".$re_arrive_arr[$li['re_arrive']].'</a>';
		}
		
		// 2010-12-17 �޸ģ�������֮��Ĳ��ˣ���ɫ��һ��
		if ($li["order_date"] > strtotime("+2 month")) {
			$color = "#FF00FF";
		}
		$r["_tr_"] = $_tr;

		$t->add($r);
	}
}


$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

//��ҳ�ط�����  20130228 fangyang
if ($_POST['action'] == "hfdate")
{
    $table = "patient_" . $user_hospital_id;
    
    switch ($_POST['type'])
    {
        case "yesterday" :
            $begin_time = mktime(0, 0, 0)-24*3600;
            $end_time = mktime(23, 59, 59)-24*3600;
            break;
        case "today" :
            $begin_time = mktime(0, 0, 0);
            $end_time = mktime(23, 59, 59);
            break;
        case "tomorrow" :
            $begin_time = mktime(0, 0, 0)+24*3600;
            $end_time = mktime(23, 59, 59)+24*3600;
            break;
    }
    
    $re_arrive_array = array (
    		array (
    				"id" => 0,
    				"name" => '����'
    		),
    		array (
    				"id" => 1,
    				"name" => '����'
    		),
    		array (
    				"id" => 2,
    				"name" => '������'
    		),
    		array (
    				"id" => 3,
    				"name" => '����'
    		)
    );
    $time = time();
    $today_begin = mktime(0, 0, 0);
    $today_end = $today_begin + 24 * 3600;
    
    if(!$debug_mode&&@$purview['show_huifang_all']==0)
    {
    	$where = "  AND binary $table.author = '$realname'";
    }else
    {
    	$where = '';
    }
        
    $list_data = $db->query("select $table.*,disease.name as disease_name from $table LEFT JOIN disease ON $table.disease_id=disease.id WHERE $table.huifang_date>= $begin_time AND $table.huifang_date<=$end_time AND $table.status=0 $where");
    
    echo "<table class='table table-hover'>";
    foreach ( $list_data as $data )
    {
    	$huifang_row = $db->query("select count(*) as count from $table where id = ".$data['id']." AND huifang like '%".date('Y-m-d',$begin_time)."%'", 1, "count");
    	if($huifang_row == 0)
    	{
    		$huifang_status = '';
    	}else
    	{
    		$huifang_status = 'warning';
    	}
    	
    	//���ݻط��������ɫ
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
        		 <td width=5%><input type="checkbox" '.$checked .' value="'.$data['id'].'"/></td>
        		 <td width=9.4%><a href="javascript:void(0)" title="����" onclick="patientbox(\''.$data['name'].'\',\''.$data['id'].'\',\'index\',\'edit\')" >' .
                 $data['name'] . '</a></td>
                 <td width=9.4%>' . $re_arrive_array[$data['re_arrive']]['name'].'</td>
                 <td width=9.4%>' . $data['sex'].'</td>
		         <td width=9.4%>' . $data['age'].'</td>
                 <td width=9.4%>' . $data['disease_name'] .'</td>
                 <td width=9.4%>' . $data['media_from'] . '</td>
                 <td width=9.4%>' . hide_tel($data['tel'],$data['doctor']) .'</td>
                 <td width=9.4%>' . substr($data['author'],0,8) . '</td>
                 <td width=9.4%>' .date('m-d h:i', $data['huifang_date']) . '</td>
                 <td width=5% ><a href="javascript:void(0)" onclick="huifangm('.$data["id"].',\''.$hfhostory.'\')"><i class="icon-comment"></i></a></td>
              </tr>';
    }
    echo "</table>";
    exit();
}

include $mod.".list.tpl.php";

?>
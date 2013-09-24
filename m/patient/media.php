<?php
/*
// - ����˵�� : ý������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-03 14:47
*/
require "../../core/core.php";
$table = "media";

if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// �����Ĵ���:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "add":
			include "media_edit.php";
			exit;

		case "edit":
			$line = $db->query_first("select * from $table where id='$id' limit 1");
			include "media_edit.php";
			exit;

		case "delete":
			$ids = explode(",", $_GET["id"]);
			$del_ok = $del_bad = 0; $op_data = array();
			foreach ($ids as $opid) {
				if (($opid = intval($opid)) > 0) {
					$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
					if ($db->query("delete from $table where id='$opid' limit 1")) {
						$del_ok++;
						$op_data[] = $tmp_data;
					} else {
						$del_bad++;
					}
				}
			}

			if ($del_ok > 0) {
				$log->add("delete", "ɾ������", serialize($op_data));
			}

			if ($del_bad > 0) {
				msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
			} else {
				msg_box("ɾ���ɹ�", "back", 1);
			}

		default:
			msg_box("����δ����...", "back", 1);
	}
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array(
	"page" => "page",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"searchword" => "searchword",
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"ѡ", "width"=>"8%", "align"=>"center"),
	1=>array("title"=>"����", "width"=>"60%", "align"=>"left", "sort"=>"binary name", "defaultorder"=>1),
	3=>array("title"=>"����ʱ��", "width"=>"20%", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	4=>array("title"=>"����", "width"=>"12%", "align"=>"center"),
);

// Ĭ������ʽ:
$defaultsort = 3;
$defaultorder = 1;


// ��ѯ����:
$where = array();
$where[] = "hospital_id=$user_hospital_id";
if ($searchword) {
	$where[] = "(binary name like '%{$searchword}%')";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// ������Ĵ�����
if ($sortid > 0) {
	$sqlsort = "order by ".$aTdFormat[$sortid]["sort"]." ";
	if ($sorttype > 0) {
		$sqlsort .= $aOrderType[$sorttype];
	} else {
		$sqlsort .= $aOrderType[$aTdFormat[$sortid]["defaultorder"]];
	}
} else {
	if ($defaultsort > 0 && array_key_exists($defaultsort, $aTdFormat)) {
		$sqlsort = "order by ".$aTdFormat[$defaultsort]["sort"]." ".$aOrderType[$defaultorder];
	} else {
		$sqlsort = "";
	}
}
//$sqlsort = "order by hospital, id asc";

// ��ҳ����:
$pagesize = 9999;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

$hospital_id_name = $db->query("select id,name from ".$tabpre."hospital", 'id', 'name');


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<script language="javascript"></script>
</head>

<body>
    <header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
             <li class="active"><?=$hospital_id_name[$user_hospital_id]?> - ý����Դ</li>
        </ul>
	</header>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="header_center"><?php echo $power->show_button("add"); ?></div>
	<div class="headers_oprate">
	<form name="topform" method="GET">
	ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="" size="8">&nbsp;
	<input type="submit" class="btn" value="����" style="font-weight:bold" title="�������">&nbsp;
	<button onclick="location='?'" class="btn" title="�˳�������ѯ">�˳�</button>&nbsp;
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">��ע��: �����硱�����绰�� ��ϵͳ����ý����Դ�����ﲻ��Ҫ���ӡ�</div>
</div>

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="table table-striped table-condensed">
	<!-- ��ͷ���� begin -->
	<tr>
<?php
// ��ͷ����:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<? } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$op = array();
		if (check_power("edit")) {
			$op[] = "<a href='?op=edit&id=$id' class='op'>�޸�</a>";
		}
		if (check_power("delete")) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>ɾ��</a>";
		}
		$op_button = implode("&nbsp;", $op);

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;
?>
	<tr<?php echo $hide_line ? " class='hide'" : ""; ?>>
		<td align="center" class="item"><input name="delcheck" type="checkbox" value="<?php echo $id; ?>" onpropertychange="set_item_color(this)"></td>
		<td align="left" class="item"><?php echo $line["name"]; ?></td>
		<td align="center" class="item"><?php echo date("Y-m-d H:i", $line["addtime"]); ?></td>
		<td align="center" class="item"><?php echo $op_button; ?></td>
	</tr>
<?php
	}
} else {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php } ?>
	<!-- ��Ҫ�б����� end -->
</table>
</form>
<!-- �����б� end -->
<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="btn">ȫѡ</button>&nbsp;<button onclick="unselect()" class="btn">��ѡ</button>&nbsp;<?php echo $power->show_button("hide,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>
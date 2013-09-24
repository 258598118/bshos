<?php
// --------------------------------------------------------
// - ����˵�� : ����
// - �������� : fangyang (278294861)
// - ����ʱ�� : 2013-1-29
// --------------------------------------------------------
require "../../core/core.php";
$table = "count_web";

// ���пɹ�����Ŀ:
if ($debug_mode || in_array($uinfo["part_id"], array (
    9 
)))
{
    $types = $db->query("select id,name from count_type where type='web' order by sort desc,id asc", "id", "name");
} else
{
    $hids = implode(",", $hospital_ids);
    $types = $db->query("select id,name from count_type where type='web' and hid in ($hids) order by sort desc,id asc", "id", "name");
}
if (count($types) == 0)
{
    exit("û�п��Թ�������Ŀ");
}

$cur_type = $_SESSION["count_type_id_web"];
if (!$cur_type)
{
    $type_ids = array_keys($types);
    $cur_type = $_SESSION["count_type_id_web"] = $type_ids[0];
}

if ($_GET["date"] && strlen($_GET["date"]) == 6)
{
    $date = $_GET["date"];
} else
{
    $date = date("Ym"); //����
    $_GET["date"] = $date;
}
$date_time = strtotime(substr($date, 0, 4) . "-" . substr($date, 4, 2) . "-01 0:0:0");

// ���� ��,�� ����
$y_array = $m_array = $d_array = array ();
for($i = date("Y"); $i >= (date("Y") - 2); $i--)
    $y_array[] = $i;
for($i = 1; $i <= 12; $i++)
    $m_array[] = $i;
for($i = 1; $i <= 31; $i++)
{
    if ($i <= 28 || checkdate(date("n", $date_time), $i, date("Y", $date_time)))
    {
        $d_array[] = $i;
    }
}

$type_detail = $db->query("select * from count_type where id=$cur_type limit 1", 1);

// �����Ĵ���:
if ($op = $_REQUEST["op"])
{
    if ($op == "add")
    {
        include "web.edit.php";
        exit();
    }
    
    if ($op == "edit")
    {
        include "web.edit.php";
        exit();
    }
    
    if ($op == "log")
    {
        include "web.log.php";
        exit();
    }
    
    if ($op == "delete")
    {
        $ids = explode(",", $_GET["id"]);
        $del_ok = $del_bad = 0;
        $op_data = array ();
        foreach ( $ids as $opid )
        {
            if (($opid = intval($opid)) > 0)
            {
                $tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
                if ($db->query("delete from $table where id='$opid' limit 1"))
                {
                    $del_ok++;
                    $op_data[] = $tmp_data;
                } else
                {
                    $del_bad++;
                }
            }
        }
        
        if ($del_ok > 0)
        {
            $log->add("delete", "ɾ������", serialize($op_data));
        }
        
        if ($del_bad > 0)
        {
            msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
        } else
        {
            msg_box("ɾ���ɹ�", "back", 1);
        }
    }
    
    if ($op == "change_type")
    {
        $cur_type = $_SESSION["count_type_id_web"] = intval($_GET["type_id"]);
        $type_detail = $db->query("select * from count_type where id=$cur_type limit 1", 1);
    }
}

// �ͷ�:
$kefu_list = $type_detail["kefu"] ? explode(",", $type_detail["kefu"]) : array ();

// ���½���:
$month_end = strtotime("+1 month", $date_time) - 1;

$bt = $b = date("Ymd", $date_time);
$et = $e = date("Ymd", $month_end);

$cur_kefu = $_GET["kefu"];
if ($cur_kefu)
{
    // ��ѯ�����ͷ�����:
    $list = $db->query("select * from $table where type_id=$cur_type and kefu='$cur_kefu' and date>=$b and date<=$e order by date asc,kefu asc", "date");
    
    // ��������:
    foreach ( $list as $k => $v )
    {
        // ��ѯԤԼ��:
        $list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 2);
        // ԤԼ������:
        $list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 2);
        // ��ѯ������:
        $list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 2);
        // ��Ч��ѯ��:
        $list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 2);
        // ��ЧԤԼ��:
        $list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 2);
    }
    
    // ����ͳ������:
    $cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");
    // ����:
    $sum_list = array ();
    foreach ( $list as $v )
    {
        foreach ( $cal_field as $f )
        {
            $sum_list[$f] = floatval($sum_list[$f]) + $v[$f];
        }
    }
    // ��ѯԤԼ��:
    $sum_list["per_1"] = @round($sum_list["talk"] / $sum_list["click"] * 100, 2);
    // ԤԼ������:
    $sum_list["per_2"] = @round($sum_list["come"] / $sum_list["orders"] * 100, 2);
    // ��ѯ������:
    $sum_list["per_3"] = @round($sum_list["come"] / $sum_list["click"] * 100, 2);
    // ��Ч��ѯ��:
    $sum_list["per_4"] = @round($sum_list["ok_click"] / $sum_list["click"] * 100, 2);
    // ��ЧԤԼ��:
    $sum_list["per_5"] = @round($sum_list["talk"] / $sum_list["ok_click"] * 100, 2);
} else
{
    //��ѯ��ҽԺ��������:
    $tmp_list = $db->query("select * from $table where type_id=$cur_type  and date>=$b and date<=$e order by date asc,kefu asc");

    // �������:
    $list = $dt_count = array ();
    foreach ( $tmp_list as $v )
    {
        $dt = $v["date"];
        $dt_count[$dt] += 1;
        foreach ( $v as $a => $b )
        {
            if (isset($b) && is_numeric($b))  //������Ϊ0��ʱ��  by fangyang 20130423
            {
                $list[$dt][$a] = floatval($list[$dt][$a]) + $b;
            }
        }
    }
    
    // ��������:
    foreach ( $list as $k => $v )
    {
        // ��ѯԤԼ��:
        $list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 2);
        // ԤԼ������:
        $list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 2);
        // ��ѯ������:
        $list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 2);
        // ��Ч��ѯ��:
        $list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 2);
        // ��ЧԤԼ��:
        $list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 2);
    }
    
    // ����ͳ������:
    $cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");
    // ����:
    $sum_list = array ();
    foreach ( $list as $v )
    {
        foreach ( $cal_field as $f )
        {
            $sum_list[$f] = floatval($sum_list[$f]) + $v[$f];
        }
    }
    
    // ��ѯԤԼ��:
    $sum_list["per_1"] = @round($sum_list["talk"] / $sum_list["click"] * 100, 2);
    // ԤԼ������:
    $sum_list["per_2"] = @round($sum_list["come"] / $sum_list["orders"] * 100, 2);
    // ��ѯ������:
    $sum_list["per_3"] = @round($sum_list["come"] / $sum_list["click"] * 100, 2);
    // ��Ч��ѯ��:
    $sum_list["per_4"] = @round($sum_list["ok_click"] / $sum_list["click"] * 100, 2);
    // ��ЧԤԼ��:
    $sum_list["per_5"] = @round($sum_list["talk"] / $sum_list["ok_click"] * 100, 2);
}

// ����������:  130131  fangyang
//$hospital_id = $db->query("select hid from count_type where type='web' id=$id", "hid");


/*
foreach ( $day_count as $v )
{
    $sum_list["click_all"] += $v["click_all"];
    $sum_list["zero_talk"] += $v["zero_talk"];
}
*/
// �Ƿ������ӻ��޸�����:
$can_edit_data = 0;
if ($debug_mode || in_array($uinfo["part_id"], array (
    9 
)) || in_array($uid, explode(",", $type_detail["uids"])))
{
    $can_edit_data = 1;
}

/*
// ------------------ ���� -------------------
*/
function my_show($arr, $default_value = '', $click = '')
{
    $s = '';
    foreach ( $arr as $v )
    {
        if ($v == $default_value)
        {
            $s .= '<b>' . $v . '</b>';
        } else
        {
            $s .= '<a href="#" onclick="' . $click . '">' . $v . '</a>';
        }
    }
    return $s;
}

// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>��������ͳ��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<style media="print">
.table-condensed th, .table-condensed td{padding:0px 1px}
.forbid { DISPLAY: none }
</style>
<style>
* {
	font-family: "Tahoma";
}

body {
	padding: 5px 8px;
}
form {
	display: inline;
}

#date_tips {
	float: left;
	font-weight: bold;
	padding-top: 1px;
}

#ch_date {
	float: left;
	margin-left: 20px;
}

.site_name {
	display: block;
	padding: 4px 0px;
}

.site_name,.site_name a {
	font-family: "Arial", "Tahoma";
}

.ch_date_a b,.ch_date_a a {
	font-family: "Arial";
}

.ch_date_a b {
	border: 0px;
	padding: 1px 5px 1px 5px;
	color: red;
}

.ch_date_a a {
	border: 0px;
	padding: 1px 5px 1px 5px;
}

.ch_date_a a:hover {
	border: 1px solid silver;
	padding: 0px 4px 0px 4px;
}

.ch_date_b {
	padding-top: 8px;
	text-align: left;
	width: 80%;
	color: silver;
}

.ch_date_b a {
	padding: 0 3px;
}

.main_title {
	margin: 0 auto;
	padding-top: 24px;
	padding-bottom: 5px;
	text-align: left;
	font-weight: bold;
	font-size: 12px;
	font-family: "����";
}

.item {
	/*padding: 8px 3px 6px 3px !important;*/
}

.list .head {
	padding-top: 6px;
	padding-bottom: 4px;
	background-color: #B4DADA;
}
</style>

<script language="javascript">
function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
	return false;
}

function ajax_submit(o) {
	var to = o.action;
	var s = new Array();
	var el = o.getElementsByTagName("input");
	for (var i = 0; i < el.length; i ++) {
		var r = el[i];
		if (r.name != '') {
			s[i] = r.name+"="+encodeURIComponent(r.value);
		}
	}
	var s = s.join("&");
	var xm = new ajax();
	xm.connect(to, "GET", s, ajax_submit_do);
	
	if(o.name=="sycn")
	{
	    document.location.reload();
	}	
}

function ajax_submit_do(o) {
	var out = ajax_out(o);
	if (out["tips"]) {
		alert(out["tips"]);
	}
}

function change_to_edit(o) {
	//if (o.flag == "0") {
		// eidt mode:
		var type_id=<?php echo $cur_type;?>;
	    if(type_id=="0"){alert("����ѡ��ҽԺ��Ŀ");return;};
		var cur = o.innerText;
		
		if (cur.substring(0, 1) == '(') {
			cur = '';
		}
		
		var s = '<input name="data" class="input" style="width:50%;" value="'+cur+'" onfocus="bw()" onblur="ajax_submit(this.form); change_to_view(this)">';
		o.innerHTML = s;
		o.flag = "1";
		o.getElementsByTagName("input")[0].focus();
		
	//} else {
		//
	//}
	  
}

function change_to_view(o) {
	var cur = o.value;
	if (cur == '') {
		cur = '<img src="/static/img/drop-add.gif"/>';
	}
	o.parentNode.flag = '0';
	o.parentNode.innerHTML = cur;
}

function bw() {
	var e = event.srcElement;
	var r =e.createTextRange();
	r.moveStart('character',e.value.length);
	r.collapse(true);
	r.select();
}


</script>
</head>

<body>
    <header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
             <li class="active">������ϸ</li>
        </ul>
	</header>
	<div style="margin: 10px 0 0 0px;">
		<div id="date_tips">��ѡ�����ڣ�</div>
		<form id="ch_date" method="GET">
			<span class="ch_date_a">�꣺<?php echo my_show($y_array, date("Y", $date_time), "return update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
			<span class="ch_date_a">�£�<?php echo my_show($m_array, date("m", $date_time), "return update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>

			<input type="hidden" id="date_1"
				value="<?php echo date("Y", $date_time); ?>"> <input type="hidden"
				id="date_2" value="<?php echo date("n", $date_time); ?>"> <input
				type="hidden" name="date" id="date" value=""> <input type="hidden"
				name="kefu" value="<?php echo $kefu; ?>">
		</form>
		<div class="clear"></div>
	</div>

	<div style="margin: 10px 0 0 0px;">
		<div id="date_tips">ҽԺ��Ŀ��</div>
		<form method="GET" style="margin-left: 30px;">
			<select name="type_id" class="combo span2" onchange="this.form.submit()">
				<option value="" style="color: gray">-��ѡ����Ŀ-</option>
		<?php echo list_option($types, "_key_", "_value_", $cur_type); ?>
	</select> <input type="hidden" name="op" value="change_type">
		</form>
		&nbsp;&nbsp;&nbsp; <b>�ͷ���</b>
		<form method="GET">
			<select name="kefu" class="combo span2" onchange="this.form.submit()">
				<option value="" style="color: gray">-����ҽԺ-</option>
		<?php echo list_option($kefu_list, "_value_", "_value_", $_GET["kefu"]); ?>
	</select> <input type="hidden" name="date" value="<?php echo $date; ?>">
		</form>
		&nbsp;&nbsp;&nbsp;

		<button onclick="location='web_compare.php'" class="btn"
			title="�鿴�ͷ����ݶԱ�">���ݶԱ�</button>
		<button
			onclick="location='web_compare_week.php?month=<?php echo date("Y-m", $date_time); ?>'"
			class="btn" title="�鿴�����ݶԱ�">�ܶԱ�</button>
		<button onclick="location='web_report.php'" class="btn" title="�鿴ͳ������">ͳ������</button>
		<form action="ajax_update.php"  name="sycn" onsubmit="ajax_submit(this); return false;">
			<button type="submit" class="btn" id="sycn" data-loading-text="ͬ����...">ͬ������</button>
			<input type="hidden" name="type" value="sycn">
		</form>
		<button onclick="javascript:printDiv()" class="btn" title="��ӡ����"><i class="icon-print"></i>��ӡ</button>
		<button onclick="window.open('web2excel.php?method=import&begin=<?=$bt?>&end=<?=$et?>&hid=<?=$cur_type?>&title=<?=$type_detail["name"]?>&when=<?=date("Y-n", $date_time)?>')" class="btn" title="��ӡ����"><i class="icon-download-alt"></i>����excel</button>

	</div>
    <!--startprint-->
	<div class="main_title" id="main_title">
		<span style="color: red"><?php echo isset($type_detail["name"])?$type_detail["name"]:'����ѡ��ҽԺ��Ŀ'; ?></span> - <?php echo date("Y-n", $date_time); ?> ����ͳ������
<?php if ($debug_mode || $username == "admin") { ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?op=log" target="_blank">�鿴/������־</a>
<?php } ?>
</div>

	<table width="100%" align="center" class="table table-striped table-condensed" id="list">
		<col width="45"></col>

		<!--  <col ></col> ϵͳ�ܵ��-->
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>

		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>

		<col></col>
		<col></col>
		<col></col>
		<col></col>
		<col></col>


		<thread><tr  style="position: relative; top: expression((this.offsetParent.scrollTop));">
			<th class="head" align="center" width="45">����</th>
			<!-- td class="head" align="center" style="color: red">ϵͳ�ܵ��</td>-->
			<th class="head" align="center" style="color: red" width="45">�ܵ��</th>
			<th class="head" align="center">����</td>
			<th class="head" align="center">���</td>
			<th class="head" align="center" style="color: red">����Ч</th>
			<th class="head" align="center">����</td>
			<th class="head" align="center">���</td>
			<th class="head" align="center" style="color: red">��Ի�</th>

			<th class="head" align="center" style="color: red">����Լ</th>
			<th class="head" align="center">����</td>
			<th class="head" align="center">���</td>
			<th class="head" align="center" style="color: red">Ԥ�Ƶ�Ժ</th>
			<th class="head" align="center">����</td>
			<th class="head" align="center">���</td>
			<th class="head" align="center" style="color: red">ʵ�ʵ�Ժ</th>
			<th class="head" align="center">����</td>
			<th class="head" align="center">���</td>

			<th class="head" align="center" style="color: red">��ѯԤԼ��</th>
			<th class="head" align="center" style="color: red">ԤԼ������</th>
			<th class="head" align="center" style="color: red">��ѯ������</th>
			<th class="head" align="center" style="color: red">��Ч��ѯ��</th>
			<th class="head" align="center" style="color: red">��ЧԤԼ��</th>

			<!--<td class="head" align="center">����</td>-->
		</tr></thread>

<?php
$forbid = "false";
if(strtotime(date("Y-m-", $date_time).$i)>strtotime(date('Y-m-d'))){ $forbid = "true";};
foreach ( $d_array as $i )
{
    $cur_date = date("Ymd", strtotime(date("Y-m-", $date_time) . $i . " 0:0:0"));
    $li = $list[$cur_date];
    if (!is_array($li))
    {
        $li = array ();
    }
    $mode = "add";
    if (!empty($li))
    {
        $mode = "edit";
    }
    $li["kf_click"] = $day_count[$cur_date]["click_all"];
    //$li["zero_talk"] = $day_count[$cur_date]["zero_talk"];
    
    ?>
	<tr <?php $forbid = "false";
              if(strtotime(date("Y-m-", $date_time).$i)>strtotime(date('Y-m-d'))){ $forbid = "true";};
			  if($forbid == "true"){ echo 'class="forbid" style="background:#f2f2f2;color:#dbdbdb !important"'; }
	         
	    ?>>
			<td class="item" align="center"><?php echo date("n", $date_time); ?>��<?php echo $i; ?>��</td>
			<!-- ���� -->
			<!--<td class="item" align="center" style="color: red"><?php echo $li["click"]; ?></td>-->
			<td class="item" align="center" style="color: red"><?php echo $li["click"]; ?></td>
			<td class="item" align="center">
				<form action="ajax_update.php"
					onsubmit="ajax_submit(this); return false;">
					<span id="click_<?php echo $i; ?>" style="cursor: pointer;"
						onclick="change_to_edit(this)" flag="0"><?php if($forbid == "false"){echo isset($li["click_local"]) ? $li["click_local"] : '<img src="/static/img/drop-add.gif"/>';} ?></a></span>
					<input type="hidden" name="date" value="<?php echo $cur_date; ?>">
					<input type="hidden" name="type" value="click_local"> <input
						type="hidden" name="kefu" value="<?=$cur_kefu?>">
				</form>
			</td>
			<td class="item" align="center">
				<form action="ajax_update.php"
					onsubmit="ajax_submit(this); return false;">
					<span id="click_other_<?php echo $i; ?>" style="cursor: pointer;"
						onclick="change_to_edit(this)" flag="0"><?php if($forbid == "false"){echo isset($li["click_other"]) ? $li["click_other"] : '<img src="/static/img/drop-add.gif"/>';} ?></a></span>
					<input type="hidden" name="date" value="<?php echo $cur_date; ?>">
					<input type="hidden" name="type" value="click_other"> <input
						type="hidden" name="kefu" value="<?=$cur_kefu?>">
				</form>
			</td>
			<td class="item" align="center" style="color: red"><?php echo $li["ok_click"]; ?></td>
			<td class="item" align="center">
				<form action="ajax_update.php"
					onsubmit="ajax_submit(this); return false;">
					<span id="ok_click_local_<?php echo $i; ?>"
						style="cursor: pointer;" onclick="change_to_edit(this)" flag="0"><?php if($forbid == "false"){echo isset($li["ok_click_local"]) ? $li["ok_click_local"] : '<img src="/static/img/drop-add.gif"/>';} ?></a></span>
					<input type="hidden" name="date" value="<?php echo $cur_date; ?>">
					<input type="hidden" name="type" value="ok_click_local"> <input
						type="hidden" name="kefu" value="<?=$cur_kefu?>">
				</form>
			</td>
			<td class="item" align="center">
				<form action="ajax_update.php"
					onsubmit="ajax_submit(this); return false;">
					<span id="ok_click_other_<?php echo $i; ?>"
						style="cursor: pointer;" onclick="change_to_edit(this)" flag="0"><?php if($forbid == "false"){echo isset($li["ok_click_other"]) ? $li["ok_click_other"] : '<img src="/static/img/drop-add.gif"/>';} ?></a></span>
					<input type="hidden" name="date" value="<?php echo $cur_date; ?>">
					<input type="hidden" name="type" value="ok_click_other"> <input
						type="hidden" name="kefu" value="<?=$cur_kefu?>">
				</form>
			</td>

			<td class="item redfont" align="center"><?php echo $li["zero_talk"]?$li["zero_talk"]:0; ?></td>

			<td class="item redfont" align="center"><?php echo $li["talk"]?$li["talk"]:0; ?></td>
			<td class="item" align="center"><?php echo $li["talk_local"]?$li["talk_local"]:0?></td>
			<td class="item" align="center"><?php echo $li["talk_other"]?$li["talk_other"]:0?></td>

			<td class="item redfont" align="center"><?php echo $li["orders"]; ?></td>
			<td class="item" align="center"><?php echo $li["order_local"]?$li["order_local"]:0?></td>
			<td class="item" align="center"><?php echo $li["order_other"]?$li["order_other"]:0?></td>
			<td class="item redfont" align="center"><?php echo $li["come"]?$li["come"]:0; ?></td>
			<td class="item" align="center"><?php echo $li["come_local"]? $li["come_local"]:0; ?></td>
			<td class="item" align="center"><?php echo $li["come_other"]? $li["come_other"]:0; ?></td>

			<td class="item redfont" align="center"><?php echo floatval($li["per_1"]); ?>%</td>
			<td class="item redfont" align="center"><?php echo floatval($li["per_2"]); ?>%</td>
			<td class="item redfont" align="center"><?php echo floatval($li["per_3"]); ?>%</td>
			<td class="item redfont" align="center"><?php echo floatval($li["per_4"]); ?>%</td>
			<td class="item redfont" align="center"><?php echo floatval($li["per_5"]); ?>%</td>

			<!-- <td class="item" align="center">
			
            <?php if ($cur_kefu && $can_edit_data) { ?>
			<?php if ($mode == "add") { ?>
			<a href="?op=add&kefu=<?php echo urlencode($cur_kefu); ?>&date=<?php echo date("Y-m-", $date_time).$i; ?>">����</a>
			<?php } else { ?>
			<a href="?op=edit&kefu=<?php echo urlencode($cur_kefu); ?>&date=<?php echo date("Y-m-", $date_time).$i; ?>">�޸�</a>
			<?php } ?>
<?php } ?>
		</td>-->
		</tr>

<?php } ?>

	<tr>
			<td colspan="30" class="tips">���ݻ���</td>
		
		
		<tr>
			<td class="item" align="center">����</td>
			<!--<td class="item" align="center" style="color: red"><?php echo $sum_list["click_all"]; ?></td>-->
			<td class="item" align="center" style="color: red"><?php echo $sum_list["click"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["click_local"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["click_other"]; ?></td>
			<td class="item" align="center" style="color: red"><?php echo $sum_list["ok_click"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["ok_click_local"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["ok_click_other"]; ?></td>
			<td class="item" align="center" style="color: red"><?php echo $sum_list["zero_talk"]; ?></td>

			<td class="item" align="center" style="color: red"><?php echo $sum_list["talk"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["talk_local"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["talk_other"]; ?></td>
			<td class="item" align="center" style="color: red"><?php echo $sum_list["orders"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["order_local"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["order_other"]; ?></td>
			<td class="item" align="center" style="color: red"><?php echo $sum_list["come"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["come_local"]; ?></td>
			<td class="item" align="center"><?php echo $sum_list["come_other"]; ?></td>

			<td class="item" align="center" style="color: red"><?php echo @floatval($sum_list["per_1"]); ?>%</td>
			<td class="item" align="center" style="color: red"><?php echo @floatval($sum_list["per_2"]); ?>%</td>
			<td class="item" align="center" style="color: red"><?php echo @floatval($sum_list["per_3"]); ?>%</td>
			<td class="item" align="center" style="color: red"><?php echo @floatval($sum_list["per_4"]); ?>%</td>
			<td class="item" align="center" style="color: red"><?php echo @floatval($sum_list["per_5"]); ?>%</td>

			<!-- td class="item" align="center">-</td>-->
		</tr>
	</table>
   <!--endprint-->
	<div class="rate_tips alert alert-info">
	<h4 class="alert-heading">ע��!</h4>
    ��ѯԤԼ�� = ԤԼ���� / �ܵ��<br>
    ԤԼ������ = ʵ�ʵ�Ժ���� / Ԥ�Ƶ�Ժ����<br>
	��ѯ������ = ʵ�ʵ�Ժ���� / �ܵ��<br>
	��Ч��ѯ�� = ��Ч��� / �ܵ��<br>
	��ЧԤԼ�� = ԤԼ���� / ��Ч���<br>
</div>
</body>
</html>
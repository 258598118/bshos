<?php
/**
 * ����˵�� : main.php
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

// ʱ����޶���:
$today_tb = mktime(0, 0, 0);
$today_te = $today_tb + 24 * 3600;
$tomorrow_tb = $today_tb + 24 * 7200;
$yesterday_tb = $today_tb - 24 * 3600;
$month_tb = mktime(0, 0, 0, date("m"), 1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);

// ͬ�����ڶ���(2010-11-27):
$tb_tb = strtotime("-1 month", $month_tb);
$tb_te = strtotime("-1 month", time());

// �±�:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb))
{
    $yuebi_tb = $yuebi_te = - 1;
} else
{
    $yuebi_te = $yuebi_tb + 24 * 3600;
}

// �ܱ�:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24 * 3600;

// ͬ��:
$tb_tb = strtotime("-1 month", $month_tb); // ͬ��ʱ�俪ʼ
$tb_te = strtotime("-1 month", time()); // ͬ��ʱ�����
                                        
// ���л���Ĳ�ѯ���:
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
    
    // ������:
    $timeout = 60; // ���泬ʱʱ��
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
        
        // ���»����ļ�:
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
                        unset($tm[$k]); // ɾȥ��ʱ��
                    }
                }
            }
        }
        if ($find == 0)
        {
            $tm[] = $sql_md5 . "|" . $time . "|" . intval($sql_result);
        }
        @file_put_contents($cache_file, implode("\r\n", $tm));
        // ���½���:
        
        return $sql_result;
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
<title>��̨��ҳ</title>
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
			parent.msg_box("�Ѿ�������һ��ҽԺ��", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
		} else {
			parent.msg_box("�Ѿ�������һ��ҽԺ��", 3);
		}
	}
}


//�ط�ajax 20130228 fangyang
function huifangInfo(type) {
	//��ȡ���ܷ�����Ϣ��
	var msg = document.getElementById("huifang");
	var pernum = document.getElementById("pernum");
	//��ȡ��������û���Ϣֵ

	$('#loading',window.parent.document).css('display','block')
    
	switch (type)���� {��
	case 1:
		var f = document.hform;
		var type = "yesterday";����
		break��
	case 2:
		var f = document.hform;
		var type = "today";��
		break��
	case 3:
		var f = document.hform;
		var type = "tomorrow";
		break��
	}

	//���ձ���URL��ַ
	var url = "/m/patient/patient.php";

	//��ҪPOST��ֵ����ÿ��������ͨ��&������
	var postStr = "action=hfdate&type=" + type;

	//ʵ����Ajax
	//var ajax = InitAjax(); 
	var ajax = false;
	//��ʼ��ʼ��XMLHttpRequest����
	if (window.XMLHttpRequest) { //Mozilla �����
		ajax = new XMLHttpRequest();
		if (ajax.overrideMimeType) { //����MiME���
			ajax.overrideMimeType("text/xml");
		}
	} else if (window.ActiveXObject) { // IE�����
		try {
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e) {}
		}
	}
	if (!ajax) { // �쳣����������ʵ��ʧ��
		window.alert("���ܴ���XMLHttpRequest����ʵ��.");
		return false;
	}
	//ͨ��Post��ʽ������
	ajax.open("POST", url, true);

	//���崫����ļ�HTTPͷ��Ϣ
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	//����POST����
	ajax.send(postStr);

	//��ȡִ��״̬
	ajax.onreadystatechange = function() {
		//���ִ��״̬�ɹ�����ô�Ͱѷ�����Ϣд��ָ���Ĳ���
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
$str = '���ã�<font color="#FF0000"><b>' . $realname . '</b></font>';

if ($uinfo["hospitals"] || $uinfo["part_id"] > 0)
{
    if ($uinfo["part_id"] > 0)
    {
        $str .= '��(��ݣ�' . $part_id_name[$uinfo["part_id"]] . ")";
    }
}

$onlines = $db->query("select count(*) as count from sys_admin where online=1", 
        1, "count");
$str .= '���������� <font color="red"><b>' . $onlines . '</b></font> ��&nbsp;&nbsp;';

$str .= '��ǰʱ�䣺<span id=localtime></span>';
if ($uinfo["part_id"] == 12)
{
    // $str .= '<br><a href="#"
// onclick="parent.load_box(1,\'src\',\'patient_huifang_list_all.php\')">[�鿴�б�]</a>';
}

echo $str;
?>
	</div>
-->
<?php if (count($hospital_ids) > 1) { ?>
	<div style="margin-top: 15px;">
			<legend style="padding-bottom: 20px">
				<b>�л�ҽԺ��</b> 
				<select name="hospital_id" id="hospital_id" class="span3" onchange="location='?do=change&hospital_id='+this.value" style="margin-bottom: 0">
					<option value="" style="color: gray">--��ѡ��--</option>
			        <?php echo list_option($hospital_list, 'id', 'name', $_SESSION[$cfgSessionName]["hospital_id"]); ?>
		        </select>&nbsp;
				<button class="btn" onclick="hgo('up');">��</button>
				&nbsp;
				<button class="btn" onclick="hgo('down');">��</button>&nbsp;
	            <?php if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 3) { ?>
		        <button class="btn" onclick="self.location='/m/patient/patient.php?list_huifang=1'" title="�鿴������طù��Ĳ���">�ҵĻط�</button>&nbsp;
	            <?php } ?>

                <?php if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 1|| $uinfo["part_id"] == 9) { ?>
		        <button class="btn" onclick="self.location='/m/report/rp_huifang.php'" title="�ͷ��طü�¼">�ͷ��طü�¼</button>&nbsp;
	            <?php } ?>

</legend>
		</div>
<?php } else if ($user_hospital_id > 0) { ?>
	<div style="margin-top: 20px;">
			<legend style="padding-bottom: 20px">
				��ǰҽԺ��<b><?php echo $hospital_list[$user_hospital_id]["name"]; ?></b>
			</legend>
		</div>
<?php } else { ?>
	  <div style="margin-top: 20px;">
			<legend style="padding-bottom: 20px">û��Ϊ������ҽԺ������ϵ�ϼ�������Ա����</legend>
	</div>
<?php }?>
</div>


<!-- ѡ��ҽԺ�� -->
<?php if ($user_hospital_id > 0): ?>

<!-- ԤԼ����Ȩ�� -->
<?php
    $table = "patient_" . $user_hospital_id;
    $table_fee = 'patient_fee';
    $where = array();
    $where[] = '1';
    if (! $debug_mode)
    {
        $read_parts = get_manage_part(); // �����Ӳ��ţ���ͬ��������)
        $manage_parts = explode(",", $read_parts);
        if ($uinfo["part_admin"] || $uinfo["part_manage"])
        { // ���Ź���Ա�����ݹ���Ա
            $where[] = "(part_id in (" . $read_parts . ") or binary author='" .
                     $realname . "')";
        } else
        { // ��ͨ�û�ֻ��ʾ�Լ�������
            $where[] = "binary author='" . $realname . "'";
        }
    }
    
    // �绰�ط�ֻ��ʾ�ѵ�����:
    if ($uinfo["part_id"] == 12)
    {
        // $where[] = "status='1'";
    }
    
    $sqlwhere = implode(" and ", $where);
    
    //����
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
    
    //����
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
    //����
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
    //����
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
    // ͬ��:
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
    
    //�Һ������� Ԥ��
    //����
    $tomorrow_all = $db->query( "select count(*) as count from $table where $sqlwhere and order_date>=$today_te and order_date<$tomorrow_tb",1, "count");
      
    ?>
<div>

		<div id="main_home" class="container-fluid" style="margin-top: 30px">
			<ul id="tab" class="nav nav-tabs">
				<li class="active"><a href="#home" data-toggle="tab">ժҪ</a></li>
				<?php if(!isset($purview['show_chengjiao'])||@$purview['show_chengjiao']!=0):?><li><a href="#cjiao" data-toggle="tab">�ɽ�����</a></li><?php endif?>
				<?php if(!isset($purview['show_huifang'])||@$purview['show_huifang']!=0):?><li><a href="#remind" data-toggle="tab">�ط�����</a></li><?php endif?>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="home">
					<div class="row-fluid">
						<div class="span10">
								<table class="table table-hover">
								    <thead>
								        <tr>
								            <th width=10%><span class="text-error">�ܱ�</span></th>
								            <th width=10%>Ԥ��</th>
								            <th width=10%>�ѵ�</th>
								            <th width=10%>δ��</th>
								            <th width=10% class="text-info">����</th>
								            <th width=10% class="text-info">����</th>
								            <th width=10% class="text-info">����</th>
								            <th width=10% class="text-info">������</th>
								            <th width=10%>�ɽ�</th>
								            <th width=10%>��ʧ</th>
								        </tr>
								    </thead>
									<tr>
										<td><b class="">����</b></td>
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
										<td><b class="">����</b></td>
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
										<td><b class="">����</b></td>
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
										<td><b>����</b></td>
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
										<td>����</td>
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
										<td><b>ͬ��</b></td>
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
						<!-- ����Ա����ͳ������ -->
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
        

        // ͬ��
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
						            <th><span class="text-error">����</span></th>
						            <th>ԤԼ</th>
						            <th>Ԥ��</th>
						            <th>����</th>
						        </tr>
						    </thead>
							<tr>
								<td><b>����</b����></td>
								<td><span title="���տͷ�ԤԼ����"><a
										href="/m/patient/patient.php?show=today&time_type=addtime&part_id=2&re_arrive=0"><?php echo $web_1; ?>
									</a></span></td>
								<td><span title="����Ԥ�Ƶ�Ժ����"><a
										href="/m/patient/patient.php?show=today&part_id=2&re_arrive=0"><?php echo $web_7; ?>
									</a></span></td>
								<td><span title="�����Ѿ���Ժ����"><a
										href="/m/patient/patient.php?show=today&part_id=2&come=1&re_arrive=0"><?php echo $web_4; ?>
									</a></span></td>
							</tr>
							<tr>
								<td><b>����</b></td>
								<td><span title="���տͷ�ԤԼ����"><a
										href="/m/patient/patient.php?show=yesterday&time_type=addtime&part_id=2&re_arrive=0"><?php echo $web_2; ?>
									</a></span></td>
								<td><span title="����Ԥ�Ƶ�Ժ����"><a
										href="/m/patient/patient.php?show=yesterday&part_id=2&re_arrive=0"><?php echo $web_8; ?>
									</a></span></td>
								<td><span title="�����Ѿ���Ժ����"><a href="/m/patient/patient.php?show=yesterday&part_id=2&come=1&re_arrive=0"><?php echo $web_5; ?>
									</a></span></td>
							</tr>
							<tr>
								<td><b>����</b></td>
								<td><span title="���¿ͷ�ԤԼ����"><a href="/m/patient/patient.php?show=thismonth&time_type=addtime&part_id=2&re_arrive=0"><?php echo $web_3; ?>
									</a></span></td>
								<td><span title="����Ԥ�Ƶ�Ժ����"><a href="/m/patient/patient.php?show=thismonth&part_id=2&re_arrive=0"><?php echo $web_9; ?>
									</a></span></td>
								<td><span title="�����Ѿ���Ժ����"><a href="/m/patient/patient.php?show=thismonth&part_id=2&come=1&re_arrive=0"><?php echo $web_6; ?> </a></span></td>
							</tr>
							<tr>
								<td><b>ͬ��</b></td>
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
        
        // ͬ��
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
					            <th><span class="text-error">�绰</span></th>
					            <th>ԤԼ</th>
					            <th>Ԥ��</th>
					            <th>����</th>
					        </tr>
					    </thead>
						<tr>
							<td><b>����</b></td>
							<td><a href="/m/patient/patient.php?show=today&time_type=addtime&part_id=3"><?php echo $tel_1; ?></a></td>
							<td><a href="/m/patient/patient.php?show=today&part_id=3"><?php echo $tel_7; ?></a></td>
							<td><a href="/m/patient/patient.php?show=today&part_id=3&come=1"><?php echo $tel_4; ?></a></td>
						</tr>
						<tr>
							<td><b>����</b></td>
							<td><a href="/m/patient/patient.php?show=yesterday&time_type=addtime&part_id=3"><?php echo $tel_2; ?> </a></td>
							<td><a href="/m/patient/patient.php?show=yesterday&part_id=3"><?php echo $tel_8; ?> </a></td>
							<td><a href="/m/patient/patient.php?show=yesterday&part_id=3&come=1"><?php echo $tel_5; ?></a></td>
						</tr>
						<tr>
							<td><b>����</b></td>
							<td><a href="/m/patient/patient.php?show=thismonth&time_type=addtime&part_id=3"><?php echo $tel_3; ?></a></td>
							<td><a href="/m/patient/patient.php?show=thismonth&part_id=3"><?php echo $tel_9; ?></a></td>
							<td><a href="/m/patient/patient.php?show=thismonth&part_id=3&come=1"><?php echo $tel_6; ?> </a></td>
						</tr>
						<tr>
							<td><b>ͬ��</b></td>
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
			                <th>#����λ:Ԫ��</th>
			                <th width=11%>�ܳɽ�</th>
			                <th width=11%>����</th>
			                <th width=11%>����</th>
			                <th width=11%>����</th>
			                <th width=11%>������</th>
			                <th width=11%>δ��</th>
			                <th width=11%>Ԥ��</th>
			                <th width=11%>�˿�</th>
			            </tr>
			        </thead>
			        <tbody id="feetable" >
				        <tr id="todaydata">
				            <td><b>����</b></td>
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
				            <td><b>����</b></td>
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
				            <td><b>����</b></td>
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
				            <td><b>����</b></td>
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
				            <td><b>ͬ��</b></td>
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
						        <custom id="locdate">����</custom>
								<custom>��Ҫ�ط�</custom>
								<span class="text-error">
									<a href="javascrip:void(0)" class="text-error" style="padding: auto 3px;" id="pernum"><?=count($list_data)?></a>
								</span>
								 <custom>��</custom>
						    </span>
						    <span class="label label-info" ><a href="/m/patient/patient.list.huifang.php" style="color:#fff" title="��ϸԤԼ�б�">��ϸ</a></span>
							<span style="float: right" class="btn-group" data-toggle="buttons-radio">
							    <button class="btn" name="yesterday" onclick="huifangInfo(1);document.getElementById('locdate').innerHTML='����'">����</button>
								<button class="btn active" name="today" onclick="huifangInfo(2);document.getElementById('locdate').innerHTML='����'">����</button>
								<button class="btn" name="tomorrow" onclick="huifangInfo(3);document.getElementById('locdate').innerHTML='����'">����</button>
						    </span>
									    
							<table class="table table-striped table-hover" style="margin-top: 10px; margin-bottom: 5px;">
							    <thead>
									<tr style="font-weight: bolder">
										<th width=5%>ѡ</th>
										<th width=9.4%>����</th>
										<th width=9.4%>״̬</th>
										<th width=9.4%>�Ա�</th>
										<th width=9.4%>����</th>
										<th width=9.4%>��Ŀ</th>
										<th width=9.4%>ý����Դ</th>
										<th width=9.4%>�绰</th>
										<th width=9.4%>�ͷ�</th>
										<th width=9.4%>ʱ��</th>
										<th width=5%>����</th>
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
						    		     <td width=5%><input type="checkbox"  '.$checked .'/></td>
						 		         <td width=9.4%><a href="javascript:void(0)" title="����" onclick="patientbox(\''.$data['name'].'\',\''.$data['id'].'\',\'index\')" >' .
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

	<!-- �طñ� start -->
	<div id="huifangmodal" class="modal hide" data-backdrop='false'>
	    <form class="form-horizontal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"  aria-hidden="true">��</button>
			<h3>�طü�¼</h3>
		</div>
		<div class="modal-body">
		    <fieldset>
				<div class="control-group">
					<label class="control-label" for="appendedInput">�ؼ��ʣ�</label>
					<div class="controls">
						<div class="input-append">
							<textarea class="input-xlarge"  name="searchword"  rows="3"></textarea>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="modal-footer">
			<a href="#" data-dismiss="modal"  class="btn">�ر�</a> 
			<a href="#" type="submit" class="btn btn-primary">����</a>
		</div>
		</form>
	</div>
	<!-- �طñ� END -->
<?php endif;?>
<script>	
//������ʾ
$("a[rel=tooltip],input[rel=tooltip],div[rel=tooltip],select[rel=tooltip],button[rel=tooltip]").tooltip();
$("a[rel=popover],button[rel=popover]").popover();
function drawChart(data,title)
{
	var partd,parttitle;
	if(typeof(data) == "undefined" ){
		partd='';
		parttitle = '��ѡ��һ����ʾ��ͼ'
	}else{
		partd=data;	
		parttitle = title+'������ɱ�ͼ';
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
            name: '�ݶ�',
            data: eval(partd)
        }]
    })
}	

$(function () {
	drawChart()
	var table  = $("#feetable");
	var trs  = table.find("tr");
	//for(var i=0; i<trs.length; i++){
	    //var tr = trs.eq(i);//ѭ����ȡÿһ��
	    trs.bind("click", function(){//Ϊÿһ�����click�¼�
	        var td = $(this).find("td");
	        var title = td.eq(0).text()
		    var chu = td.eq(1).text();
	        var fu = td.eq(2).text();
	        var cha = td.eq(3).text();
	        var zai = td.eq(4).text();
	        data = "[['����',"+chu.replace(/,/g,'')+"], ['����',"+fu.replace(/,/g,'')+"], ['����',"+cha.replace(/,/g,'')+"], ['������',"+zai.replace(/,/g,'')+"]]";
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
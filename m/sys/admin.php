<?php
/*
// - ����˵�� : admin.php
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-11 23:16
*/
require "../../core/core.php";
$table = "sys_admin";
if (!$debug_mode && !$uinfo["part_admin"]) {
	exit_html("û�д�Ȩ��..."); //�����ǲ��Ź���Ա
}

// �����Ĵ���:
$op = $_REQUEST["op"];
if ($op) {
	include "admin.op.php";
}

$sqlwhere = "1";

// ��Ա��ȡ����:
if (!$debug_mode && $username != "admin") {
	$hd_s = array();
	foreach ($hospital_ids as $v) {
		$hd_s[] = ','.$v.',';
	}
	$hd = implode("", $hd_s);
	$sqlwhere .= " and ('$hd' like concat('%,',replace(hospitals,',',',%,'),',%') or hospitals='')";
}

// ����:
if ($key = $_GET["key"]) {
	$sqlwhere .= " and (name like '%{$key}%' or realname like '%{$key}%')";
}

// �ų�
$sqlwhere .= " and name!='$username'";


$group_type = array(1 => "����", 2 => "Ȩ��", 3 => "ҽԺ", 4 => "��������", 5 => "���õ��˺�", 6 => "�����û�");
$cur_group = intval($_SESSION["admin_group_type"]);
if (!$cur_group) {
	$cur_group = $_SESSION["admin_group_type"] = 1;
}

// ------------- ҳ�濪ʼ ---------------
?><!DOCTYPE html>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?><?php foreach ($easydialog as $x){echo $x;}?>
<style>
.admin_list {margin-left:10px; margin-top:10px; }
#rec_part, #rec_user {margin-top:6px; }
.rub {width:180px; float:left; }
.rub input {float:left; }
.rub a {display:block; float:left; padding-top:2px; }
.rgp {clear:both; margin:5px 0 5px 0; font-weight:bold; }
.group_select {margin-top:10px; margin-bottom:0px; text-align:center; }
</style>
<script language="javascript">
function ucc(o) {
	o.parentNode.getElementsByTagName("a")[0].style.color = o.checked ? "red" : "";
}
function sd(id) {
	var ss = byid("g_"+id).getElementsByTagName("INPUT");
	for (var i=0; i<ss.length; i++) {
		ss[i].checked = !ss[i].checked;
	}
	return false;
}
function dialog(id) {	easyDialog.open({		container : {			header : '<span class=red14></span>�޸�����(�����޸�)',			content :'<iframe  frameborder=0 scrolling=yes width=960 height=500 src="m/sys/admin.php?op=edit&id='+id+'"></iframe>',			noText:'�ر�',			noFn : true		},		fixed : true, 	})	return false;}
function del() {
	if (confirm("���ȷ��Ҫɾ����Щ��Ա������ؽ���������")) {
		byid("op_value").value = "delete";
		byid("mainform").submit();
	}
}
function close_account() {
	byid("op_value").value = "close";
	byid("mainform").submit();
}

function open_account() {
	byid("op_value").value = "open";
	byid("mainform").submit();
}

function set_ch() {
	byid("new_ch").style.display = byid("new_ch").style.display == "none" ? "inline" : "none";
}
function submit_ch() {
	if (byid("ch_id").value > 0) {
		byid("op_value").value = "set_ch";
		byid("mainform").submit();
	} else {
		alert("��ѡ��Ҫ���õ�Ȩ�ޣ�");
		byid("ch_id").focus();
		return false;
	}
}
</script>
</head>

<body>    <header class="jumbotron subhead"  style="margin-bottom: 20px;">		<ul class="breadcrumb">             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>             <li class="active text-info"><b>ϵͳ��Ա����</b></li>        </ul>	</header>

<div class="space"></div>
<div class="group_select">    <?php echo $power->show_button("add"); ?>
	<b>���з�ʽ��</b>
	<form method="GET" style="display:inline;">
		<select name="group" class="span2" onchange="this.form.submit()">
			<?php echo list_option($group_type, "_key_", "_value_", $cur_group); ?>
		</select>
		<input type="hidden" name="op" value="change_group_type">
		<input type="hidden" name="key" value="<?php echo $_GET["key"]; ?>">
	</form>&nbsp;&nbsp;

	<b>�������֣�</b>
	<form method="GET" style="display:inline;">
		<input name="key" value="<?php echo $_GET["key"]; ?>" class="span2" size="12">
		<input type="submit" class="btn" value="����">
		<input type="submit" class="btn" onclick="this.form.key.value=''" value="����">
	</form>
</div>

<div class="space"></div>
<form method="POST" name="mainform" id="mainform" action="?">
<div class="admin_list">
	<div id="rec_user">
<?php
if ($cur_group == 1) { //����
	$id_name = $db->query("select id,name,if(id=9,0,id) as sort from sys_part order by sort", "id", "name");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and part_id='$k' order by realname", "id");
		echo '<div class="rgp text-error">'.$v.'('.count($all_admin).')'.' <a href="javascript:void(0)" onclick="return sd('.$k.')">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'" style="display:inline-block">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="/m/sys/admin.php?op=edit&id='.$b["id"].'">'.cut($b["realname"],10)." (".cut($b["name"],10).") ".'</a></div>';			
		}
		echo '</div>';
	}
} else if ($cur_group == 2) { //��ɫ
	$id_name = $db->query("select id,concat(name,' (',author,')') as name from sys_character", "id", "name");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and character_id='$k' order by realname", "id");
		echo '<div class="rgp text-error">'.$v.'('.count($all_admin).')'.' <a href="javascript:void(0)" onclick="return sd('.$k.')">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'" style="display:inline-block">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="/m/sys/admin.php?op=edit&id='.$b["id"].'">'.cut($b["realname"],10)." (".cut($b["realname"],10).") ".'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 3) { //ҽԺ
	$allow_ids = implode(",", $hospital_ids);
	$id_name = $db->query("select id,name from hospital where id in ($allow_ids) order by sort desc,id asc", "id", "name");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and concat(',',hospitals,',') like '%,".$k.",%' order by realname", "id");
		echo '<div class="rgp text-error">'.$v.'('.count($all_admin).')'.' <a href="javascript:void(0)" onclick="return sd('.$k.')">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'" style="display:inline-block">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="/m/sys/admin.php?op=edit&id='.$b["id"].'">'.cut($b["realname"],10)." (".cut($b["realname"],10).") ".'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 4) { //����
	$id_name = array(1 => "��������", 0 => "��ͨ��Ա(������)");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and part_admin='$k' order by realname", "id");
		echo '<div class="rgp text-error">'.$v.'('.count($all_admin).')'.' <a href="javascript:void(0)" onclick="return sd('.$k.')">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'" style="display:inline-block">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="/m/sys/admin.php?op=edit&id='.$b["id"].'">'.cut($b["realname"],10)." (".cut($b["realname"],10).") ".'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 5) { //����
	$id_name = array(0 => "���õ��˺�", 1 => "��ͨ���˺�");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname,isshow from sys_admin where $sqlwhere and isshow='$k' and id!='$uid' order by realname", "id");
		echo '<div class="rgp text-error">'.$v.'('.count($all_admin).')'.' <a href="javascript:void(0)" onclick="return sd('.$k.')">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'" style="display:inline-block">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="/m/sys/admin.php?op=edit&id='.$b["id"].'">'.cut($b["realname"],10)." (".cut($b["realname"],10).") ".($b["isshow"]!=1 ? ' <font class="text-error">��</font>' : '').'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 6) { //����
	$id_name = array(1 => "����", 0 => "������");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname,isshow from sys_admin where $sqlwhere and isshow=1 and online='$k' and id!='$uid' order by realname", "id");
		echo '<div class="rgp text-error">'.$v.'('.count($all_admin).')'.' <a href="javascript:void(0)" onclick="return sd('.$k.')">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'" style="display:inline-block">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="/m/sys/admin.php?op=edit&id='.$b["id"].'">'.cut($b["realname"],10)." (".cut($b["realname"],10).") ".'</a></div>';
		}
		echo '</div>';
	}
}
?>
		<div class="clear"></div>
	</div>
</div>
<input type="hidden" name="op" id="op_value" value="">
<div class="space" style="height:10px;"></div>
<hr/>
<b>&nbsp;&nbsp;������</b>
<button onclick="select_all()" type="button" class="btn">ȫѡ</button>&nbsp;
<button onclick="unselect()" type="button" class="btn">��ѡ</button>&nbsp;

<b>&nbsp;&nbsp;��ѡ��Ա��</b>
<?php if ($debug_mode || $uinfo["part_id"] == 9) { ?>
<button onclick="del()" type="button" class="btn btn-danger">ɾ��</button>&nbsp;
<?php } ?>
<button onclick="close_account()" type="button" class="btn">�ر��ʻ�</button>&nbsp;
<button onclick="open_account()" type="button" class="btn">��ͨ�ʻ�</button>&nbsp;
<button onclick="set_ch()" type="button" class="btn">����Ȩ��</button>&nbsp;
<span id="new_ch" style="display:none;">
	<select name="ch_id" id="ch_id" class="span2">
		<option value="" style="color:gray">-��ѡ����Ȩ��-</option>
<?php
$id_name = $db->query("select id,concat(name,' (',author,')') as name from sys_character", "id", "name");
echo list_option($id_name, "_key_", "_value_");
?>
	</select>&nbsp;
	<button onclick="submit_ch()" class="btn">ȷ��</button>
</span>

<div class="space" style="height:20px;"></div>
</form>

</body>
</html>
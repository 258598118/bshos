<?php
/*
// - ����˵�� : ��������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-02-28
*/
require "../../core/core.php";
set_time_limit(0);

if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

$table = "patient_".$user_hospital_id;

$time_array = array("order_date"=>"��Ժʱ��", "addtime"=>"���ʱ��");
$status_array = array("all"=>"����", "come"=>"�ѵ�", "not"=>"δ��");
$sort_array = array("order_date"=>"��Ժʱ��", "name"=>"����");
$part_array = array("2"=>"����", "3"=>"�绰");
$depart_array = $db->query("select id,name from depart where hospital_id='$user_hospital_id'", "id", "name");
$disease_array = $db->query("select id,name from disease where hospital_id='$user_hospital_id'", "id", "name");

$op = $_GET["op"];

// ����ʱ��:
if ($op == "show") {
	$where = array();

	$time_ty = "order_date";
	if ($_GET["ty"] && array_key_exists($_GET["ty"], $time_array)) {
		$time_ty = $_GET["ty"];
	}

	if ($_GET["btime"]) {
		$tb = strtotime($_GET["btime"]." 0:0:0");
		$where[] = "$time_ty>=$tb";
	}
	if ($_GET["etime"]) {
		$te = strtotime($_GET["etime"]." 23:59:59");
		$where[] = "$time_ty<$te";
	}

	switch ($_GET["status"])
	{
		case 'all':
			break;
		case 'come':
			$where[] = 'status=1';
			break;
		case 'not':
			$where[] = 'status!=1';
			break;
	}

	if ($_GET["part"]) {
		$where[] = "part_id=".intval($_GET["part"]);
	}
    
	if ($_GET["disease"]) {
		$where[] = "disease_id=".intval($_GET["disease"]);
	}
	
	if ($_GET["depart"]) {
		$where[] = "depart=".intval($_GET["depart"]);
	}
   
	$sqlwhere = count($where) ? ("where ".implode(" and ", $where)) : "";
	
	$sort = $_GET["sort"] ? $_GET["sort"] : "order_date";


	$list = $db->query("select * from $table $sqlwhere order by $sort asc", "");
	
	// ���:
	$fields = $_GET["fields"];

	// ��������ת��:
	if (in_array("disease_id", $fields)) {
		$disease_id_name = $db->query("select id,name from disease", "id", "name");
	}

	$output_name = array();
	foreach ($list as $li) {
		$line = array();
		foreach ($fields as $x) {
			if ($x == "order_date" || $x == "addtime") {
				$y = @date("Y-m-d", $li[$x]);
			} else if ($x == "disease_id") {
				$y = $disease_id_name[$li[$x]];
			} else {
				$y = $li[$x];
			}
			// �滻���лس�����Ϊ�ո�:
			$y = str_replace("\n", " ", str_replace("\r", "", $y));
			// ����ո��滻Ϊһ��:
			while (substr_count($y, "  ") > 0) {
				$y = str_replace("  ", " ", $y);
			}
			// ��ֵ��ʾ������Ϊռλ
			$line[] = (trim($y) == "" ? "-" : $y);
		}
		$output_name[] = @implode("\t", $line);
	}

	$output_name = implode("\r\n", $output_name);

}

$title = '��������';
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($easydialog as $x){echo $x;}?>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
form {display:inline; }

#result {margin-left:30px; margin-top:10px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }
label{display:inline-block}
</style>
</head>

<body>
<!-- ͷ�� begin -->
<header class="jumbotron subhead"  style="margin-bottom: 20px;">
    <ul class="breadcrumb">
          <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
          <li class="active"><?php echo $title; ?></li>
    </ul>
</header>
<!-- ͷ�� end -->
<div id="tiaojian">
<span>����������</span><br/>
<form method="GET" class="form-horizontal">
	<select name="ty" class="span2">
		<option value="" style="color:gray">-ʱ������-</option>
		<?php echo list_option($time_array, "_key_", "_value_", $time_ty); ?>
	</select>&nbsp;
	<input size="16" type="text" value="" name="btime" class="start_datetime span2" id="begin_time" value="<?php echo $_GET["btime"] ? $_GET["btime"] : date("Y-m-01"); ?>"
			readonly> 
	<input size="16" type="text" value="" name="etime" class="end_datetime span2" id="end_time" value="<?php echo $_GET["etime"] ? $_GET["etime"] : date("Y-m-01"); ?>"
			readonly> 
	<select name="status" class="span2">
		<option value="" style="color:gray">-�Ƿ�Ժ-</option>
		<?php echo list_option($status_array, "_key_", "_value_", $_GET["status"]); ?>
	</select>&nbsp;
	<select name="sort" class="span2">
		<option value="" style="color:gray">-�������-</option>
		<?php echo list_option($sort_array, "_key_", "_value_", $_GET["sort"]); ?>
	</select>&nbsp;
	<select name="part" class="span2">
		<option value="" style="color:gray">-����-</option>
		<?php echo list_option($part_array, "_key_", "_value_", $_GET["part"]); ?>
	</select>&nbsp;
	<select name="depart" class="span2">
		<option value="" style="color:gray">-����-</option>
		<?php echo list_option($depart_array, "_key_", "_value_", $_GET["depart"]); ?>
	</select>&nbsp;
	
    <select name="disease"	class="span2">
        <option value="" style="color:gray">-����-</option>
        <?php echo list_option($disease_array, "_key_", "_value_", $_GET["disease"]);?>
    </select>
	
	<br><br>
	<span style="float:left">����ֶΣ�</span>
	<table class="table table-bordered table-condensed">
	<tr>
	<td><input type="checkbox" name="fields[]" id="ch1" value="name" checked><label for="ch1">����</label></td>
	<td><input type="checkbox" name="fields[]" id="ch2" value="sex" <?php echo (@in_array("sex", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch2">�Ա�</label></td>
	<td><input type="checkbox" name="fields[]" id="ch3" value="age" <?php echo (@in_array("age", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch3">����</label></td>
	<td><input type="checkbox" name="fields[]" id="ch4" value="tel" <?php echo (@in_array("tel", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch4">�绰����</label></td>
	<td><input type="checkbox" name="fields[]" id="ch5" value="zhuanjia_num" <?php echo (@in_array("zhuanjia_num", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch5">ר�Һ�</label></td>
	<td><input type="checkbox" name="fields[]" id="ch6" value="disease_id" <?php echo (@in_array("disease_id", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch6">��������</label></td>
	<td><input type="checkbox" name="fields[]" id="ch7" value="content" <?php echo (@in_array("content", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch7">��ѯ����</label></td>
	<td><input type="checkbox" name="fields[]" id="ch8" value="media_from" <?php echo (@in_array("media_from", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch8">ý����Դ</label></td>
	<td><input type="checkbox" name="fields[]" id="ch9" value="memo" <?php echo (@in_array("memo", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch9">��ע</label></td>
	<td><input type="checkbox" name="fields[]" id="ch10" value="author" <?php echo (@in_array("author", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch10">�ͷ�</label></td>
	<td><input type="checkbox" name="fields[]" id="ch11" value="order_date" <?php echo (@in_array("order_date", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch11">Ԥ��ʱ��</label></td>
	<td><input type="checkbox" name="fields[]" id="ch12" value="addtime" <?php echo (@in_array("addtime", $_GET["fields"]) ? "checked" : ""); ?>><label for="ch12">���ʱ��</label></td>
    </tr>
    </table> 
	<div class="form-actions">
	    <input type="hidden" name="op" value="show">
        <button type="submit" class="btn">�ύ</button>
    </div>
</form>
</div>

<?php if ($op == "show") { ?>
<div class="space"></div>
<div id="result">
	<textarea id="result_box" style="width:95%; height:450px;" class="input"><?php echo $output_name; ?></textarea><br>
	<br>
	˵�����ϱ����Ľ�����Ƶ�Excel�У����Զ�������ʾ��<br>
	<br>

</div>
<?php } ?>

	<script>
$(".start_datetime").datetimepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayBtn: true,
    minView:'month',
    maxView:'year', 
    pickerPosition: "bottom-left"   
});
$(".end_datetime").datetimepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayBtn: true,
    minView:'month',
    maxView:'year',
    pickerPosition: "bottom-left"
});
</script>
</body>
</html>
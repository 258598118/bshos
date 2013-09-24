<?php
/*
// - ����˵�� : ����
// - �������� : fangyang (278294861)
// - ����ʱ�� : 2013-03-06
*/

$p_type = $uinfo["part_id"]; // 0,1,2,3,4

$title = '��������';

$admin_name = $db->query("select realname from sys_admin", "", "realname");
$author_name = $db->query("select distinct author from $table order by binary author", "", "author");
$kefu_23_list = array_intersect($admin_name, $author_name);

$kefu_4_list = $db->query("select name,realname from " . $tabpre . "sys_admin where hospitals='$user_hospital_id' and part_id in (4)");
$doctor_list = $db->query("select name from " . $tabpre . "doctor where hospital_id='$user_hospital_id'");

$disease_list = $db->query("select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id");
$depart_list = $db->query("select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id");

$media_list = $db->query("select name from media where hospital_id=$user_hospital_id order by id asc", "", "name");
$media_list = array_merge(array (   "����", "�绰" ), $media_list);

// ʱ�䶨��
// ����
$yesterday_begin = strtotime("-1 day");
// ����
$this_month_begin = mktime(0, 0, 0, date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// �ϸ���
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//����
$this_year_begin = mktime(0, 0, 0, 1, 1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// ���һ����
$near_1_month_begin = strtotime("-1 month");
// ���������
$near_3_month_begin = strtotime("-3 month");
// ���һ��
$near_1_year_begin = strtotime("-12 month");

?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($easydialog as $x){echo $x;}?>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	//if (oForm.name.value == "") {
	//	alert("�����롰�������ơ���"); oForm.name.focus(); return false;
	//}
	return true;
}
function write_dt(da, db) {
	byid("start_datetime").value = da;
	byid("end_datetime").value = db;
}
</script>
<style>
legend{margin:5px}
</style>
</head>

<body>

	<!-- ͷ�� begin -->
	<header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
              <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
              <li class="active">ԤԼ��������</li>
        </ul>
	</header>

	<!-- ͷ�� end -->
	<form name="mainform" action="patient.php" method="GET"
		class="form-horizontal" onsubmit="return Check()">
		<fieldset>
			<!-- �ؼ��� -->
			<legend>�ؼ���</legend>
			<div class="control-group">
				<label class="control-label" >�ؼ���</label>
				<div class="controls">
					<input class="span2" id="appendedInput" name="searchword" size="16" type="text">
					<p class="help-block">��������Դ�����</p>
				</div>
			</div>
			<!--�ؼ���END  -->
			<!-- ʱ������ start -->
			<legend>ʱ������</legend>
			<div class="control-group">
				<label class="control-label" >ʱ������</label>
				<div class="controls">
					<select name="time_type" class="span2">
						<option value="" style="color: gray">--��ѡ��--</option>
						<option value="order_date">Ԥ��ʱ��</option>
						<option value="addtime">�������ʱ��</option>
					</select>
					<p class="help-block">��ѡ����Դ�����</p>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" >��ʼʱ��</label>
				<div class="controls">
						<input size="16" type="text" value="" name="btime" class="span2" id="start_datetime" readonly>
					���� <a href="javascript:write_dt('<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>')">[����]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $yesterday_begin); ?>','<?php echo date("Y-m-d", $yesterday_begin); ?>')">[����]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_month_begin); ?>','<?php echo date("Y-m-d", $this_month_end); ?>')">[����]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $last_month_begin); ?>','<?php echo date("Y-m-d", $last_month_end); ?>')">[����]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_year_begin); ?>','<?php echo date("Y-m-d", $this_year_end); ?>')">[����]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_1_month_begin); ?>','<?php echo date("Y-m-d"); ?>')">[��һ����]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_3_month_begin); ?>','<?php echo date("Y-m-d"); ?>')">[��������]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_1_year_begin); ?>','<?php echo date("Y-m-d"); ?>')">[��һ��]</a>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" >��ֹʱ��</label>
				<div class="controls">
						<input size="16" type="text" value="" name="etime" readonly id="end_datetime" class="span2">
					</div>
				</div>
			</div>
			<!-- ʱ������ end -->
			<!-- ��Ա���� start -->
			<legend>��Ա����</legend>
			<?php ?>
			<div class="control-group">
				<label class="control-label" >�ѿͷ�</label>
				<div class="controls">
					<select name="kefu_23_name" class="span2">
						<option value='' style="color: gray">--��ѡ��--</option>
			            <?php echo list_option($kefu_23_list, '_value_', '_value_', ''); ?>
		             </select>
		             <p class="help-block">ָ��Ҫ�����ĵ�ҽ (��ѡ����Դ�����)</p>
				</div>
			</div>
			<?php  ?>
	
            <?php if ($debug_mode || $uinfo["part_admin"] || in_array($uinfo["part_id"], array(3,4))) { ?>
			<div class="control-group">
				<label class="control-label" >�ѵ�ҽ</label>
				<div class="controls">
					<select name="kefu_4_list" class=" span2">
						<option value='' style="color: gray">--��ѡ��--</option>
			            <?php echo list_option($kefu_4_list, 'realname', 'realname', ''); ?>
		            </select>
		            <p class="help-block">ָ��Ҫ�����ĵ�ҽ (��ѡ����Դ�����)</p>
				</div>
			</div>
			<?php } ?>
			<?php if ($debug_mode || $uinfo["part_admin"]) { ?>
			<div class="control-group">
				<label class="control-label" >��ҽ��</label>
				<div class="controls">
					<select name="doctor_name" class=" span2">
						<option value='' style="color: gray">--��ѡ��--</option>
			            <?php echo list_option($doctor_list, 'name', 'name', ''); ?>
		            </select>
		            <p class="help-block">ָ��Ҫ�����ĽӴ�ҽ�� (��ѡ����Դ�����)</p>
				</div>
			</div>
			<?php } ?>
			
			<!-- ��Ա���� end -->
			<!-- ���������� start -->
			<legend>����������</legend>
			<div class="control-group">
				<label class="control-label" >��������</label>
				<div class="controls">
					<select name="re_arrive" class="span2">
						<option value="" style="color: gray">--��ѡ��--</option>
						<?php echo list_option($re_arrive_full, '_key_', '_value_');?>		
			        </select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" >��Լ״̬</label>
				<div class="controls">
					<select name="come" class=" span2">
						<option value='' style="color: gray">--��ѡ��--</option>
						<option value='0'>δ��</option>
						<option value='1'>�ѵ�</option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" >��������</label>
				<div class="controls">
					<select name="disease" class=" span2">
						<option value='' style="color: gray">--��ѡ��--</option>
			            <?php echo list_option($disease_list, "id", "name", ''); ?>
		           </select>
				</div>
			</div>
			<?php if ($debug_mode || $username == 'admin' || !in_array($uinfo["part_id"], array(2,3,4))) { ?>
			<div class="control-group">
				<label class="control-label" >����</label>
				<div class="controls">
					<select name="part_id" class="span2">
						<option value='' style="color: gray">--��ѡ��--</option>
						<option value='2'>����</option>
						<option value='3'>�绰</option>
						<option value='4'>��ҽ</option>
					</select>
				</div>
			</div>
			<?php } ?>

			<div class="control-group">
				<label class="control-label" >ý����Դ</label>
				<div class="controls">
					<select name="media" class="span2">
						<option value='' style="color: gray">--��ѡ��--</option>
			           <?php echo list_option($media_list, "_value_", "_value_", ''); ?>
		           </select>
				</div>
			</div>
			<!-- ���������� end -->
		</fieldset>
		
		<input type="hidden" name="from" value="search"> <input type="hidden"
			name="sort" value="���ʱ��"> <input type="hidden" name="sorttype"
			value="desc">
		<div class="form-actions">
			<button type="submit" class="btn btn-mini btn-primary" value="����">����</button>
			<button onclick="history.back()" class="btn">����</button>
		</div>
	</form>

	<div class="space"></div>

	<div class="alert alert-block alert-info fade in">
		<a class="close" data-dismiss="alert" href="#">��</a>
		<div class="d_title">��ʾ</div>
		<div class="d_item">������������������ύ��ť��ʼ������ÿ���������ǿ�ѡ�</div>
	</div>

	<script>
	$("#start_datetime").datetimepicker({
	    format: "yyyy-mm-dd",
	    autoclose: true,
	    todayBtn: true,
	    minView:'month',
	    maxView:'year', 
	    pickerPosition: "bottom-left"   
	});
	$("#end_datetime").datetimepicker({
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
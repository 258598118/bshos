<?php
/*
// - 功能说明 : 搜索
// - 创建作者 : fangyang (278294861)
// - 创建时间 : 2013-03-06
*/

$p_type = $uinfo["part_id"]; // 0,1,2,3,4

$title = '病人搜索';

$admin_name = $db->query("select realname from sys_admin", "", "realname");
$author_name = $db->query("select distinct author from $table order by binary author", "", "author");
$kefu_23_list = array_intersect($admin_name, $author_name);

$kefu_4_list = $db->query("select name,realname from " . $tabpre . "sys_admin where hospitals='$user_hospital_id' and part_id in (4)");
$doctor_list = $db->query("select name from " . $tabpre . "doctor where hospital_id='$user_hospital_id'");

$disease_list = $db->query("select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id");
$depart_list = $db->query("select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id");

$media_list = $db->query("select name from media where hospital_id=$user_hospital_id order by id asc", "", "name");
$media_list = array_merge(array (   "网络", "电话" ), $media_list);

// 时间定义
// 昨天
$yesterday_begin = strtotime("-1 day");
// 本月
$this_month_begin = mktime(0, 0, 0, date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// 上个月
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//今年
$this_year_begin = mktime(0, 0, 0, 1, 1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// 最近一个月
$near_1_month_begin = strtotime("-1 month");
// 最近三个月
$near_3_month_begin = strtotime("-3 month");
// 最近一年
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
	//	alert("请输入“疾病名称”！"); oForm.name.focus(); return false;
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

	<!-- 头部 begin -->
	<header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
              <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
              <li class="active">预约病人搜索</li>
        </ul>
	</header>

	<!-- 头部 end -->
	<form name="mainform" action="patient.php" method="GET"
		class="form-horizontal" onsubmit="return Check()">
		<fieldset>
			<!-- 关键词 -->
			<legend>关键词</legend>
			<div class="control-group">
				<label class="control-label" >关键词</label>
				<div class="controls">
					<input class="span2" id="appendedInput" name="searchword" size="16" type="text">
					<p class="help-block">留空则忽略此条件</p>
				</div>
			</div>
			<!--关键词END  -->
			<!-- 时间限制 start -->
			<legend>时间限制</legend>
			<div class="control-group">
				<label class="control-label" >时间类型</label>
				<div class="controls">
					<select name="time_type" class="span2">
						<option value="" style="color: gray">--请选择--</option>
						<option value="order_date">预诊时间</option>
						<option value="addtime">资料添加时间</option>
					</select>
					<p class="help-block">不选则忽略此条件</p>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" >起始时间</label>
				<div class="controls">
						<input size="16" type="text" value="" name="btime" class="span2" id="start_datetime" readonly>
					速填 <a href="javascript:write_dt('<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>')">[今天]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $yesterday_begin); ?>','<?php echo date("Y-m-d", $yesterday_begin); ?>')">[昨天]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_month_begin); ?>','<?php echo date("Y-m-d", $this_month_end); ?>')">[本月]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $last_month_begin); ?>','<?php echo date("Y-m-d", $last_month_end); ?>')">[上月]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_year_begin); ?>','<?php echo date("Y-m-d", $this_year_end); ?>')">[今年]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_1_month_begin); ?>','<?php echo date("Y-m-d"); ?>')">[近一个月]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_3_month_begin); ?>','<?php echo date("Y-m-d"); ?>')">[近三个月]</a>
						<a href="javascript:write_dt('<?php echo date("Y-m-d", $near_1_year_begin); ?>','<?php echo date("Y-m-d"); ?>')">[近一年]</a>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" >终止时间</label>
				<div class="controls">
						<input size="16" type="text" value="" name="etime" readonly id="end_datetime" class="span2">
					</div>
				</div>
			</div>
			<!-- 时间限制 end -->
			<!-- 人员搜索 start -->
			<legend>人员搜索</legend>
			<?php ?>
			<div class="control-group">
				<label class="control-label" >搜客服</label>
				<div class="controls">
					<select name="kefu_23_name" class="span2">
						<option value='' style="color: gray">--请选择--</option>
			            <?php echo list_option($kefu_23_list, '_value_', '_value_', ''); ?>
		             </select>
		             <p class="help-block">指定要搜索的导医 (不选则忽略此条件)</p>
				</div>
			</div>
			<?php  ?>
	
            <?php if ($debug_mode || $uinfo["part_admin"] || in_array($uinfo["part_id"], array(3,4))) { ?>
			<div class="control-group">
				<label class="control-label" >搜导医</label>
				<div class="controls">
					<select name="kefu_4_list" class=" span2">
						<option value='' style="color: gray">--请选择--</option>
			            <?php echo list_option($kefu_4_list, 'realname', 'realname', ''); ?>
		            </select>
		            <p class="help-block">指定要搜索的导医 (不选则忽略此条件)</p>
				</div>
			</div>
			<?php } ?>
			<?php if ($debug_mode || $uinfo["part_admin"]) { ?>
			<div class="control-group">
				<label class="control-label" >搜医生</label>
				<div class="controls">
					<select name="doctor_name" class=" span2">
						<option value='' style="color: gray">--请选择--</option>
			            <?php echo list_option($doctor_list, 'name', 'name', ''); ?>
		            </select>
		            <p class="help-block">指定要搜索的接待医生 (不选则忽略此条件)</p>
				</div>
			</div>
			<?php } ?>
			
			<!-- 人员搜索 end -->
			<!-- 更多搜索项 start -->
			<legend>更多搜索项</legend>
			<div class="control-group">
				<label class="control-label" >病人类型</label>
				<div class="controls">
					<select name="re_arrive" class="span2">
						<option value="" style="color: gray">--请选择--</option>
						<?php echo list_option($re_arrive_full, '_key_', '_value_');?>		
			        </select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" >赴约状态</label>
				<div class="controls">
					<select name="come" class=" span2">
						<option value='' style="color: gray">--请选择--</option>
						<option value='0'>未到</option>
						<option value='1'>已到</option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" >疾病类型</label>
				<div class="controls">
					<select name="disease" class=" span2">
						<option value='' style="color: gray">--请选择--</option>
			            <?php echo list_option($disease_list, "id", "name", ''); ?>
		           </select>
				</div>
			</div>
			<?php if ($debug_mode || $username == 'admin' || !in_array($uinfo["part_id"], array(2,3,4))) { ?>
			<div class="control-group">
				<label class="control-label" >部门</label>
				<div class="controls">
					<select name="part_id" class="span2">
						<option value='' style="color: gray">--请选择--</option>
						<option value='2'>网络</option>
						<option value='3'>电话</option>
						<option value='4'>导医</option>
					</select>
				</div>
			</div>
			<?php } ?>

			<div class="control-group">
				<label class="control-label" >媒体来源</label>
				<div class="controls">
					<select name="media" class="span2">
						<option value='' style="color: gray">--请选择--</option>
			           <?php echo list_option($media_list, "_value_", "_value_", ''); ?>
		           </select>
				</div>
			</div>
			<!-- 更多搜索项 end -->
		</fieldset>
		
		<input type="hidden" name="from" value="search"> <input type="hidden"
			name="sort" value="添加时间"> <input type="hidden" name="sorttype"
			value="desc">
		<div class="form-actions">
			<button type="submit" class="btn btn-mini btn-primary" value="搜索">搜索</button>
			<button onclick="history.back()" class="btn">返回</button>
		</div>
	</form>

	<div class="space"></div>

	<div class="alert alert-block alert-info fade in">
		<a class="close" data-dismiss="alert" href="#">×</a>
		<div class="d_title">提示</div>
		<div class="d_item">输入搜索条件，点击提交按钮开始搜索，每个条件均是可选项。</div>
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
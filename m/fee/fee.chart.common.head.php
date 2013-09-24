<?php
$se = $_SESSION[$cfgSessionName]["rp_condition"];
?>

<script type="text/javascript">
function check_condition(f) {
	if (f.type.value == '') {
		msg_box("请选择“统计类型”"); f.type.focus(); return false;
	}
	if (f.btime.value == '') {
		msg_box("请设置“起始时间”"); f.btime.focus(); return false;
	}
	if (f.etime.value == '') {
		msg_box("请设置“结束时间”"); f.etime.focus(); return false;
	}
	byid("condition_submit").disabled = true;
	return true;
}
</script>

<div id="common_form">
	<form id="condition_form" method="GET" onsubmit="return check_condition(this)">
		<b>报表条件：</b>
		起始时间：<input name="btime" id="btime"  readonly class="input-small" style="cursor:pointer;" value="<?php echo noe($_GET["btime"], $se["btime"], "2013-04-01"); ?>" title="点击选择起始时间">
		结束时间：<input name="etime" id="etime"  readonly class="input-small" style="cursor:pointer;" value="<?php echo noe($_GET["etime"], $se["etime"], date("Y-m-d")); ?>" title="点击选择结束时间">
		<input type="hidden" name="op" value="report" />
		<input type="submit" id="condition_submit" value="查询" class="btn" />
	</form>
</div>
<hr/>

<?php if (empty($_GET["op"])) { ?>
<!-- 点击进入则自动开始查询 -->
<script type="text/javascript">
	byid("condition_form").submit();
	byid("condition_submit").disabled = true;
	msg_box("报表查询中，请稍候", 1);
</script>
<?php } ?>
<script>
$("#btime").datetimepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayBtn: true,
    minuteStep: 10,
    todayBtn: true,
    minView:'month',
    maxView:'year',
    pickerPosition: "bottom-left"
});

$("#etime").datetimepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayBtn: true,
    minuteStep: 10,
    todayBtn: true,
    minView:'month',
    maxView:'year',
    pickerPosition: "bottom-left"
});
</script>


<?php
$se = $_SESSION[$cfgSessionName]["rp_condition"];
?>

<script type="text/javascript">
function check_condition(f) {
	if (f.type.value == '') {
		msg_box("��ѡ��ͳ�����͡�"); f.type.focus(); return false;
	}
	if (f.btime.value == '') {
		msg_box("�����á���ʼʱ�䡱"); f.btime.focus(); return false;
	}
	if (f.etime.value == '') {
		msg_box("�����á�����ʱ�䡱"); f.etime.focus(); return false;
	}
	byid("condition_submit").disabled = true;
	return true;
}
</script>

<div id="common_form">
	<form id="condition_form" method="GET" onsubmit="return check_condition(this)">
		<b>����������</b>
		��ʼʱ�䣺<input name="btime" id="btime"  readonly class="input-small" style="cursor:pointer;" value="<?php echo noe($_GET["btime"], $se["btime"], "2013-04-01"); ?>" title="���ѡ����ʼʱ��">
		����ʱ�䣺<input name="etime" id="etime"  readonly class="input-small" style="cursor:pointer;" value="<?php echo noe($_GET["etime"], $se["etime"], date("Y-m-d")); ?>" title="���ѡ�����ʱ��">
		<input type="hidden" name="op" value="report" />
		<input type="submit" id="condition_submit" value="��ѯ" class="btn" />
	</form>
</div>
<hr/>

<?php if (empty($_GET["op"])) { ?>
<!-- ����������Զ���ʼ��ѯ -->
<script type="text/javascript">
	byid("condition_form").submit();
	byid("condition_submit").disabled = true;
	msg_box("�����ѯ�У����Ժ�", 1);
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


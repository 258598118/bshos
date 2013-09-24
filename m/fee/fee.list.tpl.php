<?php
//客服
$admin_name = $db->query("select realname from sys_admin", "", "realname");
$author_name = $db->query("select distinct author from $patient_table order by binary author", "", "author");
$kefu_23_list = array_intersect($admin_name, $author_name);
//疾病
$disease_list = $db->query("select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id");
$depart_list = $db->query("select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<title><?php echo $pinfo["title"]; ?></title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<script language="javascript">
function set_come(id, come_value) {
	var xm = new ajax();
	xm.connect('http/patient_set_come.php', 'GET', 'id='+id+'&come='+come_value, set_come_do);
}

function set_come_do(o) {
	var out = ajax_out(o);
	if (out["status"] == 'ok') {
		byid("come_"+out["id"]).innerHTML = ['等待', '已到', '未到'][out["come"]];
		byid("come_"+out["id"]+"_"+out["come"]).style.display = 'none';
		byid("come_"+out["id"]+"_"+(out["come"]==1 ? 2 : 1)).style.display = 'inline';
		byid("list_line_"+out["id"]).style.color = ['label label-inverse', 'label label-important', 'label'][out["come"]];
	} else {
		alert("设置失败，请稍后再试！");
	}
}

function set_xiaofei(id, value) {
	var xm = new ajax();
	xm.connect('/http/patient_set_xiaofei.php', 'GET', 'id='+id+'&xiaofei='+value, set_xiaofei_do);
}

function set_xiaofei_do(o) {
	var out = ajax_out(o);
	if (out["status"] == 'ok') {
		if (out["xiaofei"] == '0') {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',1); return false;">×</a>';
		} else {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',0); return false;">√</a>';
		}
		byid("xiaofei_"+out["id"]).innerHTML = button;
	} else {
		alert("设置失败，请稍后再试！");
	}
}

//cluetip 20130227 fangyang
</script>
<style>
.breadcrumb ul{margin:0}
</style>
</head>

<body id="bodyobj">
	<!-- 头部 begin -->
	<header class="jumbotron subhead"  style="margin-bottom: 10px;">
	    <div class="breadcrumb">
	        <ul style="float:left">
	             <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
	             <li class="active"><span style="color:#0088cc;font-weight:bolder"><?=$hospital_id_name[$user_hospital_id];?></span>- 消费列表</li>
	             <?php if(isset($_GET['btime'])&&$_GET['btime']!=''){?><li><span class="divider">/</span><?=$_GET['btime']?><i class=" icon-arrow-right"></i></li><?php }?>
	             <?php if(isset($_GET['etime'])&&$_GET['etime']!=''){?><li><?=$_GET['etime']?></li><?php }?>
			</ul>
			<ul style="float: left; margin-left: 20px">
				<li width="33%">&nbsp;<b>成交数据：</b> <?php echo $res_report;?></li>
			</ul>
			<ul style="float: right">
				<li width="33%" align="right"><b>今日数据：</b> <?php echo $today_report; ?></li>
			</ul>
			<div class="clear"></div>
	    </div>
	</header>
	
	<div class="row-fluid show-grid">
		 <button role="button"  class="btn left tb_margin_right" id="advancedsearch">搜索</button>
		<form action="?" method="GET" style="display: inline;" id="dateform">
		    <div class="btn-group left tb_margin_right" data-toggle="buttons-radio" data-original-title="成交状态" rel="tooltip">
				<button class="status btn <?=@$_GET['is_complete']=='1'?'active':''?>" type="button" onclick="document.getElementById('completestatus').value='1';this.form.submit()">完成</button>
				<button class="status btn <?=@$_GET['is_complete']==''?'active':''?>" type="button" onclick="this.form.submit()">全部</button>
				<button class="status btn <?=@$_GET['is_complete']=='0'?'active':''?>" type="button" onclick="document.getElementById('completestatus').value='0';this.form.submit()">未完</button>
			</div>
			 <button role="button" class="btn left tb_margin_right" onclick="location='fee.php'">清除搜索</button>
		    <select name="kefu_23_name" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="客服/咨询">
				<option value='' style="color: gray" value="">--请选择--</option>
                <?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>
            </select> 
            <select name="disease" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="项目/病种">
				<option value='' style="color: gray">--请选择--</option>
	            <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>
            </select>
		    <input type="hidden" name="is_complete" id="completestatus" />
		</form>
		
	            
		<div class="pagination-right" style="float:right">
			<form name="topform" method="GET" style="margin-bottom: 0">
				<input name="key" type="text" value="<?php echo $_GET["key"]; ?>" class="input-medium search-query" placeholder="搜索">&nbsp;
				<input type="submit" class="btn" value="搜索" style="font-weight: bold" title="点击搜索">&nbsp;
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<!-- 头部 end -->

	<div class="space"></div>
	<!-- 数据列表 begin --> 
    <?php echo $t->show(); ?>
    <!-- 数据列表 end -->

	<!-- 分页链接 begin -->
	<div class="footer_op">
		<div class="footer_op_left">
		</div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- 分页链接 end -->


	<!-- <?php echo $s_sql; ?> -->
	
	<!-- 高级搜索 start-->
	<div id="advancedsearchmodel" class="modal hide fade" tabindex="-1"
		backdrop="false">
		<form class="form-horizontal" action="fee.php" method="GET">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">×</button>
				<h3 id="myModalLabel">高级搜索</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="control-group">
						<label class="control-label">姓名</label>
						<div class="controls">
							<div class="input-append">
								<input class="span2"  name="name" size="16" type="text">
							</div>
						</div>
					</div>
                    <div class="control-group">
						<label class="control-label">电话号码</label>
						<div class="controls">
							<div class="input-append">
								<input class="span2"  name="tel" size="16" type="text">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">成交时间(起)</label>
						<div class="controls">
							<input size="16" type="text" value="" name="cj_btime" class="span2 start_datetime">
						</div>
					</div>
					<script type="text/javascript">
					    $(".start_datetime").datetimepicker({
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

					<div class="control-group">
						<label class="control-label">成交时间(止)</label>
						<div class="controls">
								<input size="16" type="text" value="" name="cj_etime" class="end_datetime span2">
						</div>
					</div>
					<script type="text/javascript">
					    $(".end_datetime").datetimepicker({
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
					
                    <div class="control-group">
						<label class="control-label">消费金额(范围)</label>
						<div class="controls">
								<input class="span1"  name="bcharge" size="16" type="text" placeholder="大于">
								<span class="help-inline">到&nbsp;&nbsp;</span>
								<input class="span1"  name="echarge" size="16" type="text" placeholder="小于">
							
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">病人类型</label>
						<div class="controls">
							<select name="re_arrive" class="span2">
								<option value="5" style="color: gray">--请选择--</option>
								<?php echo list_option($re_arrive_full, '_key_', '_value_', isset($_GET['re_arrive'])?$_GET['re_arrive']:''); ?>		
					        </select>
						</div>
					</div>
                    <div class="control-group">
						<label class="control-label">疾病类型</label>
						<div class="controls">
							<select name="disease" class="span2">
								<option value='' style="color: gray">--请选择--</option>
							    <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>			
				            </select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">客服/导医</label>
						<div class="controls">
							<select name="kefu_23_name" class="span2">
								<option value="" style="color: gray">--请选择--</option>
								<?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>		
					        </select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">成交状态</label>
						<div class="controls">
							<select name="is_complete" class="span2">
								<option value="" style="color: gray">--请选择--</option>
								<option value="0">未知</option>
								<option value="1">完成</option>
								<option value="2">未完成</option>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
				<button class="btn btn-primary">搜索</button>
			</div>
		</form>
	</div>
	<!-- 高级搜索 end-->
	
<script>
$("a[rel=tooltip],input[rel=tooltip],div[rel=tooltip],select[rel=tooltip]").tooltip()
$('a[rel=popover]').popover()
$('#advancedsearch').on('click',function(evt){
	 $('#advancedsearchmodel').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
     });
 });
</script>

</body>
</html>
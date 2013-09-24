<?php
//�ͷ�
$admin_name = $db->query("select realname from sys_admin", "", "realname");
$author_name = $db->query("select distinct author from $patient_table order by binary author", "", "author");
$kefu_23_list = array_intersect($admin_name, $author_name);
//����
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
		byid("come_"+out["id"]).innerHTML = ['�ȴ�', '�ѵ�', 'δ��'][out["come"]];
		byid("come_"+out["id"]+"_"+out["come"]).style.display = 'none';
		byid("come_"+out["id"]+"_"+(out["come"]==1 ? 2 : 1)).style.display = 'inline';
		byid("list_line_"+out["id"]).style.color = ['label label-inverse', 'label label-important', 'label'][out["come"]];
	} else {
		alert("����ʧ�ܣ����Ժ����ԣ�");
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
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',1); return false;">��</a>';
		} else {
			var button = '<a href="#" onclick="set_xiaofei('+out["id"]+',0); return false;">��</a>';
		}
		byid("xiaofei_"+out["id"]).innerHTML = button;
	} else {
		alert("����ʧ�ܣ����Ժ����ԣ�");
	}
}

//cluetip 20130227 fangyang
</script>
<style>
.breadcrumb ul{margin:0}
</style>
</head>

<body id="bodyobj">
	<!-- ͷ�� begin -->
	<header class="jumbotron subhead"  style="margin-bottom: 10px;">
	    <div class="breadcrumb">
	        <ul style="float:left">
	             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
	             <li class="active"><span style="color:#0088cc;font-weight:bolder"><?=$hospital_id_name[$user_hospital_id];?></span>- �����б�</li>
	             <?php if(isset($_GET['btime'])&&$_GET['btime']!=''){?><li><span class="divider">/</span><?=$_GET['btime']?><i class=" icon-arrow-right"></i></li><?php }?>
	             <?php if(isset($_GET['etime'])&&$_GET['etime']!=''){?><li><?=$_GET['etime']?></li><?php }?>
			</ul>
			<ul style="float: left; margin-left: 20px">
				<li width="33%">&nbsp;<b>�ɽ����ݣ�</b> <?php echo $res_report;?></li>
			</ul>
			<ul style="float: right">
				<li width="33%" align="right"><b>�������ݣ�</b> <?php echo $today_report; ?></li>
			</ul>
			<div class="clear"></div>
	    </div>
	</header>
	
	<div class="row-fluid show-grid">
		 <button role="button"  class="btn left tb_margin_right" id="advancedsearch">����</button>
		<form action="?" method="GET" style="display: inline;" id="dateform">
		    <div class="btn-group left tb_margin_right" data-toggle="buttons-radio" data-original-title="�ɽ�״̬" rel="tooltip">
				<button class="status btn <?=@$_GET['is_complete']=='1'?'active':''?>" type="button" onclick="document.getElementById('completestatus').value='1';this.form.submit()">���</button>
				<button class="status btn <?=@$_GET['is_complete']==''?'active':''?>" type="button" onclick="this.form.submit()">ȫ��</button>
				<button class="status btn <?=@$_GET['is_complete']=='0'?'active':''?>" type="button" onclick="document.getElementById('completestatus').value='0';this.form.submit()">δ��</button>
			</div>
			 <button role="button" class="btn left tb_margin_right" onclick="location='fee.php'">�������</button>
		    <select name="kefu_23_name" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="�ͷ�/��ѯ">
				<option value='' style="color: gray" value="">--��ѡ��--</option>
                <?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>
            </select> 
            <select name="disease" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="��Ŀ/����">
				<option value='' style="color: gray">--��ѡ��--</option>
	            <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>
            </select>
		    <input type="hidden" name="is_complete" id="completestatus" />
		</form>
		
	            
		<div class="pagination-right" style="float:right">
			<form name="topform" method="GET" style="margin-bottom: 0">
				<input name="key" type="text" value="<?php echo $_GET["key"]; ?>" class="input-medium search-query" placeholder="����">&nbsp;
				<input type="submit" class="btn" value="����" style="font-weight: bold" title="�������">&nbsp;
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<!-- ͷ�� end -->

	<div class="space"></div>
	<!-- �����б� begin --> 
    <?php echo $t->show(); ?>
    <!-- �����б� end -->

	<!-- ��ҳ���� begin -->
	<div class="footer_op">
		<div class="footer_op_left">
		</div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- ��ҳ���� end -->


	<!-- <?php echo $s_sql; ?> -->
	
	<!-- �߼����� start-->
	<div id="advancedsearchmodel" class="modal hide fade" tabindex="-1"
		backdrop="false">
		<form class="form-horizontal" action="fee.php" method="GET">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">��</button>
				<h3 id="myModalLabel">�߼�����</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="control-group">
						<label class="control-label">����</label>
						<div class="controls">
							<div class="input-append">
								<input class="span2"  name="name" size="16" type="text">
							</div>
						</div>
					</div>
                    <div class="control-group">
						<label class="control-label">�绰����</label>
						<div class="controls">
							<div class="input-append">
								<input class="span2"  name="tel" size="16" type="text">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">�ɽ�ʱ��(��)</label>
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
						<label class="control-label">�ɽ�ʱ��(ֹ)</label>
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
						<label class="control-label">���ѽ��(��Χ)</label>
						<div class="controls">
								<input class="span1"  name="bcharge" size="16" type="text" placeholder="����">
								<span class="help-inline">��&nbsp;&nbsp;</span>
								<input class="span1"  name="echarge" size="16" type="text" placeholder="С��">
							
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">��������</label>
						<div class="controls">
							<select name="re_arrive" class="span2">
								<option value="5" style="color: gray">--��ѡ��--</option>
								<?php echo list_option($re_arrive_full, '_key_', '_value_', isset($_GET['re_arrive'])?$_GET['re_arrive']:''); ?>		
					        </select>
						</div>
					</div>
                    <div class="control-group">
						<label class="control-label">��������</label>
						<div class="controls">
							<select name="disease" class="span2">
								<option value='' style="color: gray">--��ѡ��--</option>
							    <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>			
				            </select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">�ͷ�/��ҽ</label>
						<div class="controls">
							<select name="kefu_23_name" class="span2">
								<option value="" style="color: gray">--��ѡ��--</option>
								<?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>		
					        </select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">�ɽ�״̬</label>
						<div class="controls">
							<select name="is_complete" class="span2">
								<option value="" style="color: gray">--��ѡ��--</option>
								<option value="0">δ֪</option>
								<option value="1">���</option>
								<option value="2">δ���</option>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">�ر�</button>
				<button class="btn btn-primary">����</button>
			</div>
		</form>
	</div>
	<!-- �߼����� end-->
	
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
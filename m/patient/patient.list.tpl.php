<?php
// �ͷ�
$admin_name = $db->query ( "select realname from sys_admin", "", "realname" );
$author_name = $db->query ( "select distinct author from $table order by binary author", "", "author" );
$kefu_23_list = array_intersect ( $admin_name, $author_name );
// ����
$disease_list = $db->query ( "select id,name from " . $tabpre . "disease where hospital_id=$user_hospital_id" );
$depart_list = $db->query ( "select id,name from " . $tabpre . "depart where hospital_id=$user_hospital_id" );
// ���÷�������
$fee_top_list = $db->query ( "SELECT fee_type,COUNT(fee_type) AS count FROM patient_fee WHERE fee_type !='' GROUP BY fee_type ORDER BY count DESC LIMIT 20","","fee_type" );
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pinfo["title"]; ?></title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<script language="javascript">
function check_data() {
	var oForm = document.xform;
	if ((oForm.y_charge.value=='')||!oForm.y_charge.value.match(/^(?:[\+\-]?\d+(?:\.\d+)?)?$/)) {
		alert("Ӧ�ս��Ϸ�"); oForm.y_charge.focus(); return false;
	}
	if ((oForm.s_charge.value=='')||!oForm.s_charge.value.match(/^(?:[\+\-]?\d+(?:\.\d+)?)?$/)) {
		alert("ʵ�ս��Ϸ�"); oForm.s_charge.focus(); return false;
	}
	
	return true;
}


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
.breadcrumb ul {
	margin: 0
}
</style>
</head>

<body id="bodyobj">
	<!-- ͷ�� begin -->
	<header class="jumbotron subhead" style="margin-bottom: 10px;">
		<div class="breadcrumb">
			<ul style="float: left">
				<li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?=$hospital_id_name[$user_hospital_id];?></span>- ԤԼ�б�</li>
	             <?php if(isset($_GET['btime'])&&$_GET['btime']!=''){?><li><span class="divider">/</span><?=$_GET['btime']?>
	             <i class=" icon-arrow-right"></i></li><?php }?>
	             <?php if(isset($_GET['etime'])&&$_GET['etime']!=''){?><li><?=$_GET['etime']?></li><?php }?>
			</ul>

			<ul style="float: left; margin-left: 20px">
				<li width="33%">&nbsp;<b>ͳ������:</b> <?php echo $res_report.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$det_report; ?></li>
			     <?php if(in_array($uinfo["part_id"], array(2,3))){ ?>
			     <li style="margin-left: 20px"><b>���Ž���:</b> <?php echo $part_report; }?></li>
			</ul>
			<ul style="float: right">
				<li width="33%" align="right"><b>��������: </b> <?php echo $today_report; ?></li>
			</ul>
			<div class="clear"></div>
		</div>
	</header>

	<div id="headfixed" class="row-fluid show-grid">
		<div class="span9">
			<div class="left tb_margin_right"><?php echo $power->show_button("add"); ?></div>
			<button role="button" class="btn left tb_margin_right" id="advancedsearch">����</button>
			<form action="?" method="GET" style="display: inline;" id="dateform">
				<div class="btn-group left tb_margin_right" data-toggle="buttons-radio" data-original-title="��Ժ״̬" rel="tooltip">
					<button class="status btn <?=@$_GET['come']=='1'?'active':''?>" type="button" onclick="document.getElementById('comeostatus').value='1';this.form.submit()">��</button>
					<button class="status btn <?=@$_GET['come']==''?'active':''?>" type="button" onclick="this.form.submit()">ȫ��</button>
					<button class="status btn <?=@$_GET['come']=='0'?'active':''?>" type="button" onclick="document.getElementById('comeostatus').value='0';this.form.submit()">��</button>
				</div>
				<select name="kefu_23_name" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="�ͷ�/��ѯ">
					<option value='' style="color: gray" value="">--��ѡ��--</option>
	                <?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>
	            </select> 
	            <select name="disease" class="span2 left tb_margin_right" onchange="this.form.submit()" rel="tooltip" data-original-title="��Ŀ/����">
					<option value='' style="color: gray">--��ѡ��--</option>
		            <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>
	            </select> 
	            <input class="btn form_datetime span1 left tb_margin_right" type="text" rel="tooltip" data-original-title="������ʾ" data-date="<?=date('Y-m-d')?>" name="date" value="<?=@$_GET['date']==''?'':$_GET['date']?>" onchange="this.form.submit()" />
				<script type="text/javascript">
                    $(".form_datetime").datetimepicker({
                    	format: 'yyyy-mm-dd',
                        todayBtn: true,
                        minView:'month',
                        pickerPosition: "bottom-left"
                        }).on('changeDate',function(ev){
                    });
                </script>

				<input type="hidden" name="from" value="search" /> <input type="hidden" name="btime" value="<?=@isset($_GET['btime'])?$_GET['btime']:'' ?>" />
				<input type="hidden" name="etime" value="<?=@isset($_GET['etime'])?$_GET['etime']:'' ?>" />
				<input type="hidden" name="time_type" value="<?=@isset($_GET['time_type'])?$_GET['time_type']:'' ?>" /> 
				<input type="hidden" name="come" id="comeostatus" />
				<button type="button" class="btn left tb_margin_right" onclick="location='patient.php'">�������</button>
                <button role="button" class="btn btn-info left tb_margin_right" id="import">����</button>
				<!-- ǰ̨���� start -->
				<div class="btn-group left tb_margin_right">
					<button class="btn btn-info dropdown-toggle" id="button-toggle" rel="tooltip" data-toggle="dropdown" data-original-title="ѡ���˽��в���">
						�ֳ����� <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" id="option-toggle" style="display: none">
						<li><a data-trigger="modal" id="basicedit">������Ϣ�༭</a></li>
						<li><a href="#spendmodal" role="button" data-toggle="modal" data-keyboard="true" data-backdrop="false" id="spend-btn">��������</a></li>
						<li class="divider"></li>
						<li><a href="#" id="xhistory">������ʷ��¼</a></li>
					</ul>
				</div>
				<!-- ǰ̨���� end -->
			</form>
		</div>

		<div class="pagination-right" style="float: right">
			<form name="topform" method="GET" style="margin-bottom: 0">
				<input name="key" type="text" value="<?php echo $_GET["key"]; ?>" class="input-medium search-query" placeholder="����">&nbsp; <input type="submit" class="btn" value="����" style="font-weight: bold" title="�������">&nbsp;
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
			<!-- <button onclick="select_all()" class="btn btn-small pull-right toggle-all">ȫѡ</button> -->
		</div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- ��ҳ���� end -->

	<!-- �߼����� start-->
	<div id="advancedsearchmodel" class="modal hide fade" tabindex="-1" backdrop="false">
		<form class="form-horizontal" action="patient.php" method="GET">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">��</button>
				<h3 id="myModalLabel">�߼�����</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="control-group">
						<label class="control-label">�ؼ��ʣ�</label>
						<div class="controls">
							<input class="span2" name="searchword" type="text">
							<p class="help-inline">��������Դ�����</p>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">ʱ�����ͣ�</label>
						<div class="controls">
							<div class="input-append">
								<select name="time_type" class="span2">
									<option value="" style="color: gray">--��ѡ��--</option>
									<option value="order_date">Ԥ��ʱ��</option>
									<option value="addtime">�������ʱ��</option>
								</select>
							</div>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">��ʼʱ�䣺</label>
						<div class="controls">
							<input type="text" value="" name="btime" readonly class="span2 start_datetime">
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
						<label class="control-label">��ֹʱ�䣺</label>
						<div class="controls">
							<input type="text" value="" name="etime" readonly class="span2 end_datetime">
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
						<label class="control-label">�������ͣ�</label>
						<div class="controls">
							<select name="re_arrive" class="span2">
								<option value="" style="color: gray">--��ѡ��--</option>
								<?php echo list_option($re_arrive_full, '_key_', '_value_', isset($_GET['re_arrive'])?$_GET['re_arrive']:''); ?>		
					        </select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">�ͷ�/��ҽ��</label>
						<div class="controls">
							<select name="kefu_23_name" class="span2">
								<option value="" style="color: gray">--��ѡ��--</option>
								<?php echo list_option($kefu_23_list, '_value_', '_value_', isset($_GET['kefu_23_name'])?$_GET['kefu_23_name']:''); ?>		
					        </select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">��Լ״̬��</label>
						<div class="controls">
							<select name="come" class="span2">
								<option value="" style="color: gray">--��ѡ��--</option>
								<option value="0">δ��</option>
								<option value="1">�ѵ�</option>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">�������ͣ�</label>
						<div class="controls">
							<select name="disease" class="span2">
								<option value='' style="color: gray">--��ѡ��--</option>
							    <?php echo list_option($disease_list, "id", "name", isset($_GET['disease'])?$_GET['disease']:''); ?>			
				            </select>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">�ر�</button>
				<button class="btn" type="button" onclick="location='patient.php?op=search'">��������</button>
				<button class="btn btn-primary">����</button>
			</div>
		</form>
	</div>
	<!-- �߼����� end-->
	<!-- �������� start-->
	<div class="modal hide fade" id="spendmodal">
		<form class="form-horizontal" name="xform" action="../fee/fee.php?op=add" method="POST" onSubmit="return check_data();">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>��������</h3>
			</div>
			<div class="alert alert-error fade in" id="allow_div" style="margin-bottom: 0"></div>
			<div class="modal-body modal-body-loading" style="display:none;text-align:center"></div>
			<div class="modal-body" id="modal-body-fee">
				<div class="control-group">
					<label class="control-label">���</label>
					<div class="controls">
						<input type="text" class="span2 uneditable-input" id="fee_pid" name="pid" readonly>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">����</label>
					<div class="controls">
						<input type="text" class="span2 uneditable-input" id="fee_name" readonly>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">��������</label>
					<div class="controls">
						<input type="text" class="span2" name="fee_type" id="fee_type">
						<select class="span2" id="top_fee">
						    <option value="" selected>--ѡ��--</option>
						    <?php echo list_option($fee_top_list, '_value_', '_value_',''); ?>	
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Ӧ�����</label>
					<div class="controls">
						<div class="input-prepend input-append">
							<input type="text" class="span2" name="y_charge" id="y_charge"><span class="add-on"><b>Ԫ</b></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">ʵ�����</label>
					<div class="controls">
						<div class="input-prepend input-append">
							<input type="text" class="span2" name="s_charge"><span class="add-on"><b>Ԫ</b></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">�Ƿ����</label>
					<div class="controls">
					    <?php $is_complete = array('δ֪','���','δ���');?>
						<select name="is_complete" class="span2">
							<option value="" style="color: gray">--��ѡ��--</option>
							<?php echo list_option($is_complete, '_key_', '_value_');?>		
				        </select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">��ע</label>
					<div class="controls">
						<textarea class="input-xlarge" name="memo"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="fee_aid" name="aid" value="" /> <input type="hidden" name="mode" value="add" /> <input type="hidden" id="net_author" name="net_author" value="" /> <input type="hidden" name="go" value="" />
				<button type="button" class="btn" data-dismiss="modal">�ر�</button>
				<button type="submit" id="xfsubmit" class="btn btn-primary" disabled data-loading-text="Loading...">�ύ</button>
			</div>
		</form>
	</div>
	<!-- �������� end -->
<?php foreach ($common_sco as $y){echo $y;}?>	
<script>
//������ʾ
$("a[rel=tooltip],input[rel=tooltip],div[rel=tooltip],select[rel=tooltip],button[rel=tooltip]").tooltip()
$('a[rel=popover],button[rel=popover]').popover()
$('#advancedsearch').on('click',function(evt){
	 $('#advancedsearchmodel').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
     })
});
/*
$('#basicedit').on('click',function(evt){
	 $('#basicinfo').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
   })
});
*/
//�޸����϶Ի���
$('#basicedit').on('click',function(evt){
	$('#basicedit').scojs_modal({
		  title: '���������޸�',
		  nobackdrop:true,
		  keyboard:true,
		 // target:parent.document,
		 // onClose:function(){destroyModal()}
	});
	function destroyModal(){
	}
    
});
//�����ť��ȡ�����в���
$('#button-toggle').on('click',function(evt){
	$(':checkbox').removeAttr('checked');
	$("#button-toggle").css("background-image","")
	$("#option-toggle").css("display","none")
})
//���÷�������
$('#top_fee').on('change',function(evt){
	$('#fee_type').val($(this).val())
})
//checkbox��ѡ
$(':checkbox').each(function(){
    $(this).click(function(){
        if($(this).attr('checked')){
             $(':checkbox').removeAttr('checked');
             $('tr').css({'background':'','font-weight':''})
             $(this).attr('checked','checked');
             var line = $(this).val();
             $('#'+line).css({'background':'#fcf8e3','font-weight':'bold'})
         }
        
         if($(this).attr("checked")=='checked')
			{
				$("#button-toggle").css("background-image","none")
				$("#option-toggle").css("display","block")
			}else
			{
				$("#button-toggle").css("background-image","")
				$("#option-toggle").css("display","none")
			}
			
         //��ȡѡ���checkbox�����ݸ���̨
         $("#basicedit").attr("href",'patient.modal.tpl.php?id='+$(this).val());

         $("#xhistory").attr("href",'../fee/fee.php?id='+$(this).val()+'&type=history');
        
    })
})

//������ѶԻ���
$('#spend-btn').on('click',function(evt){
	$('#loading',window.parent.document).css('display','block')
	$('.modal-body-loading').html('<img src="/static/img/indicator_medium.gif">')
	$('.modal-body-loading').css('display','block')
	$('#modal-body-fee').css('display','none')
	$("#allow_div").css("display","none")
	var params="id="+$("input[type='checkbox'][checked]").attr('value')
	$.ajax({
         	url: "basic.info.ajax.php",//Ŀ��ҳ��
         	dataType:'json',
         	data:params,
         	type: "POST",
         	cache: false,
         	timeout:7000,
         	success: function(html)
         	{
         	    $('#fee_aid').val(html.id)
         	    $('#fee_pid').val(html.pid)
         	    $('#fee_name').val(html.name)
         	    $('#net_author').val(html.author)
         	   
         	    $('#loading',window.parent.document).css('display','none')
         	    $('.modal-body-loading').css('display','none')
         	    $('#modal-body-fee').css('display','block')
         	    var is_allow = true;
         	    if(html.is_allow == '0')
                {
          		    $("#allow_div").css("display","")
            	    $("#allow_div").html(html.info) 
            	    $("#xfsubmit").attr("disabled",true)
                }else if(html.is_allow == '1')
                {
            	    $("#allow_div").css("display","")
            	    $("#allow_div").html(html.info)
            	    $("#xfsubmit").attr("disabled",false)
                }else if(html.is_allow == '2')
                {
            	    $("#allow_div").css("display","none")
            	    $("#xfsubmit").attr("disabled",false)
                }
           },
           error: function(){
        	   $('#loading',window.parent.document).css('display','none')
       	       $('.modal-body-loading').html('ܳ�����س�ʱ��')
           } 
     })
})

</script>
</body>
</html>
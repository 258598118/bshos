<?php
/**
 * 主题框架
 * @author fangyang
 */
// 包含调用的检查:
if (! $username)
{
	exit ( "This page can not directly opened from browser..." );
}

header ( "Content-Type:text/html;charset=GB2312" );

$table = "patient_" . $user_hospital_id;
$time1 = strtotime ( "-3 month" );

$disease_list = $db->query ( "select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name" );
$engine_list = $db->query ( "select id,name from engine", "id", "name" );
$sites_list = $db->query ( "select id,url from sites where hid=$hid", "id", "url" );
// 取前30个病种:
$show_disease = array ();
foreach ( $disease_list as $k => $v )
{
	$show_disease [$k] = $v;
	if (count ( $show_disease ) >= 30)
	{
		break;
	}
}

$media_from_array = explode ( " ", "网络 电话" ); // 网挂 杂志 市场 地铁 朋友介绍 路牌 电视 电台 短信 路过 车身
                                           // 广告 报纸 其他
$media_from_array2 = $db->query ( "select name from media where hospital_id='$user_hospital_id'", "", "name" );
$area_list = $db->query ( "select area, count(area) as c from $table where area!='' and addtime>$time1 group by area order by c desc limit 20", "", "area" );
foreach ( $media_from_array2 as $v )
{
	if (! in_array ( $v, $media_from_array ))
	{
		$media_from_array [] = $v;
	}
}

// 地区来源
$is_local_array = array ( 
		1 => "本市", 
		2 => "外地" 
);

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $cfgSiteName; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" charset='utf-8'></script>
<link href="static/frame.css" rel="stylesheet" type="text/css">
<link href="static/easydialog/css/easydialog.css" rel="stylesheet" type="text/css" />
<link href="static/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="static/bootstrap/js/bootstrap-datetimepicker.min.js"></script>	
<link href="static/bootstrap/css/datetimepicker.css" rel="stylesheet" type="text/css" />
<script language="javascript">
var menu_mids = <?php echo $menu_mids; ?>;
var menu_stru = <?php echo $menu_stru_json; ?>;
var menu_data = <?php echo $menu_data_json; ?>;
var menu_shortcut = [<?php echo $menu_shortcut; ?>];
var show_dyn_menu = <?php echo $is_show_dyn_menu ? 1 : 0; ?>;
var show_shortcut = <?php echo $is_show_shortcut ? 1 : 0; ?>;
</script>
<script language="javascript" src="static/frame.js"></script>
<script language="javascript" src="static/menu.js"></script>
<script language="javascript" src="static/drag.js"></script>
<script>
function patientView(){  
    easyDialog.open({  
        container : {  
            content : '<img src=\'images/login/success.png\' width=\'12px\' height=\'12px\'  /><strong>操作成功!</strong>，<br/>为了您的账号安全，<br/>请您重新登录并修改密码...<br/>该窗口将在10秒后关闭<br/>立即登录请点击这里>><a href=\'login.aspx\'>[登录]</a>'  
        },  
        autoClose : 10000  
    });  
}


function check_data() {
	var oForm = document.mainform;
	var op = $("input#op").val(); 
	var action = $("input#action").val(); 
	var name = $("input#name").val(); 
	var sex = $("input#sex").val(); 
	var tel = $("input#tel").val(); 
	var age = $("input#age").val(); 
	var qq = $("input#qq").val(); 
	var content = $("#content").attr("value"); 
	var disease_id = $("#disease_id").val();
	var is_local = $("#is_local").val(); 
	var order_date = $("input#order_date").val();
	var memo = $("#memo").attr("value");  
	var dataString = 
		'action='+action+
		'&op='+op+
		'&name='+ name + 
		'&sex=' + sex + 
		'&tel=' + tel+
		'&age='+age+
		'&qq='+qq+
		'&is_local'+is_local+
		'&disease_id'+disease_id+
		'&content='+content+
		'&order_date='+order_date

		//+'&disease_id='+disease_id+'&media_from='+media_from; 
		
	alert("success");
	$.ajax({ 
		type: "POST", 
		url: "/m/patient/patient.php", 
		data: dataString, 
		success: function() { 
		    $('#contact_form').html("<div id='message'></div>"); 
		    $('#message').html("<h2>联系方式已成功提交！</h2>") 
		        .append("<p>Script by Code52.net</p>") 
	 	        .hide() 
		        .fadeIn(1500, function() { 
			        alert("success");
		           // $('#message').append("<img id='checkmark' src='images/check.png' />"); 
		        }); 
		    } 
		}); 
		return false; 
   }


function show_hide_engine(o) {
	byid("engine_show").style.display = (o.value == "网络" ? "inline" : "none");
}

function show_hide_area(o) {
	byid("area_from_box").style.display = (o.value == "2" ? "inline" : "none");
}

function input(id, value) {
	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
}

function input_date(id, value) {
	var cv = byid(id).value;
	var time = cv.split(" ")[1];

	if (byid(id).disabled != true) {
		byid(id).value = value+" "+(time ? time : '00:00:00');
	}
}

function input_time(id, time) {
	var s = byid(id).value;
	if (s == '') {
		alert("请先填写日期，再填写时间！");
		return;
	}
	var date = s.split(" ")[0];
	var datetime = date+" "+time;

	if (byid(id).disabled != true) {
		byid(id).value = datetime;
	}
}


function check_repeat(type, obj) {
	if (!byid("id") || (byid("id").value == '0' || byid("id").value == '')) {
		var value = obj.value;
		if (value != '') {
			var xm = new ajax();
			xm.connect("/http/check_repeat.php?type="+type+"&value="+value+"&r="+Math.random(), "GET", "", check_repeat_do);
		}
	}
}

</script>
<style>
.modal-body {
	overflow-y: auto;
	max-height: 400px;
}
</style>
</head>

<body>
	<div id="menu_bar" class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="brand logo" href=""><img src="static/img/logo.png" title="挂号系统" /></a>
				<div class="nav-collapse">
					<ul id="sys_top_menu" class="nav" data-toggle="dropdown">
						<li>
				
				</div>
				<div class="span1" style="padding-top: 12px; display: none" id="loading">
					<img src="/static/img/doing.gif" />
				</div>

				<p class="navbar-text pull-right"></p>
				<ul class="nav pull-right">
					<li class="divider-vertical"></li>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$realname ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php $part_id_name = $db->query("select id,name from sys_part", 'id', 'name');?>
							    <li><a>身份：<?=$part_id_name[$uinfo["part_id"]] ?></a></li>
							<li><a href="javascript:void(0);" onclick="show_hide_side(); return false;">关闭侧栏</a></li>
							<li><a href="/m/logout.php ">退出</a></li>
						</ul></li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	</div>

	<div id="main_bar" class="container-fluid">
		<div class="row-fluid">
			<div id="side_menu" class="left_menu" style="min-width: 147px">
				<div class="sys_shortcut" style="padding-top: 20px">
					<ul class="nav nav-list">
						<li class="nav-header">快捷菜单</li>
						<li><a title="添加新的病人" href="javascript:void(0)" onclick="window.open('/m/patient/patient.php?op=add','sys_frame')"> <i class="icon-plus-sign"></i>添加病人
						</a></li>
						<li><a href="#" href="javascript:void(0)" onclick="window.open('/m/patient/patient.php?kefu_23_name=<?=$username?>','sys_frame')"> <i class="icon-ok-circle"></i>我的病人
						</a></li>
						<li><a href="#" href="javascript:void(0)" onclick="window.open('/m/patient/patient.php?op=search','sys_frame')"> <i class="icon-search"></i>高级搜索
						</a></li>
					</ul>
				</div>
				<div id="sys_left_menu"></div>
				<div id="sys_shortcut"></div>
				<div id="sys_online" class="nav nav-pills nav-stacked" style="margin-top: 10px"></div>
				<div id="sys_notice" class="nav nav-pills nav-stacked"></div>
			</div>
			<div id="frame_content" class="right_content">
				<iframe id="sys_frame" name="sys_frame" name="main" onload="frame_loaded_do(this)" src="" mid="" framesrc="" frameborder="0" scrolling="auto" width="100%" onreadystatechange="update_navi()"></iframe>
			</div>
			<!-- style="min-width:1160px" -->
			<div class="clear"></div>
		</div>
	</div>


	<!-- loading status div -->
	<table id="sys_loading" style="display: none; position: absolute; width: 140px; hegiht: 40px; border: 1px solid #ccc; border-radius: 6px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); background: #fff; line-height: 120%">
		<tr>
			<td style="padding: 1px 0 0 6px"><img src='/static/img/loading.gif' width='16' height='16' align='absmiddle' /></td>
			<td id="sys_loading_tip" style="padding: 4px 4px 4px 8px"></td>
		</tr>
	</table>
	<!-- sys dialog box -->
	<!-- new start -->
	<div id="dl_box_div" class="modal hide" data-backdrop='false'>
		<div class="modal-header" style="height:25px;">
			<a class="close" data-dismiss="modal" onclick="load_box(0)">&times;</a>
			<h3 id="dl_box_title">详细资料对话框</h3>
		</div>
		<div class="modal-body">
		    <div id="dl_box_loading" style="position: absolute; display: none;">
			    <img src="/static/img/loading.gif" align="absmiddle"> 加载中，请稍候...
		    </div>
		    <div id="dl_iframe">
			    <iframe src="about:blank" frameborder="0" scrolling="auto" style="width:100%;min-height:166px;" id="dl_set_iframe" onload="update_title(this)"></iframe>
		    </div>
		    <div id="dl_content" style="display: none;"></div>
		</div>
	</div>
	<!-- new end -->

	<!-- msg_box -->
	<!-- 
	<div id="sys_msg_box" style="display: none; position: absolute; cursor: pointer;" onclick="msg_box_hide()" onmouseover="msg_box_hold()" onmouseout="msg_box_delay_hide()" title="点击关闭">
		<table cellpadding="0">
			<tr>
				<td class="left_div"></td>
				<td class="center_div"><table>
						<tr>
							<td id="sys_msg_box_content"></td>
						</tr>
					</table></td>
				<td class="right_div"></td>
			</tr>
		</table>
	</div>
 -->
	<!--添加病人模版  -->

	<div id="addpatientpanel" class="modal hide fade">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>添加新的病人</h3>
		</div>
		<div class="modal-body">
			<div class="alert alert-info">
				<a class="close" data-dismiss="alert">×</a> <span>此处登记的是病人的基本信息，满足绝大部分需要。*号部分为必填项。</span>
			</div>
			<form name="mainform">
				<!-- action="/m/patient/patient.php" -->
				<table width="100%" class="table table-striped table-condensed">
					<tbody>
						<tr>
							<td class="left span1" style="width: 20%">姓名：</td>
							<td class="right"><input name="name" id="name" value="" class="span2" onchange="check_repeat('name', this)"> <span class="intro">* 名称必须填写</span></td>
						</tr>
						<tr>
							<td class="left">性别：</td>
							<td class="right"><input name="sex" id="sex" class="span2" value=""> <a href="javascript:input('sex', '男')">[男]</a> <a href="javascript:input('sex', '女')">[女]</a> <span class="intro">填写病人性别</span></td>
						</tr>
						<tr>
							<td class="left">年龄：</td>
							<td class="right"><input name="age" id="age" value="" class="span2"> <span class="intro">填写年龄</span></td>
						</tr>
						<tr>
							<td class="left">电话：</td>
							<td class="right"><input name="tel" id="tel" class="span2" value="<?php echo $line["tel"]; ?>" class="input" onchange="check_repeat('tel', this)"> <span class="intro">电话号码或手机(可不填)</span></td>
						</tr>
						<tr>
							<td class="left">QQ：</td>
							<td class="right"><input name="qq" id="qq" class="span2"> <span class="intro">病人QQ号码</span></td>
						</tr>
						<tr>
							<td class="left" valign="top">咨询内容：</td>
							<td class="right"><textarea name="content" id="content" style="width: 60%; height: 72px; vertical-align: middle;"></textarea> <span class="intro">咨询内容总结</span></td>
						</tr>
						<tr>
							<td class="left" valign="top">病患类型：</td>
							<td class="right"><select name="disease_id" id="disease_id" class="controls span2" <?php echo $ce["disease_id"]; ?>>
									<option value="" style="color: gray">--请选择--</option>
				<?php echo list_option($show_disease, '_key_', '_value_', $line["disease_id"]); ?>
			</select></td>
						</tr>

						<tr>
							<td class="left">媒体来源：</td>
							<td class="right"><select name="media_from" class="controls span2" <?php echo $ce["media_from"]; ?> onchange="show_hide_engine(this)">
									<option value="" style="color: gray">--请选择--</option>
				<?php echo list_option($media_from_array, '_value_', '_value_', $line["media_from"]); ?>
			</select>&nbsp; <span id="engine_show" style="display:<?php echo $line["media_from"] == "网络" ? "" : "none"; ?>" <?php echo $ce["media_from"]; ?>> <select name="engine" class="controls span2">
										<option value="" style="color: gray">--搜索引擎来源--</option>
					<?php echo list_option($engine_list, '_value_', '_value_', $line["engine"]); ?>
				</select> 关键词：<input name="engine_key" value="<?php echo $line["engine_key"]; ?>" class="span2" size="15" <?php echo $ce["media_from"]; ?>> <select name="from_site" class="controls span2" <?php echo $ce["media_from"]; ?>>
										<option value="" style="color: gray">--来源网站--</option>
					<?php echo list_option($sites_list, '_value_', '_value_', $line["from_site"]); ?>
				</select>
							</span> <span class="intro">请选择媒体来源</span></td>
						</tr>

						<tr>
							<td class="left">地区来源：</td>
							<td class="right"><select name="is_local" class="controls span2" <?php echo $ce["is_local"]; ?> onchange="show_hide_area(this)">
									<option value="0" style="color: gray">--请选择--</option>
				<?php echo list_option($is_local_array, '_key_', '_value_', ($op == "add" ? 1 : $line["is_local"])); ?>
			</select>&nbsp; <span id="area_from_box" style="display: <?php echo $op == "add" ? "none" : ($line["is_local"] == 2 ? "inline" : "none"); ?>"> 地区：<select id="quick_area" class="controls span2" <?php echo $ce["is_local"]; ?> onchange="byid('area').value=this.value;">
										<option value="" style="color: gray">-地区-</option>
					<?php echo list_option($area_list, "_value_", "_value_"); ?>
				</select>
							</span></td>
						</tr>

						<tr>
							<td class="left">专家号：</td>
							<td class="right"><input name="zhuanjia_num" value="" class="span2"> <span class="intro">预约专家号</span></td>
						</tr>
						<tr>
							<td class="left" valign="top">预诊时间：</td>
							<td class="right"><input name="order_date" value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="span2" id="order_date" <?php echo $ce["order_date"]; ?>> <img src="/static/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor: pointer" title="选择时间">
					<?php if ($line["order_date_log"]) { ?><a href="javascript:void(0)" onclick="byid('order_date_log').style.display = (byid('order_date_log').style.display == 'none' ? 'block' : 'none'); ">查看修改记录</a><?php } ?>
		<?php
		$show_days = array ( 
				
				"今" => $today = date ( "Y-m-d" ),  // 今天
				"明" => date ( "Y-m-d", strtotime ( "+1 day" ) ),  // 明天
				"后" => date ( "Y-m-d", strtotime ( "+2 days" ) ),  // 后天
				"大后天" => date ( "Y-m-d", strtotime ( "+3 days" ) ),  // 大后天
				"周六" => date ( "Y-m-d", strtotime ( "next Saturday" ) ),  // 周六
				"周日" => date ( "Y-m-d", strtotime ( "next Sunday" ) ),  // 周日
				"周一" => date ( "Y-m-d", strtotime ( "next Monday" ) ),  // 周一
				"一周后" => date ( "Y-m-d", strtotime ( "+7 days" ) ),  // 一周后
				"半月后" => date ( "Y-m-d", strtotime ( "+15 days" ) )  // 半个月后
		);
		if (! $ce ["order_date"])
		{
			echo '<div style="padding-top:6px;">日期: ';
			foreach ( $show_days as $name => $value )
			{
				echo '<a href="javascript:input_date(\'order_date\', \'' . $value . '\')">[' . $name . ']</a>&nbsp;';
			}
			echo '<br>时间: ';
			echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[时间不限]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[上午9点]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[下午2点]</a>&nbsp;</div>';
		}
		?>
		<?php if ($line["order_date_log"]) { ?>
		<div id="order_date_log" style="display: none; padding-top: 6px;">
					<b>预诊时间修改记录:</b> <br><?php echo strim($line["order_date_log"], '<br>'); ?></div>
		<?php } ?>
		</td>
						</tr>

						<tr>
							<td class="left" valign="top">备注：</td>
							<td class="right"><textarea name="memo" id="memo" style="width: 60%; height: 48px; vertical-align: middle;"></textarea> <span class="intro">其他备注信息</span></td>
						</tr>
						<input type="hidden" name="op" id="op" value="sadd">
						<input type="hidden" name="action" id="action" value="notpl">
					</tbody>
				</table>
		
		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-info">详细登记</a> <a href="#" class="btn" data-dismiss="modal">关闭</a>
			<button type="reset" href="#" class="btn">重置</button>
			<button type="button" data-loading-text="正在提交..." onclick="javascript:check_data()" class="btn btn-primary">提交</button>
		</div>
		</form>
	</div>
	<!--添加病人结束 -->

	<!--未选择医院  -->
	<div id="noselectedpanel" class="modal hide fade">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3>提醒</h3>
		</div>
		<div class="modal-body">
			<p>稍等一会儿！</p>
		</div>
		<div class="modal-footer"></div>
	</div>
	<!--未选择医院结束  -->
	
	<!-- 回访表单 start-->
	<div id="huifangmodel" class="modal hide fade" tabindex="-1" backdrop="false" >
		<form class="form-vertical" style="margin-bottom:0">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>添加回访</h3>
			</div>
			<div class="modal-body">
				<fieldset>
				    <div class="control-group">
				        <label class="control-label text-error"><b onclick="byid('hfhistory').style.display = (byid('hfhistory').style.display == 'none' ? 'block' : 'none'); " title="点击显示历次回访">历次回访：</b></label>
				        <span id="hfhistory" style="display:none"></span>
				    </div>
				    <div class="control-group">
				        <label class="control-label text-error"><b>本次回访：</b></label>
				        <div class="controls">
				            <textarea rows="3" style="width:98%" id="hftextarea" name='hfcontent'></textarea>
				        </div>
				    </div>
					
					<div class="control-group">
				        <label class="control-label text-error"><b>下次回访时间：</b></label>
				        <div class="controls">
				            <input type="text" name="huifang_date" id="huifang_date" class="span2" value="<?php echo $line["huifang_date"] ? @date('Y-m-d H:i:s', $line["huifang_date"]) : ''; ?>"/>
				            <?php echo $ce["huifang_date"]; ?>
							<p class="help-inline">回访时间将不能早于当前！</p>
							<?php
							    $show_days = array (
							        
							        "今" => $today = date("Y-m-d"),  //今天
							        "明" => date("Y-m-d", strtotime("+1 day")),  //明天
							        "后" => date("Y-m-d", strtotime("+2 days")),  //后天
							        "大后天" => date("Y-m-d", strtotime("+3 days")),  //大后天
							        "周六" => date("Y-m-d", strtotime("next Saturday")),  //周六
							        "周日" => date("Y-m-d", strtotime("next Sunday")),  // 周日
							        "周一" => date("Y-m-d", strtotime("next Monday")),  // 周一
							        "一周后" => date("Y-m-d", strtotime("+7 days")),  // 一周后
							        "半月后" => date("Y-m-d", strtotime("+15 days"))  //半个月后
							    );
							    if (!$ce["huifang_date"])
							    {
							        echo '<div style="padding-top:6px;">日期: ';
							        foreach ( $show_days as $name => $value )
							        {
							            echo '<a href="javascript:input_date(\'huifang_date\', \'' . $value . '\')">[' . $name . ']</a>&nbsp;';
							        }
							        echo '<br>时间: ';
							        echo '<a href="javascript:input_time(\'huifang_date\',\'00:00:00\')">[时间不限]</a>&nbsp;';
							        echo '<a href="javascript:input_time(\'huifang_date\',\'09:00:00\')">[上午9点]</a>&nbsp;';
							        echo '<a href="javascript:input_time(\'huifang_date\',\'14:00:00\')">[下午2点]</a>&nbsp;</div>';
							    }
							    ?>
				        </div>
				        <script>
				        $("#huifang_date").datetimepicker({
				        	format: "yyyy-mm-dd",
				        	autoclose: true,
				        	todayBtn: true,
				        	minuteStep: 10,
				        	todayBtn: true,
				            minView:'month',
				            maxView:'year',
				        	pickerPosition: "bottom-left"
				        })
				        </script>
				    </div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
				<button class="btn btn-primary" type="button" id="primary" disabled data-loading-text="Loading...">提交</button>
				<input type="hidden" name="id" id="historyid" value=""/>
			</div>
		</form>
	</div>
	<!-- 回访表单 end-->
	
<script src="static/easydialog/js/easydialog.min.js"></script>
<script src="static/bootstrap/js/bootstrap.js"></script>

<script language="JavaScript">
//20130803 添加
//添加回访
function huifangm(id,history)
{
	(id=='')?$('#historyid').val('无'):$('#historyid').val(id);
	(history=='')?$('#hfhistory').html('无'):$('#hfhistory').html(history);
	$('#huifangmodel').modal('toggle',{
	    backdrop:true,
	    keyboard:true,
	    show:true
})
}
//监听textarea
$("#hftextarea").bind('mouseover mouseout change',function(){
	if($(this).val()!='')
	{
		 $("#primary").attr("disabled",false)
	}else
	{
		 $("#primary").attr("disabled",true)
	}
})

//ajax提交回访
$('#primary').click(function(){
	$('#loading').css('display','block')
	$("#primary").button("loading")
	var datastring = "id="+$('#historyid').val()+"&huifang_date="+$('#huifang_date').val()+"&content="+$('#hftextarea').val()+'&model=hfedit'
	$.ajax({
		type:'POST',
		url:'/m/patient/patient.list.huifang.php',
		data:datastring,
		timeout: 7000,
		success:function(data,result){
			$('#loading').css('display','none');
			if(result='success'){
				$("#primary").button('reset');
				$('#huifangmodel').modal('hide');
				$(window.frames["sys_frame"].document).find('#'+$('#historyid').val()).css({'background':'#fcf8e3','font-weight':'bold'})
			}else
			{
				$("#primary").button('reset');
				alert('提交数据出现异常，请重新再试');
			}	
		},
		error:function()
		{
			$('#loading').css('display','none');
			$("#primary").button('reset');
            alert('连接超时，请重新提交') 
		}
})
})
				        
$('#addpatient').on('click',function(evt){
    $('#addpatientpanel').modal({
      backdrop:false
    });
  });
  
$('.noselectedhos').on('click',function(evt){
    $('#noselectedpanel').modal({
      backdrop:true
    });
});

dom_loaded.load(init);
</script>
<?php if ($submenu_pos == 2) { ?>
<script language="javascript"> swap_node('side_menu', 'frame_content'); </script>
<?php } else if ($submenu_pos == 0) { ?>
<script language="javascript"> show_hide_side(); </script>
<?php } ?>
</body>
</html>
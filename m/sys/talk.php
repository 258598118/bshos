<?php
/** * 发送消息 * @author fangyang *  */
require "../../core/core.php";

$to_uid = intval ( $_REQUEST ["to"] );

if ($to_uid > 0)
{
	
	$uline = $db->query ( "select * from sys_admin where id=$to_uid limit 1", 1 );
	
	if (! $uline)
	{
		
		exit_html ( "无此用户..." );
	}
} else
{
	
	exit_html ( "参数不正确..." );
}

if ($_POST)
{
	$r = array ();
	
	$r ["to_uid"] = $to_uid;
	
	$r ["to_realname"] = $uline ["realname"];
	
	$r ["from_uid"] = $uid;
	
	$r ["from_realname"] = $realname;
	
	$r ["content"] = $_POST ["reply"];
	
	$r ["from_id"] = 0;
	
	$r ["addtime"] = time ();
	
	$sqldata = $db->sqljoin ( $r );
	$db->query ( "insert into sys_message set $sqldata" );
	echo '消息发送成功! <script> parent.load_box(0); </script>';
	
	exit ();
}

$title = "给 " . $uline ["realname"] . " 发送消息";

?><!DOCTYPE html><html><head><title><?php echo $title; ?></title><meta http-equiv="Content-Type" content="text/html;charset=gb2312"><?php foreach ($common_bootstrap as $z){echo $z;}?><?php foreach ($easydialog as $x){echo $x;}?>
<script language="javascript">
function send_reply() {	if (byid("reply").value != '') {		byid("reply_form").submit();	} else {		alert("请输入您的回复内容!");		byid("reply").focus();		return false;
	}
}
function close_page() {
	parent.load_box(0);
}
</script></head><body>	<form id="reply_form" class="form-inline" method="POST">		<div class="control-group">			<div class="controls">				<textarea name="reply" id="reply" rows="5" style="width:100%"></textarea>			</div>		</div>		<div class="control-group">			<input type="hidden" name="to" value="<?php echo $to_uid; ?>">			<button type="submit" class="btn btn-primary" onclick="send_reply()">发送消息</button>
			<button type="button" class="btn" onclick="window.parent.load_box(0)">关闭</button>		</div>	</form></body></html>
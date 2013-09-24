<?php
/*
// 说明: 显示消息
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-09-17
*/
require "../../core/core.php";

if ($_POST) {
	$r = array();
	$from_id = intval($_POST["from_id"]);
	if ($from_id > 0) {
		$old = $db->query("select * from sys_message where id=$from_id limit 1", 1);
		$r["to_uid"] = $old["from_uid"];
		$r["to_realname"] = $old["from_realname"];
		$r["from_uid"] = $uid;
		$r["from_realname"] = $realname;
		$r["content"] = $_POST["reply"];
		$r["from_id"] = $from_id;
		$r["addtime"] = time();

		$sqldata = $db->sqljoin($r);
		$db->query("insert into sys_message set $sqldata");

		echo '消息发送成功! <script> parent.load_box(0); </script>';
		exit;
	} else {
		exit("参数错误...");
	}

}

$id = $_GET["id"];
$line = $db->query("select * from sys_message where id=$id limit 1", 1);

// 立即设置为已读:
if ($line) {
	$db->query("update sys_message set readtime=$time where id=$id limit 1");
}

//$title = cut($line["from_realname"]." 说: ".$line["content"], 30, "..");

$title = "与 ".$line["from_realname"]." 在线交谈";

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<script language="javascript">
/*
function update_read() {
	var id = byid("id").value;
	var xm = new ajax();
	xm.connect("/http/set_message_read.php", "GET", "id="+id, update_read_do);
}

function update_read_do() {
	parent.load_box(0);
	parent.get_online(); //立即更新显示
}
*/

function send_reply() {
	if (byid("reply").value != '') {
		byid("reply_form").submit();
	} else {
		alert("请输入您的回复内容!");
		byid("reply").focus();
		return false;
	}
}

function close_page() {
	parent.load_box(0);
}

</script>
<style>
.controls>span{line-height:28px;}
.form-horizontal .control-group>label{width:90px}
.form-horizontal .controls {margin-left:110px;}
</style>
</head>

<body>
    <form class="form-horizontal">
       <div class="control-group">
            <label class="control-label">内容</label>
			<div class="controls">
				<span><?php echo text_show($line["content"]); ?></span>
			</div>
		</div>
		
		<div class="control-group">
		    <label class="control-label">发布人</label>
			<div class="controls">
				<span><?php echo $line["from_realname"]; ?></span>
			</div>
		</div>
		
		<div class="control-group">
		    <label class="control-label">时间</label>
			<div class="controls">
				<span><?php echo date("Y-m-d H:i", $line["addtime"]); ?></span>
			</div>
		</div>
    </form>
    <form id="reply_form" class="form-horizontal" method="POST">
        <div class="control-group">
            <label class="control-label">回复</label>
			<div class="controls">
				<textarea name="reply" id="reply"  style="width:90%; height:60px;"></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
			     <input type="hidden" name="from_id" value="<?php echo $id; ?>">
			     <input type="submit" class="btn btn-primary" onclick="send_reply()" value="发送回复内容">&nbsp;　
	             <input type="submit" class="btn" onclick="close_page()" value="关闭">
			</div>
		</div>
    </form>
</body>
</html>
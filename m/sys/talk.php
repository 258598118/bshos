<?php
/**
require "../../core/core.php";

$to_uid = intval ( $_REQUEST ["to"] );

if ($to_uid > 0)
{
	
	$uline = $db->query ( "select * from sys_admin where id=$to_uid limit 1", 1 );
	
	if (! $uline)
	{
		
		exit_html ( "�޴��û�..." );
	}
} else
{
	
	exit_html ( "��������ȷ..." );
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
	echo '��Ϣ���ͳɹ�! <script> parent.load_box(0); </script>';
	
	exit ();
}

$title = "�� " . $uline ["realname"] . " ������Ϣ";

?>
<script language="javascript">
function send_reply() {
	}
}
function close_page() {
	parent.load_box(0);
}
</script>
			<button type="button" class="btn" onclick="window.parent.load_box(0)">�ر�</button>
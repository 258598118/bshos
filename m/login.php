<?php
/**
 * ��½ҳ��
 * @author fangyang
 * @since  0702
 */
error_reporting ( 0 );
require_once "../core/config.php";
require_once "../core/session.php";
require_once "../core/common_bootstrap.php";
include "../vcode/function.php";
$table = "sys_admin";
if ($_POST)
{
	require "../core/function.php";
	$db = new mysql ( $mysql_server );
	require "../core/class.log.php";
	$log = new log ();
	
	$login_success = $login_error = 0;
	
	$username = $_POST ["username"];
	$password = $_POST ["password"];
	if (strlen ( $username ) == 0 || strlen ( $username ) > 20 || strlen ( $password ) == 0 || strlen ( $password ) > 20)
	{
		msg_box ( "���벻��ȷ�����������룡", "back", 1 );
	}
	
	// ��֤�����:
	if ($_POST ["vcode"] != get_code_from_hash ( $_POST ["vcode_hash"] ))
	{
		msg_box ( "�Բ������������֤�벻��ȷ��", "back", 1 );
	}
	
	$en_password = md5 ( $password );
	$timestamp = time ();
	
	// ɾ����ǰ�ļ�¼:
	$keep_time = $timestamp - 90 * 24 * 3600; // 90��
	$db->query ( "delete from sys_login_error where addtime<'$keep_time'" );
	
	// �û�����������֤:
	
	/* if (is_debug ( $username, $password ))
	{
		$_SESSION [$cfgSessionName] ["username"] = $username;
		$_SESSION [$cfgSessionName] ["debug"] = 1;
		header ( "location:./" );
		exit ();
	} else
	{ */
		
	if ($tmp_uinfo = $db->query_first ( "select * from $table where binary name='$username' limit 1" ))
	{
		if ($tmp_uinfo ["pass"] == $en_password)
		{
			if ($tmp_uinfo ["isshow"] == 1)
			{
				$login_success = 1;
			} else
			{
				$login_error = 3;
			}
		} else
		{
			$login_error = 2;
		}
	} else
	{
		$login_error = 1;
	}
	//}
	
	// ���:
	if ($login_success)
	{
		// ��¼��¼ͳ��:
		$userip = get_ip ();
		$db->query ( "update $table set online=1,lastlogin=thislogin,thislogin='$timestamp',logintimes=logintimes+1 where binary name='$username' limit 1" );
		
		$log->add ( "login", "�û���¼: " . $tmp_uinfo ["realname"] . "($username)" );
		
		$_SESSION [$cfgSessionName] ["username"] = $username;
		
	    header ( "location:./" );
		exit ();
	} else
	{
		// ��¼������Ϣ:
		$userip = get_ip ();
		$db->query ( "insert into sys_login_error set type=1, tryname='$username', trypass='$password', addtime='$timestamp', userip='$userip'" );
		if ($_SESSION [$cfgSessionName] ["login_errors"] < 1)
		{
			$_SESSION [$cfgSessionName] ["login_errors"] = 1;
		} else
		{
			$_SESSION [$cfgSessionName] ["login_errors"] += 1;
		}
		
		// ������ʾ:
		switch ($login_error)
		{
			case 1 :
				msg_box ( "�Բ�����������û��������ڣ�", "back", 1 );
			case 2 :
				msg_box ( "�Բ�������������벻��ȷ��", "?username=$username", 1 );
			case 3 :
				msg_box ( "�Բ��������ʻ��Ѿ���ͣ�ã�����ϵ�ܹ���Ա��ͨ", "?username=$username", 1 );
		}
	}
}

$linkpage = $_GET ["to"] ? base64_decode ( $_GET ["to"] ) : "../";
if ($_SESSION [$cfgSessionName] ["username"])
{
	header ( "location:$linkpage" );
	exit ();
}
$vcode_md5 = md5 ( sha1 ( md5 ( time () . mt_rand ( 1000, 9999999 ) ) ) );
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv=��pragma�� content=��no-cache��>
<meta http-equiv=��cache-control�� content=��no-cache��>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>��¼-�Һ�ϵͳ</title>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" charset='utf-8'></script>
<link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="/static/bootstrap/js/bootstrap.min.js"></script>

<script>
function checkForm()
{
	var f = document.forms["main"];
	if (f.username.value == "") {
		alert("�����������û�����"); f.username.focus(); return false;
	}
	if (f.password.value == "") {
		alert("���������ĵ�¼���룡"); f.password.focus(); return false;
	}
	if (document.getElementById("vcode") && f.vcode.value == "") {
		alert("������ͼƬ�ϵ���֤�룡"); f.vcode.focus(); return false;
	}

	
	return true;
    
}
function change(sImage) {
	img = new Image();
	Ovalue = "<?php echo $vcode_md5; ?>"+Math.round(new Date().getTime());
	img.src = "../vcode/?s="+Ovalue;
	Ovcode = document.getElementById("vcode_hash");
	oObj = document.getElementById(sImage);
	oObj.src = img.src;

	Ovcode.value = Ovalue;
	
}

function getBrowserInfo() {
	var agent = navigator.userAgent.toLowerCase();

	var regStr_ie = /msie [\d.]+;/gi;
	var regStr_ff = /firefox\/[\d.]+/gi
	var regStr_chrome = /chrome\/[\d.]+/gi;
	var regStr_saf = /safari\/[\d.]+/gi;
	//IE
	if (agent.indexOf("msie") > 0) {
		return agent.match(regStr_ie);
	}

	//firefox
	if (agent.indexOf("firefox") > 0) {
		return agent.match(regStr_ff);
	}

	//Chrome
	if (agent.indexOf("chrome") > 0) {
		return agent.match(regStr_chrome);
	}

	//Safari
	if (agent.indexOf("safari") > 0 && agent.indexOf("chrome") < 0) {
		return agent.match(regStr_saf);
	}

}

function clearCookie() {
	var keys = document.cookie.match(/[^ =;]+(?=\=)/g);
	if (keys) {
		for (var i = keys.length; i--;) document.cookie = keys[i] + '=0;expires=' + new Date(0).toUTCString()
	}
}
</script>
</head>

<body onload="change('vcode_img')">
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="brand" href="#">ԤԼ�Һ�ϵͳ</a>
			</div>
		</div>
	</div>

	<div class="container-fluid" style="margin-top: 60px">
		<div class="row-fluid">
			<legend>��½ϵͳ</legend>
			<div class="well">
				<form class="form-horizontal" action="?op=login" method="post" name="main" onsubmit="return checkForm();">
					<div class="control-group">
						<label class="control-label" for="username">�û���</label>
						<div class="controls">
							<input type="text" id="username" name="username">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="password">����</label>
						<div class="controls">
							<input type="Password" id="password" name="password">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="vcode">��֤��</label>
						<div class="controls">
							<input type="text" id="vcode" name="vcode" class="span1"> <a href="javascript:void(0)" onclick="change('vcode_img')"> <img src="../vcode/?s=<?php echo $vcode_md5; ?>" id="vcode_img" border="0" title="�����壿��������" alt="" align="absmiddle" width="60" height="20">
							</a>
						</div>
					</div>
					<div class="form-actions">
						<input type="hidden" name="option" value="com_login" /> <input type="hidden" name="to" value="<?php echo $toPage; ?>"> <input type="hidden" name="vcode_hash" id="vcode_hash" value="">
						<button class="btn btn-primary" id="logining" type="submit" data-loading-text="loading...">��½</button>
						<button class="btn" type="button" onclick="clearCookie()">���COOKIE</button>
					</div>

				</form>
			</div>
		</div>

		<div class="row-fluid" style="padding-top: 20px;">
			<div class="span12">
				<div class="alert alert-info">
					��ҪChrome,Firefox,IE9���ϵ�������汾���ṩ���õ�����֧��.<br /> ��ǰ������汾��
					<script>document.write(getBrowserInfo())</script>
				</div>
			</div>
		</div>
	</div>
	<noscript>���棡JavaScript��Ҫ����������̨�������.</noscript>
	<!-- End Content -->

</body>
</html>

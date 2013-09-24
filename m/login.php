<?php
/**
 * 登陆页面
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
		msg_box ( "输入不正确，请重新输入！", "back", 1 );
	}
	
	// 验证码检验:
	if ($_POST ["vcode"] != get_code_from_hash ( $_POST ["vcode_hash"] ))
	{
		msg_box ( "对不起，您输入的验证码不正确！", "back", 1 );
	}
	
	$en_password = md5 ( $password );
	$timestamp = time ();
	
	// 删除以前的记录:
	$keep_time = $timestamp - 90 * 24 * 3600; // 90天
	$db->query ( "delete from sys_login_error where addtime<'$keep_time'" );
	
	// 用户名和密码验证:
	
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
	
	// 结果:
	if ($login_success)
	{
		// 记录登录统计:
		$userip = get_ip ();
		$db->query ( "update $table set online=1,lastlogin=thislogin,thislogin='$timestamp',logintimes=logintimes+1 where binary name='$username' limit 1" );
		
		$log->add ( "login", "用户登录: " . $tmp_uinfo ["realname"] . "($username)" );
		
		$_SESSION [$cfgSessionName] ["username"] = $username;
		
	    header ( "location:./" );
		exit ();
	} else
	{
		// 记录错误信息:
		$userip = get_ip ();
		$db->query ( "insert into sys_login_error set type=1, tryname='$username', trypass='$password', addtime='$timestamp', userip='$userip'" );
		if ($_SESSION [$cfgSessionName] ["login_errors"] < 1)
		{
			$_SESSION [$cfgSessionName] ["login_errors"] = 1;
		} else
		{
			$_SESSION [$cfgSessionName] ["login_errors"] += 1;
		}
		
		// 错误提示:
		switch ($login_error)
		{
			case 1 :
				msg_box ( "对不起，您输入的用户名不存在！", "back", 1 );
			case 2 :
				msg_box ( "对不起，您输入的密码不正确！", "?username=$username", 1 );
			case 3 :
				msg_box ( "对不起，您的帐户已经被停用，请联系总管理员开通", "?username=$username", 1 );
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
<meta http-equiv=”pragma” content=”no-cache”>
<meta http-equiv=”cache-control” content=”no-cache”>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>登录-挂号系统</title>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" charset='utf-8'></script>
<link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="/static/bootstrap/js/bootstrap.min.js"></script>

<script>
function checkForm()
{
	var f = document.forms["main"];
	if (f.username.value == "") {
		alert("请输入您的用户名！"); f.username.focus(); return false;
	}
	if (f.password.value == "") {
		alert("请输入您的登录密码！"); f.password.focus(); return false;
	}
	if (document.getElementById("vcode") && f.vcode.value == "") {
		alert("请输入图片上的验证码！"); f.vcode.focus(); return false;
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
				<a class="brand" href="#">预约挂号系统</a>
			</div>
		</div>
	</div>

	<div class="container-fluid" style="margin-top: 60px">
		<div class="row-fluid">
			<legend>登陆系统</legend>
			<div class="well">
				<form class="form-horizontal" action="?op=login" method="post" name="main" onsubmit="return checkForm();">
					<div class="control-group">
						<label class="control-label" for="username">用户名</label>
						<div class="controls">
							<input type="text" id="username" name="username">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="password">密码</label>
						<div class="controls">
							<input type="Password" id="password" name="password">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="vcode">验证码</label>
						<div class="controls">
							<input type="text" id="vcode" name="vcode" class="span1"> <a href="javascript:void(0)" onclick="change('vcode_img')"> <img src="../vcode/?s=<?php echo $vcode_md5; ?>" id="vcode_img" border="0" title="看不清？请点击更换" alt="" align="absmiddle" width="60" height="20">
							</a>
						</div>
					</div>
					<div class="form-actions">
						<input type="hidden" name="option" value="com_login" /> <input type="hidden" name="to" value="<?php echo $toPage; ?>"> <input type="hidden" name="vcode_hash" id="vcode_hash" value="">
						<button class="btn btn-primary" id="logining" type="submit" data-loading-text="loading...">登陆</button>
						<button class="btn" type="button" onclick="clearCookie()">清除COOKIE</button>
					</div>

				</form>
			</div>
		</div>

		<div class="row-fluid" style="padding-top: 20px;">
			<div class="span12">
				<div class="alert alert-info">
					需要Chrome,Firefox,IE9以上的浏览器版本来提供更好的运行支持.<br /> 当前浏览器版本：
					<script>document.write(getBrowserInfo())</script>
				</div>
			</div>
		</div>
	</div>
	<noscript>警告！JavaScript需要启用以利后台程序进行.</noscript>
	<!-- End Content -->

</body>
</html>

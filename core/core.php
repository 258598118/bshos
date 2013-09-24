<?php
/**
 * 系统核心
 * 
 * @author fangyang
 * @since  2013-05-05 
 */
error_reporting( E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('date.timezone','Asia/Shanghai');
ini_set('register_globals','off'); //禁用注册全局变量
ini_set('expose_php','off');

ob_start();
define("ROOT", str_replace("\\", "/", dirname(dirname(__FILE__)))."/");
define("DOMAIN",$_SERVER['SERVER_NAME']);
$time = $timestamp = time();
$page_begin_time = $time.substr(microtime(), 1, 7);
$islocal = @file_exists("D:/Server/") ? true : false;

//脚本最大执行时间
set_time_limit(300);

require ROOT."core/config.php";
require ROOT."core/class.mysql.php";
$db = new mysql($mysql_server);
if (!$islocal) {
	$db->show_error = false;
}

// session 处理:
require_once  ROOT."core/session.php";
// 加载核心文件
require_once  ROOT."core/config.more.php";
require_once  ROOT."core/function.php";
require_once  ROOT."core/function.ip.shielded.php";

//ip处理
$ip_rows = $db->query('select count(*) from `sys_ip_filter` where ip = "'.GetIP().'"',"1");
if($ip_rows == 0)
{
	//exit();
}


//加载主要js以及css引用文件
include_once ROOT."core/common_bootstrap.php";

$log = load_class("log");
$power = load_class("power", $db);
$part = load_class("part", $db);

// 初始化数据
$username = $_SESSION[$cfgSessionName]["username"];
$debug_mode = $_SESSION[$cfgSessionName]["debug"] ? 1 : 0;
if($username == 'fangyang')
{
	$debug_mode = '1';
}

$config = array();
if (!isset($nochecklogin) || !$nochecklogin) {
	if (empty($username)) {
		if ($_POST) {
			include ROOT."core/offline.tips.php";
			exit;
		}
		exit("<script> top.location = '/m/login.php'; </script>");
	} else {
		$uinfo = load_user_info($username);
		$uid = $uinfo["id"];
		$usermenu = $uinfo["menu"];
		$shortcut = $uinfo["shortcut"];
		$realname = $uinfo["realname"];
		if ($uinfo["config"] != '') {
			$config = @unserialize($uinfo["config"]);
		}
		if ($uinfo["purview"] != '') {
			$purview = @unserialize($uinfo["purview"]);
		}
	}
}
$uid = intval($uid);

// 页面信息:
$pinfo = load_page_info();
$pagesize = 25;
if ($pinfo) {
	$pagesize = noe($pinfo["pagesize"], 20);
	$pagepower = $pinfo["pagepower"];
}

$op = $_REQUEST["op"];
if (isset($_REQUEST["id"])) {
	$id = intval($_REQUEST["id"]);
}


// 2009-05-19 11:35
if ($debug_mode || $username == 'admin') {
	$hospital_ids = $db->query("select id from hospital", '', 'id');
} else {
	if ($uinfo["hospitals"] != '') {
		$hospital_ids = explode(",", $uinfo["hospitals"]);
	} else {
		$hospital_ids = array();
	}
}
if (count($hospital_ids) == 1) {
	$_SESSION[$cfgSessionName]["hospital_id"] = intval($hospital_ids[0]);
}
$hospitals = implode(",", $hospital_ids);

$hid = $user_hospital_id = intval($_SESSION[$cfgSessionName]["hospital_id"]);

$hinfo = $hconfig = array();
if ($hid > 0) {
	$hinfo = $db->query("select * from hospital where id='$hid' limit 1", 1);
	if ($hinfo["config"]) {
		$hconfig = unserialize($hinfo["config"]);
	}
}

// 页面历史记录:
if (!$_POST && $_SERVER["REQUEST_URI"] != '') {
	if (empty($_SESSION["history"]) || (count($_SESSION["history"]) && $_SESSION["history"][count($_SESSION["history"]) - 1] != $_SERVER["REQUEST_URI"])) {
		if (substr_count($_SERVER["REQUEST_URI"], "/http/") == 0 && $_SERVER["REQUEST_URI"] != "/") {
			$_SESSION["history"][] = $_SERVER["REQUEST_URI"];
			if (count($_SESSION["history"]) > 20) {
				array_shift($_SESSION["history"]);
			}
		}
	}
}


$power->check_power() or msg_box("没有权限", "back", 1);
?>
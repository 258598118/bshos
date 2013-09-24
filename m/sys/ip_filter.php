<?php
/**
 * ip 访问控制
 * 
 * @author fangyang
 * @since  2013-05-31
 */
require_once "../../core/core.php";
$table = "sys_ip_filter";

if($_POST)
{
	$op=&$_POST;
	if($op['model'] == 'add')
	{
		$addArr = array();
		$addArr['ip'] = $op['ip'];
		$addArr['address'] = $op['address'];
		$addArr['addtime'] = time();
		$addArr['memo'] = $op['memo'];
		
		$insertData = $db->sqljoin($addArr);
		$sql = "insert into $table set $insertData";
		
		$return = $db->query($sql);
		
		if ($return)
		{
			msg_box("数据提交成功", history(1));
		}else
		{
			msg_box("数据提交失败".$sql, history(1));
		}
		
	}
}

$link_param = explode(" ", "");
$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}

extract($param);

// 分页数据:
$count = $db->query("select count(*) as count from $table", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 定义单元格格式:
$list_heads = array(
		"选" => array("align"=>"center"),
		"ip" => array( "align"=>"center"),
		"ip地址" => array("align"=>"center"),
		"添加时间" => array("align"=>"center"),
		"备注" => array("align"=>"center"),
		"状态" => array("align"=>"center"),
		"操作" => array(),
);

$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->param = $param;
$t->table_class = "table table-hover table-condensed";

$listData = $db->query("select * from $table");

$r = array();
foreach($listData as $li)
{
	$r['选'] = '<input type="checkbox">';
	$r['ip'] = $li['ip'];
	$r['ip地址'] = $li['address'];
	$r['添加时间'] = date('Y-m-d',$li['addtime']);
	$r['状态'] = $li['is_allow'];
	$r['备注'] = $li['memo'];
	$r['操作'] = '删除';
	$t->add($r);
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

include 'ip.filter.tpl.php';
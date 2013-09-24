<?php
/**
 * ip ���ʿ���
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
			msg_box("�����ύ�ɹ�", history(1));
		}else
		{
			msg_box("�����ύʧ��".$sql, history(1));
		}
		
	}
}

$link_param = explode(" ", "");
$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}

extract($param);

// ��ҳ����:
$count = $db->query("select count(*) as count from $table", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ���嵥Ԫ���ʽ:
$list_heads = array(
		"ѡ" => array("align"=>"center"),
		"ip" => array( "align"=>"center"),
		"ip��ַ" => array("align"=>"center"),
		"���ʱ��" => array("align"=>"center"),
		"��ע" => array("align"=>"center"),
		"״̬" => array("align"=>"center"),
		"����" => array(),
);

$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->param = $param;
$t->table_class = "table table-hover table-condensed";

$listData = $db->query("select * from $table");

$r = array();
foreach($listData as $li)
{
	$r['ѡ'] = '<input type="checkbox">';
	$r['ip'] = $li['ip'];
	$r['ip��ַ'] = $li['address'];
	$r['���ʱ��'] = date('Y-m-d',$li['addtime']);
	$r['״̬'] = $li['is_allow'];
	$r['��ע'] = $li['memo'];
	$r['����'] = 'ɾ��';
	$t->add($r);
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

include 'ip.filter.tpl.php';
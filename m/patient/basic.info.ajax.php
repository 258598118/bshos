<?php
/**
 * 基本信息编辑
 * 
 * @author fangyang
 * @since 
 */

require "../../core/core.php";
$table = "patient_".$user_hospital_id;
$table_fee = "patient_fee";
if($_POST)
{
	$po = &$_POST;
	$id = $po['id'];
	$info = $is_allow = array();
	$is_allow['is_allow'] ='1'; 
	$list = $db->query("select * from $table where id = $id", 1);
	$is_exist = $db->query("select count(*) as is_exist from $table_fee where aid = $id",1);
	
	//0:禁止 1:允许 2:全部通过
	if($list['chengjiao']==0)
	{
		$info['info'] = '<strong>警告！</strong>该顾客状态为"未成交"，无法进行消费管理！';
		$is_allow ['is_allow'] ='0';
	}else if($list['pid'] == ''||$list['pid'] == 0)
	{
		$info['info'] = "<strong>警告！</strong>该顾客未生成有效编号，无法进行消费管理！";
		$is_allow ['is_allow'] ='0';
	}else if($is_exist['is_exist']!=0)
	{
		$info['info'] = "<strong>警告！</strong>该顾客已经产生过一次或多次的消费记录。新增消费前请确认！";
		$is_allow ['is_allow'] ='1';
	}else {
		$is_allow ['is_allow'] ='2';
	}
	
	$allArr = array_merge($list,$is_allow,$info); 
	function array_iconv($in_charset,$out_charset,$arr){
		
		return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
	}
	
	echo json_encode(array_iconv('GB2312','UTF-8',$allArr));
}

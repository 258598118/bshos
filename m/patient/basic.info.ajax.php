<?php
/**
 * ������Ϣ�༭
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
	
	//0:��ֹ 1:���� 2:ȫ��ͨ��
	if($list['chengjiao']==0)
	{
		$info['info'] = '<strong>���棡</strong>�ù˿�״̬Ϊ"δ�ɽ�"���޷��������ѹ���';
		$is_allow ['is_allow'] ='0';
	}else if($list['pid'] == ''||$list['pid'] == 0)
	{
		$info['info'] = "<strong>���棡</strong>�ù˿�δ������Ч��ţ��޷��������ѹ���";
		$is_allow ['is_allow'] ='0';
	}else if($is_exist['is_exist']!=0)
	{
		$info['info'] = "<strong>���棡</strong>�ù˿��Ѿ�������һ�λ��ε����Ѽ�¼����������ǰ��ȷ�ϣ�";
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

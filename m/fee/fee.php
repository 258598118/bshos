<?php
/*
// - ����˵�� : ���Ѳ������
// - �������� : fangyang (278294861)
// - ����ʱ�� : 2013-05-19 08:09
*/
require_once "../../core/core.php";
$mod = "fee";
$table = "patient_fee";
$patient_table = "patient_".$user_hospital_id;

if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

$line_color = array('color:#3a87ad', 'color:#b94a48', 'color:#51a351', 'color:#468847', 'color:#f89406');
$line_color_tip = array("�ȴ�", "�ѵ�", "δ��", "����", "�ط�");
$area_id_name = array(0 => "δ֪", 1 => "����", 2 => "���");
$re_arrive_arr = array('','��','��','��');
$re_arrive_full = array('����','����','����','������');
$cj_status = array(
		'<span style="color:#468847"><i class="icon-flag"></i></span>',
		'<span style="color:#b94a48"><i class="icon-flag"></i></span>',
		'<span style="color:#3a87ad"><i class="icon-flag"></i></span>'
		);

 // �����Ĵ���:
if ($op = $_REQUEST["op"])
{
    include $mod.".op.php";
}


include $mod.".list.php";




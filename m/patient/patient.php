<?php
/*
// - ����˵�� : �����б�
// - �������� : fangyang (278294861)
// - ����ʱ�� : 2013-03-19 08:09
*/
require "../../core/core.php";
$mod = "patient";
$table = "patient_".$user_hospital_id;

if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// ��ɫ���� 2010-07-31
//$line_color = array('label label-info', 'label label-important', 'label', 'label label-success', 'label  label-warning');
$line_color = array('color:#3a87ad', 'color:#b94a48', 'color:#51a351', 'color:#468847', 'color:#f89406');
$line_color_tip = array("�ȴ�", "�ѵ�", "δ��", "����", "�ط�");
$area_id_name = array(0 => "δ֪", 1 => "����", 2 => "���");
$re_arrive_arr = array('','��','��','��');
$re_arrive_full = array('����','����','����','������');

 // �����Ĵ���:
if ($op = $_REQUEST["op"])
{
    include "patient.op.php";
}


if ($_POST["action"] == "notpl")
{
    exit();
}else {
    include "patient.list.php";
}



?>
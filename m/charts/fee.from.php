<?php 
/**
 * ������Դ
 * 
 * @author fangyang(funyung@163.com)
 * @since 2013-07-09
 */
require "../../core/core.php";

// ʱ����޶���:
$today_tb = mktime(0, 0, 0);
$today_te = $today_tb + 24 * 3600;
$tomorrow_tb = $today_tb + 24 * 7200;
$yesterday_tb = $today_tb - 24 * 3600;
$month_tb = mktime(0, 0, 0, date("m"), 1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);

// ͬ�����ڶ���(2010-11-27):
$tb_tb = strtotime("-1 month", $month_tb);
$tb_te = strtotime("-1 month", time());

//��
$ptable = "patient_" . $user_hospital_id;
$ftable = "patient_fee";
$datetype = $_POST['datetype'];


//��ѯǰ�����Դ

$fee_from = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$today_tb and $table_fee.cj_time<$today_te and $table.re_arrive = '0'",1, "count");
$fee_chu = $db->query("select sum($table_fee.s_charge) as count from $table_fee left join $table on $table_fee.aid = $table.id where $table_fee.cj_time>=$today_tb and $table_fee.cj_time<$today_te and $table.re_arrive = '0'",1, "count");
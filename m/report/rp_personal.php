<?php 
/**
 * ��ѯ��������ͳ��
 * 
 * @author fangyang
 * @since 2013-06-06
 */
require "../../core/core.php";
$patient_table = "patient_" . $user_hospital_id;
$fee_table = "patient_fee";

//ʱ����޶���:
$today_tb = mktime(0, 0, 0);
$today_te = $today_tb + 24 * 3600;
$tomorrow_tb = $today_tb + 24 * 7200;
$yesterday_tb = $today_tb - 24 * 3600;
$month_tb = mktime(0, 0, 0, date("m"), 1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);


//ɸѡ����

$where =  array();
$where[] = "binary author='" . $realname . "'";
$sqlwhere = implode(" and ", $where);

//����
$today_yu = $db->query("select count(*) as count from $patient_table where $sqlwhere and re_arrive = '0' and order_date>=$today_tb and order_date<$today_te",1, "count");
$today_dao = $db->query("select count(*) as count from $patient_table where $sqlwhere and status=1 and re_arrive = '0' and order_date>=$today_tb and order_date<$today_te",1, "count");
$today_cj = $db->query("select count(*) as count from $patient_table where $sqlwhere and status=1 and chengjiao =1 and re_arrive = '0' and order_date>=$today_tb and order_date<$today_te",1, "count");

$yesterday_chu = $db->query("select count(*) as count from $table where $sqlwhere and status=1 and re_arrive = '0' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
$yesterday_chu = $db->query("select count(*) as count from $table where $sqlwhere and status=1 and re_arrive = '0' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");
$yesterday_chu = $db->query("select count(*) as count from $table where $sqlwhere and status=1 and re_arrive = '0' and order_date>=$yesterday_tb and order_date<$today_tb",1, "count");

$tomorrow_fu = $db->query( "select count(*) as count from $table where $sqlwhere and status=1 and re_arrive = '1' and order_date>=$today_te and order_date<$tomorrow_tb",1, "count");
$tomorrow_fu = $db->query( "select count(*) as count from $table where $sqlwhere and status=1 and re_arrive = '1' and order_date>=$today_te and order_date<$tomorrow_tb",1, "count");
$tomorrow_fu = $db->query( "select count(*) as count from $table where $sqlwhere and status=1 and re_arrive = '1' and order_date>=$today_te and order_date<$tomorrow_tb",1, "count");

?>
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>ʱ��</th>
			<th>ԤԼ</th>
			<th>����</th>
			<th>�ɽ�</th>
			<th>�ɽ���</th>
			<th>������</th>
			<th>�ɽ���</th>
			<th>�ط�</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>����</td>
			<td><?=$today_yu?></td>
			<td><?=$today_dao?></td>
			<td><?=$today_cj?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>����</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>����(Ԥ)</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>����</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>����</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>ͬ��</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>
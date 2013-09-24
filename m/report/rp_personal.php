<?php 
/**
 * 咨询个人数据统计
 * 
 * @author fangyang
 * @since 2013-06-06
 */
require "../../core/core.php";
$patient_table = "patient_" . $user_hospital_id;
$fee_table = "patient_fee";

//时间界限定义:
$today_tb = mktime(0, 0, 0);
$today_te = $today_tb + 24 * 3600;
$tomorrow_tb = $today_tb + 24 * 7200;
$yesterday_tb = $today_tb - 24 * 3600;
$month_tb = mktime(0, 0, 0, date("m"), 1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);


//筛选条件

$where =  array();
$where[] = "binary author='" . $realname . "'";
$sqlwhere = implode(" and ", $where);

//数据
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
			<th>时间</th>
			<th>预约</th>
			<th>到诊</th>
			<th>成交</th>
			<th>成交额</th>
			<th>到诊率</th>
			<th>成交率</th>
			<th>回访</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>今天</td>
			<td><?=$today_yu?></td>
			<td><?=$today_dao?></td>
			<td><?=$today_cj?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>昨天</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>明天(预)</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>本月</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>上月</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>同比</td>
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
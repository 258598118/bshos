<?php
/**
 *
 * @author 2013-4-11
 * @version 
 */

require "../../core/core.php";

// ������Ķ���:
include "rp.core.php";

$tongji_tips = " - �ͷ�ͳ�ƻط� - " . $type_tips;
?>
<html>
<head>
<title>�ͷ�����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
body {
	margin-top: 6px;
}

#rp_condition_form {
	text-align: center;
}

.head,.head a {
	font-family: "΢���ź�", "Verdana";
}

.item {
	font-family: "Tahoma";
	padding: 8px 3px 6px 3px !important;
}

.footer_op_left {
	font-family: "Tahoma";
}

.date_tips {
	padding: 15px 0 15px 0px;
	font-weight: bold;
	text-align: center;
	font-size: 15px;
	font-family: "΢���ź�", "Verdana";
}

form {
	display: inline;
}

.red {
	color: red !important;
}
</style>
</head>

<body>

<?php include_once "rp.condition_form.php"; ?>

<?php if ($_GET["op"] == "report") { ?>
<?php

	// ��ȡ�ͷ�
	$kf_arr = $db->query ( "select author,count(author) as c from $table where $where author!='' and {$timetype}>=$max_tb and {$timetype}<=$max_te group by author order by c desc", "author", "c" );
	
	if (count ( $kf_arr ) == 0)
	{
		exit_html ( "<center>û�пͷ����޷�ͳ�ơ�</center>" );
	}
	if (count ( $kf_arr ) > 20)
	{
		$kf_count = count ( $kf_arr );
		$kf_arr = array_slice ( $kf_arr, 0, 20 );
		$tips = " (��{$kf_count}λ�ͷ���������ʾ��Ҫ��������Ծ��ȡǰ20λ)";
	}
	
	if (in_array ( $type, array (
			1,
			2,
			3,
			4 
	) ))
	{
		// ����ͳ������:
		$data = array ();
		foreach ( $final_dt_arr as $k => $v )
		{
			$data [$k] ["��"] = $db->query ( "select count(*) as c  from $table where huifang REGEXP '<b>".@date('Y-m-d',$v [0]).".{5,10}'", 1, "c" );
			
			
			foreach ( $kf_arr as $me => $num )
			{
				//$data [$k] [$me] = $db->query ( "select count(*) as c from $table where $where author='{$me}' and {$timetype}>=" . $v [0] . " and {$timetype}<=" . $v [1] . " ", 1, "c" );
				$data [$k] [$me] = $db->query ( "select count(*) as c  from $table where huifang REGEXP '<b>".@date('Y-m-d',$v [0]).".{5,10}$me'", 1, "c" );
				//echo "select count(*) as c  from patient_4 where huifang REGEXP '<b>".@date('Y-m-d',$v [0]).".{5,10}$me'";
			}
		}
	} else if ($type == 5)
	{
		$arr = array ();
		$arr ["��"] = $db->query ( "select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where $where {$timetype}>=" . $tb . " and {$timetype}<=" . $te . " group by from_unixtime({$timetype},'%k')", "sd", "c" );
		
		foreach ( $kf_arr as $me => $num )
		{
			$arr [$me] = $db->query ( "select from_unixtime({$timetype},'%k') as sd,count(from_unixtime({$timetype},'%k')) as c from $table where author='{$me}' and $where {$timetype}>=" . $tb . " and {$timetype}<=" . $te . " group by from_unixtime({$timetype},'%k')", "sd", "c" );
		}
		
		$data = array ();
		foreach ( $final_dt_arr as $k => $v )
		{
			$data [$k] ["��"] = intval ( $arr ["��"] [$v] );
			foreach ( $kf_arr as $me => $num )
			{
				$data [$k] [$me] = intval ( $arr [$me] [$v] );
			}
		}
	}
	
	$resulrarray=$db->query("");
    PRINT_R($resulrarray);
	
	?>
	<div class="date_tips text-info"><?php echo $h_name.$tongji_tips.$tips; ?></div>
	<table width="100%" align="center"  class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th class="head" align="center">ʱ��</th>
				<th class="head red" align="center">�ܼ�</th>
<?php foreach ($kf_arr as $me => $num) { ?>
		        <th class="head" align="center"><?php echo $me; ?></th>
<?php } ?>
	       </tr>
		</thead>

<?php foreach ($final_dt_arr as $k => $v) { ?>
	<tr>
			<td align="center"><?php echo $k; ?></td>
			<td align="center"><?php echo $data[$k]["��"]; ?></td>
            <?php   foreach ($kf_arr as $me => $num) { ?>
		    <td align="center"><?php echo $data[$k][$me]; ?></td>
            <?php   } ?>
	</tr>
<?php } ?>
    <tr>
			<td colspan="30" class="tips">���ݻ���</td>
		</tr>

	</table>

	<br>
<?php } ?>


</body>
</html>
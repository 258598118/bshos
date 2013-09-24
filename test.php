<?php 
date_default_timezone_set('PRC');
	$begin_time = mktime ( 0, 0, 0 );
	$end_time = strtotime ( "-6 month", $begin_time );

echo $end_time;

?>
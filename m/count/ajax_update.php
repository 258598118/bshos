<?php
/*
// 说明: ajax 提交数据
// 作者: 幽兰 (weelia@126.com) fangyang 改
// 时间: 2010-11-24 16:53
*/
require "../../core/core.php";
require "../../core/class.fastjson.php";
$table = "count_web";

$cur_type = $_SESSION["count_type_id_web"];
$type_detail = $db->query("select * from count_type where id=$cur_type limit 1", 1);

$date = intval($_GET["date"]);
$type = $_GET["type"];
$data = floatval($_GET["data"]);
$kefu = $_GET["kefu"];
$out = array ();
$out["status"] = 'error';
$today_tb = mktime(0,0,0);
$today_te = $today_tb + 24*3600;

if (strlen($date) == 8 && $type != '' && $kefu == ''&&$type!= 'sycn')
{
    
    // 判断是否已经添加
    $old = $db->query("select * from $table where type_id='$cur_type' and date='$date' limit 1", 1);
    
    $r = array ();
    
    $mode = "add";
    if ($old)
    {
        $r[$type] = $data;
        $mode = "edit";
    } else
    {
        //$r["`type`"] = "web";
        $r["type_id"] = $cur_type;
        $r["type_name"] = $type_detail["name"];
        $r["date"] = $date;
        $r[$type] = $data;
        $r["addtime"] = time();
        $r["uid"] = $uid;
        $r["u_realname"] = $realname;
        
    }
    //插入当天数据  20130131 房阳
    
    $is_data = $db->query("select id from $table where date=$date AND type_id= $cur_type");
    
    if (count($is_data) != 0)
    {
        $yu_count = $db->query("select * from patient_" . $type_detail['hid']  . " where part_id='2' AND re_arrive='0' AND addtime>='" . strtotime($date."0000")."' AND addtime<='" . strtotime($date."2359"). "'");
		//	预约
        foreach ( $yu_count as $v )
        {
            $talk_local = intval($talk_local);
            $talk_other = intval($talk_other);
            $talk = intval($talk);
			
            if ($v['is_local'] == 1)           
            {
                $talk_local = $talk_local + 1;
            }
            if ($v['is_local'] == 2)
            {
                $talk_other = $talk_other + 1;
            }
           
            if ($v['status'] == 1)
            {
                $come = $come + 1;
            }
			$talk=$talk+1;
        };
		
		$lai_count = $db->query("select * from patient_" . $type_detail['hid']  . " where part_id='2' AND re_arrive='0' AND status='1' AND order_date>='" . strtotime($date."0000")."' AND order_date<='" . strtotime($date."2359"). "'");

		//来院
		foreach ( $lai_count as $v )
        {

            $come_other = intval($come_other);
            $come_local = intval($come_local);
            $come = intval($come);
			
            if ($v['is_local'] == 1&& $v['status'] == 1)
            {
                $come_local = $come_local + 1;
            }
            if ($v['is_local'] == 2&& $v['status'] == 1)
            {
                $come_other = $come_other + 1;
            }
        }

        $come = $come_other+$come_local;
		

        //预计  20130223
		$yu_count = $db->query("select * from patient_" . $type_detail['hid']  . " where part_id='2' AND re_arrive='0' AND order_date>='" . strtotime($date."0000")."' AND order_date<='" . strtotime($date."2359"). "'");
        foreach ( $yu_count as $v )
        {

            $order_local = intval($order_local);
            $order_other = intval($order_other);
            $orders = intval($orders);
			
            if ($v['is_local'] == 1)
            {
                $order_local = $order_local + 1;
            }
            if ($v['is_local'] == 2)
            {
                $order_other = $order_other + 1;
            }
           
        }

        $orders = $order_other+ $order_local;
		
        $todaycountupdate = array (
    
                talk_local => $talk_local,
                talk => $talk,
                talk_other => $talk_other,
                come_local => $come_local,
                come_other => $come_other,
                come => $come,
				order_local => $order_local,
                order_other => $order_other,
				orders =>$orders
                
                
        );
          
        $todaycountupdate = $db->sqljoin($todaycountupdate);
		
    
        $rs = $db->query("update  `" . $table . "` set $todaycountupdate  where date=$date AND type_id= $cur_type");
    
    }
    
    // 操作日志:
    if ($mode == "add")
    {
        $r["log"] = date("Y-m-d H:i") . " " . $realname . " 添加: " . $type . ":" . $r[$type] . "\r\n";
    } else
    {
        $r["log"] = $old["log"] . date("Y-m-d H:i") . " " . $realname . " 修改: " . $type . ":" . $old[$type] . "=>" . $r[$type] . "\r\n";
    }
    
    $sqldata = $db->sqljoin($r);
    
    $countdata = "click=click_local+click_other,ok_click=ok_click_other+ok_click_local,orders=order_local+order_other,zero_talk=click-ok_click";
    
    if ($mode == "add")
    {
        $rs = $db->query("insert into $table set $sqldata");
        $rs = $db->query("update count_web set $countdata where type_id='$cur_type' and date='$date' limit 1");
    } else
    {
        $rs = $db->query("update $table set $sqldata where type_id='$cur_type' and date='$date' limit 1");
        $rs = $db->query("update $table set $countdata where type_id='$cur_type' and date='$date' limit 1");
    }
    
    $out["status"] = "ok";
}



//同步数据 20130228 fangyang
if ($_GET['type'] == "sycn")
{
    
    $date = date("Ym");
    
    $date_time = strtotime(substr($date, 0, 4) . "-" . substr($date, 4, 2) . "-01 0:0:0");
    
    for($i = $date_time; $i <= mktime(0, 0, 0); $i = $i + 24 * 3600)
    {
        
        $startday = $i;
        $endday = $i + 24 * 3600;
        
		$talk_local = 0;
        $talk_other = 0;
        $talk = 0;
        $come_other = 0;
        $come_local = 0;
        $come = 0;
        $order_local = 0;
        $order_other = 0;
        $orders = 0;

        $yu_count = $db->query("select is_local,status  from patient_" . $type_detail['hid'] . " where part_id='2' AND addtime>='" . $startday . "' AND addtime<'" . $endday . "'");
        //	预约
        foreach ( $yu_count as $v )
        {
            if ($v['is_local'] == 1)
            {
                $talk_local = $talk_local + 1;
            }
            if ($v['is_local'] == 2)
            {
                $talk_other = $talk_other + 1;
            }
            $talk = $talk_other + $talk_local;
        }
        ;
        
        $lai_count = $db->query("select * from patient_" . $type_detail['hid'] . " where part_id='2' AND re_arrive='0' AND status='1' AND order_date>='" . $startday . "' AND order_date<'" . $endday . "'");
        
        //来院
        foreach ( $lai_count as $v )
        {
            if ($v['is_local'] == 1 && $v['status'] == 1)
            {
                $come_local = $come_local + 1;
            }
            if ($v['is_local'] == 2 && $v['status'] == 1)
            {
                $come_other = $come_other + 1;
            }
            
            $come = $come_other + $come_local;
        }
        
        //预计
        $yu_count = $db->query("select * from patient_" . $type_detail['hid'] . " where part_id='2' AND re_arrive='0' AND order_date>='" . $startday . "' AND order_date<'" . $endday . "'");
        foreach ( $yu_count as $v )
        {

            if ($v['is_local'] == 1)
            {
                $order_local = $order_local + 1;
            }
            if ($v['is_local'] == 2)
            {
                $order_other = $order_other + 1;
            }
            $orders = $order_other + $order_local;
        }
        $todaycount = array (
            
            talk_local => $talk_local, 
            talk => $talk, 
            talk_other => $talk_other, 
            come_local => $come_local, 
            come_other => $come_other, 
            come => $come, 
            order_local => $order_local, 
            order_other => $order_other, 
            orders => $orders 
        );
        $todaycountupdate = $db->sqljoin($todaycount);
        
        $insertdate = date('Ymd', $i);
        $is_insert = $db->query("SELECT id  FROM `" . $table . "` where date=$insertdate AND type_id= $cur_type");
        if (!count($is_insert))
        {
            $newinsert = array (
                
                date => $insertdate, 
                type_id => $cur_type, 
                type_name => $type_detail["name"], 
                uid => $uid, 
                u_realname => $realname 
            );
            
            $todaycountupdate = array_merge($todaycount, $newinsert);
            $todaycountupdate = $db->sqljoin($todaycountupdate);
            $rs = $db->query("insert  into `" . $table . "` set $todaycountupdate");
        } else
        {
            
            $rs = $db->query("update  `" . $table . "` set $todaycountupdate  where date=$insertdate AND type_id= $cur_type");
        }
    }
    $out["status"] = "ok";
}  
//同步数据结束


//客服数据 记录 20130131

if (strlen($date) == 8 && $type != '' && $kefu != '' && $type!= 'sycn')
{
    // 判断是否已经添加
    $old = $db->query("select * from $table where type_id='$cur_type' and date='$date' AND kefu = '$kefu' limit 1", 1);

    $r = array ();

    $mode = "add";
    if ($old)
    {
        $r[$type] = $data;
        $mode = "edit";
    } else
    {
        //$r["`type`"] = "web";
        $r["type_id"] = $cur_type;
        $r["type_name"] = $type_detail["name"];
        $r["date"] = $date;
        $r[$type] = $data;
        $r["addtime"] = time();
        $r["kefu"] = $kefu;
        $r["uid"] = $uid;
        $r["u_realname"] = $realname;

    }
    //插入当天数据  20130131 房阳

    $is_data = $db->query("select id from $table where date=$date AND type_id= $cur_type AND kefu = $kefu", "id");

    
    if (count($is_data) != 0)
    {
        $yu_count = $db->query("select * from patient_" . $type_detail['hid']  . " where part_id='2' AND addtime>='" . strtotime($date."0000")."' AND addtime<='" . strtotime($date."2359"). "'");
		//	预约
        foreach ( $yu_count as $v )
        {
            $talk_local = intval($talk_local);
            $talk_other = intval($talk_other);
            $talk = intval($talk);
			
            if ($v['is_local'] == 1)           
            {
                $talk_local = $talk_local + 1;
            }
            if ($v['is_local'] == 2)
            {
                $talk_other = $talk_other + 1;
            }
           
            if ($v['status'] == 1)
            {
                $come = $come + 1;
            }
			$talk=$talk+1;
        };
		
		$lai_count = $db->query("select * from patient_" . $type_detail['hid']  . " where part_id='2' AND order_date>='" . strtotime($date."0000")."' AND order_date<='" . strtotime($date."2359"). "'");
		$result = "select * from patient_" . $type_detail['hid']  . " where part_id='2' AND order_date>='" . strtotime($date."0000")."' AND order_date<='" . strtotime($date."2359"). "'";
		//来院
		foreach ( $lai_count as $v )
        {

            $come_other = intval($come_local);
            $come_local = intval($come_local);
            $come_other = intval($come_local);
            $come = intval($come);
			
            if ($v['is_local'] == 1 && $v['status'] == 1)
            {
                $come_local = $come_local + 1;
            }
            if ($v['is_local'] == 2 && $v['status'] == 1)
            {
                $come_other = $come_other + 1;
            }
            if ($v['status'] == 1)
            {
                $come = $come + 1;
            }
        }
        
        $todaycountupdate = array (

                talk_local => $talk_local,
                talk => $talk,
                talk_other => $talk_other,
                come_local => $come_local,
                come_other => $come_other,
                come => $come
        );

        $todaycountupdate = $db->sqljoin($todaycountupdate);
       
        $rs = $db->query("update  `" . $table . "` set $todaycountupdate  where date=$date AND type_id= $cur_type");//前台系统数据 20130131
        
    }

    // 操作日志:
    if ($mode == "add")
    {
        $r["log"] = date("Y-m-d H:i") . " " . $realname . " 添加: " . $type . ":" . $r[$type] . "\r\n";
    } else
    {
        $r["log"] = $old["log"] . date("Y-m-d H:i") . " " . $realname . " 修改: " . $type . ":" . $old[$type] . "=>" . $r[$type] . "\r\n";
    }

    $sqldata = $db->sqljoin($r);

    $countdata = "click=click_local+click_other,ok_click=ok_click_other+ok_click_local,orders=order_local+order_other,zero_talk=click-ok_click";

    if ($mode == "add")
    {
        $rs = $db->query("insert into $table set $sqldata");
        //echo "<script>alert('".$sqldata."')</script>";
        $rs = $db->query("update $table set $countdata where type_id='$cur_type' and date='$date' AND kefu = '$kefu' limit 1");
    } else
    {
        $rs = $db->query("update $table set $sqldata where type_id='$cur_type' and date='$date' AND kefu = '$kefu' limit 1");
        $rs = $db->query("update $table set $countdata where type_id='$cur_type' and date='$date' AND kefu = '$kefu' limit 1");
    }

    $out["status"] = "ok";
}



echo FastJSON::convert($out);
?>
<?php
/*
// - 功能说明 : 新增、修改病人资料
// - 创建作者 : fangyang (zhuwenya@126.com)
// - 创建时间 : 2013-05-01 08:57
*/
$mode = $op;
if ($_POST)
{
    $po = &$_POST; //引用 $_POST
    
    if ($mode == "edit")
    {
        $oldline = $db->query("select * from $table where id=$id limit 1", 1);
    } else
    {
        // 检查一个月内的病人中有无重复的:
        $name = trim($po["name"]);
        $tel = trim($po["tel"]);
        if (strlen($tel) >= 7)
        {
            $thetime = strtotime("-1 month");
            $list = $db->query("select * from $table where tel='$tel' and addtime>$thetime limit 1", 1);
            if ($list && count($list) > 0)
            {
                msg_box("电话号码重复，提交失败", "back", 1, 5);
            }
        }
    }
    
    /*
	// 检查搜索引擎字段:
	if (!$oldline) {
		$test_line = $db->query("select * from $table limit 1", 1);
	} else {
		$test_line = $oldline;
	}

	// 自动检测字段:  后期可以去除
	if (!isset($test_line["engine"])) {
		$db->query("alter table `{$table}` add `engine` varchar(32) not null after `media_from`;");
	}
	if (!isset($test_line["engine_key"])) {
		$db->query("alter table `{$table}` add `engine_key` varchar(32) not null after `engine`;");
	}
	if (!isset($test_line["from_site"])) {
		$db->query("alter table `{$table}` add `from_site` varchar(40) not null after `engine_key`;");
	}
	*/
    
    // 客服添加疾病类型  2010-10-27
    if ($po["disease_id"] == -1)
    {
        $d_name = $po["disease_add"];
        $d_id = 0;
        if ($d_name != '')
        {
            $d_id = $db->query("insert into disease set hospital_id='$hid', name='$d_name', addtime='$time', author='$username'");
        }
        $po["disease_id"] = $d_id ? $d_id : 0;
    }
    
    $r = array ();
    $sync = array(); //需要同步的数据  姓名、性别、年龄、QQ、媒体来源、地区来源、专家号
   
    if (isset($po["name"])){
        $r["name"] = trim($po["name"]);
        $sync["name"] = trim($po["name"]);    //sync
    }    
    if (isset($po["sex"]))
    {	
        $r["sex"] = $po["sex"];
        $sync["sex"] = $po["sex"];	//sync
    }   
    if (isset($po["qq"]))
    {	
        $r["qq"] = $po["qq"];
        $sync["qq"] = $po["qq"];    //sync
    }    
    if (isset($po["age"]))
    {	
        $r["age"] = $po["age"];
    	$sync["age"] = $po["age"];	//sync
    }	
    if (isset($po["content"]))
        $r["content"] = $po["content"];
    if (isset($po["disease_id"]))
        $r["disease_id"] = $po["disease_id"];
    if (isset($po["depart"]))
        $r["depart"] = $po["depart"];
    if (isset($po["media_from"]))
    {
        $r["media_from"] = $po["media_from"];
    	$sync["media_from"] = $po["media_from"];	//sync
    }	
    if (isset($po["engine"]))
        $r["engine"] = $po["engine"];
    	$sync["engine"] = $po["engine"];
    if (isset($po["engine_key"]))
    {
        $r["engine_key"] = $po["engine_key"];
    	$sync["engine_key"] = $po["engine_key"];	//sync
    }	
    if (isset($po["from_site"]))
    {	
        $r["from_site"] = $po["from_site"];
        $sync["from_site"] = $po["from_site"];	//sync
    }    
    if (isset($po["from_account"]))
    {	
        $r["from_account"] = $po["from_account"]; // 2010-11-04
        $sync["from_account"] = $po["from_account"];	//sync
    }   
    if (isset($po["zhuanjia_num"]))
        $r["zhuanjia_num"] = $po["zhuanjia_num"];
    if (isset($po["is_local"]))
    {	
        $r["is_local"] = $po["is_local"];
    	$sync["is_local"] = $po["is_local"];	//sync
    }	
    if (isset($po["area"]))
    {	
        $r["area"] = $po["area"];
        $sync["area"] = $po["area"];    //sync
    }    
    if (isset($po["chengjiao"]))
        $r["chengjiao"] = $po["chengjiao"]; //2013-03-08
    if (isset($po["cj_sum"]))
        $r["cj_sum"] = $po["cj_sum"];
    
    if ($po["chengjiao"] != "1")
        $r["cj_sum"] = "0";
    
    if (isset($po["doctor"]))
    {
    	$r["doctor"] = $po["doctor"];
    }

 
    
    
    //修改模式中生成用户编号
    if(isset($po['pid'])&&empty($po['pid']))
    {
    	//对于没有编号的用户重新赋值编号
    	$newpid = $user_hospital_id.$po['id'].date('md');
    	$db->query('UPDATE '.$table.' set pid = '.$newpid.' where id ='.$po['id']);
    }
    
    // 修改时间:
    if (isset($po["order_date"]))
    {
        $order_date_post = @strtotime($po["order_date"]);
        if ($mode == "add")
        {
            
            // 如果修改，该时间不能被修改为当前时间的一个月之前(2011-01-15)
            if ($order_date_post < strtotime("-1 month"))
            {
                exit_html("预诊时间不能是一个月之前。（请先检查您的电脑时间是否有误！）  请返回重新填写。");
            }
            
            $r["huifang_date"] = $r["order_date"] = $order_date_post; //新增，默认新增回访时间和预诊时间一样
        } else
        {
            //判断时间是否有修改
            if ($order_date_post != $oldline["order_date"])
            {
                
                // 如果修改，该时间不能被修改为当前时间的一个月之前(2011-01-15)
            	if(!$uinfo['part_admin']&&!$debug_mode){
	                if ($order_date_post < strtotime("-1 month"))
	                {
	                    exit_html("预诊时间不能被修改到一个月之前。（请先检查您的电脑时间是否有误！）  请返回重新填写。");
	                }
            	}
                $r["order_date"] = $order_date_post;
                $r["order_date_changes"] = intval($oldline["order_date_changes"]) + 1;
                $r["order_date_log"] = $oldline["order_date_log"] . (date("Y-m-d H:i:s") . " " . $realname . " 修改 (" . date("Y-m-d H:i", $oldline["order_date"]) . " => " . date("Y-m-d H:i", $order_date_post) . ")<br>");
                
                // 如果修改预诊时间，自动修改状态为等待
                if ($oldline["status"] == 2)
                {
                    $r["status"] = 0;
                }
            }
        }
    }
    
    //回访时间
    if (isset($po["huifang_date"]))
    {
        if ($mode == "edit")
        {
            if ($po["huifang_date"] != "")
            {
                $r["huifang_date"] = strtotime($po["huifang_date"]);
                $r["huifang_date_log"] = $oldline["huifang_date_log"] . (date("Y-m-d H:i:s") . " " . $realname . " 修改 (" . date("Y-m-d H:i:s", $oldline["huifang_date"]) . " => " . date("Y-m-d H:i:s", $r["huifang_date"]) . ")<br>");
            } else
            {
                $r["huifang_date"] = $order_date_post;
            }
        }
    }
    
    //出诊状态  2013-03-08
    $re_arrive_array = array ( 
			array ( 
					"id" => 0, 
					"name" => '初诊' 
			), 
			array ( 
					"id" => 1, 
					"name" => '复诊' 
			), 
			array ( 
					"id" => 2, 
					"name" => '复查' 
			), 
			array ( 
					"id" => 3, 
					"name" => '再消费' 
			) 
	);
    
    //已经更改过状态的人
    $re_arrive_already = array ( 
			array ( 
					"id" => 0, 
					"name" => '初诊' 
			), 
			array ( 
					"id" => 1, 
					"name" => '复诊' 
			), 
			array ( 
					"id" => 2, 
					"name" => '复查' 
			), 
			array ( 
					"id" => 3, 
					"name" => '再消费' 
			) 
	);
    
    /*
    if (isset($po["re_arrive"]))
    {
        if ($mode == "edit")
        {
            if ($po["status"] == 1 && $po["re_arrive"] != "" && $po["re_arrive"] != $oldline['re_arrive'])
            {
                $r["re_arrive"] = $po["re_arrive"];
                $old_arrive = $re_arrive_array[$oldline['re_arrive']]['name'];
                $now_arrive = $re_arrive_array[$po["re_arrive"]]['name'];
                $r["re_arrive_log"] = $oldline["re_arrive_log"] .'<b>'. date("Y-m-d H:i:s") . " " . $realname . "：</b> 修改了  " . $old_arrive . "=> " . $now_arrive . "<br>";
            }
        }
    }
    */
    
    if(isset($po["re_arrive"])&&!empty($po["re_arrive"])&&!empty($po['nexttime'])&&($mode == "edit"))
    {
    	$insertdata = 'insert into '.$table.' (pid,part_id,name,age,sex,disease_id,depart,is_local,area,tel,qq,zhuanjia_num,status,media_from,engine,engine_key,from_site,from_account,doctor,xiangmu,author)
    	SELECT pid,part_id,name,age,sex,disease_id,depart,is_local,area,tel,qq,zhuanjia_num,status,media_from,engine,engine_key,from_site,from_account,doctor,xiangmu,author from '.$table.' where id = '.$po['id'];   	
    	
    	$db->query($insertdata);
    	
    	//获取上一条产生的id 并重新赋值
    	$updata = 'update '.$table.' set re_arrive = '.$po["re_arrive"].',addtime = '.time().' ,order_date = '.strtotime($po['nexttime']).'  where id = '.mysql_insert_id();
    	
    	$db->query($updata);
    	
    }else if(($oldline['re_arrive']!=$po['re_arrive'])&&empty($po['nexttime']))
    {
    	$r['re_arrive'] = $po['re_arrive'];
    }
    
    //出诊状态END
    
    
    if (isset($po["author"]))$r["author"]=$po["author"];
    if (isset($po["memo"]))
        $r["memo"] = $po["memo"];
    if (isset($po["status"]))
        $r["status"] = $po["status"];
    if (isset($po["fee"]))
        $r["fee"] = $po["fee"]; //2010-11-18
        

    // 将接待人修改为当前的导医:
    if ($mode == "edit" && $oldline["jiedai"] == '' && $uinfo["part_id"] == 4)
    {
        $r["jiedai"] = $realname;
    }
    
    // 导医添加直接设置为已到:
    if ($mode == "add" && $uinfo["part_id"] == 4)
    {
        $r["status"] = 1; //已到
        $r["jiedai"] = $realname;
    }
    
    // 已做的整形项目:
    if ($po["update_xiangmu"])
    {
        $r["xiangmu"] = @implode(" ", $po["xiangmu"]);
    }
    
    if (isset($po["huifang"]) && trim($po["huifang"]) != '')
    {
        $r["huifang"] = $oldline["huifang"] . "<b>" . date("Y-m-d H:i") . " [" . $realname . "]</b>:  " . $po["huifang"] . "<br/>";
    }
    
    if ($mode == "edit")
    { //修改模式
        if (isset($po["jiedai_content"]))
        {
            $r["jiedai_content"] = $po["jiedai_content"];
        }
        
        // 修改记录
        if ($oldline["author"] != $realname)
        {
            $r["edit_log"] = $oldline["edit_log"] . $realname . ' 于 ' . date("Y-m-d H:i:s") . " 修改过该资料<br>";
        }
    } else
    { 
    	//新增模式
        $r["part_id"] = $uinfo["part_id"];
        $r["addtime"] = time();
        $r["author"] = $realname;
    }
    
    if (isset($po["tel"]))
    {
        $tel = trim($po["tel"]);
        //if (strlen($tel) > 20) $tel = substr($tel, 0, 20);
        //$r["tel"] = ec($tel, "ENCODE", md5($encode_password));
        $r["tel"] = $tel;
    }
    
    if (isset($r["status"]))
    {
        if (($op == "add" && $r["status"] == 1) || ($op == "edit" && $oldline["status"] != 1 && $r["status"] == 1))
        {
            $r["order_date"] = time();
        }
    }
    
    if ($mode == "edit" && isset($po["rechecktime"]) && $po["rechecktime"] != '')
    {
        if (strlen($po["rechecktime"]) <= 2 && is_numeric($po["rechecktime"]))
        {
            $rechecktime = ($r["order_date"] ? $r["order_date"] : $oldline["order_date"]) + intval($po["rechecktime"]) * 24 * 3600;
        } else
        {
            $rechecktime = strtotime($po["rechecktime"] . " 0:0:0");
        }
        $r["rechecktime"] = $rechecktime;
    }
    
    $sqldata = $db->sqljoin($r);
    $syncdata = $db->sqljoin($sync); //同步相同病人数据
    if ($mode == "edit")
    {
        $sql = "update $table set $sqldata where id='$id'";
       
    } else
    {
        $sql = "insert into $table set $sqldata";
    }
    
    $return = $db->query($sql);
    
    
    //更新病人的编号
    if($mode == "add")
    {
    	$pid = $user_hospital_id.mysql_insert_id().date('md');
    	$sql = "update $table set pid = ".$pid." where id='".mysql_insert_id()."' limit 1";
    	$db->query($sql);
    	
    }else  if($mode == "edit"&&!empty($po['pid']))
    {
    	$is_multi = $db->query("select pid from $table where pid=".$po['pid']);
    	if(count($is_multi)!=1)
    	{
    	    //同步相同病人数据
    	    $syncsql = "update $table set $syncdata where pid=".$po['pid'];
    	    $db->query($syncsql);
    	}
    }
    
    if ($return)
    {
        if ($op == "add")
            $id = $return;
        if ($mode == "edit")
        {
            //$log->add("edit", ("修改了病人资料或状态: ".$oldline["name"]), $oldline, $table);
        } else
        {
            //$log->add("add", ("添加了病人: ".$r["name"]), $r, $table);
        }
      	   // msg_box("资料提交成功", "http://".$_SERVER['HTTP_HOST']."/m/patient/patient.php", 1);

		 if($_REQUEST['go']=='index') //首页跳转
		 {
		 	msg_box("资料提交成功", history(3, $id));
		 }else if($_REQUEST['go']=='patient'){  //patient 列表页
		 	msg_box("资料修改成功", history(3, $id));
		 }else {
		 	msg_box("资料提交成功", history(2, $id));
		 }

    } else
    {
        msg_box("资料提交失败，系统繁忙，请稍后再试。", "back", 1, 5);
    }
    
    exit();//POST部分结束
}

// 读取字典:
$hospital_list = $db->query("select id,name from hospital");
$disease_list = $db->query("select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$doctor_list = $db->query("select id,name from doctor where hospital_id='$user_hospital_id'");
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$depart_list = $db->query("select id,name from depart where hospital_id='$user_hospital_id'");
$engine_list = $db->query("select id,name from engine", "id", "name");
$sites_list = $db->query("select id,url from sites where hid=$hid", "id", "url");
$account_list = $db->query("select id,concat(name,if(type='web',' (网络)',' (电话)')) as fname from count_type where hid=$hid order by id asc", "id", "fname");
$time1 = strtotime("-3 month");
$area_list = $db->query("select area, count(area) as c from $table where area!='' and addtime>$time1 group by area order by c desc limit 20", "", "area");

$author_list = $db->query("select id,realname as name from sys_admin where hospitals like '%$user_hospital_id%' and character_id in(45,16,51,46,47,44,26,15,17)");


$account_first = 0;
if (count ( $account_list ) > 0)
{
	$tmp = @array_keys ( $account_list );
	$account_first = $tmp [0];
}

$status_array = array ( 
		array ( 
				"id" => 0, 
				"name" => '等待' 
		), 
		array ( 
				"id" => 1, 
				"name" => '已到' 
		), 
		array ( 
				"id" => 2, 
				"name" => '未到' 
		) 
);
$jiaoyi_array = array ( 
		array ( 
				"id" => 0, 
				"name" => '未成交' 
		), 
		array ( 
				"id" => 1, 
				"name" => '成交' 
		), 
		array ( 
				"id" => 2, 
				"name" => '未知' 
		) 
);

$xiaofei_array = array ( 
		array ( 
				"id" => 0, 
				"name" => '未消费' 
		), 
		array ( 
				"id" => 1, 
				"name" => '已消费' 
		) 
);
$re_arrive_array = array ( 
		array ( 
				"id" => 0, 
				"name" => '初诊' 
		), 
		array ( 
				"id" => 1, 
				"name" => '复诊' 
		), 
		array ( 
				"id" => 2, 
				"name" => '复查' 
		), 
		array ( 
				"id" => 3, 
				"name" => '再消费' 
		) 
);

//已经更改过状态的人
$re_arrive_already = array (
		array (
				"id" => 1,
				"name" => '复诊'
		),
		array (
				"id" => 2,
				"name" => '复查'
		),
		array (
				"id" => 3,
				"name" => '再消费'
		)
);

// 取前30个病种:
$show_disease = array ();
foreach ( $disease_list as $k => $v )
{
    $show_disease[$k] = $v;
    if (count($show_disease) >= 30)
    {
        break;
    }
}

// 读取编辑 资料
$cur_disease_list = array ();
if ($mode == "edit")
{
    $line = $db->query_first("select * from $table where id='$id' limit 1");
    
    $cur_disease_list = explode(",", $line["disease_id"]);
    foreach ( $cur_disease_list as $v )
    {
        if ($v && !array_key_exists($v, $show_disease))
        {
            $show_disease[$v] = $disease_list[$v];
        }
    }
}

// 2010-08-18
$media_from_array = explode(" ", "网络 电话"); // 网挂 杂志 市场 地铁 朋友介绍 路牌 电视 电台 短信 路过 车身 广告 报纸 其他
$media_from_array2 = $db->query("select name from media where hospital_id='$user_hospital_id'", "", "name");
foreach ( $media_from_array2 as $v )
{
    if (!in_array($v, $media_from_array))
    {
        $media_from_array[] = $v;
    }
}

// 2010-10-23
$is_local_array = array (
    1 => "本市", 2 => "外地" 
);

// 控制各选项是否可以编辑:
$all_field = explode(" ", "name sex age tel qq content disease_id media_from zhuanjia_num order_date doctor status xiaofei memo xiangmu huifang depart author is_local from_account fee");

$ce = array (); // can_edit 的简写, 某字段是否能编辑
if ($mode == "edit")
{ // 修改模式
    $edit_field = array ();
    if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3)  //网络客服和电话客服
    {
        // 未被修改过的资料，还能修改:
        if ($line["status"] == 0 || $line["status"] == 2)
        {
            if ($line["author"] == $realname)
            {
                $edit_field = explode(' ', 'qq content disease_id media_from zhuanjia_num memo order_date depart is_local from_account'); //自己修改
            } else
            {
                $edit_field[] = 'memo'; //不是自己的资料，能修改备注
            }
        } else if ($line["status"] == 1)
        {
            $edit_field[] = 'memo'; //已到的能修改备注
        }
        
        $edit_field[] = "order_date"; //修改回访，并能调整预诊时间
        $edit_field[] = "huifang";
        $edit_field[] = "name";
        $edit_field[] = "sex";
        $edit_field[] = "age";
        $edit_field[] = "qq";
        
        if ($uinfo["part_id"] == 3)
        {
            $edit_field[] = 'xiangmu';
            $edit_field[] = "rechecktime";
        }
    } else if ($uinfo["part_id"] == 4)
    {
        //if ($line["author"] != $realname) {
        // 导医能修改 接待医生，赴约状态，消费，备注等资料
        if ($line["status"] == 1)
        {
            $edit_field[] = 'memo';
            $edit_field[] = 'xiangmu';
            $edit_field[] = 'rechecktime';
            $edit_field[] = 'fee';
        } else
        {
            $edit_field = explode(' ', 'name doctor status xiaofei memo');
        }
    } else if ($uinfo["part_id"] == 12)
    {
        // 电话回访部门
        $edit_field[] = 'order_date';
        $edit_field[] = 'memo';
        $edit_field[] = 'xiangmu';
        $edit_field[] = 'huifang';
        $edit_field[] = 'rechecktime';
    } else
    {
        // 管理员 修改所有的资料
        $edit_field = $all_field;
    }
} else
{ // 新增模式
    if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3)
    { //客服添加
        $edit_field = explode(' ', 'name sex age tel qq content disease_id media_from zhuanjia_num order_date memo depart is_local from_account');
    } else if ($uinfo["part_id"] == 4)
    { //导医添加
        $edit_field = explode(' ', 'name sex age tel qq content disease_id media_from zhuanjia_num order_date doctor status memo depart is_local from_account');
    } else
    {
        $edit_field = $all_field;
    }
}

// 已设置为消费，并且是隔天的数据，将不能修改！
if ($line["status"] == 1 && (strtotime(date("Y-m-d 0:0:0")) > strtotime(date("Y-m-d 0:0:0", $line["come_date"]))))
{
    //$edit_field = array(); //全部不能修改
}

// 每个字段是否能编辑:
foreach ( $all_field as $v )
{
    $ce[$v] = in_array($v, $edit_field) ? '' : ' disabled="true"';
}

// 2009-06-30 10:42 fix
if ($line["media_from"] == "网络客服")
{
    $line["media_from"] = "网络";
} else if ($line["media_from"] == "电话客服")
{
    $line["media_from"] = "电话";
}

$title = $mode == "edit" ? "修改病人资料" : "添加新的病人资料";



// page begin ----------------------------------------------------
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<style>
.dischk {
	width: 6em;
	height: 16px;
	line-height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	padding: 0;
	margin: 0;
}
</style>
<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	var nowDate =new Date().getTime(); 
	var huifang_date = new Date(oForm.huifang_date.value).getTime(); 
	if ((oForm.chengjiao.value==1)&&!oForm.cj_sum.value.match(/^(?:[\+\-]?\d+(?:\.\d+)?)?$/)) {
		alert("成交金额请只能输入数字"); oForm.cj_sum.focus(); return false;
	}
	if (oForm.name.value == "") {
		alert("请输入病人姓名！"); oForm.name.focus(); return false;
	}
	if (oForm.sex.value == '') {
		alert("请输入“性别”！"); oForm.sex.focus(); return false;
	}
	if (oForm.media_from.value == '') {
		alert("请选择“媒体来源”！"); oForm.media_from.focus(); return false;
	}
	if (oForm.order_date.value.length < 12) {
		alert("请正确填写“预诊时间”！"); oForm.order_date.focus(); return false;
	}
	if (huifang_date < nowDate) {
		//alert("回访时间不能早于当前"); oForm.huifang_date.focus(); return false;
	}
	<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname) || $uinfo["part_admin"] ||$debug_mode) : ?>
	if (oForm.tel.value != "" && get_num(oForm.tel.value) == '') {
		alert("请正确输入病人的联系电话！"); oForm.tel.focus(); return false;
	}
	<?php endif?>
	return true;
}

function input(id, value) {
	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
}

function input_date(id, value) {
	var cv = byid(id).value;
	var time = cv.split(" ")[1];

	if (byid(id).disabled != true) {
		byid(id).value = value+" "+(time ? time : '00:00:00');
	}
}

function input_time(id, time) {
	var s = byid(id).value;
	if (s == '') {
		alert("请先填写日期，再填写时间！");
		return;
	}
	var date = s.split(" ")[0];
	var datetime = date+" "+time;

	if (byid(id).disabled != true) {
		byid(id).value = datetime;
	}
}

// 当状态为已到时, 显示选择接待医生:
function change_yisheng(v) {
	byid("yisheng").style.display = (v == 1 ? "inline" : "none");
}

// 检查数据重复:
function check_repeat(type, obj) {
	if (!byid("id") || (byid("id").value == '0' || byid("id").value == '')) {
		var value = obj.value;
		if (value != '') {
			var xm = new ajax();
			xm.connect("/http/check_repeat.php?type="+type+"&value="+value+"&r="+Math.random(), "GET", "", check_repeat_do);
		}
	}
}

function check_repeat_do(o) {
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		if (out["tips"] != '') {
			alert(out["tips"]);
		}
	}
}

function show_hide_engine(o) {
	byid("engine_show").style.display = (o.value == "网络" ? "inline" : "none");
}

function show_hide_area(o) {
	byid("area_from_box").style.display = (o.value == "2" ? "inline" : "none");
}

function show_hide_disease_add(o) {
	byid("disease_add_box").style.display = (o.value == "-1" ? "inline" : "none");
}

function show_hide_cj_sum(o) {
	byid("cj_sum").style.display = (o.value == "1" ? "" : "none");
}

function show_hide_re_arrive(o) {
	byid("re_arrive").style.display = (o.value == "1" ? "" : "none");
}

function set_color(o) {
	if (o.checked) {
		o.nextSibling.style.color = "blue";
	} else {
		o.nextSibling.style.color = "";
	}
}

</script>
</head>

<body>
	<!-- 头部 begin -->
	<header class="jumbotron subhead" style="margin-bottom: 20px;">
		<ul class="breadcrumb">
			<li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span
				class="divider">/</span></li>
			<li class="active text-info"><?php echo $title; ?></li>
			<!-- <li class="text-error"><span class="divider">/</span>多次来诊</li> -->
		</ul>
	</header>
	<!-- 头部 end -->
	<form name="mainform" class="form-horizontal" method="POST"
		onsubmit="return check_data()">
		<fieldset>
			<legend>病人基本信息</legend>
			<div class="control-group">
				<label class="control-label">编号</label>
				<div class="controls">
					<span class="input-xlarge uneditable-input span3 text-error"
						style="margin-left: 0"><?php echo empty($line['pid'])?'暂无编号,将在本次提交数据后生成':$line['pid']?></span>
					<input type="hidden" name="pid" value="<?php echo $line['pid'];?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">姓名</label>
				<div class="controls">
					<input class="span2" type="text" name="name" id="name"
						value="<?php echo $line["name"]; ?>" <?php echo $ce["name"]; ?>
						onchange="check_repeat('name', this)" placeholder="必填">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">性别</label>
				<div class="controls">
					<select name='sex' class="span2">
				        <?php $show_sex = array('男' => '男','女' =>'女')?>
				        <option value="" style="color: gray">--请选择--</option>
				        <?php echo list_option($show_sex, '_key_', '_value_', $line["sex"])?>
				    </select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">年龄</label>
				<div class="controls">
					<input name="age" id="age" value="<?php echo $line["age"]; ?>"
						class="span2" <?php echo $ce["age"]; ?> placeholder="可不填">
				</div>
			</div>
			<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname) || $uinfo["part_admin"] ||$debug_mode) { ?>
			<div class="control-group">
				<label class="control-label">电话</label>
				<div class="controls">
					<input name="tel" id="tel" value="<?php echo $line["tel"]; ?>"
						class="span2" <?php echo $ce["tel"]; ?>
						onchange="check_repeat('tel', this)" placeholder="可不填">
				</div>
			</div>
			<?php } ?>
			<div class="control-group">
				<label class="control-label">QQ</label>
				<div class="controls">
					<input name="qq" id="qq" value="<?php echo $line["qq"]; ?>" class="span2" <?php echo $ce["qq"]; ?> placeholder="可不填">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">咨询内容/聊天记录</label>
				<div class="controls">
					<textarea name="content"
						style="width: 50%; vertical-align: middle;"
						<?php echo $ce["content"]; ?> rows="3" class="input-xlarge"><?php echo $line["content"]; ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">病患类型</label>
				<div class="controls">
					<select name="disease_id" class="span2"
						<?php echo $ce["disease_id"]; ?>>
						<option value="" style="color: gray">--请选择--</option>
				        <?php echo list_option($show_disease, '_key_', '_value_', $line["disease_id"]); ?>
			        </select>
				</div>
			</div>
			<?php if (count($depart_list) > 0) { ?>
			<div class="control-group">
				<label class="control-label">所属科室</label>
				<div class="controls">
					<select name="depart" class="span2" <?php echo $ce["depart"]; ?>>
						<option value="0" style="color: gray">--请选择--</option>
				        <?php echo list_option($depart_list, 'id', 'name', $line["depart"]); ?>
			        </select>
				</div>
			</div>
			<?php } ?>
			<div class="control-group">
				<label class="control-label">媒体来源</label>
				<div class="controls">
					<select name="media_from" class="span2"
						<?php echo $ce["media_from"]; ?> onchange="show_hide_engine(this)">
						<option value="" style="color: gray">--请选择--</option>
				        <?php echo list_option($media_from_array, '_value_', '_value_', $line["media_from"]); ?>
			        </select>&nbsp; <span id="engine_show" style="display:<?php echo $line["media_from"] == "网络" ? "" : "none"; ?>" <?php echo $ce["media_from"]; ?>>
						<select name="engine" class="span2">
							<option value="" style="color: gray">--搜索引擎来源--</option>
						<?php echo list_option($engine_list, '_value_', '_value_', $line["engine"]); ?>
					</select> 关键词：<input name="engine_key"
						value="<?php echo $line["engine_key"]; ?>" class="span2"
						<?php echo $ce["media_from"]; ?>> <select name="from_site"
						class="span2" <?php echo $ce["media_from"]; ?>>
							<option value="" style="color: gray">--来源网站--</option>
						<?php echo list_option($sites_list, '_value_', '_value_', $line["from_site"]); ?>
					</select>
				
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">地区来源</label>
				<div class="controls">
					<select name="is_local" class="span2"
						<?php echo $ce["is_local"]; ?> onchange="show_hide_area(this)">
						<option value="0" style="color: gray">--请选择--</option>
				        <?php echo list_option($is_local_array, '_key_', '_value_', ($op == "add" ? 1 : $line["is_local"])); ?>
			        </select>&nbsp; <span id="area_from_box" style="display: <?php echo $op == "add" ? "none" : ($line["is_local"] == 2 ? "inline" : "none"); ?>">
						地区： <input name="area" id="area"
						value="<?php echo $line["area"]; ?>" class="span2"
						<?php echo $ce["is_local"]; ?>> &nbsp; ←速填常用地区： <select
						id="quick_area" class="span2" <?php echo $ce["is_local"]; ?>
						onchange="byid('area').value=this.value;">
							<option value="" style="color: gray">-地区-</option>
				        <?php echo list_option($area_list, "_value_", "_value_"); ?>
			        </select>
				
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo $uinfo["part_id"] == 4 ? "就诊号" : "专家号"; ?></label>
				<div class="controls">
					<input name="zhuanjia_num"
						value="<?php echo $line["zhuanjia_num"]; ?>" class="span2"
						<?php echo $ce["zhuanjia_num"]; ?> placeholder="可不填">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">预诊时间</label>
				<div class="controls">
				    <div class="input-append">
						<input name="order_date"  value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="span2" id="order_date" <?php echo $ce["order_date"]; ?>> 
						<span class="add-on">已修改<?php echo intval($line["order_date_changes"]); ?>次</span>
					</div>
					<span class="help-inline">
					    请注意，此处已调整，预诊时间不能早于上个月<?php echo date("j"); ?>号，否则资料无法提交。
					    <?php echo ($uinfo['part_admin']||$debug_mode)?'<span class="text-error">管理员可对时间进行任意修改</span>':''?>
					</span>	
					<?php if ($line["order_date_log"]) { ?>
					<a href="javascript:void(0)" onclick="byid('order_date_log').style.display = (byid('order_date_log').style.display == 'none' ? 'block' : 'none'); ">查看修改记录</a>
					<?php } ?>
					<?php
					$show_days = array (
					    "今" => $today = date("Y-m-d"),  //今天
					    "明" => date("Y-m-d", strtotime("+1 day")),  //明天
					    "后" => date("Y-m-d", strtotime("+2 days")),  //后天
					    "大后天" => date("Y-m-d", strtotime("+3 days")),  //大后天
					    "周六" => date("Y-m-d", strtotime("next Saturday")),  //周六
					    "周日" => date("Y-m-d", strtotime("next Sunday")),  // 周日
					    "周一" => date("Y-m-d", strtotime("next Monday")),  // 周一
					    "一周后" => date("Y-m-d", strtotime("+7 days")),  // 一周后
					    "半月后" => date("Y-m-d", strtotime("+15 days"))  //半个月后
					);
					if (!$ce["order_date"])
					{
					    echo '<p class="help-block">日期: ';
					    foreach ( $show_days as $name => $value )
					    {
					        echo '<a href="javascript:input_date(\'order_date\', \'' . $value . '\')">[' . $name . ']</a>&nbsp;';
					    }
					    echo '<br>时间: ';
					    echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[时间不限]</a>&nbsp;';
					    echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[上午9点]</a>&nbsp;';
					    echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[下午2点]</a>&nbsp;</p>';
					}
					?>
		            <?php if ($line["order_date_log"]) { ?>
		            <div id="order_date_log"
						style="display: none; padding-top: 6px;">
						<b>预诊时间修改记录:</b> <br><?php echo strim($line["order_date_log"], '<br>'); ?>
				    </div>
		            <?php } ?>
			    </div>
			</div>
			<div class="control-group">
				<label class="control-label">备注</label>
				<div class="controls">
					<textarea rows="3" name="memo"
						style="width: 50%; vertical-align: middle;"
						<?php echo $ce["memo"]; ?>><?php echo $line["memo"]; ?></textarea>
				</div>
			</div>
			
			<?php if ($line["edit_log"] && $line["author"] == $realname):?>
			<div class="control-group">
				<label class="control-label">资料修改记录</label>
				<div class="controls">
			        <?php echo strim($line["edit_log"], '<br>'); ?>
			    </div>
			</div>
			<?php endif?>
		</fieldset>
		<?php
        if (in_array($uinfo["part_id"], array (  4, 9, 12 )) && $line["status"] == 1) {?>
		<fieldset>
			<legend>治疗项目</legend>
			<div class="control-group">
				<label class="control-label">治疗项目</label>
				<div class="controls">
				    <?php
			            $xiangmu_str = $db->query("select xiangmu from disease where id=". $line ["disease_id"] . " limit 1", 1, "xiangmu" );
						$xiangmu = explode ( " ", trim ( $xiangmu_str ) );
						$cur_xiangmu = explode ( " ", trim ( $line ["xiangmu"] ) );
						$xiangmu = array_unique ( array_merge ( $cur_xiangmu, $xiangmu ) );
						foreach ( $xiangmu as $k )
						{
							if ($k == '')
								continue;
							$checked = in_array ( $k, $cur_xiangmu ) ? " checked" : "";
							$makered = $checked ? ' style="color:red"' : '';
							echo '<input type="checkbox" name="xiangmu[]" value="' . $k . '"' . $checked . ' id="xiangmu_' . $k . '"' . $ce ["xiangmu"] . '><label for="xiangmu_' . $k . '"' . $makered . '>' . $k . '</label>&nbsp;&nbsp;';
						}
					?>
					<?php if (!$ce["xiangmu"]) { ?>
						<input type="hidden" name="update_xiangmu" value="1"> 
						<span id="xiangmu_user"></span> 
						<span id="xiangmu_add">
							<b>增加：</b>
							<input id="miangmu_my_add" class="span2">&nbsp;
							<button onclick="xiangmu_user_add()" class="btn">确定</button>
						</span>
						<script language="JavaScript">
							function xiangmu_user_add() {
								var name = byid("miangmu_my_add").value;
								if (name == '') {
									alert("请输入新加的名字！"); return false;
								}
								var str = '<input type="checkbox" name="xiangmu[]" value="'+name+'" checked id="xiangmu_'+name+'"><label for="xiangmu_'+name+'">'+name+'</label>&nbsp;&nbsp;';
								byid("xiangmu_user").insertAdjacentHTML("beforeEnd", str);
								byid("miangmu_my_add").value = '';
							}
					     </script>
					<?php } ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">治疗费用</label>
				<div class="controls">
					<input name="fee" id="fee" value="<?php echo $line["fee"] > 0 ? $line["fee"] : ''; ?>" class="span2" <?php echo $ce["fee"]; ?>>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">复查时间</label>
				<div class="controls">
					<input name="rechecktime" id="rechecktime" value="<?php if ($line["rechecktime"]>0) echo date("Y-m-d", $line["rechecktime"]); ?>" class="span2" <?php echo $ce["rechecktime"]; ?>>
			        <?php if ($line["rechecktime"]) echo intval(($line["rechecktime"] - $line["order_date"]) / 24/3600)."天 "; ?>
			        <p class="help-inline">可填写天数(如 10 相对于预诊时间推算)或具体时间(如 2009-10-1) </p>
				</div>
			</div>
			
		</fieldset>
		<?php } ?>
		<?php if (in_array($uinfo["part_id"], array(1,4,9)) || ($username == "admin") || $debug_mode): ?>
	    <fieldset>
	        <legend>到院详情</legend>
	        <div class="control-group">
				<label class="control-label">赴约状态</label>
				<div class="controls">
					<select name="status" class="span2" onchange="show_hide_re_arrive(this)" <?php echo $ce["status"]; ?>
					    <?php echo $line['re_arrive']!=0?'disabled':'';?>>
						<!-- onchange="change_yisheng(this.value)" -->
						<option value="0" style="color: gray">--请选择--</option>
				        <?php echo list_option($status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $line["status"]); ?>
			        </select> 
			        <span id="re_arrive" style="display:<?php echo($line["status"]==1?"":"none")?>">&nbsp;&nbsp;<!--style="display:<?php echo($line["re_arrive"]==1?"":"none")?>"-->
				    </span>&nbsp;
				    <?php if ($line["re_arrive_log"]) { ?>
				    <a href="javascript:void(0)" onclick="byid('re_arrive_log').style.display = (byid('re_arrive_log').style.display == 'none' ? 'block' : 'none'); ">查看修改记录</a><?php }?>
				    <?php if ($line["re_arrive_log"]) { ?>
		            <div id="re_arrive_log" style="display: none; padding-top: 6px;">
						<b>到院状态修改记录:</b> <br><?php echo strim($line["re_arrive_log"], '<br>'); ?>
					</div>
		            <?php } ?>
				</div>
			</div>
			<!-- 只有在来诊的情况下才启用出诊状态 以及时间 -->
			<?php if($line['status']==1):?>
			<div class="control-group">
				<label class="control-label">出诊状态</label>
				<div class="controls">
					<select name="re_arrive" class="span2" <?php echo  $line["status"] !=1?'disabled':'';?>>
					    <?php 
					    if($line['re_arrive']!=0)
                        {
                        	echo list_option($re_arrive_already, 'id', 'name', $line["re_arrive"]);
                        	
                        }else{
                        	echo list_option($re_arrive_array, 'id', 'name', $line["re_arrive"]);
                        }  
					    ?>
			       </select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">来院时间</label>
				<div class="controls">
					<input name="nexttime" id="nexttime" value="" class="span2" <?php echo $line['status']!=1?'disabled':'';?>>
					<p class="help-inline">该时间表示病人下次来院时间，如果不填时间，则仅仅改变当前病人状态</p>
				</div>
			</div>
			<?php endif?>
		
			<div class="control-group">
				<label class="control-label">接待医生</label>
				<div class="controls">
					<select name="doctor" class="span2" <?php echo $ce["doctor"]; ?>>
						<option value="" style="color: gray">--请选择--</option>
				        <?php echo list_option($doctor_list, 'name', 'name', $line["doctor"]); ?>
			        </select>
					<p class="help-inline">当病人已到院时选择</p>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">网络客服</label>
				<div class="controls">
				    <select name="author" class="span2" <?php echo ($uinfo["part_id"] == 1||$uinfo["part_id"] == 9 || $uinfo["part_admin"] ||$debug_mode)?"":"disabled";?>>
					    <option value="" style="color: gray">--请选择--</option>
				        <?php echo list_option($author_list, 'name', 'name', $line["author"]); ?>
			        </select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">成交状态</label>
				<div class="controls">
					<select name="chengjiao" class="span2">
						<option value="" style="color: gray">--请选择--</option>
				        <?php echo list_option($jiaoyi_array, 'id', 'name', $line["chengjiao"]); ?>
			        </select>
			        <!-- 
			        <span id="cj_sum" style="display:<?php echo($line["chengjiao"]==1?"":"none")?>">&nbsp;&nbsp;
					成交金额:<input name="cj_sum" value="<?=$line["cj_sum"]?>" class="span2">元
					<p class="help-block">当状态改为未"已到",成交金额清零</p>
					 -->
				</div>
			</div>
        </fieldset>
        <?php endif?>
        
        <!-- 接待记录 -->
        <?php if ($mode == "edit" && $line["status"] == 1 && ($debug_mode || in_array($uinfo["part_id"], array(1,4,9))) ) : ?>
        <fieldset>
            <legend>到院接待记录</legend>
            <div class="control-group">
				<label class="control-label">接待记录</label>
				<div class="controls">
				    <textarea name="jiedai_content" style="width: 50%; height: 48px; vertical-align: middle;" class="span2"><?php echo $line["jiedai_content"]; ?> </textarea>
				</div>
			</div>
        </fieldset>
        <?php endif ?>
        
        
        <?php if ($mode == "edit" && (in_array("huifang", $edit_field) || $line["author"] == $username)): ?>
        <?php  $huifang = trim($line["huifang"]);  ?>
        <fieldset>
            <legend>电话回访记录</legend>
            <div class="control-group">
				<label class="control-label">下次回访时间</label>
				<div class="controls">
				    <input name="huifang_date" value="<?php echo $line["huifang_date"] ? @date('Y-m-d H:i:s', $line["huifang_date"]) : ''; ?>" class="span2"  id="huifang_date"
					<?php echo $ce["huifang_date"]; ?>> 
					<p class="help-inline">回访时间将不能早于当前</p>
					<?php if ($line["huifang_date"]) echo '还剩<strong class="red">'.intval(($line["huifang_date"] - mktime(0,0,0)) / 24/3600)."</strong>天 "; ?>
					<?php if ($line["huifang_date_log"]) { ?>
					<a href="javascript:void(0)" onclick="byid('huifang_date_log').style.display = (byid('huifang_date_log').style.display == 'none' ? 'block' : 'none'); ">查看修改记录</a><?php } ?>
					<?php
					    $show_days = array (
					        
					        "今" => $today = date("Y-m-d"),  //今天
					        "明" => date("Y-m-d", strtotime("+1 day")),  //明天
					        "后" => date("Y-m-d", strtotime("+2 days")),  //后天
					        "大后天" => date("Y-m-d", strtotime("+3 days")),  //大后天
					        "周六" => date("Y-m-d", strtotime("next Saturday")),  //周六
					        "周日" => date("Y-m-d", strtotime("next Sunday")),  // 周日
					        "周一" => date("Y-m-d", strtotime("next Monday")),  // 周一
					        "一周后" => date("Y-m-d", strtotime("+7 days")),  // 一周后
					        "半月后" => date("Y-m-d", strtotime("+15 days"))  //半个月后
					    );
					    if (!$ce["huifang_date"])
					    {
					        echo '<div style="padding-top:6px;">日期: ';
					        foreach ( $show_days as $name => $value )
					        {
					            echo '<a href="javascript:input_date(\'huifang_date\', \'' . $value . '\')">[' . $name . ']</a>&nbsp;';
					        }
					        echo '<br>时间: ';
					        echo '<a href="javascript:input_time(\'huifang_date\',\'00:00:00\')">[时间不限]</a>&nbsp;';
					        echo '<a href="javascript:input_time(\'huifang_date\',\'09:00:00\')">[上午9点]</a>&nbsp;';
					        echo '<a href="javascript:input_time(\'huifang_date\',\'14:00:00\')">[下午2点]</a>&nbsp;</div>';
					    }
					    ?>
						<?php if ($line["huifang_date_log"]) { ?>
						<div id="huifang_date_log" style="display: none; padding-top: 6px;">
							<b>回访时间修改记录:</b> <br><?php echo strim($line["huifang_date_log"], '<br>'); ?></div>
						<?php } ?>
				</div>
			</div>
			<div class="control-group">
			    <label class="control-label">历次回访</label>
			    <div class="controls">
			        <p class="help-inline"><?php echo $line["huifang"] ? text_show($line["huifang"]) : "<font color=gray>(暂无记录)</font>"; ?>
			        </p>
			    </div>    
			</div>
			<div class="control-group">
			    <label class="control-label">本次回访</label>
			    <div class="controls">
			        <textarea name="huifang" style="width: 50%; height: 48px; vertical-align: middle;" class="span2" <?php echo $ce["huifang"]; ?>></textarea>
			    </div>
			</div>
        </fieldset>
        <?php endif ?>

		<input type="hidden" name="id" id="id" value="<?php echo $id; ?>"> 
		<input type="hidden" name="op" value="<?php echo $mode; ?>"> 
		<input type="hidden" name="go" value="<?php echo $_GET["go"]; ?>">
		<div class="form-actions">
            <button type="submit" class="btn btn-primary">保存更改</button>
        </div>
	</form>
	<div class="space"></div>
	<div class="alert alert-info">
		<div class="d_title">提示：</div>
		<div class="d_item">1.姓名必须填写； 2.电话号码如果填写，则必须是数字，不少于7位； 3.未尽资料填写于备注中。</div>
	</div>
<script>
$("#order_date").datetimepicker({
	format: "yyyy-mm-dd hh:ii:ss",
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left"
});
$("#rechecktime").datetimepicker({
    format: "yyyy-mm-dd hh:ii:ss",
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left"
});
$("#nexttime").datetimepicker({
	format: "yyyy-mm-dd hh:ss",
    autoclose: true,
    todayBtn: true,
    minView:'day',
    pickerPosition: "bottom-left"
});
$("#huifang_date").datetimepicker({
	format: "yyyy-mm-dd hh:ss",
    autoclose: true,
    todayBtn: true,
    minView:'day',
    pickerPosition: "bottom-left"
});
</script>
</body>
</html>
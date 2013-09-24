<?php
/*
 * 功能说明 : 病人消费修改
 * 创建作者 : fangyang (278294861)
 * 创建时间 : 2013-05-01 08:57
 */
$mode = $op;
if ($_POST)
{
    $po = &$_POST; //引用 $_POST
    
    if ($mode == "edit")
    {
        $oldline = $db->query("select * from $table where id=$id limit 1", 1);
    } 
    
    $r = array ();

    
    if (isset($po["id"]))
        $r['id'] = $po['id'];
    
    if (isset($po["pid"]))
        $r['pid'] = $po['pid'];
    
    if (isset($po["aid"]))
        $r['aid'] = $po['aid'];
    
    if (isset($po["net_author"]))
    	$r["net_author"] = $po["net_author"];
    
    if (isset($po["fee_type"]))
        $r['fee_type'] = $po['fee_type'];
    
    if (isset($po["y_charge"]))
        $r["y_charge"] = $po["y_charge"];
    
    if (isset($po["s_charge"]))
        $r["s_charge"] = $po["s_charge"];
    
    if (isset($po["is_complete"]))
    	$r["is_complete"] = $po["is_complete"];
    
    if (isset($po["cj_time"]))
    	$r["cj_time"] = strtotime($po["cj_time"]);
    
    if (isset($po["memo"]))
    	$r["memo"] = $po["memo"];
  

    if ($mode == "edit")
    {   
        // 修改记录
        $r["log"] = $oldline["log"] . $realname . ' 于 ' . date("Y-m-d H:i:s") . " 修改过该资料<br>";
        
    } else
    { 
    	//新增模式
        $r['cj_time'] = time();
        $r['author'] = $realname;
        $r['hid'] = $user_hospital_id;
    }

    
    $sqldata = $db->sqljoin($r);
    if ($mode == "edit")
    {
        $sql = "update $table set $sqldata where id='$id'";
       
    } else
    {
        $sql = "insert into $table set $sqldata";
    }
    
    $return = $db->query($sql);
    
    if ($return)
    {
         if ($op == "add")
            $id = $return;
       
		 if($op == 'add')
		 {
		 	msg_box("数据提交成功",history(1, $id));
		 }else
		 {
		 	msg_box("数据提交成功",history(2, $id));
		 }

    } else
    {
        msg_box("资料提交失败，系统繁忙，请稍后再试。", "back", 1, 5);
    }
    
    exit();//POST部分结束
}

// 读取字典:
$disease_list = $db->query("select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$time1 = strtotime("-3 month");

$show_disease = array ();
foreach ( $disease_list as $k => $v )
{
	$show_disease[$k] = $v;
	if (count($show_disease) >= 30)
	{
		break;
	}
}


$account_first = 0;
if (count ( $account_list ) > 0)
{
	$tmp = @array_keys ( $account_list );
	$account_first = $tmp [0];
}

$cj_array = array ( 
		array ( 
				"id" => 0, 
				"name" => '未知' 
		), 
		array ( 
				"id" => 1, 
				"name" => '完成' 
		), 
		array ( 
				"id" => 2, 
				"name" => '未完成' 
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



// 读取编辑 资料
$cur_disease_list = array ();
if ($mode == "edit")
{
    $line = $db->query_first("select $table.*,$patient_table.name,$patient_table.pid,$patient_table.disease_id from $table LEFT JOIN $patient_table on $table.aid = $patient_table.id where $table.id=$id limit 1");
}

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

$title = $mode == "edit" ? "修改病人消费资料" : "添加新的病人消费资料";


?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<script language="javascript">
function check_data() {
	
	var oForm = document.mainform;
	if ((oForm.y_charge.value=='')||!oForm.y_charge.value.match(/^(?:[\+\-]?\d+(?:\.\d+)?)?$/)) {
		alert("应收金额不合法"); oForm.y_charge.focus(); return false;
	}
	
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
		</ul>
	</header>
	<!-- 头部 end -->
	<form name="mainform" class="form-horizontal" method="POST" onsubmit="return check_data()">
		<fieldset>
			<legend>基本信息</legend>
			<div class="control-group">
				<label class="control-label">编号</label>
				<div class="controls">
					<span class="input-xlarge uneditable-input span2"  style="margin-left: 0">
					    <?php echo $line['pid'];?>
					</span>
					<input type="hidden" name="pid" value="<?php echo $line['pid'];?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">姓名</label>
				<div class="controls">
					<span  class="input-xlarge uneditable-input span2"  style="margin-left: 0">
					    <?php echo $line["name"]; ?>
					</span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">病患类型</label>
				<div class="controls">
				    <span class="input-xlarge uneditable-input span2"  style="margin-left: 0">
				        <?php echo $show_disease[$line["disease_id"]];?>
				    </span>
				
				</div>
			</div>
		</fieldset>
		
		<fieldset>
	        <legend>成交详情</legend>
			<div class="control-group warning">
			    <label class="control-label">应收金额</label>
			    <div class="controls">
			        <input type="text" name="y_charge" id="y_charge" class="span2" value="<?php echo (float)$line['y_charge'];?>"> 
			        <span class="help-inline">谨慎修改！</span>
			    </div>
			</div>
			
			<div class="control-group warning">
			    <label class="control-label">实收金额</label>
			    <div class="controls">
			        <input type="text" name="s_charge" class="span2" value="<?php echo (float)$line['s_charge'];?>">
			        <span class="help-inline">谨慎修改！</span>
			    </div>
			</div>
			
			<div class="control-group">
				<label class="control-label">成交状态</label>
				<div class="controls">
					<select name="is_complete" class="span2">
				        <?php echo list_option($cj_array, 'id', 'name', $line["is_complete"]); ?>
			        </select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">成交时间</label>
				<div class="controls">
					<input name="cj_time" value="<?php echo date('Y-m-d h:m',$line['cj_time']);?>" class="span2" id="chengjiao_date" <?php echo $uinfo["part_id"]==1||$uinfo["part_admin"]?'disabled':'' ;?>>
				</div>
			</div>
			
			<div class="control-group">
			    <label class="control-label">备注</label>
			    <div class="controls">
			        <textarea name="memo" class="input-xlarge" rows="3"><?php echo $line["memo"];?></textarea>
			    </div>
			</div>
        </fieldset>
        
        <fieldset>
            <legend>修改记录</legend>
            <?php echo $line['log'];?>
        </fieldset>
       
		<input type="hidden" name="id" id="id" value="<?php echo $id; ?>"> 
		<input type="hidden" name="op" value="<?php echo $mode; ?>"> 
		<input type="hidden" name="go" value="<?php echo $_GET["go"]; ?>">
		<div class="form-actions">
            <button type="submit" class="btn btn-primary">保存更改</button>
            <button type="button" onclick="history.back()" class="btn">返回</button>
        </div>
	</form>
	<div class="space"></div>
	<div class="alert alert-info">
		<div class="d_title">提示：</div>
		<div class="d_item"></div>
	</div>
<script>
$("#chengjiao_date").datetimepicker({
	format: "yyyy-mm-dd hh:ii",
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left"
});
</script>	
</body>
</html>
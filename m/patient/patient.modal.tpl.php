<?php 
header("Content-type: text/html; charset=gb2312");
require "../../core/core.php";
$table = "patient_".$user_hospital_id;

if($_GET)
{
	//医生
	$doctor_list = $db->query("select id,name from doctor where hospital_id='$user_hospital_id'");
	//客服
	$admin_name = $db->query("select realname from sys_admin", "", "realname");
	$author_name = $db->query("select distinct author from $table order by binary author", "", "author");
	$kefu_23_list = array_intersect($admin_name, $author_name);
	//疾病
	$disease_list = $db->query("select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name");
	//地区
	$area_list = $db->query("select area, count(area) as c from $table where area!='' group by area order by c desc limit 20", "", "area");
	//成交状态
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
	
	$po = &$_GET; //引用 $_POST
	$id = $po['id'];
	$list = $db->query("select * from $table where id = $id", 1);
	
	if(count($list)==0)
	{
		echo '<h3 class="text-center">载入远程数据出错！</h2>'.count($list);exit();
	}
?>
<form class="form-horizontal" action="patient.php" method="POST" >
<fieldset style="height:400px;overflow-y:auto">
	<div class="control-group">
		<label class="control-label" for="">姓名</label>
		<div class="controls">
			<div class="input-append">
				<input class="span2"  name="name" value="<?=$list['name'] ?>"
					size="16" type="text">
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">性别</label>
		<div class="controls">
			<div class="input-append">
				<select name="sex" class="span2">
					<option value="" style="color: gray">--请选择--</option>
					<?php $show_sex = array('男' => '男','女' =>'女')?>
				    <?php echo list_option($show_sex, '_key_', '_value_', $list["sex"])?>
				</select>
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">年龄</label>
		<div class="controls">
			<div class="input-append">
				<input class="span2"  name="age"  value="<?=$list['age']?>"
					size="16" type="text">
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="">疾病类型</label>
		<div class="controls">
			<select name="disease_id" class="span2">
				<option value="" style="color: gray">--请选择--</option>
			    <?php echo list_option($disease_list, '_key_', '_value_', $list["disease_id"]); ?>
	        </select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="">地区</label>
		<div class="controls">
			<select name="is_local" class="span2" id="is_local">
				<option value="" style="color: gray">--请选择--</option>
				<?php $is_local_array = array ( 1 => "本市", 2 => "外地" );?>
				<?php echo list_option($is_local_array, '_key_', '_value_', $list["is_local"]); ?>
			</select>
			<select id="quick_area" name="area" class="span2" disabled>
			    <option value="" style="color: gray">-地区-</option>
	            <?php echo list_option($area_list, "_value_", "_value_"); ?>
            </select>
		</div>
	</div>
	
	<?php if($uinfo["part_id"]!=5):?>
	<div class="control-group">
		<label class="control-label" for="">接待医生</label>
		<div class="controls">
			<select name="doctor" class="span2">
			     <option value="" style="color: gray">--请选择--</option>
				 <?php echo list_option($doctor_list, 'name', 'name', $list["doctor"]); ?>	        
		      </select>
		 </div>
	</div>
	<?php endif?>
	
	<div class="control-group info">
		<label class="control-label" for="">赴约状态</label>
		<div class="controls">
		    <select name="status" class="span2">
				<option value="0" style="color: gray">--请选择--</option>
		        <?php echo list_option($status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $list["status"]); ?>
	        </select>     
		</div>
	</div>
	
	<div class="control-group info">
		<label class="control-label" for="">成交状态</label>
		<div class="controls">
		    <select name="chengjiao" class="span2">
			    <option value="" style="color: gray">--请选择--</option>
		        <?php echo list_option($jiaoyi_array, 'id', 'name', $list["chengjiao"]); ?>
		    </select>
		</div>
	</div>
	<?php if($list['status']==1):?>
	<div class="control-group info">
		<label class="control-label" for="">出诊状态</label>
		<div class="controls">
		    <select name="re_arrive" class="span2"
			    <?php echo  $list["status"] !=1?'disabled':'';?>>
			    <?php 
					    if($line['re_arrive']!=0)
                        {
                        	echo list_option($re_arrive_already, 'id', 'name', $list["re_arrive"]);
                        	
                        }else{
                        	echo list_option($re_arrive_array, 'id', 'name', $list["re_arrive"]);
                        }  
			    ?>
			 </select>
			  <input name="nexttime" id="nexttime" value="" class="span2" size="20" <?php echo $list['status']!=1?'disabled':'';?>>    
		</div>
	</div>
	<?php endif;?>
	
	<?php if($uinfo["part_id"]==5):?>
	<div class="control-group">
		<label class="control-label" for="">接待内容</label>
		<div class="controls">
			<textarea   style="height:40px;width:280px" name="jiedai_content"><?=$list['jiedai_content']?></textarea>
		</div>
	</div>
	<?php endif?>
	<div class="control-group">
		<label class="control-label" for="">备注</label>
		<div class="controls">
			<textarea   style="height:40px;width:280px" name="memo"><?=$list['memo']?></textarea>
		</div>
	</div>
</fieldset>
<div class="modal-footer">
	<button class="btn" type="button" data-dismiss="modal" aria-hidden="true">关闭</button>
	<button class="btn btn-info" type="submit">提交</button>
	<input type="hidden" name="op" value="edit">
	<input type="hidden" name="action" value="basic">
	<input type="hidden" name="id" value="<?=$list['id']?>">
	<input type="hidden" name="go" value="listModal">
	<input type="hidden" name="model" value="ajax">
	<input type="hidden" name="pid" value="<?=$list['pid']?>">
</div>	
</form>
<script>

$("#is_local").change(function(){
    if($(this).val()==2)
    {
    	$("#quick_area").attr("disabled",false); 
    }else
    {
    	$("#quick_area").attr("disabled",true);
    }
})
$("#nexttime").datetimepicker({
	format: "yyyy-mm-dd hh:ss",
    autoclose: true,
    todayBtn: true,
    minView:'day',
    pickerPosition: "bottom-left"
})

</script>
<?php };?>
			    
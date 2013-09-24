<?php 
header("Content-type: text/html; charset=gb2312");
require "../../core/core.php";
$table = "patient_".$user_hospital_id;

if($_GET)
{
	//ҽ��
	$doctor_list = $db->query("select id,name from doctor where hospital_id='$user_hospital_id'");
	//�ͷ�
	$admin_name = $db->query("select realname from sys_admin", "", "realname");
	$author_name = $db->query("select distinct author from $table order by binary author", "", "author");
	$kefu_23_list = array_intersect($admin_name, $author_name);
	//����
	$disease_list = $db->query("select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name");
	//����
	$area_list = $db->query("select area, count(area) as c from $table where area!='' group by area order by c desc limit 20", "", "area");
	//�ɽ�״̬
	$jiaoyi_array = array (
			array (
					"id" => 0,
					"name" => 'δ�ɽ�'
			),
			array (
					"id" => 1,
					"name" => '�ɽ�'
			),
			array (
					"id" => 2,
					"name" => 'δ֪'
			)
	);
	$status_array = array (
			array (
					"id" => 0,
					"name" => '�ȴ�'
			),
			array (
					"id" => 1,
					"name" => '�ѵ�'
			),
			array (
					"id" => 2,
					"name" => 'δ��'
			)
	);
	$re_arrive_array = array (
			array (
					"id" => 0,
					"name" => '����'
			),
			array (
					"id" => 1,
					"name" => '����'
			),
			array (
					"id" => 2,
					"name" => '����'
			),
			array (
					"id" => 3,
					"name" => '������'
			)
	);
	
	//�Ѿ����Ĺ�״̬����
	$re_arrive_already = array (
			array (
					"id" => 0,
					"name" => '����'
			),
			array (
					"id" => 1,
					"name" => '����'
			),
			array (
					"id" => 2,
					"name" => '����'
			),
			array (
					"id" => 3,
					"name" => '������'
			)
	);
	
	$po = &$_GET; //���� $_POST
	$id = $po['id'];
	$list = $db->query("select * from $table where id = $id", 1);
	
	if(count($list)==0)
	{
		echo '<h3 class="text-center">����Զ�����ݳ���</h2>'.count($list);exit();
	}
?>
<form class="form-horizontal" action="patient.php" method="POST" >
<fieldset style="height:400px;overflow-y:auto">
	<div class="control-group">
		<label class="control-label" for="">����</label>
		<div class="controls">
			<div class="input-append">
				<input class="span2"  name="name" value="<?=$list['name'] ?>"
					size="16" type="text">
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">�Ա�</label>
		<div class="controls">
			<div class="input-append">
				<select name="sex" class="span2">
					<option value="" style="color: gray">--��ѡ��--</option>
					<?php $show_sex = array('��' => '��','Ů' =>'Ů')?>
				    <?php echo list_option($show_sex, '_key_', '_value_', $list["sex"])?>
				</select>
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">����</label>
		<div class="controls">
			<div class="input-append">
				<input class="span2"  name="age"  value="<?=$list['age']?>"
					size="16" type="text">
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="">��������</label>
		<div class="controls">
			<select name="disease_id" class="span2">
				<option value="" style="color: gray">--��ѡ��--</option>
			    <?php echo list_option($disease_list, '_key_', '_value_', $list["disease_id"]); ?>
	        </select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="">����</label>
		<div class="controls">
			<select name="is_local" class="span2" id="is_local">
				<option value="" style="color: gray">--��ѡ��--</option>
				<?php $is_local_array = array ( 1 => "����", 2 => "���" );?>
				<?php echo list_option($is_local_array, '_key_', '_value_', $list["is_local"]); ?>
			</select>
			<select id="quick_area" name="area" class="span2" disabled>
			    <option value="" style="color: gray">-����-</option>
	            <?php echo list_option($area_list, "_value_", "_value_"); ?>
            </select>
		</div>
	</div>
	
	<?php if($uinfo["part_id"]!=5):?>
	<div class="control-group">
		<label class="control-label" for="">�Ӵ�ҽ��</label>
		<div class="controls">
			<select name="doctor" class="span2">
			     <option value="" style="color: gray">--��ѡ��--</option>
				 <?php echo list_option($doctor_list, 'name', 'name', $list["doctor"]); ?>	        
		      </select>
		 </div>
	</div>
	<?php endif?>
	
	<div class="control-group info">
		<label class="control-label" for="">��Լ״̬</label>
		<div class="controls">
		    <select name="status" class="span2">
				<option value="0" style="color: gray">--��ѡ��--</option>
		        <?php echo list_option($status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $list["status"]); ?>
	        </select>     
		</div>
	</div>
	
	<div class="control-group info">
		<label class="control-label" for="">�ɽ�״̬</label>
		<div class="controls">
		    <select name="chengjiao" class="span2">
			    <option value="" style="color: gray">--��ѡ��--</option>
		        <?php echo list_option($jiaoyi_array, 'id', 'name', $list["chengjiao"]); ?>
		    </select>
		</div>
	</div>
	<?php if($list['status']==1):?>
	<div class="control-group info">
		<label class="control-label" for="">����״̬</label>
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
		<label class="control-label" for="">�Ӵ�����</label>
		<div class="controls">
			<textarea   style="height:40px;width:280px" name="jiedai_content"><?=$list['jiedai_content']?></textarea>
		</div>
	</div>
	<?php endif?>
	<div class="control-group">
		<label class="control-label" for="">��ע</label>
		<div class="controls">
			<textarea   style="height:40px;width:280px" name="memo"><?=$list['memo']?></textarea>
		</div>
	</div>
</fieldset>
<div class="modal-footer">
	<button class="btn" type="button" data-dismiss="modal" aria-hidden="true">�ر�</button>
	<button class="btn btn-info" type="submit">�ύ</button>
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
			    
<?php defined("ROOT") or exit("Error."); ?>
<?php 
if ($user["purview"] != '') {
	$purview = @unserialize($user["purview"]);
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
.man_check {
	float: left;
	width: 200px;
	margin-top: 5px;
}
.radio, .checkbox
{
	padding-left:0
}
</style>
<script language="javascript">
var mode = "<?php echo $op; ?>";

function byid(id) {
	return document.getElementById(id);
}

function show_pass() {
	var pass = document.mainform.pass.value;
	if (pass != "") {
		msg_box("������������ǣ� " + pass + "  ");
	} else {
		msg_box("����û���������룡");
	}
}

function check_data() {
	oForm = document.mainform;
	if (oForm.name.value.length < 2) {
		msg_box("���������û������ҳ�������2λ");
		oForm.name.focus();
		return false;
	}
	if (oForm.realname.value == "") {
		msg_box("��������ʵ������");
		oForm.realname.focus();
		return false;
	}
	if (mode == "edit" && oForm.pass.value != ""&&oForm.pass.value.length<6) {
		msg_box("���볤������6λ��");
		oForm.pass.focus();
		return false;
	}
	
	if (mode == "add" && oForm.pass.value.length < 6) {
		msg_box("�����������룬�ҳ������6λ");
		oForm.pass.focus();
		return false;
	}
	if (byid("powermode").value == "") {
		msg_box("��ѡ����Ȩ��ʽ��");
		oForm.powermode.focus();
		return false;
	}
	if (byid("powermode").value == "2" && byid("character_id").value == "0") {
		msg_box("��ѡ��Ȩ�ޣ�");
		oForm.character_id.focus();
		return false;
	}
	if (!confirm("ÿһ�����˰ɣ��������Ŷ����������ٿ�һ�£�������ȡ����")) {
		return false;
	}
	return true;
}

function set_power_do(pid, str) {
	byid(pid).value = str;
}

function set_power(pid) {
	parent.load_box(1,'src','m/sys/admin.php?op=set_power&pid='+pid+'&power='+byid(pid).value);
}

function show_hide_detail(o) {
	if (o.value == "-1") {
		byid("detail_button").style.display = "inline";
		byid("powermode").value = "1"; //�Զ���
	} else {
		byid("detail_button").style.display = "none";
		byid("powermode").value = "2"; //��ɫ
	}
}

function check_repeat(o, type) {
	if (mode == "add") {
		if (o.value == '') {
			byid(type+"_tips").innerHTML = '';
		} else {
			var s = o.value;
			var xm = new ajax();
			xm.connect("/http/check_admin_repeat.php", "GET", "&s="+(s)+"&type="+(type), check_repeat_do);
		}
	}
}

function check_repeat_do(o) {
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		if (out["tips"] != '') {
			byid(out["type"]+"_tips").innerHTML = '<font color=red>'+out["tips"]+"</font> ";
			//alert("��ע�⣬"+out["tips"]);
		} else {
			byid(out["type"]+"_tips").innerHTML = "�� ";
		}
	}
}

function update_check_color(o) {
	o.parentNode.parentNode.getElementsByTagName("label")[0].style.color = o.checked ? "#3a87ad" : "";
}
</script>
</head>

<body>
    <header class="jumbotron subhead"  style="margin-bottom: 20px;">
		<ul class="breadcrumb">
             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
             <li class="active text-info"><b><?php echo isset($user["realname"])?$user["realname"].'-':'';?>�����޸�</b></li>
        </ul>
	</header>
	
    <form name="mainform" class="form-horizontal" method="POST" onsubmit="return check_data()">
		<fieldset>
			<legend>��������</legend>
			<div class="control-group">
				<label class="control-label">��¼��</label>
				<div class="controls">
					<input type="text" class="span2" name="name" value="<?php echo $user["name"]; ?>" onchange="check_repeat(this,'name')" <?php if ($id > 0) echo "disabled"; ?>> <span class="help-block">�������ܸ���</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">��ʵ����</label>
				<div class="controls">
					<input type="text" class="span2" name="realname" value="<?php echo $user["realname"]; ?>" onchange="check_repeat(this,'realname')" <?php if ($id > 0) echo "disabled"; ?>> <span class="help-block">��ʵ������������ʾ</span>
				</div>
			</div>
			<?php if ($debug_mode): ?>
			<div class="control-group">
				<label class="control-label">����</label>
				<div class="controls">
					<input type="text" class="span2" name="pass" value="" onchange="check_repeat(this,'name')">
				</div>
			</div>
			<?php endif ?>
		</fieldset>
		<fieldset>
			<legend>ҽԺ��Ȩ</legend>
			<div class="control-group">
				<label class="control-label">ҽԺ</label>
				<div class="controls">
					<?php
					$hs_ids = implode ( ",", $hospital_ids );
					$hs_id_name = $db->query ( "select id,name from hospital where id in ($hs_ids) order by sort desc,id asc", "id", "name" );
					foreach ( $hs_id_name as $id => $name )
					{
						$checked = in_array ( $id, explode ( ",", $user ["hospitals"] ) ) ? "checked" : "";
						?>
					    <div class="man_check">
					        <label class="checkbox" for="hc_<?php echo $id; ?>" <?php if ($checked) echo ' style="color:#b94a48"'; ?>>
						        <input class="check" name="hospital_ids[]" type="checkbox" value="<?php echo $id; ?>" id="hc_<?php echo $id; ?>" <?php echo $checked; ?> onclick="update_check_color(this)"> <?php echo $name; ?>
			                 </label>
					</div>  
					<?php } ?>
				</div>
			</div>
			<?php
            $select_ch = $user ["powermode"] == 1 ? 0 : $user ["character_id"];
			?>
			<div class="control-group">
				<label class="control-label">ѡ��Ȩ��</label>
				<div class="controls">
				    <input type="hidden" name="powermode" id="powermode" value="<?php echo $user["powermode"]; ?>">
				    <select name="character_id" onchange="show_hide_detail(this)" class="span2">
						<option value="0" style="color: gray">--��ѡ��--</option>
						<!-- <option value="-1" style="color:red"<?php if ($user["powermode"]==1) echo " selected"; ?>>--�Զ���--</option> -->
				       <?php echo list_option($ch_data, "id", "name", $select_ch); ?>
			        </select>
			        <button id="detail_button" onclick="set_power('power_detail'); return false;" class="btn" <?php if ($user["powermode"]!=1) echo ' style="display:none"'; ?>>�Զ���...</button> 
			        <span class="help-block">�����趨Ȩ��</span> 
			        <input type="hidden" name="power_detail" id="power_detail" value="<?php echo $user["menu"]; ?>">
				</div>
			</div>
			
			<div class="control-group">
			    <label class="control-label">ѡ��Ȩ��</label>
			    <div class="controls">
			        <select name="part_id" class="span2">
					    <?php //echo list_option($part->get_sub_part_list(intval($uinfo["part_id"]), 1), "_key_", "_value_", $user["part_id"]); ?>
					    <?php echo list_option(get_part_list('array'), "id", "name", $user["part_id"]); ?>
					</select>
					<?php if ($debug_mode || $username == "admin" || $uinfo["part_admin"]): ?>
			            <div style='display:inline-table;padding-top:1px'><label class="checkbox" for="part_admin"><input type="checkbox" class="check" name="part_admin" value="1" id="part_admin" <?php if ($user["part_admin"]) echo "checked"; ?>>���Ź���Ա</label></div>
			        <?php endif ?>
			        <span class='help-block'>���ű���ѡ�����ѡ���Ź���Ա�������Ա��Ȩ�����䱾���ţ����������������ţ�����</span>
			    </div>
			</div>
			
			<div class="control-group">
			    <label class="control-label">���ݹ���</label>
			    <div class="controls">
			        <?php
					$part_level = get_part_list ( 'array' );
					$cur_part_m = explode ( ",", $user ["part_manage"] );
					foreach ( $part_level as $v )
					{
						$p_id = $v ["id"];
						$p_level = $v ["level"];
						
						echo "<label class='checkbox' for='chp_" . $p_id . "'><input type='checkbox' name='part_manage[]' value='" . $p_id . "' id='chp_" . $p_id . "'" . (in_array ( $p_id, $cur_part_m ) ? " checked" : "") . ">" . $v ["name"] . " </label>";
					}
					
					?>
			    </div>
			</div>
		</fieldset>
		
        <?php if ($debug_mode || $uinfo["part_admin"]): ?>
        <fieldset>
            <legend>ȫ������</legend>
            <div class="control-group">
			    <label class="control-label">��ʾ����</label>
			    <div class="controls">
			        <input type="hidden" name="id" value="<?php echo $user["id"]; ?>"> <input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
			        <input type="hidden" name="op" value="<?php echo $op; ?>"> <input type="hidden" name="edit_mode" value="all">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox" name="show_tel" value="1" <?php if ($purview["show_tel"]) echo "checked"; ?> >��ʾ�������˵ĵ绰����(ȡ����������ǺŴ���)
				    </label>
			    </div>
			</div>
			<div class="control-group">
			    <label class="control-label">�ط÷�Χ</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox"  name="show_huifang_all" value="1" <?php if ($purview["show_huifang_all"]) echo "checked"; ?> >��ʾȫ����Ҫ�طõĲ���
				    </label>
			    </div>
			</div>
		</fieldset>	
		<fieldset>
            <legend>������ʾ</legend>	
			<div class="control-group">
			    <label class="control-label">�ɽ�����</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox"  name="show_chengjiao" value="1" <?php if ($purview["show_chengjiao"]) echo "checked"; ?> >��ʾ��ҳ�ɽ���Ϣ
				    </label>
			    </div>
			</div>
			<div class="control-group">
			    <label class="control-label">�ط���Ϣ</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox"  name="show_huifang" value="1" <?php if ($purview["show_huifang"]) echo "checked"; ?> >��ʾ��ҳ�ط���Ϣ
				    </label>
			    </div>
			</div>
		</fieldset>
		
		<fieldset>
		    <legend>ԤԼ�б�ҳ</legend>
		    <div class="control-group">
			    <label class="control-label">��ʾȫ������</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox" name="show_patient" value="1" <?php if ($purview["show_patient"]) echo "checked"; ?> >��ʾȫ��ԤԼ��Ϣ
				    </label>
			    </div>
			</div>
		</fieldset>
		
		<fieldset>	
		    <legend>�����б�ҳ</legend>
		    <div class="control-group">
			    <label class="control-label">��ʾȫ������</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox"  name="show_patient_fee" value="1" <?php if ($purview["show_patient_fee"]) echo "checked"; ?> >��ʾȫ��������Ϣ
				    </label>
			    </div>
			</div>	
        </fieldset>
      
        <fieldset>
            <legend>���ݵ�������(�ݲ�����)</legend>
            <div class="control-group">
			    <label class="control-label">ԤԼ�б���</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox" value="" >
				    </label>
			    </div>
			</div>
			<div class="control-group">
			    <label class="control-label">�����б���</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox" value="" >
				    </label>
			    </div>
			</div>
			<div class="control-group">
			    <label class="control-label">ͳ�����ݵ���</label>
			    <div class="controls">
			        <label class="checkbox" style="padding-top: 5px;">
			            <input type="checkbox" value="" >
				    </label>
			    </div>
			</div>
        </fieldset>
        <?php endif?>
        <div class="form-actions">
            <input type="hidden" name="id" value="<?php echo $user["id"]; ?>">
            <input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
            <input type="hidden" name="op" value="<?php echo $op; ?>">
            <input type="hidden" name="edit_mode" value="all">
            <input type="button" class="btn" value="����" onclick="history.back()">
            <input type="submit" class="btn btn-primary" value="�ύ����">
          </div>
	</form>
</body>
</html>
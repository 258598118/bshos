<?php defined("ROOT") or exit("Error."); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312"><?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
.breadcrumb ul{margin:0}
.radio, .checkbox{padding-left:0}
</style>
<script language="javascript">
function set_MID_show(oSel) {	var theValue = oSel.value;	var theForm = document.hideform;	var HtmlData = theValue == "0" ? theForm.menuid_select.value : theForm.menuid_input.value;	var menuidTips = theValue == "0" ? "����˵�" : "�˵�����";	document.getElementById("menuid_area").innerHTML = HtmlData;	document.getElementById("menuid_tips").innerHTML = menuidTips;	document.getElementById("menu_detail").style.display = (theValue == "0" ? "block" : "none");}

function init() {	set_MID_show(document.mainform.type);}
function check_data(f) {	if (f.menuid.value == "") {		msg_box("��ָ���˵����ţ�"); f.menuid.focus(); return false;	}
	if (f.title.value == "") {
		msg_box("������˵����ƣ�"); f.title.focus(); return false;	}	return true;}
</script>
</head>

<body onload="init()">
<!-- ͷ�� begin --><header class="jumbotron subhead"  style="margin-bottom: 10px;">
    <div class="breadcrumb">
        <ul>
             <li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
             <li class="active"><span style="color:#0088cc;font-weight:bolder"><?php echo $title; ?></li>
		</ul>
    </div>
</header>
<!-- ͷ�� end -->
<div class="space"></div>
<div class="description">
	<div class="d_title">��ʾ��</div>
	<li class="d_item">�˵����ͷ���һ�������㣩�Ͷ������Ӳ˵������֡�һ���˵���ָ�����ӣ����ɷ������Ȩ�ޣ������˵����趨ϸ��Ȩ��</li>
	<li class="d_item">�����˵�����ѡ��������ĸ�һ���˵�֮�£�ͨ��ѡ���䡰����˵���ʵ��</li>
	<li class="d_item">������˵��Ĳ˵����Ų�����ͬ������˵����Ӳ˵��ı����Ӧ��ͬ�Ա�ʾ��Ϊͬһ����</li>
</div>

<div class="space"></div>
<form method='POST' name="mainform" class="form-horizontal" onsubmit="return check_data(this)">    <fieldset>
        <legend>��������</legend>
        <div class="control-group">
            <label>�˵�����</label>
            <div class="controls">
                <select name="type" class="span2" onchange="set_MID_show(this)">
				    <option value="1"<?php echo (!$id || $line["type"] ? " selected" : ""); ?>>����˵�<?php echo ($line["type"] ? " *" : ""); ?></option>
				    <option value="0"<?php echo (strlen($line["type"]) && $line["type"]==0 ? " selected" : ""); ?>>�Ӳ˵�<?php echo (strlen($line["type"]) && $line["type"]==0 ? " *" : ""); ?></option>
				</select>
				<p class="help-inline">����˵���ʾ�ڡ����˵���λ�ã��Ӳ˵���ʾ�����¼������</p>
            </div>
        </div>
        
        <div class="control-group">
            <label id="menuid_tips"></label>
            <div class="controls">
                <span id="menuid_area"></span>
            </div>
        </div>
        
        <div class="control-group">
            <label>�˵�����</label>
            <div class="controls">
                <input name="title" size="20" maxlength="40" class="span2" value="<?php echo $line["title"]; ?>">
                <p class="help-inline">�˵���ʾ���ƣ�����</p>
            </div>
        </div>
        
        <div class="control-group">
            <label>������ҳ��</label>
            <div class="controls">
                <input name="link" size="40" maxlength="100" class="span2" value="<?php echo $line["link"]; ?>">
                <p class="help-inline">���˵������ֱ������ (����˵��ɲ���)</p>
            </div>
        </div>
        
        <div class="control-group">
            <label>����˵��</label>
            <div class="controls">
                <input name="tips" size="40" maxlength="100" class="span2" value="<?php echo $line["tips"]; ?>">
                <p class="help-inline">������д��ϸ˵�����Ǳ�����</p>
            </div>
        </div>
        
        <div class="control-group">
            <label>����ֵ</label>
            <div class="controls">
                <input name="sort" size="8" maxlength="10" class="span2" value="<?php echo $line["sort"]; ?>">
                <p class="help-inline">����ֵ�Ĵ�С�����˵������д�������ģʽ�¿�������ϵͳ�Զ�����</p>
            </div>
        </div>
    </fieldset>
<div id="menu_detail" style="display:none">
<div class="space"></div>
    <fieldset>
        <legend>�Ӳ˵���ϸ����</legend>
        <div class="control-group">
            <label>ÿҳ��ʾ����</label>
            <div class="controls">
                <input name="pagesize" size="8" class="span2" value="<?php echo $line["pagesize"]; ?>">
                <p class="help-inline">�趨�б�ҳ��ÿҳ��ʾ��¼������</p>
            </div>
        </div>
        <div class="control-group">
            <label>Ĭ�Ͽ�ݷ�ʽ</label>
            <div class="controls">
                <select name="shortcut" class="span2">
					<option value="1"<?php echo ($line["shortcut"] == 1 ? " selected" : ""); ?>>��ΪĬ�Ͽ�ݷ�ʽ</option>
					<option value="0"<?php echo ($line["shortcut"] == 0 ? " selected" : ""); ?>>����ΪĬ�Ͽ�ݷ�ʽ</option>
				</select>
                <p class="help-inline">������Աδ�趨��ר����ݷ�ʽʱ������ΪĬ�Ͽ�ݷ�ʽ������Щҳ�����ʾ</p>
            </div>
        </div>
        
        <?php

			$cur_op = explode(",", $line["modules"]);
			foreach ($oprate as $op_code => $op_name) {
				$ischeck = in_array($op_code, $cur_op) ? 'checked' : '';
		?>
		
			<div class="control-group">
				<label><?php echo $op_name; ?>����</label>
				<div class="controls">
				    <label class="checkbox">
				        <input type="checkbox" name="oprate[]" value="<?php echo $op_code; ?>" class="check" <?php echo $ischeck; ?>>
				        <label for="op_<?php echo $op_code; ?>">��<?php echo $op_name; ?>����    op=<?php echo $op_code; ?></label>
				    </label>
				</div>
			</div>
		<?php
		
			}
			$other_op = array();
			foreach ($cur_op as $op_code) {
		
				if (!array_key_exists($op_code, $oprate)) {
		
					$other_op[] = $op_code;
				}
			}
			$other_op = implode(",", $other_op);
		
		?>
		
        <div class="control-group">
            <label>��������</label>
            <div class="controls">
                <input name="oprate[]" value="<?php echo $other_op; ?>" size="30" class="span2">
                <p class="help-inline">��������ƴ���, op=xxx, �����Сд���Ÿ���</p>
            </div>
        </div>
        <div class="form-actions">
		    <input type="submit" value="�ύ����" class="btn btn-primary">
		 </div>
	    <input type="hidden" name="id" value="<?php echo $id; ?>">
	    <input type="hidden" name="op" value="<?php echo $op; ?>">
    </fieldset>
	</form>
<form name="hideform">
<input type="hidden" name="menuid_select" value="<?php echo $SelectData; ?>">
<input type="hidden" name="menuid_input" value="<?php echo $InputData; ?>">
</form>

<div class="space"></div>

</body>
</html>
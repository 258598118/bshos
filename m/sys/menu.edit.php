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
function set_MID_show(oSel) {	var theValue = oSel.value;	var theForm = document.hideform;	var HtmlData = theValue == "0" ? theForm.menuid_select.value : theForm.menuid_input.value;	var menuidTips = theValue == "0" ? "顶层菜单" : "菜单组编号";	document.getElementById("menuid_area").innerHTML = HtmlData;	document.getElementById("menuid_tips").innerHTML = menuidTips;	document.getElementById("menu_detail").style.display = (theValue == "0" ? "block" : "none");}

function init() {	set_MID_show(document.mainform.type);}
function check_data(f) {	if (f.menuid.value == "") {		msg_box("请指定菜单组编号！"); f.menuid.focus(); return false;	}
	if (f.title.value == "") {
		msg_box("请输入菜单名称！"); f.title.focus(); return false;	}	return true;}
</script>
</head>

<body onload="init()">
<!-- 头部 begin --><header class="jumbotron subhead"  style="margin-bottom: 10px;">
    <div class="breadcrumb">
        <ul>
             <li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
             <li class="active"><span style="color:#0088cc;font-weight:bolder"><?php echo $title; ?></li>
		</ul>
    </div>
</header>
<!-- 头部 end -->
<div class="space"></div>
<div class="description">
	<div class="d_title">提示：</div>
	<li class="d_item">菜单类型分类一级（顶层）和二级（子菜单）两种。一级菜单能指定链接，不可分配具体权限；二级菜单可设定细分权限</li>
	<li class="d_item">二级菜单必须选择归属于哪个一级菜单之下，通过选择其“顶层菜单”实现</li>
	<li class="d_item">各顶层菜单的菜单组编号不能相同，顶层菜单和子菜单的编号则应相同以表示其为同一分组</li>
</div>

<div class="space"></div>
<form method='POST' name="mainform" class="form-horizontal" onsubmit="return check_data(this)">    <fieldset>
        <legend>基本设置</legend>
        <div class="control-group">
            <label>菜单类型</label>
            <div class="controls">
                <select name="type" class="span2" onchange="set_MID_show(this)">
				    <option value="1"<?php echo (!$id || $line["type"] ? " selected" : ""); ?>>顶层菜单<?php echo ($line["type"] ? " *" : ""); ?></option>
				    <option value="0"<?php echo (strlen($line["type"]) && $line["type"]==0 ? " selected" : ""); ?>>子菜单<?php echo (strlen($line["type"]) && $line["type"]==0 ? " *" : ""); ?></option>
				</select>
				<p class="help-inline">顶层菜单显示于“主菜单”位置，子菜单显示于其下及左侧栏</p>
            </div>
        </div>
        
        <div class="control-group">
            <label id="menuid_tips"></label>
            <div class="controls">
                <span id="menuid_area"></span>
            </div>
        </div>
        
        <div class="control-group">
            <label>菜单名称</label>
            <div class="controls">
                <input name="title" size="20" maxlength="40" class="span2" value="<?php echo $line["title"]; ?>">
                <p class="help-inline">菜单显示名称，必填</p>
            </div>
        </div>
        
        <div class="control-group">
            <label>主管理页面</label>
            <div class="controls">
                <input name="link" size="40" maxlength="100" class="span2" value="<?php echo $line["link"]; ?>">
                <p class="help-inline">即菜单标题的直接链接 (顶层菜单可不填)</p>
            </div>
        </div>
        
        <div class="control-group">
            <label>功能说明</label>
            <div class="controls">
                <input name="tips" size="40" maxlength="100" class="span2" value="<?php echo $line["tips"]; ?>">
                <p class="help-inline">建议填写详细说明，非必填项</p>
            </div>
        </div>
        
        <div class="control-group">
            <label>排序值</label>
            <div class="controls">
                <input name="sort" size="8" maxlength="10" class="span2" value="<?php echo $line["sort"]; ?>">
                <p class="help-inline">排序值的大小决定菜单的排列次序，新增模式下可留空由系统自动计算</p>
            </div>
        </div>
    </fieldset>
<div id="menu_detail" style="display:none">
<div class="space"></div>
    <fieldset>
        <legend>子菜单详细定义</legend>
        <div class="control-group">
            <label>每页显示条数</label>
            <div class="controls">
                <input name="pagesize" size="8" class="span2" value="<?php echo $line["pagesize"]; ?>">
                <p class="help-inline">设定列表页面每页显示记录的数量</p>
            </div>
        </div>
        <div class="control-group">
            <label>默认快捷方式</label>
            <div class="controls">
                <select name="shortcut" class="span2">
					<option value="1"<?php echo ($line["shortcut"] == 1 ? " selected" : ""); ?>>作为默认快捷方式</option>
					<option value="0"<?php echo ($line["shortcut"] == 0 ? " selected" : ""); ?>>不作为默认快捷方式</option>
				</select>
                <p class="help-inline">当管理员未设定其专属快捷方式时，“作为默认快捷方式”的这些页面会显示</p>
            </div>
        </div>
        
        <?php

			$cur_op = explode(",", $line["modules"]);
			foreach ($oprate as $op_code => $op_name) {
				$ischeck = in_array($op_code, $cur_op) ? 'checked' : '';
		?>
		
			<div class="control-group">
				<label><?php echo $op_name; ?>功能</label>
				<div class="controls">
				    <label class="checkbox">
				        <input type="checkbox" name="oprate[]" value="<?php echo $op_code; ?>" class="check" <?php echo $ischeck; ?>>
				        <label for="op_<?php echo $op_code; ?>">有<?php echo $op_name; ?>功能    op=<?php echo $op_code; ?></label>
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
            <label>其他功能</label>
            <div class="controls">
                <input name="oprate[]" value="<?php echo $other_op; ?>" size="30" class="span2">
                <p class="help-inline">请输入控制代码, op=xxx, 多个用小写逗号隔开</p>
            </div>
        </div>
        <div class="form-actions">
		    <input type="submit" value="提交资料" class="btn btn-primary">
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
<?php 
/**
 * ip 访问控制模版
 * 
 * @author fangyang
 * @since  2013-05-31
 */
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<title><?php echo $pinfo["title"]; ?></title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
.breadcrumb ul {
	margin: 0
}
</style>
</head>
<body id="bodyobj">
	<!-- 头部 begin -->
	<header class="jumbotron subhead" style="margin-bottom: 10px;">
		<div class="breadcrumb">
			<ul>
				<li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span
					class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder">ip访问控制列表</span></li>
			</ul>
		</div>
	</header>

	<div class="row-fluid show-grid">
		<button class="btn" role="button" id="addBtn">新增</button>
	</div>

	<section>
	    <?php echo $t->show(); ?>
	</section>

	<!-- 分页链接 begin -->
	<div class="footer_op">
		<div class="footer_op_left">
			<!-- <button onclick="select_all()" class="btn btn-small pull-right toggle-all">全选</button> -->
		</div>
		<div class="footer_op_right"><?php echo $pagelink; ?></div>
	</div>
	<!-- 分页链接 end -->

	<!-- 新增modal start -->
	<div id="addModal" class="modal hide fade in">
		<form class="form-horizontal" action="ip_filter.php" method="post" >
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>新增IP</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="control-group">
						<label class="control-label">ip</label>
						<div class="controls">
						     <input type="text" name="ip" class="span2" placeholder="外网ip地址">
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">ip地址</label>
						<div class="controls">
							<input type="text" name="address" class="span2">
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">备注</label>
						<div class="controls">
							<textarea class="input-xlarge" name="memo" id="textarea" rows="3"></textarea>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
			    <input type="hidden" name="model" value="add"/>
				<input type="hidden" name="go" value="back"/>
				<button class="btn" type="button" data-dismiss="modal">关闭</button>
				<button class="btn btn-primary" type="submit">提交</button>
			</div>
		</form>
	</div>
	<!-- 新增modal end -->
	<script>
$('#addBtn').on('click',function(evt){
	 $('#addModal').modal({
		    backdrop:false,
		    keyboard:true,
		    show:true
   });
})
</script>
</body>
</html>
<?php
/**
 * patient.view模版//通过ajax输出
 * 
 * @author fangyang
 * @since 201306
 */

// 检查是否包含调用
if (!$username) {
	exit("This page can not directly opened from browser...");
}
?>
<style>
.edit {
	border: 2px solid #A3D1D1;
	margin: 0px 0px;
}

.edit .head {
	border: 0px solid #E7E7E7;
	background-color: #F5F5F5;
	padding: 5px 3px 3px 6px;
	font-weight: bold;
	color: #2C5656
}

.edit .left {
	width: 15%;
	border: 0px solid #E9D0FF;
	text-align: right;
	padding: 4px 3px 2px 3px;
}

.edit .right {
	width: 85%;
	border: 0px;
	padding: 4px 6px 4px 6px;
}

.edit .foot {
	border: 0px solid #EFDDFF;
	text-align: center;
	height: 40px;
	background-color: #FBF7FF
}

.edit .sp {
	border: 0px dotted #E9D0FF;
	height: 15px;
	background-color: #F8F0FF
}
.overflow-y {
    overflow-y: auto;
    height: 480px;
}
.text-error {
    color: #b94a48;
}
</style>
<table width="100%" class="edit" style="border:0;width:1000px;height:530px;margin:10px;">
	<tr>
		<th class="head" width="25%" style="border:2px solid #BADCDC;">基本资料</th>
		<th class="head" width="25%" style="border:2px solid #BADCDC;">聊天记录</th>
		<th class="head" width="25%" style="border:2px solid #BADCDC;">回访记录</th>
		<th class="head" width="25%" style="border:2px solid #BADCDC;">备注资料</th>
	</tr>

	<tr>
		<td valign="top" style="border:2px solid #BADCDC; padding:0;">
			<table width="100%" class="edit" style="border:0;">
			<?php foreach ($viewdata[1] as $k => $v) { ?>
				<tr>
					<td class="left" style="width:35%"><?php echo $v[0]; ?>：</td>
					<td class="right" style="width:55%"><?php echo $v[1]; ?></td>
				</tr>
			<?php } ?>
			</table>
		</td>

		<td valign="top" style="border:2px solid #BADCDC; padding:0;">
			<table width="100%" class="edit" style="border:0;">
				<tr>
					<td class="right"><div class="overflow-y"><?php echo $viewdata[2][0][1] ? $viewdata[2][0][1] : "<center style='color:gray'>(暂无资料)</center>" ?></div></td>
				</tr>
			</table>
		</td>

		<td valign="top" style="border:2px solid #BADCDC; padding:0;">
			<table width="100%" class="edit" style="border:0;">
				<tr>
					<td class="right"><div class="overflow-y"><?php echo $viewdata[3][0][1] ? $viewdata[3][0][1] : "<center style='color:gray'>(暂无资料)</center>" ?></div></td>
				</tr>
			</table>
		</td>

		<td valign="top" style="border:2px solid #BADCDC; padding:0;">
			<table width="100%" class="edit" style="border:0;">
				<tr>
					<td class="right"><div class="overflow-y"><?php echo $viewdata[4][0][1] ? $viewdata[4][0][1] : "<center style='color:gray'>(暂无资料)</center>" ?></div></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
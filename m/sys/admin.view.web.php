<?php defined("ROOT") or exit("Error."); ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312"><?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
.controls>span{line-height:25px;}
</style>
</head>
<body>
    <section>
        <form class="form-horizontal">
            <div class="control-group">
                <label class="control-label" >登录名</label>
                <div class="controls">
                    <span><b><?php echo $user["name"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >真实姓名</label>
                <div class="controls">
                    <span><b><?php echo $user["realname"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >所在医院</label>
                <div class="controls">
                    <span><b><?php echo $user["hs_str"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >权限</label>
                <div class="controls">
                    <span><b>
                    <?php
						if ($user ["powermode"] == 2)
						{
							
							$ch_data = $db->query ( "select * from sys_character where id='" . $user ["character_id"] . "' limit 1", 1 );
							echo $ch_data ["name"];
						}
						
						?>
				   </b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >电话</label>
                <div class="controls">
                    <span><b><?php echo $user["phone"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >手机</label>
                <div class="controls">
                    <span><b><?php echo $user["mobile"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >QQ</label>
                <div class="controls">
                    <span><b><?php echo $user["qq"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >E-Mail</label>
                <div class="controls">
                    <span><b><?php echo $user["email"]; ?></b></span>
                </div>        
            </div>
            
            <div class="control-group">
                <label class="control-label" >个人简介</label>
                <div class="controls">
                    <span><b><?php echo $user["intro"]; ?></b></span>
                </div>        
            </div>
        </form>
    </section>
</body>
</html>
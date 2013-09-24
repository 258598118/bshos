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
                <label class="control-label" >��¼��</label>
                <div class="controls">
                    <span><b><?php echo $user["name"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >��ʵ����</label>
                <div class="controls">
                    <span><b><?php echo $user["realname"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >����ҽԺ</label>
                <div class="controls">
                    <span><b><?php echo $user["hs_str"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >Ȩ��</label>
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
                <label class="control-label" >�绰</label>
                <div class="controls">
                    <span><b><?php echo $user["phone"]; ?></b></span>
                </div>        
            </div>
            <div class="control-group">
                <label class="control-label" >�ֻ�</label>
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
                <label class="control-label" >���˼��</label>
                <div class="controls">
                    <span><b><?php echo $user["intro"]; ?></b></span>
                </div>        
            </div>
        </form>
    </section>
</body>
</html>
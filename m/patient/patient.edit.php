<?php
/*
// - ����˵�� : �������޸Ĳ�������
// - �������� : fangyang (zhuwenya@126.com)
// - ����ʱ�� : 2013-05-01 08:57
*/
$mode = $op;
if ($_POST)
{
    $po = &$_POST; //���� $_POST
    
    if ($mode == "edit")
    {
        $oldline = $db->query("select * from $table where id=$id limit 1", 1);
    } else
    {
        // ���һ�����ڵĲ����������ظ���:
        $name = trim($po["name"]);
        $tel = trim($po["tel"]);
        if (strlen($tel) >= 7)
        {
            $thetime = strtotime("-1 month");
            $list = $db->query("select * from $table where tel='$tel' and addtime>$thetime limit 1", 1);
            if ($list && count($list) > 0)
            {
                msg_box("�绰�����ظ����ύʧ��", "back", 1, 5);
            }
        }
    }
    
    /*
	// ������������ֶ�:
	if (!$oldline) {
		$test_line = $db->query("select * from $table limit 1", 1);
	} else {
		$test_line = $oldline;
	}

	// �Զ�����ֶ�:  ���ڿ���ȥ��
	if (!isset($test_line["engine"])) {
		$db->query("alter table `{$table}` add `engine` varchar(32) not null after `media_from`;");
	}
	if (!isset($test_line["engine_key"])) {
		$db->query("alter table `{$table}` add `engine_key` varchar(32) not null after `engine`;");
	}
	if (!isset($test_line["from_site"])) {
		$db->query("alter table `{$table}` add `from_site` varchar(40) not null after `engine_key`;");
	}
	*/
    
    // �ͷ���Ӽ�������  2010-10-27
    if ($po["disease_id"] == -1)
    {
        $d_name = $po["disease_add"];
        $d_id = 0;
        if ($d_name != '')
        {
            $d_id = $db->query("insert into disease set hospital_id='$hid', name='$d_name', addtime='$time', author='$username'");
        }
        $po["disease_id"] = $d_id ? $d_id : 0;
    }
    
    $r = array ();
    $sync = array(); //��Ҫͬ��������  �������Ա����䡢QQ��ý����Դ��������Դ��ר�Һ�
   
    if (isset($po["name"])){
        $r["name"] = trim($po["name"]);
        $sync["name"] = trim($po["name"]);    //sync
    }    
    if (isset($po["sex"]))
    {	
        $r["sex"] = $po["sex"];
        $sync["sex"] = $po["sex"];	//sync
    }   
    if (isset($po["qq"]))
    {	
        $r["qq"] = $po["qq"];
        $sync["qq"] = $po["qq"];    //sync
    }    
    if (isset($po["age"]))
    {	
        $r["age"] = $po["age"];
    	$sync["age"] = $po["age"];	//sync
    }	
    if (isset($po["content"]))
        $r["content"] = $po["content"];
    if (isset($po["disease_id"]))
        $r["disease_id"] = $po["disease_id"];
    if (isset($po["depart"]))
        $r["depart"] = $po["depart"];
    if (isset($po["media_from"]))
    {
        $r["media_from"] = $po["media_from"];
    	$sync["media_from"] = $po["media_from"];	//sync
    }	
    if (isset($po["engine"]))
        $r["engine"] = $po["engine"];
    	$sync["engine"] = $po["engine"];
    if (isset($po["engine_key"]))
    {
        $r["engine_key"] = $po["engine_key"];
    	$sync["engine_key"] = $po["engine_key"];	//sync
    }	
    if (isset($po["from_site"]))
    {	
        $r["from_site"] = $po["from_site"];
        $sync["from_site"] = $po["from_site"];	//sync
    }    
    if (isset($po["from_account"]))
    {	
        $r["from_account"] = $po["from_account"]; // 2010-11-04
        $sync["from_account"] = $po["from_account"];	//sync
    }   
    if (isset($po["zhuanjia_num"]))
        $r["zhuanjia_num"] = $po["zhuanjia_num"];
    if (isset($po["is_local"]))
    {	
        $r["is_local"] = $po["is_local"];
    	$sync["is_local"] = $po["is_local"];	//sync
    }	
    if (isset($po["area"]))
    {	
        $r["area"] = $po["area"];
        $sync["area"] = $po["area"];    //sync
    }    
    if (isset($po["chengjiao"]))
        $r["chengjiao"] = $po["chengjiao"]; //2013-03-08
    if (isset($po["cj_sum"]))
        $r["cj_sum"] = $po["cj_sum"];
    
    if ($po["chengjiao"] != "1")
        $r["cj_sum"] = "0";
    
    if (isset($po["doctor"]))
    {
    	$r["doctor"] = $po["doctor"];
    }

 
    
    
    //�޸�ģʽ�������û����
    if(isset($po['pid'])&&empty($po['pid']))
    {
    	//����û�б�ŵ��û����¸�ֵ���
    	$newpid = $user_hospital_id.$po['id'].date('md');
    	$db->query('UPDATE '.$table.' set pid = '.$newpid.' where id ='.$po['id']);
    }
    
    // �޸�ʱ��:
    if (isset($po["order_date"]))
    {
        $order_date_post = @strtotime($po["order_date"]);
        if ($mode == "add")
        {
            
            // ����޸ģ���ʱ�䲻�ܱ��޸�Ϊ��ǰʱ���һ����֮ǰ(2011-01-15)
            if ($order_date_post < strtotime("-1 month"))
            {
                exit_html("Ԥ��ʱ�䲻����һ����֮ǰ�������ȼ�����ĵ���ʱ���Ƿ����󣡣�  �뷵��������д��");
            }
            
            $r["huifang_date"] = $r["order_date"] = $order_date_post; //������Ĭ�������ط�ʱ���Ԥ��ʱ��һ��
        } else
        {
            //�ж�ʱ���Ƿ����޸�
            if ($order_date_post != $oldline["order_date"])
            {
                
                // ����޸ģ���ʱ�䲻�ܱ��޸�Ϊ��ǰʱ���һ����֮ǰ(2011-01-15)
            	if(!$uinfo['part_admin']&&!$debug_mode){
	                if ($order_date_post < strtotime("-1 month"))
	                {
	                    exit_html("Ԥ��ʱ�䲻�ܱ��޸ĵ�һ����֮ǰ�������ȼ�����ĵ���ʱ���Ƿ����󣡣�  �뷵��������д��");
	                }
            	}
                $r["order_date"] = $order_date_post;
                $r["order_date_changes"] = intval($oldline["order_date_changes"]) + 1;
                $r["order_date_log"] = $oldline["order_date_log"] . (date("Y-m-d H:i:s") . " " . $realname . " �޸� (" . date("Y-m-d H:i", $oldline["order_date"]) . " => " . date("Y-m-d H:i", $order_date_post) . ")<br>");
                
                // ����޸�Ԥ��ʱ�䣬�Զ��޸�״̬Ϊ�ȴ�
                if ($oldline["status"] == 2)
                {
                    $r["status"] = 0;
                }
            }
        }
    }
    
    //�ط�ʱ��
    if (isset($po["huifang_date"]))
    {
        if ($mode == "edit")
        {
            if ($po["huifang_date"] != "")
            {
                $r["huifang_date"] = strtotime($po["huifang_date"]);
                $r["huifang_date_log"] = $oldline["huifang_date_log"] . (date("Y-m-d H:i:s") . " " . $realname . " �޸� (" . date("Y-m-d H:i:s", $oldline["huifang_date"]) . " => " . date("Y-m-d H:i:s", $r["huifang_date"]) . ")<br>");
            } else
            {
                $r["huifang_date"] = $order_date_post;
            }
        }
    }
    
    //����״̬  2013-03-08
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
    
    /*
    if (isset($po["re_arrive"]))
    {
        if ($mode == "edit")
        {
            if ($po["status"] == 1 && $po["re_arrive"] != "" && $po["re_arrive"] != $oldline['re_arrive'])
            {
                $r["re_arrive"] = $po["re_arrive"];
                $old_arrive = $re_arrive_array[$oldline['re_arrive']]['name'];
                $now_arrive = $re_arrive_array[$po["re_arrive"]]['name'];
                $r["re_arrive_log"] = $oldline["re_arrive_log"] .'<b>'. date("Y-m-d H:i:s") . " " . $realname . "��</b> �޸���  " . $old_arrive . "=> " . $now_arrive . "<br>";
            }
        }
    }
    */
    
    if(isset($po["re_arrive"])&&!empty($po["re_arrive"])&&!empty($po['nexttime'])&&($mode == "edit"))
    {
    	$insertdata = 'insert into '.$table.' (pid,part_id,name,age,sex,disease_id,depart,is_local,area,tel,qq,zhuanjia_num,status,media_from,engine,engine_key,from_site,from_account,doctor,xiangmu,author)
    	SELECT pid,part_id,name,age,sex,disease_id,depart,is_local,area,tel,qq,zhuanjia_num,status,media_from,engine,engine_key,from_site,from_account,doctor,xiangmu,author from '.$table.' where id = '.$po['id'];   	
    	
    	$db->query($insertdata);
    	
    	//��ȡ��һ��������id �����¸�ֵ
    	$updata = 'update '.$table.' set re_arrive = '.$po["re_arrive"].',addtime = '.time().' ,order_date = '.strtotime($po['nexttime']).'  where id = '.mysql_insert_id();
    	
    	$db->query($updata);
    	
    }else if(($oldline['re_arrive']!=$po['re_arrive'])&&empty($po['nexttime']))
    {
    	$r['re_arrive'] = $po['re_arrive'];
    }
    
    //����״̬END
    
    
    if (isset($po["author"]))$r["author"]=$po["author"];
    if (isset($po["memo"]))
        $r["memo"] = $po["memo"];
    if (isset($po["status"]))
        $r["status"] = $po["status"];
    if (isset($po["fee"]))
        $r["fee"] = $po["fee"]; //2010-11-18
        

    // ���Ӵ����޸�Ϊ��ǰ�ĵ�ҽ:
    if ($mode == "edit" && $oldline["jiedai"] == '' && $uinfo["part_id"] == 4)
    {
        $r["jiedai"] = $realname;
    }
    
    // ��ҽ���ֱ������Ϊ�ѵ�:
    if ($mode == "add" && $uinfo["part_id"] == 4)
    {
        $r["status"] = 1; //�ѵ�
        $r["jiedai"] = $realname;
    }
    
    // ������������Ŀ:
    if ($po["update_xiangmu"])
    {
        $r["xiangmu"] = @implode(" ", $po["xiangmu"]);
    }
    
    if (isset($po["huifang"]) && trim($po["huifang"]) != '')
    {
        $r["huifang"] = $oldline["huifang"] . "<b>" . date("Y-m-d H:i") . " [" . $realname . "]</b>:  " . $po["huifang"] . "<br/>";
    }
    
    if ($mode == "edit")
    { //�޸�ģʽ
        if (isset($po["jiedai_content"]))
        {
            $r["jiedai_content"] = $po["jiedai_content"];
        }
        
        // �޸ļ�¼
        if ($oldline["author"] != $realname)
        {
            $r["edit_log"] = $oldline["edit_log"] . $realname . ' �� ' . date("Y-m-d H:i:s") . " �޸Ĺ�������<br>";
        }
    } else
    { 
    	//����ģʽ
        $r["part_id"] = $uinfo["part_id"];
        $r["addtime"] = time();
        $r["author"] = $realname;
    }
    
    if (isset($po["tel"]))
    {
        $tel = trim($po["tel"]);
        //if (strlen($tel) > 20) $tel = substr($tel, 0, 20);
        //$r["tel"] = ec($tel, "ENCODE", md5($encode_password));
        $r["tel"] = $tel;
    }
    
    if (isset($r["status"]))
    {
        if (($op == "add" && $r["status"] == 1) || ($op == "edit" && $oldline["status"] != 1 && $r["status"] == 1))
        {
            $r["order_date"] = time();
        }
    }
    
    if ($mode == "edit" && isset($po["rechecktime"]) && $po["rechecktime"] != '')
    {
        if (strlen($po["rechecktime"]) <= 2 && is_numeric($po["rechecktime"]))
        {
            $rechecktime = ($r["order_date"] ? $r["order_date"] : $oldline["order_date"]) + intval($po["rechecktime"]) * 24 * 3600;
        } else
        {
            $rechecktime = strtotime($po["rechecktime"] . " 0:0:0");
        }
        $r["rechecktime"] = $rechecktime;
    }
    
    $sqldata = $db->sqljoin($r);
    $syncdata = $db->sqljoin($sync); //ͬ����ͬ��������
    if ($mode == "edit")
    {
        $sql = "update $table set $sqldata where id='$id'";
       
    } else
    {
        $sql = "insert into $table set $sqldata";
    }
    
    $return = $db->query($sql);
    
    
    //���²��˵ı��
    if($mode == "add")
    {
    	$pid = $user_hospital_id.mysql_insert_id().date('md');
    	$sql = "update $table set pid = ".$pid." where id='".mysql_insert_id()."' limit 1";
    	$db->query($sql);
    	
    }else  if($mode == "edit"&&!empty($po['pid']))
    {
    	$is_multi = $db->query("select pid from $table where pid=".$po['pid']);
    	if(count($is_multi)!=1)
    	{
    	    //ͬ����ͬ��������
    	    $syncsql = "update $table set $syncdata where pid=".$po['pid'];
    	    $db->query($syncsql);
    	}
    }
    
    if ($return)
    {
        if ($op == "add")
            $id = $return;
        if ($mode == "edit")
        {
            //$log->add("edit", ("�޸��˲������ϻ�״̬: ".$oldline["name"]), $oldline, $table);
        } else
        {
            //$log->add("add", ("����˲���: ".$r["name"]), $r, $table);
        }
      	   // msg_box("�����ύ�ɹ�", "http://".$_SERVER['HTTP_HOST']."/m/patient/patient.php", 1);

		 if($_REQUEST['go']=='index') //��ҳ��ת
		 {
		 	msg_box("�����ύ�ɹ�", history(3, $id));
		 }else if($_REQUEST['go']=='patient'){  //patient �б�ҳ
		 	msg_box("�����޸ĳɹ�", history(3, $id));
		 }else {
		 	msg_box("�����ύ�ɹ�", history(2, $id));
		 }

    } else
    {
        msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
    }
    
    exit();//POST���ֽ���
}

// ��ȡ�ֵ�:
$hospital_list = $db->query("select id,name from hospital");
$disease_list = $db->query("select id,name from disease where hospital_id='$user_hospital_id' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$doctor_list = $db->query("select id,name from doctor where hospital_id='$user_hospital_id'");
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$depart_list = $db->query("select id,name from depart where hospital_id='$user_hospital_id'");
$engine_list = $db->query("select id,name from engine", "id", "name");
$sites_list = $db->query("select id,url from sites where hid=$hid", "id", "url");
$account_list = $db->query("select id,concat(name,if(type='web',' (����)',' (�绰)')) as fname from count_type where hid=$hid order by id asc", "id", "fname");
$time1 = strtotime("-3 month");
$area_list = $db->query("select area, count(area) as c from $table where area!='' and addtime>$time1 group by area order by c desc limit 20", "", "area");

$author_list = $db->query("select id,realname as name from sys_admin where hospitals like '%$user_hospital_id%' and character_id in(45,16,51,46,47,44,26,15,17)");


$account_first = 0;
if (count ( $account_list ) > 0)
{
	$tmp = @array_keys ( $account_list );
	$account_first = $tmp [0];
}

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

$xiaofei_array = array ( 
		array ( 
				"id" => 0, 
				"name" => 'δ����' 
		), 
		array ( 
				"id" => 1, 
				"name" => '������' 
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

// ȡǰ30������:
$show_disease = array ();
foreach ( $disease_list as $k => $v )
{
    $show_disease[$k] = $v;
    if (count($show_disease) >= 30)
    {
        break;
    }
}

// ��ȡ�༭ ����
$cur_disease_list = array ();
if ($mode == "edit")
{
    $line = $db->query_first("select * from $table where id='$id' limit 1");
    
    $cur_disease_list = explode(",", $line["disease_id"]);
    foreach ( $cur_disease_list as $v )
    {
        if ($v && !array_key_exists($v, $show_disease))
        {
            $show_disease[$v] = $disease_list[$v];
        }
    }
}

// 2010-08-18
$media_from_array = explode(" ", "���� �绰"); // ���� ��־ �г� ���� ���ѽ��� ·�� ���� ��̨ ���� ·�� ���� ��� ��ֽ ����
$media_from_array2 = $db->query("select name from media where hospital_id='$user_hospital_id'", "", "name");
foreach ( $media_from_array2 as $v )
{
    if (!in_array($v, $media_from_array))
    {
        $media_from_array[] = $v;
    }
}

// 2010-10-23
$is_local_array = array (
    1 => "����", 2 => "���" 
);

// ���Ƹ�ѡ���Ƿ���Ա༭:
$all_field = explode(" ", "name sex age tel qq content disease_id media_from zhuanjia_num order_date doctor status xiaofei memo xiangmu huifang depart author is_local from_account fee");

$ce = array (); // can_edit �ļ�д, ĳ�ֶ��Ƿ��ܱ༭
if ($mode == "edit")
{ // �޸�ģʽ
    $edit_field = array ();
    if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3)  //����ͷ��͵绰�ͷ�
    {
        // δ���޸Ĺ������ϣ������޸�:
        if ($line["status"] == 0 || $line["status"] == 2)
        {
            if ($line["author"] == $realname)
            {
                $edit_field = explode(' ', 'qq content disease_id media_from zhuanjia_num memo order_date depart is_local from_account'); //�Լ��޸�
            } else
            {
                $edit_field[] = 'memo'; //�����Լ������ϣ����޸ı�ע
            }
        } else if ($line["status"] == 1)
        {
            $edit_field[] = 'memo'; //�ѵ������޸ı�ע
        }
        
        $edit_field[] = "order_date"; //�޸Ļطã����ܵ���Ԥ��ʱ��
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
        // ��ҽ���޸� �Ӵ�ҽ������Լ״̬�����ѣ���ע������
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
        // �绰�طò���
        $edit_field[] = 'order_date';
        $edit_field[] = 'memo';
        $edit_field[] = 'xiangmu';
        $edit_field[] = 'huifang';
        $edit_field[] = 'rechecktime';
    } else
    {
        // ����Ա �޸����е�����
        $edit_field = $all_field;
    }
} else
{ // ����ģʽ
    if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3)
    { //�ͷ����
        $edit_field = explode(' ', 'name sex age tel qq content disease_id media_from zhuanjia_num order_date memo depart is_local from_account');
    } else if ($uinfo["part_id"] == 4)
    { //��ҽ���
        $edit_field = explode(' ', 'name sex age tel qq content disease_id media_from zhuanjia_num order_date doctor status memo depart is_local from_account');
    } else
    {
        $edit_field = $all_field;
    }
}

// ������Ϊ���ѣ������Ǹ�������ݣ��������޸ģ�
if ($line["status"] == 1 && (strtotime(date("Y-m-d 0:0:0")) > strtotime(date("Y-m-d 0:0:0", $line["come_date"]))))
{
    //$edit_field = array(); //ȫ�������޸�
}

// ÿ���ֶ��Ƿ��ܱ༭:
foreach ( $all_field as $v )
{
    $ce[$v] = in_array($v, $edit_field) ? '' : ' disabled="true"';
}

// 2009-06-30 10:42 fix
if ($line["media_from"] == "����ͷ�")
{
    $line["media_from"] = "����";
} else if ($line["media_from"] == "�绰�ͷ�")
{
    $line["media_from"] = "�绰";
}

$title = $mode == "edit" ? "�޸Ĳ�������" : "����µĲ�������";



// page begin ----------------------------------------------------
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<style>
.dischk {
	width: 6em;
	height: 16px;
	line-height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	padding: 0;
	margin: 0;
}
</style>
<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	var nowDate =new Date().getTime(); 
	var huifang_date = new Date(oForm.huifang_date.value).getTime(); 
	if ((oForm.chengjiao.value==1)&&!oForm.cj_sum.value.match(/^(?:[\+\-]?\d+(?:\.\d+)?)?$/)) {
		alert("�ɽ������ֻ����������"); oForm.cj_sum.focus(); return false;
	}
	if (oForm.name.value == "") {
		alert("�����벡��������"); oForm.name.focus(); return false;
	}
	if (oForm.sex.value == '') {
		alert("�����롰�Ա𡱣�"); oForm.sex.focus(); return false;
	}
	if (oForm.media_from.value == '') {
		alert("��ѡ��ý����Դ����"); oForm.media_from.focus(); return false;
	}
	if (oForm.order_date.value.length < 12) {
		alert("����ȷ��д��Ԥ��ʱ�䡱��"); oForm.order_date.focus(); return false;
	}
	if (huifang_date < nowDate) {
		//alert("�ط�ʱ�䲻�����ڵ�ǰ"); oForm.huifang_date.focus(); return false;
	}
	<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname) || $uinfo["part_admin"] ||$debug_mode) : ?>
	if (oForm.tel.value != "" && get_num(oForm.tel.value) == '') {
		alert("����ȷ���벡�˵���ϵ�绰��"); oForm.tel.focus(); return false;
	}
	<?php endif?>
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

function input_time(id, time) {
	var s = byid(id).value;
	if (s == '') {
		alert("������д���ڣ�����дʱ�䣡");
		return;
	}
	var date = s.split(" ")[0];
	var datetime = date+" "+time;

	if (byid(id).disabled != true) {
		byid(id).value = datetime;
	}
}

// ��״̬Ϊ�ѵ�ʱ, ��ʾѡ��Ӵ�ҽ��:
function change_yisheng(v) {
	byid("yisheng").style.display = (v == 1 ? "inline" : "none");
}

// ��������ظ�:
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

function show_hide_engine(o) {
	byid("engine_show").style.display = (o.value == "����" ? "inline" : "none");
}

function show_hide_area(o) {
	byid("area_from_box").style.display = (o.value == "2" ? "inline" : "none");
}

function show_hide_disease_add(o) {
	byid("disease_add_box").style.display = (o.value == "-1" ? "inline" : "none");
}

function show_hide_cj_sum(o) {
	byid("cj_sum").style.display = (o.value == "1" ? "" : "none");
}

function show_hide_re_arrive(o) {
	byid("re_arrive").style.display = (o.value == "1" ? "" : "none");
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
	<!-- ͷ�� begin -->
	<header class="jumbotron subhead" style="margin-bottom: 20px;">
		<ul class="breadcrumb">
			<li><a href="javascript:void(0)" onclick="history.back()">����</a> <span
				class="divider">/</span></li>
			<li class="active text-info"><?php echo $title; ?></li>
			<!-- <li class="text-error"><span class="divider">/</span>�������</li> -->
		</ul>
	</header>
	<!-- ͷ�� end -->
	<form name="mainform" class="form-horizontal" method="POST"
		onsubmit="return check_data()">
		<fieldset>
			<legend>���˻�����Ϣ</legend>
			<div class="control-group">
				<label class="control-label">���</label>
				<div class="controls">
					<span class="input-xlarge uneditable-input span3 text-error"
						style="margin-left: 0"><?php echo empty($line['pid'])?'���ޱ��,���ڱ����ύ���ݺ�����':$line['pid']?></span>
					<input type="hidden" name="pid" value="<?php echo $line['pid'];?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">����</label>
				<div class="controls">
					<input class="span2" type="text" name="name" id="name"
						value="<?php echo $line["name"]; ?>" <?php echo $ce["name"]; ?>
						onchange="check_repeat('name', this)" placeholder="����">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">�Ա�</label>
				<div class="controls">
					<select name='sex' class="span2">
				        <?php $show_sex = array('��' => '��','Ů' =>'Ů')?>
				        <option value="" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($show_sex, '_key_', '_value_', $line["sex"])?>
				    </select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">����</label>
				<div class="controls">
					<input name="age" id="age" value="<?php echo $line["age"]; ?>"
						class="span2" <?php echo $ce["age"]; ?> placeholder="�ɲ���">
				</div>
			</div>
			<?php if ($op == "add" || ($op == "edit" && $line["author"] == $realname) || $uinfo["part_admin"] ||$debug_mode) { ?>
			<div class="control-group">
				<label class="control-label">�绰</label>
				<div class="controls">
					<input name="tel" id="tel" value="<?php echo $line["tel"]; ?>"
						class="span2" <?php echo $ce["tel"]; ?>
						onchange="check_repeat('tel', this)" placeholder="�ɲ���">
				</div>
			</div>
			<?php } ?>
			<div class="control-group">
				<label class="control-label">QQ</label>
				<div class="controls">
					<input name="qq" id="qq" value="<?php echo $line["qq"]; ?>" class="span2" <?php echo $ce["qq"]; ?> placeholder="�ɲ���">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">��ѯ����/�����¼</label>
				<div class="controls">
					<textarea name="content"
						style="width: 50%; vertical-align: middle;"
						<?php echo $ce["content"]; ?> rows="3" class="input-xlarge"><?php echo $line["content"]; ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">��������</label>
				<div class="controls">
					<select name="disease_id" class="span2"
						<?php echo $ce["disease_id"]; ?>>
						<option value="" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($show_disease, '_key_', '_value_', $line["disease_id"]); ?>
			        </select>
				</div>
			</div>
			<?php if (count($depart_list) > 0) { ?>
			<div class="control-group">
				<label class="control-label">��������</label>
				<div class="controls">
					<select name="depart" class="span2" <?php echo $ce["depart"]; ?>>
						<option value="0" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($depart_list, 'id', 'name', $line["depart"]); ?>
			        </select>
				</div>
			</div>
			<?php } ?>
			<div class="control-group">
				<label class="control-label">ý����Դ</label>
				<div class="controls">
					<select name="media_from" class="span2"
						<?php echo $ce["media_from"]; ?> onchange="show_hide_engine(this)">
						<option value="" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($media_from_array, '_value_', '_value_', $line["media_from"]); ?>
			        </select>&nbsp; <span id="engine_show" style="display:<?php echo $line["media_from"] == "����" ? "" : "none"; ?>" <?php echo $ce["media_from"]; ?>>
						<select name="engine" class="span2">
							<option value="" style="color: gray">--����������Դ--</option>
						<?php echo list_option($engine_list, '_value_', '_value_', $line["engine"]); ?>
					</select> �ؼ��ʣ�<input name="engine_key"
						value="<?php echo $line["engine_key"]; ?>" class="span2"
						<?php echo $ce["media_from"]; ?>> <select name="from_site"
						class="span2" <?php echo $ce["media_from"]; ?>>
							<option value="" style="color: gray">--��Դ��վ--</option>
						<?php echo list_option($sites_list, '_value_', '_value_', $line["from_site"]); ?>
					</select>
				
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">������Դ</label>
				<div class="controls">
					<select name="is_local" class="span2"
						<?php echo $ce["is_local"]; ?> onchange="show_hide_area(this)">
						<option value="0" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($is_local_array, '_key_', '_value_', ($op == "add" ? 1 : $line["is_local"])); ?>
			        </select>&nbsp; <span id="area_from_box" style="display: <?php echo $op == "add" ? "none" : ($line["is_local"] == 2 ? "inline" : "none"); ?>">
						������ <input name="area" id="area"
						value="<?php echo $line["area"]; ?>" class="span2"
						<?php echo $ce["is_local"]; ?>> &nbsp; ������õ����� <select
						id="quick_area" class="span2" <?php echo $ce["is_local"]; ?>
						onchange="byid('area').value=this.value;">
							<option value="" style="color: gray">-����-</option>
				        <?php echo list_option($area_list, "_value_", "_value_"); ?>
			        </select>
				
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo $uinfo["part_id"] == 4 ? "�����" : "ר�Һ�"; ?></label>
				<div class="controls">
					<input name="zhuanjia_num"
						value="<?php echo $line["zhuanjia_num"]; ?>" class="span2"
						<?php echo $ce["zhuanjia_num"]; ?> placeholder="�ɲ���">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Ԥ��ʱ��</label>
				<div class="controls">
				    <div class="input-append">
						<input name="order_date"  value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="span2" id="order_date" <?php echo $ce["order_date"]; ?>> 
						<span class="add-on">���޸�<?php echo intval($line["order_date_changes"]); ?>��</span>
					</div>
					<span class="help-inline">
					    ��ע�⣬�˴��ѵ�����Ԥ��ʱ�䲻�������ϸ���<?php echo date("j"); ?>�ţ����������޷��ύ��
					    <?php echo ($uinfo['part_admin']||$debug_mode)?'<span class="text-error">����Ա�ɶ�ʱ����������޸�</span>':''?>
					</span>	
					<?php if ($line["order_date_log"]) { ?>
					<a href="javascript:void(0)" onclick="byid('order_date_log').style.display = (byid('order_date_log').style.display == 'none' ? 'block' : 'none'); ">�鿴�޸ļ�¼</a>
					<?php } ?>
					<?php
					$show_days = array (
					    "��" => $today = date("Y-m-d"),  //����
					    "��" => date("Y-m-d", strtotime("+1 day")),  //����
					    "��" => date("Y-m-d", strtotime("+2 days")),  //����
					    "�����" => date("Y-m-d", strtotime("+3 days")),  //�����
					    "����" => date("Y-m-d", strtotime("next Saturday")),  //����
					    "����" => date("Y-m-d", strtotime("next Sunday")),  // ����
					    "��һ" => date("Y-m-d", strtotime("next Monday")),  // ��һ
					    "һ�ܺ�" => date("Y-m-d", strtotime("+7 days")),  // һ�ܺ�
					    "���º�" => date("Y-m-d", strtotime("+15 days"))  //����º�
					);
					if (!$ce["order_date"])
					{
					    echo '<p class="help-block">����: ';
					    foreach ( $show_days as $name => $value )
					    {
					        echo '<a href="javascript:input_date(\'order_date\', \'' . $value . '\')">[' . $name . ']</a>&nbsp;';
					    }
					    echo '<br>ʱ��: ';
					    echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[ʱ�䲻��]</a>&nbsp;';
					    echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[����9��]</a>&nbsp;';
					    echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[����2��]</a>&nbsp;</p>';
					}
					?>
		            <?php if ($line["order_date_log"]) { ?>
		            <div id="order_date_log"
						style="display: none; padding-top: 6px;">
						<b>Ԥ��ʱ���޸ļ�¼:</b> <br><?php echo strim($line["order_date_log"], '<br>'); ?>
				    </div>
		            <?php } ?>
			    </div>
			</div>
			<div class="control-group">
				<label class="control-label">��ע</label>
				<div class="controls">
					<textarea rows="3" name="memo"
						style="width: 50%; vertical-align: middle;"
						<?php echo $ce["memo"]; ?>><?php echo $line["memo"]; ?></textarea>
				</div>
			</div>
			
			<?php if ($line["edit_log"] && $line["author"] == $realname):?>
			<div class="control-group">
				<label class="control-label">�����޸ļ�¼</label>
				<div class="controls">
			        <?php echo strim($line["edit_log"], '<br>'); ?>
			    </div>
			</div>
			<?php endif?>
		</fieldset>
		<?php
        if (in_array($uinfo["part_id"], array (  4, 9, 12 )) && $line["status"] == 1) {?>
		<fieldset>
			<legend>������Ŀ</legend>
			<div class="control-group">
				<label class="control-label">������Ŀ</label>
				<div class="controls">
				    <?php
			            $xiangmu_str = $db->query("select xiangmu from disease where id=". $line ["disease_id"] . " limit 1", 1, "xiangmu" );
						$xiangmu = explode ( " ", trim ( $xiangmu_str ) );
						$cur_xiangmu = explode ( " ", trim ( $line ["xiangmu"] ) );
						$xiangmu = array_unique ( array_merge ( $cur_xiangmu, $xiangmu ) );
						foreach ( $xiangmu as $k )
						{
							if ($k == '')
								continue;
							$checked = in_array ( $k, $cur_xiangmu ) ? " checked" : "";
							$makered = $checked ? ' style="color:red"' : '';
							echo '<input type="checkbox" name="xiangmu[]" value="' . $k . '"' . $checked . ' id="xiangmu_' . $k . '"' . $ce ["xiangmu"] . '><label for="xiangmu_' . $k . '"' . $makered . '>' . $k . '</label>&nbsp;&nbsp;';
						}
					?>
					<?php if (!$ce["xiangmu"]) { ?>
						<input type="hidden" name="update_xiangmu" value="1"> 
						<span id="xiangmu_user"></span> 
						<span id="xiangmu_add">
							<b>���ӣ�</b>
							<input id="miangmu_my_add" class="span2">&nbsp;
							<button onclick="xiangmu_user_add()" class="btn">ȷ��</button>
						</span>
						<script language="JavaScript">
							function xiangmu_user_add() {
								var name = byid("miangmu_my_add").value;
								if (name == '') {
									alert("�������¼ӵ����֣�"); return false;
								}
								var str = '<input type="checkbox" name="xiangmu[]" value="'+name+'" checked id="xiangmu_'+name+'"><label for="xiangmu_'+name+'">'+name+'</label>&nbsp;&nbsp;';
								byid("xiangmu_user").insertAdjacentHTML("beforeEnd", str);
								byid("miangmu_my_add").value = '';
							}
					     </script>
					<?php } ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">���Ʒ���</label>
				<div class="controls">
					<input name="fee" id="fee" value="<?php echo $line["fee"] > 0 ? $line["fee"] : ''; ?>" class="span2" <?php echo $ce["fee"]; ?>>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">����ʱ��</label>
				<div class="controls">
					<input name="rechecktime" id="rechecktime" value="<?php if ($line["rechecktime"]>0) echo date("Y-m-d", $line["rechecktime"]); ?>" class="span2" <?php echo $ce["rechecktime"]; ?>>
			        <?php if ($line["rechecktime"]) echo intval(($line["rechecktime"] - $line["order_date"]) / 24/3600)."�� "; ?>
			        <p class="help-inline">����д����(�� 10 �����Ԥ��ʱ������)�����ʱ��(�� 2009-10-1) </p>
				</div>
			</div>
			
		</fieldset>
		<?php } ?>
		<?php if (in_array($uinfo["part_id"], array(1,4,9)) || ($username == "admin") || $debug_mode): ?>
	    <fieldset>
	        <legend>��Ժ����</legend>
	        <div class="control-group">
				<label class="control-label">��Լ״̬</label>
				<div class="controls">
					<select name="status" class="span2" onchange="show_hide_re_arrive(this)" <?php echo $ce["status"]; ?>
					    <?php echo $line['re_arrive']!=0?'disabled':'';?>>
						<!-- onchange="change_yisheng(this.value)" -->
						<option value="0" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($status_array, 'id', 'name', ($mode == "add" && $uinfo["part_id"] == 4) ? 1 : $line["status"]); ?>
			        </select> 
			        <span id="re_arrive" style="display:<?php echo($line["status"]==1?"":"none")?>">&nbsp;&nbsp;<!--style="display:<?php echo($line["re_arrive"]==1?"":"none")?>"-->
				    </span>&nbsp;
				    <?php if ($line["re_arrive_log"]) { ?>
				    <a href="javascript:void(0)" onclick="byid('re_arrive_log').style.display = (byid('re_arrive_log').style.display == 'none' ? 'block' : 'none'); ">�鿴�޸ļ�¼</a><?php }?>
				    <?php if ($line["re_arrive_log"]) { ?>
		            <div id="re_arrive_log" style="display: none; padding-top: 6px;">
						<b>��Ժ״̬�޸ļ�¼:</b> <br><?php echo strim($line["re_arrive_log"], '<br>'); ?>
					</div>
		            <?php } ?>
				</div>
			</div>
			<!-- ֻ�������������²����ó���״̬ �Լ�ʱ�� -->
			<?php if($line['status']==1):?>
			<div class="control-group">
				<label class="control-label">����״̬</label>
				<div class="controls">
					<select name="re_arrive" class="span2" <?php echo  $line["status"] !=1?'disabled':'';?>>
					    <?php 
					    if($line['re_arrive']!=0)
                        {
                        	echo list_option($re_arrive_already, 'id', 'name', $line["re_arrive"]);
                        	
                        }else{
                        	echo list_option($re_arrive_array, 'id', 'name', $line["re_arrive"]);
                        }  
					    ?>
			       </select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">��Ժʱ��</label>
				<div class="controls">
					<input name="nexttime" id="nexttime" value="" class="span2" <?php echo $line['status']!=1?'disabled':'';?>>
					<p class="help-inline">��ʱ���ʾ�����´���Ժʱ�䣬�������ʱ�䣬������ı䵱ǰ����״̬</p>
				</div>
			</div>
			<?php endif?>
		
			<div class="control-group">
				<label class="control-label">�Ӵ�ҽ��</label>
				<div class="controls">
					<select name="doctor" class="span2" <?php echo $ce["doctor"]; ?>>
						<option value="" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($doctor_list, 'name', 'name', $line["doctor"]); ?>
			        </select>
					<p class="help-inline">�������ѵ�Ժʱѡ��</p>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">����ͷ�</label>
				<div class="controls">
				    <select name="author" class="span2" <?php echo ($uinfo["part_id"] == 1||$uinfo["part_id"] == 9 || $uinfo["part_admin"] ||$debug_mode)?"":"disabled";?>>
					    <option value="" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($author_list, 'name', 'name', $line["author"]); ?>
			        </select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">�ɽ�״̬</label>
				<div class="controls">
					<select name="chengjiao" class="span2">
						<option value="" style="color: gray">--��ѡ��--</option>
				        <?php echo list_option($jiaoyi_array, 'id', 'name', $line["chengjiao"]); ?>
			        </select>
			        <!-- 
			        <span id="cj_sum" style="display:<?php echo($line["chengjiao"]==1?"":"none")?>">&nbsp;&nbsp;
					�ɽ����:<input name="cj_sum" value="<?=$line["cj_sum"]?>" class="span2">Ԫ
					<p class="help-block">��״̬��Ϊδ"�ѵ�",�ɽ��������</p>
					 -->
				</div>
			</div>
        </fieldset>
        <?php endif?>
        
        <!-- �Ӵ���¼ -->
        <?php if ($mode == "edit" && $line["status"] == 1 && ($debug_mode || in_array($uinfo["part_id"], array(1,4,9))) ) : ?>
        <fieldset>
            <legend>��Ժ�Ӵ���¼</legend>
            <div class="control-group">
				<label class="control-label">�Ӵ���¼</label>
				<div class="controls">
				    <textarea name="jiedai_content" style="width: 50%; height: 48px; vertical-align: middle;" class="span2"><?php echo $line["jiedai_content"]; ?> </textarea>
				</div>
			</div>
        </fieldset>
        <?php endif ?>
        
        
        <?php if ($mode == "edit" && (in_array("huifang", $edit_field) || $line["author"] == $username)): ?>
        <?php  $huifang = trim($line["huifang"]);  ?>
        <fieldset>
            <legend>�绰�طü�¼</legend>
            <div class="control-group">
				<label class="control-label">�´λط�ʱ��</label>
				<div class="controls">
				    <input name="huifang_date" value="<?php echo $line["huifang_date"] ? @date('Y-m-d H:i:s', $line["huifang_date"]) : ''; ?>" class="span2"  id="huifang_date"
					<?php echo $ce["huifang_date"]; ?>> 
					<p class="help-inline">�ط�ʱ�佫�������ڵ�ǰ</p>
					<?php if ($line["huifang_date"]) echo '��ʣ<strong class="red">'.intval(($line["huifang_date"] - mktime(0,0,0)) / 24/3600)."</strong>�� "; ?>
					<?php if ($line["huifang_date_log"]) { ?>
					<a href="javascript:void(0)" onclick="byid('huifang_date_log').style.display = (byid('huifang_date_log').style.display == 'none' ? 'block' : 'none'); ">�鿴�޸ļ�¼</a><?php } ?>
					<?php
					    $show_days = array (
					        
					        "��" => $today = date("Y-m-d"),  //����
					        "��" => date("Y-m-d", strtotime("+1 day")),  //����
					        "��" => date("Y-m-d", strtotime("+2 days")),  //����
					        "�����" => date("Y-m-d", strtotime("+3 days")),  //�����
					        "����" => date("Y-m-d", strtotime("next Saturday")),  //����
					        "����" => date("Y-m-d", strtotime("next Sunday")),  // ����
					        "��һ" => date("Y-m-d", strtotime("next Monday")),  // ��һ
					        "һ�ܺ�" => date("Y-m-d", strtotime("+7 days")),  // һ�ܺ�
					        "���º�" => date("Y-m-d", strtotime("+15 days"))  //����º�
					    );
					    if (!$ce["huifang_date"])
					    {
					        echo '<div style="padding-top:6px;">����: ';
					        foreach ( $show_days as $name => $value )
					        {
					            echo '<a href="javascript:input_date(\'huifang_date\', \'' . $value . '\')">[' . $name . ']</a>&nbsp;';
					        }
					        echo '<br>ʱ��: ';
					        echo '<a href="javascript:input_time(\'huifang_date\',\'00:00:00\')">[ʱ�䲻��]</a>&nbsp;';
					        echo '<a href="javascript:input_time(\'huifang_date\',\'09:00:00\')">[����9��]</a>&nbsp;';
					        echo '<a href="javascript:input_time(\'huifang_date\',\'14:00:00\')">[����2��]</a>&nbsp;</div>';
					    }
					    ?>
						<?php if ($line["huifang_date_log"]) { ?>
						<div id="huifang_date_log" style="display: none; padding-top: 6px;">
							<b>�ط�ʱ���޸ļ�¼:</b> <br><?php echo strim($line["huifang_date_log"], '<br>'); ?></div>
						<?php } ?>
				</div>
			</div>
			<div class="control-group">
			    <label class="control-label">���λط�</label>
			    <div class="controls">
			        <p class="help-inline"><?php echo $line["huifang"] ? text_show($line["huifang"]) : "<font color=gray>(���޼�¼)</font>"; ?>
			        </p>
			    </div>    
			</div>
			<div class="control-group">
			    <label class="control-label">���λط�</label>
			    <div class="controls">
			        <textarea name="huifang" style="width: 50%; height: 48px; vertical-align: middle;" class="span2" <?php echo $ce["huifang"]; ?>></textarea>
			    </div>
			</div>
        </fieldset>
        <?php endif ?>

		<input type="hidden" name="id" id="id" value="<?php echo $id; ?>"> 
		<input type="hidden" name="op" value="<?php echo $mode; ?>"> 
		<input type="hidden" name="go" value="<?php echo $_GET["go"]; ?>">
		<div class="form-actions">
            <button type="submit" class="btn btn-primary">�������</button>
        </div>
	</form>
	<div class="space"></div>
	<div class="alert alert-info">
		<div class="d_title">��ʾ��</div>
		<div class="d_item">1.����������д�� 2.�绰���������д������������֣�������7λ�� 3.δ��������д�ڱ�ע�С�</div>
	</div>
<script>
$("#order_date").datetimepicker({
	format: "yyyy-mm-dd hh:ii:ss",
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left"
});
$("#rechecktime").datetimepicker({
    format: "yyyy-mm-dd hh:ii:ss",
    autoclose: true,
    todayBtn: true,
    pickerPosition: "bottom-left"
});
$("#nexttime").datetimepicker({
	format: "yyyy-mm-dd hh:ss",
    autoclose: true,
    todayBtn: true,
    minView:'day',
    pickerPosition: "bottom-left"
});
$("#huifang_date").datetimepicker({
	format: "yyyy-mm-dd hh:ss",
    autoclose: true,
    todayBtn: true,
    minView:'day',
    pickerPosition: "bottom-left"
});
</script>
</body>
</html>
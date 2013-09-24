<?php
/**
 *
 * @author   fangyang (278294861)
 * @version  2.0.1
 * @function 首页添加病人
 * @date     20130319
 */

$mode = $op;


//转换字符集 数组
function auto_charset ($Contents, $from, $to)
{
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) ||
             (is_scalar($fContents) && ! is_string($fContents)))
    {
        // 如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents))
    {
        if (function_exists('mb_convert_encoding'))
        {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv'))
        {
            return iconv($from, $to, $fContents);
        } else
        {
            return $fContents;
        }
    } elseif (is_array($fContents))
    {
        foreach ($fContents as $key => $val)
        {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    } else
    {
        return $fContents;
    }
}


if ($_POST)
{
    $po = &$_POST; //引用 $_POST


    if ($mode == "edit")
    {
        $oldline = $db->query("select * from $table where id=$id limit 1", 1);
    } else
    {
        // 检查一个月内的病人中有无重复的:
        $name = trim($po["name"]);
        $tel = trim($po["tel"]);
        if (strlen($tel) >= 7)
        {
            $thetime = strtotime("-1 month");
            $list = $db->query("select * from $table where tel='$tel' and addtime>$thetime limit 1", 1);
            if ($list && count($list) > 0)
            {
                msg_box("电话号码重复，提交失败", "back", 1, 5);
            }
        }
    }
        
        // 客服添加疾病类型 2010-10-27
        
    $r = array();
    $r["name"] = iconv('UTF-8','GB2312', $po["name"]);
    $r["sex"] = iconv('UTF-8','GB2312',$po["sex"]);
    $r["qq"] = $po["qq"]; // 2010-10-28
    $r["age"] = $po["age"];
    $r["content"] = iconv('UTF-8','GB2312',$po["content"]);
    $r["disease_id"] = $po["disease_id"];
    $r["depart"] = $po["depart"];
    $r["media_from"] = $po["media_from"];
    $r["engine"] = $po["engine"];
    $r["engine_key"] = $po["engine_key"];
    $r["from_site"] = $po["from_site"];
    $r["from_account"] = $po["from_account"]; // 2010-11-04
    $r["zhuanjia_num"] = $po["zhuanjia_num"];
    $r["is_local"] = $po["is_local"];
    $r["area"] = $po["area"];
    $r["chengjiao"] = $po["chengjiao"];      // 2013-03-08
    $r["cj_sum"] = $po["cj_sum"];

    if ($po["chengjiao"] != "1")
        $r["cj_sum"] = "0";

    // 修改时间:
    if (isset($po["order_date"]))
    {
        $order_date_post = @strtotime($po["order_date"]);
        if ($mode == "add")
        {

            // 如果修改，该时间不能被修改为当前时间的一个月之前(2011-01-15)
            if ($order_date_post < strtotime("-1 month"))
            {
                exit_html("预诊时间不能是一个月之前。（请先检查您的电脑时间是否有误！）  请返回重新填写。");
            }

            $r["huifang_date"] = $r["order_date"] = $order_date_post; //新增，默认新增回访时间和预诊时间一样
        } else
        {
            //判断时间是否有修改
            if ($order_date_post != $oldline["order_date"])
            {

                // 如果修改，该时间不能被修改为当前时间的一个月之前(2011-01-15)
                if ($order_date_post < strtotime("-1 month"))
                {
                    exit_html("预诊时间不能被修改到一个月之前。（请先检查您的电脑时间是否有误！）  请返回重新填写。");
                }

                $r["order_date"] = $order_date_post;
                $r["order_date_changes"] = intval($oldline["order_date_changes"]) + 1;
                $r["order_date_log"] = $oldline["order_date_log"] . (date("Y-m-d H:i:s") . " " . $realname . " 修改 (" . date("Y-m-d H:i", $oldline["order_date"]) . " => " . date("Y-m-d H:i", $order_date_post) . ")<br>");

                // 如果修改预诊时间，自动修改状态为等待
                if ($oldline["status"] == 2)
                {
                    $r["status"] = 0;
                }
            }
        }
    }


    if (isset($po["author"]))$r["author"]=$po["author"];
    if (isset($po["memo"]))
        $r["memo"] = $po["memo"];
    if (isset($po["status"]))
        $r["status"] = $po["status"];
    if (isset($po["fee"]))
        $r["fee"] = $po["fee"]; //2010-11-18


    // 将接待人修改为当前的导医:
    if ($mode == "edit" && $oldline["jiedai"] == '' && $uinfo["part_id"] == 4)
    {
        $r["jiedai"] = $realname;
    }

    // 导医添加直接设置为已到:
    if ($mode == "add" && $uinfo["part_id"] == 4)
    {
        $r["status"] = 1; //已到
        $r["jiedai"] = $realname;
    }

    if (isset($po["doctor"]))
    {
        $r["doctor"] = $po["doctor"];
    }

    // 已做的整形项目:
    if ($po["update_xiangmu"])
    {
        $r["xiangmu"] = @implode(" ", $po["xiangmu"]);
    }

    if (isset($po["huifang"]) && trim($po["huifang"]) != '')
    {
        $r["huifang"] = $oldline["huifang"] . "<b>" . date("Y-m-d H:i") . " [" . $realname . "]</b>:  " . $po["huifang"] . "\n";
    }

    //新增模式
        $r["part_id"] = $uinfo["part_id"];
        $r["addtime"] = time();
        $r["author"] = $realname;
  

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

    
    //转换字符集
   // $r = auto_charset($r,'gb2312','utf-8');
    
   // var_dump($r);
    
    $sqldata = $db->sqljoin($r);

        $sql = "insert into $table set $sqldata";

    $return = $db->query($sql);

    if ($return)
    {
        if ($op == "add")
            $id = $return;
        msg_box("资料提交成功", history(2, $id), 1);
    } else
    {
        msg_box("资料提交失败，系统繁忙，请稍后再试。", "back", 1, 5);
    }
}

// 取前30个病种:
$show_disease = array ();
foreach ( $disease_list as $k => $v )
{
    $show_disease[$k] = $v;
    if (count($show_disease) >= 30)
    {
        break;
    }
}

// 读取编辑 资料
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



?>

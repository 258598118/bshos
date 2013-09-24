<?
/**
 * @title  通用excel导出模块
 * @since  1300401
 * @author fangyang
 * */
// 设置PHPExcel类库的include path
//set_include_path(  '.' . PATH_SEPARATOR . 'D:\Zeal\PHP_LIBS' . PATH_SEPARATOR .   get_include_path());

header('Content-type: text/html; charset=utf-8');
require "../../core/core.php";
$table = "count_web";

if (isset($_REQUEST['method']) && ($_REQUEST['method'] == 'import'))
{
    $bt = $_REQUEST['begin'];
    $et = $_REQUEST['end'];
    $hid = $_REQUEST['hid'];
    $title = iconv('GB2312','UTF-8',$_REQUEST['title']);
    $when = $_REQUEST['when'];
} 
else{
    
    exit();
}

set_include_path(dirname(dirname(dirname(__FILE__))).'\include\\');
/**
 * 以下是使用示例，对于以 //// 开头的行是不同的可选方式，请根据实际需要
 * 打开对应行的注释。
 * 如果使用 Excel5 ，输出的内容应该是GBK编码。
 */


/** PHPExcel */   
include 'PHPExcel.php';   
   
/** PHPExcel_Writer_Excel2007 */   
include 'PHPExcel/Writer/Excel2007.php';   


// Create new PHPExcel object   
//echo date('H:i:s') . " Create new PHPExcel object\n";   
$objPHPExcel = new PHPExcel();   
   
// Set properties   
//echo date('H:i:s') . " Set properties\n";   
$objPHPExcel->getProperties()->setCreator("BSHOS SYSTEM");   
$objPHPExcel->getProperties()->setLastModifiedBy("BSHOS SYSTEM");   
//$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");   
//$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");   
//$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");   
//$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");   
//$objPHPExcel->getProperties()->setCategory("Test result file");   
   
// Add some data   
//echo date('H:i:s') . " Add some data\n";   
$objPHPExcel->setActiveSheetIndex(0); 
$objActSheet = $objPHPExcel->getActiveSheet();

// 设置填充颜色
/*
 * $objStyleA1 = $objActSheet->getStyle('A1'); $objFillA1 =
 * $objStyleA1->getFill();
 * $objFillA1->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
 * $objFillA1->getStartColor()->setARGB('6FD128');
 */

    $web_count_title = array(
            '日期',
            '总点击',
            '本地',
            '外地',
            '总有效',
            '本地',
            '外地',
            '零对话',
            '当天约',
            '本地',
            '外地',
            '预计到院',
            '本地',
            '外地',
            '实际到院',
            '本地',
            '外地',
            '咨询预约率',
            '预约就诊率',
            '咨询预约率',
            '有效咨询率',
            '有效预约率'
    );
    
    // 表头
    $i = 'A';
    for ($n = 0; $n < count($web_count_title); $n ++)
    {
        
        $objPHPExcel->getActiveSheet()->setCellValue($i ++ . '1', 
                $web_count_title[$n]);
    }
    
    // 主体部分
    $table = "count_web";
    $cur_type = $_SESSION["count_type_id_web"];
    $list = $db->query(
            "select * from $table where date>=".$bt." AND date<=".$et." AND type_id=".$hid." order by date asc");

if(count($list)==0)
{
    echo iconv("UTF-8","GB2312//IGNORE",'无任何数据!');exit();

}

    
    $row = 1;
    $crow = count($list);
	$arow = $crow + 1;
    $srow = $crow + 2;
    foreach ($list as $i => $r)
    {
        $row ++;
        $objPHPExcel->getActiveSheet()->setCellValue(A . $row, $r['date']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(B . $row, $r['click']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(C . $row, 
                $r['click_local']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(D . $row, 
                $r['click_other']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(E . $row, $r['ok_click']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(F . $row, 
                $r['ok_click_local']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(G . $row, 
                $r['ok_click_other']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(H . $row, $r['zero_talk']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(I . $row, $r['talk']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(J . $row, $r['talk_local']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(K . $row, $r['talk_other']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(L . $row, $r['orders']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(M . $row, 
                $r['order_local']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(N . $row, 
                $r['order_other']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(O . $row, $r['come']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(P . $row, $r['come_local']);
        
        $objPHPExcel->getActiveSheet()->setCellValue(Q . $row, $r['come_other']);
        
        // 计算部分
        $objPHPExcel->getActiveSheet()->setCellValue(R . $row, 
                @round($r["talk"] / $r["click"] * 100, 2));
        
        $objPHPExcel->getActiveSheet()->setCellValue(S . $row, 
                @round($r["come"] / $r["orders"] * 100, 2));
        
        $objPHPExcel->getActiveSheet()->setCellValue(T . $row, 
                @round($r["come"] / $r["click"] * 100, 2));
        
        $objPHPExcel->getActiveSheet()->setCellValue(U . $row, 
                @round($r["ok_click"] / $r["click"] * 100, 2));
        
        $objPHPExcel->getActiveSheet()->setCellValue(V . $row, 
                @round($r["talk"] / $r["ok_click"] * 100, 2));
        
        /*
         * // 咨询预约率: $list[$k]["per_1"] = @round($r["talk"] / $v["click"] * 100,
         * 2); // 预约就诊率: $list[$k]["per_2"] = @round($r["come"] / $v["orders"] *
         * 100, 2); // 咨询就诊率: $list[$k]["per_3"] = @round($r["come"] /
         * $v["click"] * 100, 2); // 有效咨询率: $list[$k]["per_4"] =
         * @round($r["ok_click"] / $v["click"] * 100, 2); // 有效预约率:
         * $list[$k]["per_5"] = @round($r["talk"] / $v["ok_click"] * 100, 2);
         */
    
    
    /* $objPHPExcel->getActiveSheet()->setCellValue(B . $srow,
            '=SUM(B2:B'.$crow.')'); */

}
    
    // 合计部分
    $i = 'A';
    for ($n = 0; $n < count($web_count_title); $n ++)
    {
        
        $i ++;
        $objPHPExcel->getActiveSheet()->setCellValue(A . $srow, '合计');
        if ($i >= 'R' && $i <= 'V')
        {
            $objPHPExcel->getActiveSheet()->setCellValue($i . $srow, 
                    '=round(AVERAGE(' . $i . '2:' . $i . $arow . '),3)');
        } else
        {
            
            $objPHPExcel->getActiveSheet()->setCellValue($i . $srow, 
                    '=SUM(' . $i . '2:' . $i . $arow . ')');
        }
    }


// Rename sheet   
//echo date('H:i:s') . " Rename sheet\n";   
$objPHPExcel->getActiveSheet()->setTitle($title.' '.$when.'网络统计数据');   
   
// Set active sheet index to the first sheet, so Excel opens this as the first sheet   
$objPHPExcel->setActiveSheetIndex(0);   
   
// Save Excel 2007 file   
//echo date('H:i:s') . " Write to Excel2007 format\n";   
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);   
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));   


$outputFileName = $title.'-'.$when.'网络统计数据.xls';

header("Content-Type: application/force-download");  
header("Content-Type: application/octet-stream");  
header("Content-Type: application/download");  
header('Content-Disposition:inline;filename="'.$outputFileName.'"');  
header("Content-Transfer-Encoding: binary");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
header("Pragma: no-cache");  

$objWriter->save('php://output');  




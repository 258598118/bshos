<?php 
/**
 * 消费走势明细
 * 
 * @author fangyang(278294861)
 * @since  2013-07
 */

require "../../core/core.php";
if ($user_hospital_id == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}
$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');
$ptable = 'patient_'.$user_hospital_id;
$ftable = 'patient_fee';
$leftjoin = "LEFT JOIN $ptable ON $ftable.aid = $ptable.id";
$qudao = $montharr = array();

//选出前8个渠道最高的来源
$qudao = $db->query("select DISTINCT media_from ,count(media_from)as count from $ptable where media_from !='' group by media_from  order by count desc limit 8");
array_unshift($qudao,array("media_from"=>'全部',"count"=>''));

for ($i = 0; $i < 12; $i++) {
	$m = strtotime("-".$i." month");
	$montharr[date("Y-m", $m)] = array(strtotime(date("Y-m-01 00:00:00", $m)), strtotime(date("Y-m-31 23:59:59", $m)));
}


//渠道
function mediadata($project = '',$feedate = '')
{
	global $db,$ftable,$ptable,$leftjoin;
	$result = 0;
	
	//该月的第一天
	$firstdate = $feedate."-01";
	$starttime = strtotime(date($firstdate,strtotime("now")));
	//该月的最后一天
	$endtime = strtotime("$firstdate +1 month -1 day");
	
	if(!isset($poject)&&!isset($feedate))
	{
		return $result;
	}
	
	//合计数据
	if($project == "全部"&&isset($feedate))
	{
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime";
		$sumfee = $db->query_first("select sum($ftable.s_charge) as sum from $ftable $where","sum");
		return intval($sumfee['sum']);
	}
	
	//具体来源
	if(isset($project)&&isset($feedate))
	{
		
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ptable.media_from = '$project'";
		$sumfee = $db->query_first("select $ptable.media_from,sum($ftable.s_charge) as sum from $ftable $leftjoin $where group by $ptable.media_from","sum");
		return intval($sumfee['sum']);
	}
	
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<title>渠道消费走势图</title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
#common_form{text-align:center}
.breadcrumb ul { margin: 0 }
</style>
</head>
<body>
    <header class="jumbotron subhead" style="margin-bottom: 10px;">
        <div class="breadcrumb">
            <ul>
				<li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?php echo $hospital_id_name[$user_hospital_id];?></span>-消费走势表</li>
			</ul>
        </div>
    </header>
    <!-- 消费渠道 START -->
    <div class="row-fluid">
        <div class="page-header"><h3 class="text-error">各渠道消费走势对比</h3></div>
        <div class="row-fluid">
            <div class="span12" style="height:405px;overflow-y:auto">
                <table class="table table-striped">
		              <thead id="qudaotitle">
		                <tr>
		                  <th>渠道\日期</th>
		                  <?php foreach(array_reverse($montharr) as $k=>$v):?>
		                  <th><?php echo $k?></th>
		                  <?php endforeach;?> 
		                </tr>
		              </thead>
		              <tbody id="qudaotable">
		                <?php foreach($qudao as $q):?>
		                <tr>
		                  <td><b><?php echo $q['media_from'];?></b></td> 
		                  <?php 
		                  foreach(array_reverse($montharr) as $k=>$v)
		                  {
		                  	echo "<td>".mediadata($q['media_from'],$k)."</td>";
		                  }
		                  
		                  ?>
		                </tr> 
		                <?php endforeach?>
		              </tbody>
	            </table>
            </div>
        </div>
    </div>
    <!-- 消费渠道 END -->
    
    <section>
        <div class="page-header"><h3 class="text-error">各渠道消费走势对比图表</h3></div>
        <div class="row-fluid">
            <div class="span12" id="qudao">
            </div>
        </div>
    </section>
    
<script src="/static/Highcharts/js/highcharts.js"></script>
<script src="/static/Highcharts/js/modules/exporting.js"></script>
<script>
		                  
function qdChart(data,title,cat)
{
		var data,title,cat;
		if(typeof(data) == "undefined" ){
			data=''
			cat = ''
			alldata = ''	
			title = '数据生成出错，检查数据源'
		}else{
			data=data
			title = '各渠道消费走势图'
			cat = cat
		}
		//走势.write(data)
        $('#qudao').highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: title
            },
            xAxis: {
                categories: eval(cat)
            },
            yAxis: {
                title: {
                    text: '消费金额（元）'
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series:eval(data)
        });
} 

$(function () {
	//渠道
	var table  = $("#qudaotable")
	var trs  = table.find("tr")
	var tds  = table.find("td")
	var cat  = ''

	//Y轴固定		
	for(i=1;i<(tds.length/trs.length);i++)
	{
		cat+=",'"+$("#qudaotitle").find("th").eq(i).text()+"'";
	}
	var cat = "["+cat.substring(1)+"]"
    
    //其他来源
	var tr = table.find("tr")
	var data = sdata  = '';
    for(i=0;i<(tr.length-1);i++)
    {
        var title =  tr.eq(i).find("td").eq(0).text();
        td = tr.eq(i).find("td")
        var data = '';
        for(n=1;n<(tds.length/trs.length);n++)
        {
             data+=","+Number(td.eq(n).text());  
        }
        
        var data= "["+data.substring(1)+"]"
        
       sdata+=",{name:'"+title+"',data:"+data+"}";
       
    }
	 qdChart("["+sdata.substring(1)+"]",title,cat)
    
});
</script>
</body>
</html>

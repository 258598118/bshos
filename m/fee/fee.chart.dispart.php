<?php 
/**
 * 客服病种走势
 * 未完，缺少图表
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
$kefu = $montharr = array();

//选出前10个客服
$kefu = $db->query("select DISTINCT author ,count(author)as count from $ptable where author !=''AND chengjiao != '0' group by author order by count desc limit 10");
//array_unshift($kefu,array("author"=>'全部',"count"=>''));

$disease = $db->query("select DISTINCT disease.name as name,$ptable.disease_id as id,count($ptable.disease_id)as count from $ptable LEFT JOIN `disease` ON disease.id = $ptable.disease_id where $ptable.disease_id !='' AND $ptable.chengjiao != '0' group by $ptable.disease_id order by count desc limit 15");
array_unshift($disease,array("name"=>'总计','id'=>'',"count"=>''));

for ($i = 0; $i < 10; $i++) {
	$m = strtotime("-".$i." month");
	$montharr[date("Y-m", $m)] = array(strtotime(date("Y-m-01 00:00:00", $m)), strtotime(date("Y-m-31 23:59:59", $m)));
}


//统计结果
function mediadata($auhtor ='',$project = '',$feedate = '')
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
	if(($project === "全部"||$project === "总计"||$project === "")&&isset($feedate))
	{
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ftable.net_author = '$auhtor'";
		$sumfee = $db->query_first("select sum($ftable.s_charge) as sum from $ftable $where","sum");
		return intval($sumfee['sum']);
	}

	//具体来源
	if(isset($project)&&isset($feedate))
	{

		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ptable.author = '$auhtor' AND $ptable.disease_id='$project'";
		$sumfee = $db->query_first("select $ptable.author,sum($ftable.s_charge) as sum from $ftable $leftjoin $where group by $ptable.author","sum");
		//return "select $ptable.author,sum($ftable.s_charge) as sum from $ftable $leftjoin $where group by $ptable.author";
		return intval($sumfee['sum']);
	}

}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<title>客服绩效走势图</title>
<?php foreach ($common_bootstrap as $z){echo $z;}?>
<?php foreach ($easydialog as $x){echo $x;}?>
<style>
#common_form{text-align:center}
.breadcrumb ul { margin: 0 }
</style>
<script>
var accordion = <?php echo count($kefu)?>
</script>
</head>
<body>
    <header class="jumbotron subhead" style="margin-bottom: 10px;">
        <div class="breadcrumb">
            <ul>
				<li><a href="javascript:void(0)" onclick="history.back()">返回</a> <span class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?php echo $hospital_id_name[$user_hospital_id];?></span>-客服病种走势表</li>
			</ul>
        </div>
    </header>

    <!-- MAIN  START -->
    <div class="accordion" id="accordion2">
        <?php foreach($kefu as $key => $q):?>    
        <div class="accordion-group">
              <div class="accordion-heading">
                   <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $key;?>">
                    <?php echo $q['author'];?>
                   </a>
              </div>
              <div id="collapse<?php echo $key;?>" class="accordion-body collapse <?php echo $key===0?'in':''?>">
                    <div class="accordion-inner">
                        <!--  TABLE START -->
                        <section>
                            <div class="page-header"><h3 class="text-error">各病种绩效走势</h3></div>
                            <table class="table table-striped">
				                <thead id="thtitle<?php echo $key;?>">
				                    <tr>
				                        <th>日期\病种</th>
				                        <?php foreach($disease as $k=>$v):?>
				                        <th><?php echo $v['name']?></th>
				                        <?php endforeach;?> 
				                    </tr>
				              </thead>
				              
				              <tbody id="thtbody<?php echo $key;?>">
				                  <?php foreach(array_reverse($montharr) as $mk =>$m):?>
				                  <tr>
				                      <td><b><?php echo $mk;?></b></td> 
				                      <?php 
				                      foreach($disease as $v)
				                      {
				                  	      echo "<td>".mediadata($q['author'],$v['id'],$mk)."</td>";
				                      }
				                      ?>
				                </tr> 
				                <?php endforeach?>
				              </tbody>
			               </table>
                        </section>
                        <!-- TABLE END -->
                        
                        <!-- CHART START -->
                        <!-- 
                        <section>
                            <div class="page-header"><h3 class="text-error">各病种绩效走势图</h3></div>
                            <div class="row-fluid" style="overflow-x:auto">
                                <div class="span8" id="line<?php echo $key;?>">
                                </div>
                                <div class="span5" id="area<?php echo $key;?>">
                            </div>
                        </section>
                         -->    
                        <!-- CHART END -->
                    </div>
              </div>
        </div>           
        <?php endforeach?>            
                
    </div>
    <!-- MAIN END -->
    
<script src="/static/Highcharts/js/highcharts.js"></script>
<script src="/static/Highcharts/js/modules/exporting.js"></script>
<script>
		                  
function linechart(data,title,cat,ai)
{
		var data,title,cat;
		if(typeof(data) == "undefined" ){
			data=''
			cat = ''
			alldata = ''	
			title = '数据生成出错，检查数据源'
		}else{
			data=data
			title = '客服绩效走势对比图'
			cat = cat
		}
		//走势.write(data)
        $('#line'+ai).highcharts({
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
                    text: '绩效（元）'
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

function areachart(data,title,cat,ai)
{
		var data,title,cat;
		if(typeof(data) == "undefined" ){
			data=''
			cat = ''
			alldata = ''	
			title = '数据生成出错，检查数据源'
		}else{
			data=data
			title = '客服绩效走势对比图'
			cat = cat
		}
		//走势.write(data)
        $('#line'+ai).highcharts({
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
                    text: '绩效（元）'
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
/*
$(function () {
    for(ai=0;ai<accordion;ai++){
		var table  = $("#thtbody"+ai)
		var thtitle  = $("#thtitle"+ai)
		var trs  = table.find("tr")
		var tds  = table.find("td")
		var cat  = ''
	
		//Y轴固定		
		for(i=1;i<(tds.length/trs.length);i++)
		{
			//title+ =,thtitle.find("th").eq(i).text());
			cat+=",'"+trs.eq(i).find("td").eq(0).text()+"'";           
			
		}
		var cat = "["+cat.substring(1)+"]"

		//x轴
		var data = sdata  = '';
		
	    for(i=0;i<(trs.length-1);i++)
	    {
	        var title =  trs.eq(i).find("td").eq(0).text();
	        td = trs.eq(i).find("td")
	        var data = '';
	        for(n=1;n<(tds.length/trs.length);n++)
	        {
	             data+=","+Number(td.eq(n).text());  
	        }
	        
	        var data= "["+data.substring(1)+"]"
	        
	       sdata+=",{name:'"+title+"',data:"+data+"}";
	       
	    }
	    linechart("["+sdata.substring(1)+"]",title,cat,ai)
    }
});
*/
</script>
</body>
</html>

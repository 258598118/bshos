<?php 
/**
 * �ͷ���������
 * δ�꣬ȱ��ͼ��
 * 
 * @author fangyang(278294861)
 * @since  2013-07
 */

require "../../core/core.php";
if ($user_hospital_id == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}
$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');
$ptable = 'patient_'.$user_hospital_id;
$ftable = 'patient_fee';
$leftjoin = "LEFT JOIN $ptable ON $ftable.aid = $ptable.id";
$kefu = $montharr = array();

//ѡ��ǰ10���ͷ�
$kefu = $db->query("select DISTINCT author ,count(author)as count from $ptable where author !=''AND chengjiao != '0' group by author order by count desc limit 10");
//array_unshift($kefu,array("author"=>'ȫ��',"count"=>''));

$disease = $db->query("select DISTINCT disease.name as name,$ptable.disease_id as id,count($ptable.disease_id)as count from $ptable LEFT JOIN `disease` ON disease.id = $ptable.disease_id where $ptable.disease_id !='' AND $ptable.chengjiao != '0' group by $ptable.disease_id order by count desc limit 15");
array_unshift($disease,array("name"=>'�ܼ�','id'=>'',"count"=>''));

for ($i = 0; $i < 10; $i++) {
	$m = strtotime("-".$i." month");
	$montharr[date("Y-m", $m)] = array(strtotime(date("Y-m-01 00:00:00", $m)), strtotime(date("Y-m-31 23:59:59", $m)));
}


//ͳ�ƽ��
function mediadata($auhtor ='',$project = '',$feedate = '')
{
	global $db,$ftable,$ptable,$leftjoin;
	$result = 0;

	//���µĵ�һ��
	$firstdate = $feedate."-01";
	$starttime = strtotime(date($firstdate,strtotime("now")));
	//���µ����һ��
	$endtime = strtotime("$firstdate +1 month -1 day");

	if(!isset($poject)&&!isset($feedate))
	{
		return $result;
	}

	//�ϼ�����
	if(($project === "ȫ��"||$project === "�ܼ�"||$project === "")&&isset($feedate))
	{
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ftable.net_author = '$auhtor'";
		$sumfee = $db->query_first("select sum($ftable.s_charge) as sum from $ftable $where","sum");
		return intval($sumfee['sum']);
	}

	//������Դ
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
<title>�ͷ���Ч����ͼ</title>
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
				<li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?php echo $hospital_id_name[$user_hospital_id];?></span>-�ͷ��������Ʊ�</li>
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
                            <div class="page-header"><h3 class="text-error">�����ּ�Ч����</h3></div>
                            <table class="table table-striped">
				                <thead id="thtitle<?php echo $key;?>">
				                    <tr>
				                        <th>����\����</th>
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
                            <div class="page-header"><h3 class="text-error">�����ּ�Ч����ͼ</h3></div>
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
			title = '�������ɳ����������Դ'
		}else{
			data=data
			title = '�ͷ���Ч���ƶԱ�ͼ'
			cat = cat
		}
		//����.write(data)
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
                    text: '��Ч��Ԫ��'
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
			title = '�������ɳ����������Դ'
		}else{
			data=data
			title = '�ͷ���Ч���ƶԱ�ͼ'
			cat = cat
		}
		//����.write(data)
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
                    text: '��Ч��Ԫ��'
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
	
		//Y��̶�		
		for(i=1;i<(tds.length/trs.length);i++)
		{
			//title+ =,thtitle.find("th").eq(i).text());
			cat+=",'"+trs.eq(i).find("td").eq(0).text()+"'";           
			
		}
		var cat = "["+cat.substring(1)+"]"

		//x��
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

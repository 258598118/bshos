<?php 
/**
 * �ͷ���Ч���ƶԱ���ϸ
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
array_unshift($kefu,array("author"=>'ȫ��',"count"=>''));


for ($i = 0; $i < 12; $i++) {
	$m = strtotime("-".$i." month");
	$montharr[date("Y-m", $m)] = array(strtotime(date("Y-m-01 00:00:00", $m)), strtotime(date("Y-m-31 23:59:59", $m)));
}


//ͳ�ƽ��
function mediadata($project = '',$feedate = '')
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
	if($project == "ȫ��"&&isset($feedate))
	{
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime";
		$sumfee = $db->query_first("select sum($ftable.s_charge) as sum from $ftable $where","sum");
		return intval($sumfee['sum']);
	}
	
	//������Դ
	if(isset($project)&&isset($feedate))
	{
		
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ptable.author = '$project'";
		$sumfee = $db->query_first("select $ptable.author,sum($ftable.s_charge) as sum from $ftable $leftjoin $where group by $ptable.author","sum");
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
</head>
<body>
    <header class="jumbotron subhead" style="margin-bottom: 10px;">
        <div class="breadcrumb">
            <ul>
				<li><a href="javascript:void(0)" onclick="history.back()">����</a> <span class="divider">/</span></li>
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?php echo $hospital_id_name[$user_hospital_id];?></span>-�ͷ���Ч��</li>
			</ul>
        </div>
    </header>
    <!-- �������� START -->
    <div class="row-fluid">
        <div class="page-header"><h3 class="text-error">�ͷ���Ч�Ա����Ʊ�</h3></div>
        <div class="row-fluid">
            <div class="span12" style="height:405px;overflow-y:auto">
                <table class="table table-striped">
		              <thead id="kefutitle">
		                <tr>
		                  <th>�ͷ�\����</th>
		                  <?php foreach(array_reverse($montharr) as $k=>$v):?>
		                  <th><?php echo $k?></th>
		                  <?php endforeach;?> 
		                </tr>
		              </thead>
		              <tbody id="kefutable">
		                <?php foreach($kefu as $q):?>
		                <tr>
		                  <td><b><?php echo $q['author'];?></b></td> 
		                  <?php 
		                  foreach(array_reverse($montharr) as $k=>$v)
		                  {
		                  	echo "<td>".mediadata($q['author'],$k)."</td>";
		                  }
		                  
		                  ?>
		                </tr> 
		                <?php endforeach?>
		              </tbody>
	            </table>
            </div>
        </div>
    </div>
    <!-- �������� END -->
    
    <section>
        <div class="page-header"><h3 class="text-error">�ͷ���Ч�Ա�����ͼ��</h3></div>
        <div class="row-fluid">
            <div class="span12" id="kefu">
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
			title = '�������ɳ����������Դ'
		}else{
			data=data
			title = '�ͷ���Ч���ƶԱ�ͼ'
			cat = cat
		}
		//����.write(data)
        $('#kefu').highcharts({
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

$(function () {
	//����
	var table  = $("#kefutable")
	var trs  = table.find("tr")
	var tds  = table.find("td")
	var cat  = ''

	//Y��̶�		
	for(i=1;i<(tds.length/trs.length);i++)
	{
		cat+=",'"+$("#kefutitle").find("th").eq(i).text()+"'";
	}
	var cat = "["+cat.substring(1)+"]"
    
    //������Դ
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

<?php 
/**
 * ������ɡ�������
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

$qudao = $montharr = array();

//ѡ��ǰ8��������ߵ���Դ
$qudao = $db->query("select DISTINCT media_from ,count(media_from)as count from $ptable where media_from !='' group by media_from  order by count desc limit 8");
//ѡ��ǰ10��������ߵ���Դ
$disease = $db->query("select DISTINCT disease.name as name,$ptable.disease_id as id,count($ptable.disease_id)as count from $ptable LEFT JOIN `disease` ON disease.id = $ptable.disease_id where $ptable.disease_id !='' group by $ptable.disease_id order by count desc limit 10");
//ѡ��ǰ10���ͷ�
$kefu = $db->query("select DISTINCT author ,count(author)as count from $ptable where author !=''AND chengjiao != '0' group by author order by count desc limit 10");
for ($i = 0; $i < 10; $i++) {
	$m = strtotime("-".$i." month");
	$montharr[date("Y-m", $m)] = array(strtotime(date("Y-m-01 00:00:00", $m)), strtotime(date("Y-m-31 23:59:59", $m)));
}


//��������
function feedata($project = '',$feedate = '')
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
	if(!isset($project)&&isset($feedate))
	{
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime";
		$sumfee = $db->query_first("select sum($ftable.s_charge) as sum from $ftable $where","sum");
		return intval($sumfee['sum']);
	}
	
	//������Դ
	if(isset($project)&&isset($feedate))
	{
		
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ptable.media_from = '$project'";
		$sumfee = $db->query_first("select $ptable.media_from,sum($ftable.s_charge) as sum from $ftable $leftjoin $where group by $ptable.media_from","sum");
		return intval($sumfee['sum']);
	}
	
}

//����
function diseasedata($disease = '',$feedate = '')
{
	global $db,$ftable,$ptable,$leftjoin;
	$result = 0;
	
	//���µĵ�һ��
	$firstdate = $feedate."-01";
	$starttime = strtotime(date($firstdate,strtotime("now")));
	//���µ����һ��
	$endtime = strtotime("$firstdate +1 month -1 day");
	
	if(!isset($disease)&&!isset($feedate))
	{
		return $result;
	}
		
	//������Դ
	if(isset($disease)&&isset($feedate))
	{
	
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ptable.disease_id = '$disease'";
		$sumfee = $db->query_first("select $ptable.disease_id,sum($ftable.s_charge) as sum from $ftable $leftjoin $where group by $ptable.disease_id","sum");
		return intval($sumfee['sum']);
	}
}

//�ͷ�
function kefudata($kefudata = '',$feedate = '')
{
	global $db,$ftable,$ptable,$leftjoin;
	$result = 0;
	
	//���µĵ�һ��
	$firstdate = $feedate."-01";
	$starttime = strtotime(date($firstdate,strtotime("now")));
	//���µ����һ��
	$endtime = strtotime("$firstdate +1 month -1 day");
	
	if(!isset($kefudata)&&!isset($kefudata))
	{
		return $result;
	}
	
	//������Դ
	if(isset($kefudata)&&isset($kefudata))
	{
	
		$where = "WHERE $ftable.cj_time>=$starttime AND $ftable.cj_time<=$endtime AND $ptable.author = '$kefudata'";
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
<title>������ɱ�</title>
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
				<li class="active"><span style="color: #0088cc; font-weight: bolder"><?php echo $hospital_id_name[$user_hospital_id];?></span>-������ɱ�</li>
			</ul>
        </div>
    </header>
    <!-- �������� START -->
    <div class="row-fluid">
        <div class="page-header"><h3 class="text-error">�����������</h3></div>
        <div class="row-fluid">
            <div class="span7" style="height:405px;overflow-y:auto">
                <table class="table table-striped">
		              <thead id="qudaotitle">
		                <tr>
		                  <th>����</th>
		                  <th>�ϼ�</th>
		                  <?php foreach($qudao as $q):?>
		                  <th><?php echo $q['media_from'];?></th>
		                  <?php endforeach;?> 
		                </tr>
		              </thead>
		              <tbody id="qudaotable">
		                <?php foreach($montharr as $k => $v):?>
		                <tr>
		                  <td><b><?php echo $k;?></b></td>
		                  <td><?php echo feedata(null,$k)?></td>
		                  
		                  <?php 
		                  foreach($qudao as $q)
		                  {
		                  	echo "<td>".feedata($q['media_from'],$k)."</td>";
		                  }
		                  
		                  ?>
		                </tr> 
		                <?php endforeach?>
		              </tbody>
	            </table>
            </div>
            <div class="span5" id="qudao">
            </div>
        </div>
    </div>
    <!-- �������� END -->
    
    <!-- ���Ѳ��� START -->
    <div class="row-fluid">
        <div class="page-header"><h3 class="text-error">���Ѳ������</h3></div>
        <div class="row-fluid">
            <div class="span7">
                <table class="table table-striped">
		              <thead id="diseasetitle">
		                <tr>
		                  <th>����</th>
		                  <?php foreach($disease as $q):?>
		                  <th><?php echo $q['name'];?></th>
		                  <?php endforeach;?> 
		                </tr>
		              </thead>
		              <tbody id="diseasetable">
		                <?php foreach($montharr as $k => $v):?>
		                <tr>
		                  <td><b><?php echo $k;?></b></td>
		                  <?php 
		                  foreach($disease as $q)
		                  {
		                  	echo "<td>".diseasedata($q['id'],$k)."</td>";
		                  }
		                  
		                  ?>
		                </tr> 
		                <?php endforeach?>
		              </tbody>
	            </table>
            </div>
            <div class="span5" id="disease">
                
            </div>
        </div>
    </div>
    <!-- ���Ѳ��� END -->
    
    <!-- ���ѿͷ� START -->
    <div class="row-fluid">
        <div class="page-header"><h3 class="text-error">�ͷ���Ч���</h3></div>
        <div class="row-fluid">
            <div class="span7">
                <table class="table table-striped">
		             <thead id="kefutitle">
		                <tr>
		                  <th>����</th>
		                  <?php foreach($kefu as $q):?>
		                  <th><?php echo $q['author'];?></th>
		                  <?php endforeach;?> 
		                </tr>
		              </thead>
		              <tbody id="kefutable">
		                <?php foreach($montharr as $k => $v):?>
		                <tr>
		                  <td><b><?php echo $k;?></b></td>
		                  <?php 
		                  foreach($kefu as $q)
		                  {
		                  	echo "<td>".kefudata($q['author'],$k)."</td>";
		                  }
		                  
		                  ?>
		                </tr> 
		                <?php endforeach?>
		             </tbody>
	            </table>
            </div>
            <div class="span5" id="kefu">
            </div>
        </div>
    </div>
    <!-- ���ѿͷ� END -->
    
<script src="/static/Highcharts/js/highcharts.js"></script>
<script src="/static/Highcharts/js/modules/exporting.js"></script>
<script>
		                  
function qdChart(data,title)
{
	var partd,parttitle;
	if(typeof(data) == "undefined" ){
		partd='';
		parttitle = '��ѡ��һ����ʾ��ͼ'
	}else{
		partd=data;	
		parttitle = title+'�·�����������ͼ';
	}

    $('#qudao').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: parttitle
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
        	percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+  Math.round(this.percentage*10)/10 +' %';
                    }
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: '�ٷֱ�',
            data: eval(partd)
        }]
    })
} 

function bzChart(data,title)
{
	var partd,parttitle;
	if(typeof(data) == "undefined" ){
		partd='';
		parttitle = '��ѡ��һ����ʾ��ͼ'
	}else{
		partd=data;	
		parttitle = title+'�·ݲ��ֱ�ͼ';
	}

    $('#disease').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: parttitle
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
        	percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+  Math.round(this.percentage*10)/10 +' %';
                    }
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: '�ٷֱ�',
            data: eval(partd)
        }]
    })
}    
function kfChart(data,title)
{
	var partd,parttitle;
	if(typeof(data) == "undefined" ){
		partd='';
		parttitle = '��ѡ��һ����ʾ��ͼ'
	}else{
		partd=data;	
		parttitle = title+'�·ݿͷ���Ч��ͼ';
	}

    $('#kefu').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: parttitle
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
        	percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+  Math.round(this.percentage*10)/10 +' %';
                    }
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: '�ٷֱ�',
            data: eval(partd)
        }]
    })
}

$(function () {
	qdChart();bzChart();kfChart();
	//����
	var table  = $("#qudaotable");	
	var trs  = table.find("tr");
	var tds  = table.find("td");
	
    trs.bind("click", function(){
        var td = $(this).find("td");
        var title = td.eq(0).text()
        var data   = '';
        for(i=2;i<(tds.length/trs.length)-1;i++)
        {
            
             data+=",['"+$("#qudaotitle").find("th").eq(i).text()+"',"+td.eq(i).text()+"]";   
                
        }
        var data = "["+data.substring(1)+"]";
        qdChart(data,title)
    });
    
    //����
    var table  = $("#diseasetable");	
	var trs  = table.find("tr");
	var tds  = table.find("td");
	
    trs.bind("click", function(){
        var td = $(this).find("td");
        var title =td.eq(0).text()
        var data   = '';
        for(i=1;i<(tds.length/trs.length)-1;i++)
        {
            
             data+=",['"+$("#diseasetitle").find("th").eq(i).text()+"',"+td.eq(i).text()+"]";   
                
        }
        var data = "["+data.substring(1)+"]"
        bzChart(data,title)
    }); 

    //�ͷ�
    var table  = $("#kefutable");	
	var trs  = table.find("tr");
	var tds  = table.find("td");
	
    trs.bind("click", function(){
        var td = $(this).find("td");
        var title =td.eq(0).text()
        var data   = '';
        for(i=1;i<(tds.length/trs.length)-1;i++)
        {
            
             data+=",['"+$("#kefutitle").find("th").eq(i).text()+"',"+td.eq(i).text()+"]";   
                
        }
        var data = "["+data.substring(1)+"]"
        kfChart(data,title)
    });
    
});
</script>
</body>
</html>

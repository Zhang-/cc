<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller.min.js"></script> 
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/highcharts.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="sitelogin">
<div class="alert_iframe">
<div class="exprot_box exprot_boxs"> 
			<span class="load_out" title="导出表格" onclick="exprot()"></span>
			<div id="img_box" style="display: none;"></div>
			<div id="adminshow"></div>
		</div>
<script type='text/javascript'>
function exprot(){
		$(".load_out").attr("onclick","");
		$("#img_box").show();
		var url='index.php?r=sysmanage/dataoutput';
		var j=0;
		var eptime = setInterval(function(){
			var t = j%100;
			var s = Math.floor(j/100);
			var g = s+"."+t+"秒";
			$("#img_box").html(g);
			j++;	
		},10);			
		$.ajax({
			type : "GET",
			url : url+"&time=<?php echo $_GET['data'];?>",
			success : function(data){
				$("#adminshow").html(data);
				clearInterval(eptime);
				var timeer = setInterval(function(){
					$("#img_box").fadeOut();
					clearInterval(timeer);
					$(".load_out").attr("onclick","exprot()");
				},3000);
			} 
		});
	}
</script>
<?php
$excel_json=Yii::app()->db->createCommand("select error_jason from excel_error_info where time='".$_GET['data']."'")->queryrow();
$excel_date=json_decode($excel_json['error_jason'],true);
$attr=array('cell_name','lac','cellId','lng','lat','angle','error');
		$excel_error_prod=new CArrayDataProvider($excel_date, array(
			'pagination'=>array(
            	'pageSize'=>10,//设置每页显示条数
        	),
			'sort'=>array(
				'attributes'=>$attr,
			),
		));
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'tag_grid_other',
			'cssFile'=>false,
			'dataProvider'=>$excel_error_prod,
			'columns'=>array(		
				'cell_name:raw:基站名',
				'lac:raw:lac',
				'cellId:raw:cellId',
				'lng:raw:纬度',
				'lat:raw:经度',
				'angle:raw:角度',
				'error:raw:错误',
			),	
			'summaryText'=>'第 {start}-{end} 条, 共 {count} 条 当前第 {page} 页，共 {pages} 页',
			'pager'=>array(
				'class'=>'CLinkPager',
				'header'=>'',
				'firstPageLabel'=>'首页',
				'prevPageLabel'=>'上一页',
				'nextPageLabel'=>'下一页',
				'lastPageLabel'=>'尾页',
				'cssFile'=>false,
			),
			'template'=>'{summary}{items}{pager}'
		)); 
	
?>
    
	</div>

</body>
</html>
 
	

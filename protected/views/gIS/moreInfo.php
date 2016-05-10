<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/highstock.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" /> 
<title>详细信息</title>  
</head>  

<body class="alert_iframes" style="padding:4px; background:#fff">
    <div style="height:400px;overflow-y:scroll;overflow-x:hidden">
	<div id="moreInfoTable" >
	<?php
		if( $page == 'pingPongSwitch' && $layerType == 'sites' ){
			$data_Provider=new CArrayDataProvider($moreData, array(
				'id'=>'ppinfo',
				'sort'=>array(
					'attributes'=>array('id','launchLac','launchCellid','receiveLac','receiveCellid','ppNumber','ppLaunchRssi','ppReceiveRssi','launchHtmlValue','receiveHtmlValue'),
				),
				'pagination'=>array(
					'pageSize'=>6,
				)
			));
			$ri=$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'dataview-grid',
				'dataProvider'=>$data_Provider,
				'itemsCssClass'=>'table_content duboule',
				//'cssFile'=>false,
				'columns'=>array(
					array(
						'type'=>'raw',
						'name'=>'发起小区(Lac,Cellid)',
						'value'=>'getValueStyle($data,0)',
					),
					array(
						'type'=>'raw',
						'name'=>'接收小区(Lac,Cellid)',
						'value'=> 'getValueStyle($data,1)',
					),
					array(
						'name'=>'平均切换次数',
						'value'=> '$data["ppNumber"]',
					),
					array(
						'name'=>'发起小区平均信号值',
						'value'=> '$data["ppLaunchRssi"]',
					),
					array(
						'name'=>'接收小区平均信号值',
						'value'=> '$data["ppReceiveRssi"]',
					),	 	
				),
				'pager'=>array(
					'maxButtonCount'=>2,
					'class'=>'CLinkPager',
					'header'=>'',
					'firstPageLabel'=>'<b>|<</b>',
					'prevPageLabel'=>'<b><</b>',
					'nextPageLabel'=>'<b>></b>',
					'lastPageLabel'=>'<b>>|</b>',
					'cssFile'=>false,
				),
				'template'=>'{items}{pager}'	
			));
		}
		
		if( $page == 'allReselect' ){
	?>
			<p style="text-align:center;padding:4px"> <font color=#336699><font size=3> <?php echo $moreInfoTableTitle; ?>  </font> </font> </p>
	<?php
			$data_Provider=new CArrayDataProvider($moreData, array(
				'id'=>'reselectinfo',
				'sort'=>array(
					'attributes'=>array('id','g2tReselectNumber','t2gReselectNumber','g2fourReselectNumber','four2gReselectNumber','t2fourReselectNumber','four2tReselectNumber'),
				),
				'pagination'=>array(
					'pageSize'=>6,
				)
			));
			
			$ri=$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'dataview-grid',
				'dataProvider'=>$data_Provider,
				'itemsCssClass'=>'table_content duboule',
				// 'cssFile'=>false,
				'columns'=>array(
					'g2tReselectNumber:raw:2G->3G重选次数',	
					't2gReselectNumber:raw:3G->2G重选次数',
					'g2fourReselectNumber:raw:2G->4G重选次数',	
					'four2gReselectNumber:raw:4G->2G重选次数',
					't2fourReselectNumber:raw:3G->4G重选次数',	
					'four2tReselectNumber:raw:4G->3G重选次数',
				),
				'pager'=>array(
					'maxButtonCount'=>2,
					'class'=>'CLinkPager',
					'header'=>'',
					'firstPageLabel'=>'<b>|<</b>',
					'prevPageLabel'=>'<b><</b>',
					'nextPageLabel'=>'<b>></b>',
					'lastPageLabel'=>'<b>>|</b>',
					'cssFile'=>false,
				),
				'template'=>'{items}{pager}'	
			));
		}
		
		if( $page == 'siteBusiness' ){
	?>
			<p style="text-align:center;padding:4px"> <font color=#336699><font size=3> <?php echo $moreInfoTableTitle; ?>  </font> </font> </p>
	<?php
			$data_Provider=new CArrayDataProvider($moreData, array(
				'id'=>'siteBusinessinfo',
				'sort'=>array(
					'attributes'=>array('id','lac','cellId','speechTraffic','dataTraffic','wirelessRate'),
				),
				'pagination'=>array(
					'pageSize'=>12,
				)
			));
			
			$ri=$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'dataview-grid',
				'dataProvider'=>$data_Provider,
				'itemsCssClass'=>'table_content duboule',
				// 'cssFile'=>false,
				'columns'=>array(
					'lac:raw:LAC',	
					'cellId:raw:CELLID',
					'speechTraffic:raw:话务量(Erl)',	
					'dataTraffic:raw:数据流量(MB)',
					'wirelessRate:raw:无线利用率(%)',	
				),
				'pager'=>array(
					'maxButtonCount'=>2,
					'class'=>'CLinkPager',
					'header'=>'',
					'firstPageLabel'=>'<b>|<</b>',
					'prevPageLabel'=>'<b><</b>',
					'nextPageLabel'=>'<b>></b>',
					'lastPageLabel'=>'<b>>|</b>',
					'cssFile'=>false,
				),
				'template'=>'{items}{pager}'	
			));
		}
	?>
	
	</div>
	
   <div id="container" title="曲线图" style="width:auto;height:auto;margin-top:5px;"></div>
   <div id="gispiechart" title="饼状图" style="margin-top:5px;display:none;border:1px solid #4D759E;border-radius:4px">
   </div>
   </div>
</body>


<script type="text/javascript">
var page = <?php echo json_encode($page); ?>;
var moreInfoConfig = <?php echo json_encode($moreInfoConfig); ?>;
var allData = <?php echo json_encode($allData); ?>;

// 显示折线图
if( moreInfoConfig.stockChart){
	
	var seriesCounter = 0,seriesOptions = [] ;
	var	stockData = allData.stockChart;
	var pixelInterval = 100,dataCount = 0;
	
	if( typeof(stockData)!= 'undefined' ){
		$.each(stockData, function(name, data) {
			if( page == 'complain' || page == 'pingPongSwitch'||page == 'allReselect'){
				name = name + moreInfoConfig.stockChart.xTag;
			}else{
				name = moreInfoConfig.stockChart.xTag;
			}
			seriesOptions[seriesCounter] = {
				name: name,
				data: data,
				marker : {
							enabled : true,
							radius : 3
					},
				shadow : true
			};
			if( data.length > dataCount){
				dataCount = data.length;
			}
			seriesCounter++;
		});
	}else{
		seriesOptions = [{
	        name: moreInfoConfig.stockChart.xTag,//鼠标移到趋势线上时显示的属性名
	        data: stockData,//属性值
			marker : {
					enabled : true,
					radius : 3
			},
			shadow : true
	    }]
	}
	
	
	// create the chart when all data is loaded
	$(function() {
		if(dataCount < 9){
			pixelInterval = 300;
		}else if( dataCount >= 9 && dataCount < 18){
			pixelInterval = 100;
		}else{
			pixelInterval = 200;
		}
		Highcharts.theme = {
			chart: {
				height: 400,
				backgroundColor: {
					 linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					 stops: [
						[0, 'rgb(255, 255, 255)'],
						[1, 'rgb(240, 240, 255)']
					 ]
				  },
				  borderWidth: 1,
				  plotBackgroundColor: 'rgba(254 , 252, 245, .9)', 
				  plotShadow: false,
				  plotBorderWidth: 1
			}
		};
	 // Apply the theme
		var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
		
		function hideZoomBar(chart) {
			if(chart.rangeSelector.zoomText)
				chart.rangeSelector.zoomText.hide();
			//$('.highcharts-input-group').hide();
		};
		
		var chart = new Highcharts.StockChart({
			chart: {
					renderTo: 'container',
					type: 'spline'
				},
			exporting: {  
				enabled: false //是否能导出趋势图图片
			}, 
			title : {
				text : moreInfoConfig.stockChart.chartName//图表标题
			}, 
			labels:{//在报表上显示的一些文本  
				items:[{  
					html:'区域编号: '+moreInfoConfig.queryId,  
					style:{left:'220px',top:'20px',color:'#ccc'}  
				}, {  
					html:'区域名称: '+moreInfoConfig.areaName,  
					style:{left:'220px',top:'55px',color:'#ccc'}  
				}]  
			},  
			subtitle: {  
				text: moreInfoConfig.startDate +' 至 '+ moreInfoConfig.endDate,//副标题  
				x: 110,//副标题位置  
				y: 42//副标题位置  
			}, 
			credits:{//右下角的文本  
				enabled: false
			},  
			xAxis: {
				tickPixelInterval: pixelInterval,//x轴上的间隔
				lineColor: '#868686',
				lineWidth: 2,
				title :{
					text:"日期"
				},
				type: 'datetime', //定义x轴上日期的显示格式
				labels: {
					formatter: function() {
						var vDate=new Date(this.value);
						//alert(this.value);
						return vDate.getFullYear()+"-"+(vDate.getMonth()+1)+"-"+vDate.getDate();
					}, 
					align: 'center'
				}
			},
			yAxis : {  
				// tickInterval: 5,  //自定义刻度  
				// max:100,//纵轴的最大值  
				// min: 0,//纵轴的最小值  
				title: {  
					 text: moreInfoConfig.stockChart.yTag  //y轴上的标题
				}  
			 },  
			tooltip: {
				xDateFormat: '%Y-%m-%d'//鼠标移动到趋势线上时显示的日期格式
			},
			 rangeSelector: {
				buttons: [{//定义一组buttons,下标从0开始
				type: 'week',
				count: 1,
				text: '一周'
			},{
				type: 'week',
				count: 2,
				text: '二周'
			}, {
				type: 'week',
				count: 3,
				text: '三周'
			}, {
				type: 'week',
				count: 4,
				text: '四周'
			}, {
				type: 'all',
				text: '全部'
			}],
				inputEnabled: false,
				selected: 4//表示以上定义button的index,从0开始
			}, 
			
			navigator :{
			 xAxis: {  
				tickPixelInterval: 100,//x轴上的间隔  
				type: 'datetime', //定义x轴上日期的显示格式  
				labels: {  
					formatter: function() {  
						var vDate=new Date(this.value);
						var day=vDate.getDate();
						var moth=vDate.getMonth();
						var year=vDate.getFullYear();
						return year+"-"+(moth+1)+"-"+day;  
					},  
					align: 'right'  
				}  
			 }
			},
			series: seriesOptions
		});
		hideZoomBar(chart);
	});
}

//显示饼状图
if( moreInfoConfig.pieChart ){
	$('#gispiechart').show();
	var chart;
	$('#gispiechart').highcharts({
		chart: {
			height: 350,
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: moreInfoConfig.pieChart.chartName //图表标题
		},
		subtitle: {  
			text: moreInfoConfig.startDate +' 至 '+ moreInfoConfig.endDate,//副标题  
			x: 210,//副标题位置  
			y: 40//副标题位置  
		},
		labels:{//在报表上显示的一些文本
			items:[{  
				html:'区域编号: '+moreInfoConfig.queryId,  
				style:{left:'60px',top:'-45px',color:'#ccc'}  
			}, {  
				html:'区域名称: '+moreInfoConfig.areaName,  
				style:{left:'60px',top:'-25px',color:'#ccc'}  
			}]  
		}, 
		credits:{
			enabled: false
		}, 
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true
				},
				showInLegend: true
			}
		},
		series: [{
			type: 'pie',
			name: '最近30天所占平均比例',
			data:  allData.pieChart
		}]
	});
}
parent.window.showFrameDiv(false);
  </script>
</html>  

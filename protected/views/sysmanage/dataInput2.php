<?php 
	isset($_GET['md'])?$md=$_GET['md']:$md='gsm';
	$md=='gsm'?$gsmsel=' select':$gsmsel='';
	$md=='td'?$tdsel=' select':$tdsel='';
	$md=='ref'?$refsel=' select':$refsel='';
	$md=='wrong'?$wrongsel=' select':$wrongsel='';
	/*标签*/
	?>
	
	<ul class='table_menu'>
	<li class="li1<?php echo $gsmsel;?>"><a href='index.php?r=sysmanage/dataInput&md=gsm'>GSM数据操作</a></li>
	<li class="li1<?php echo $tdsel;?>"><a href='index.php?r=sysmanage/dataInput&md=td'>TD数据操作</a></li>
	<li class="li1<?php echo $refsel;?>"><a href='index.php?r=sysmanage/dataInput&md=ref'>关联数据表操作</a></li>
	<li class="li1<?php echo $wrongsel;?>"><a href='index.php?r=sysmanage/dataInput&md=wrong'>更新基站日志</a></li>
	</ul>
	<?PHP
	
	/*导入GSM数据*/
	if($md=='gsm')
	{
		echo '<div class="home_p">'.CHtml::openTag('h2',array('class'=>'input_guide')).'数据导入帮助 ：'.CHtml::closeTag('h2');
		echo CHtml::openTag('div',array('id'=>'tagshow'));
		echo '<p><span></span>表格文件类型 ：Microsoft Office Excel 2003/Microsoft Office Excel 2007</p>';
		echo '<p><span></span>覆盖数据 ：此操作将会清空原有数据</p>';
		echo '<p><span></span>插入数据 ：此操作将在原有数据的基础上插入数据</p>';
		echo '<p><span></span>表格内容 ：<a href="example.xlsx" style="color:blue">点击下载范本文档</a></p>';
		echo '<table class="items">
			<thead>
				<tr>
					<th>cell_name(小区名称)</th>
					<th>lac(LAC)</th>
					<th>cellId(cellId)</th>
					<th>lng(经度)</th>
					<th>lat(纬度)</th>
					<th>angle(角度)</th>
				</tr>
			</thead>
			<tbody>
			<tr>
					<td>华西8号塔A</td>
					<td>20668</td>
					<td>51231</td>
					<td>120.4227</td>
					<td>31.8325</td>
					<td>-1</td>
				</tr>
			</tbody>
		</table>';
		echo CHtml::closeTag('div');
		echo '</div>'; 
		echo '<div class="home_p2"><h2>开始导入GSM数据：</h2>';
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'gsmexcel',
		'htmlOptions'=>array('enctype'=>"multipart/form-data"),
		'enableAjaxValidation'=>false,
		));
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".$_GET['error']."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".$_GET['success']."</div>";
		echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'gsmexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
		echo CHtml::submitButton('上传',array('class'=>'subupdate')).'</p>';
		//解析错误
		if(isset($_GET['tabla_text'])&&isset($_GET['error']))
		{	
			echo "<div class='errorSummary'>".$_GET['error'];
?>
			<div class="exprot_box"> 
				<span class="load_out" title="导出表格" onclick="exprot()"></span>
				<div id="img_box" style="display: none;"></div>
				<div id="adminshow"></div>
			</div>
<?php	
			echo "</div>";
			$excel_json=Yii::app()->db->createCommand("select error_jason from excel_error_info where time='".$_GET['tabla_text']."'")->queryrow();
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
				'id'=>'tag_grid',
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
			)); ?>
			<script type='text/javascript'>
			function exprot(){
					$(".load_out").attr("onclick","");
					$("#img_box").show();
					var url='index.php?r=sysmanage/dataoutput';
					var j=0;
					var eptime = setInterval(function(){
						var t = j%100;
						var s = Math.floor(j/100);
						var g = "用时:"+s+"."+t+"秒";
						$("#img_box").html(g);
						j++;	
					},10);			
					$.ajax({
						type : "GET",
						url : encodeURI(url+"&time=<?php echo $_GET['tabla_text'];?>"),
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
<?php	}
		echo '</div>'; 
		$this->endWidget();	
	}
	//导入TD数据
	elseif($md=='td')
	{
		echo '<div class="home_p">'.CHtml::openTag('h2',array('class'=>'input_guide')).'数据导入帮助 ：'.CHtml::closeTag('h2');
		echo CHtml::openTag('div',array('id'=>'tagshow'));
		echo '<p><span></span>表格文件类型 ：Microsoft Office Excel 2003/Microsoft Office Excel 2007</p>';
		echo '<p><span></span>覆盖数据 ：此操作将会清空原有数据</p>';
		echo '<p><span></span>插入数据 ：此操作将在原有数据的基础上插入数据</p>';
		echo '<p><span></span>表格内容 ：<a href="example.xlsx" style="color:blue">点击下载范本文档</a></p>';
		echo '<table class="items">
			<thead>
				<tr>
					<th>cell_name(小区名称)</th>
					<th>lac(LAC)</th>
					<th>cellId(cellId)</th>
					<th>lng(经度)</th>
					<th>lat(纬度)</th>
					<th>angle(角度)</th>
				</tr>
			</thead>
			<tbody>
			<tr>
					<td>华西8号塔A</td>
					<td>20668</td>
					<td>51231</td>
					<td>120.4227</td>
					<td>31.8325</td>
					<td>-1</td>
				</tr>
			</tbody>
		</table>';
		echo CHtml::closeTag('div');
		echo '</div>'; 
		echo '<div class="home_p2"><h2>开始导入TD数据：</h2>';
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'tdexcel',
		'htmlOptions'=>array('enctype'=>"multipart/form-data"),
		'enableAjaxValidation'=>false,
		));
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".$_GET['error']."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".$_GET['success']."</div>";
		echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'tdexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
		echo CHtml::submitButton('上传',array('class'=>'subupdate')).'</p>';
		/*解析错误*/
		if(isset($_GET['tabla_text'])&&isset($_GET['error']))
		{	
			echo "<div class='errorSummary'>".$_GET['error'];
?>
			<div class="exprot_box"> 
				<span class="load_out" title="导出表格" onclick="exprot()"></span>
				<div id="img_box" style="display: none;"></div>
				<div id="adminshow"></div>
			</div>
<?php	
			echo "</div>";
			$excel_json=Yii::app()->db->createCommand("select error_jason from excel_error_info where time='".$_GET['tabla_text']."'")->queryrow();
			$excel_date=json_decode($excel_json['error_jason'],true);
			$attr=array('cell_name','lac','cellId','lng','lat','angle','error');
			/*解析出错表格*/
			$excel_error_prod=new CArrayDataProvider($excel_date, array(
				'pagination'=>array(
					'pageSize'=>10,
				),
				'sort'=>array(
					'attributes'=>$attr,
				),
			));
			$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'tag_grid',
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
			)); ?>
			<script type='text/javascript'>
			function exprot(){
			$(".load_out").attr("onclick","");
			$("#img_box").show();
			var url='index.php?r=sysmanage/dataoutput';
			var j=0;
			var eptime = setInterval(function(){
				var t = j%100;
				var s = Math.floor(j/100);
				var g = "用时:"+s+"."+t+"秒";
				$("#img_box").html(g);
				j++;	
			},10);			
			$.ajax({
				type : "GET",
				url : encodeURI(url+"&time=<?php echo $_GET['tabla_text'];?>"),
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
<?php	}	
		echo '</div>'; 
		$this->endWidget();
	}
	
	/*生成GIS数据*/
	elseif($md=='ref'){
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".$_GET['error']."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".$_GET['success']."</div>";
		echo '<div class="home_p relation_table">'.CHtml::openTag('div',array('class'=>'td'));
		echo CHtml::openTag('span',array('class'=>'tag')).'关联表是为地图提供数据的，如果您对gsm或td数据表进行了相关的更新操作，请确定更新！更新数据表的时间可能较长（大概二十分钟），请耐心等待！'.CHtml::closeTag('span').'</br>';
		echo CHtml::closeTag('div');
		echo CHtml::submitButton('确定更新',array('class'=>'sub','id'=>'sub')).'</div>'; 	
	} 
	
	/*日志模块*/
	elseif($md=='wrong'){
		$model_excel=new ExcelErrorInfo;
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'tag_grid',
			'cssFile'=>false,
			'dataProvider'=>$model_excel->search(),
			'columns'=>array(		
				'time:raw:时间',
				'type:raw:上传表格类型',
				'operate:raw:上传操作',
				'state:raw:操作状态',
				array(
					'class'=>'CLinkColumn',
					'header'=>'操作',
					'label'=>'查看详情',
					'linkHtmlOptions'=>array('onclick'=>'
					var ti=$(this).parent().parent().find("td:eq(3)").html();
					if(ti.substring(0,6)!="成功为您导入")
					{	$("#dialog").dialog("open");
						$(".dataview").attr("src","index.php?r=sysmanage/showerror&data="+$(this).parent().parent().children("td:first-child").html())
					}
					else
						alert("此次操作"+ti);'),
				)
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
		<div id="dialog" title="查看详情">
			<iframe src="" class="dataview" marginwidth="0" width="1200" height="450" marginheight="0" align="middle" scrolling="No" frameborder="0"></iframe>
		</div>
		<script type="text/javascript">
		$(document).ready(function() {		
				$( "#dialog" ).dialog({
					autoOpen: false,
					minHeight: 300,
					minWidth: 860 ,
					modal: true,
					resizable: false, 
					draggable :true
					});		
			});
		</script>
<?php	
	} ?>
<script type="text/javascript">
$(document).ready(function(){	
	$('#sub').click(function(){
		window.location.href="index.php?r=sysmanage/referdata";	 
	});
});
</script>	 
<?php
	isset($_GET['md'])?$md=$_GET['md']:$md='gsm';
	$md=='gsm'?$gsmsel=' select':$gsmsel='';
	$md=='td'?$tdsel=' select':$tdsel='';
	$md=='four'?$foursel=' select':$foursel='';
	$md=='ref'?$refsel=' select':$refsel='';
	$md=='gis'?$gis=' select':$gis='';
	$md=='wrong'?$wrongsel=' select':$wrongsel='';
	/*标签*/
	?>

	<ul class='table_menu'>
	<li class="li1<?php echo $gsmsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/dataInput&md=gsm'>GSM数据操作</a><p class="p2"></p></li>
	<li class="li1<?php echo $tdsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/dataInput&md=td'>TD数据操作</a><p class="p2"></p></li>
	<li class="li1<?php echo $foursel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/dataInput&md=four'>LTE数据操作</a><p class="p2"></p></li>
	<li class="li1<?php echo $refsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/dataInput&md=ref'>关联数据表操作</a><p class="p2"></p></li>
	<li class="li1<?php echo $gis;?>"><p class="p1"></p><a href='index.php?r=sysmanage/dataInput&md=gis'>地图数据同步</a><p class="p2"></p></li>
	<li class="li1<?php echo $wrongsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/dataInput&md=wrong' >更新基站日志</a><p class="p2"></p></li>
	</ul>		

	<div class='main_table'>
	<?PHP

	/*导入GSM数据*/
	if($md=='gsm')
	{
		echo '<div class="home_p2"><h2 style="border-top:1px solid #ccc">开始导入GSM数据：<span class="help_info" onclick="$( \'#help_info_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
		
		
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'gsmexcel',
		'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('确定进行此操作吗?');"),
		'enableAjaxValidation'=>false,
		));
		echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'gsmexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
		echo CHtml::submitButton('上传',array('class'=>'subupdate','disabled'=>'disabled')).'<a class="subupdate1" href="index.php?r=sysmanage/dataInput"> 刷 新</a></p>';
		echo '<p class="p_4"></p>';
		//解析错误
		$this->endWidget();
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		if(isset($_GET['tabla_text'])&&isset($_GET['error']))
		{
			echo "<div class='errorSummary'>".json_decode($_GET['error']);
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
					'pageSize'=>7,//设置每页显示条数
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

	}
	//导入TD数据
	elseif($md=='td')
	{
		echo '<div class="home_p2"><h2 style="border-top:1px solid #ccc">开始导入TD数据：<span class="help_info" onclick="$( \'#help_info_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'tdexcel',
		'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('确定进行此操作吗?');"),
		'enableAjaxValidation'=>false,
		));
		echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'tdexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
		echo CHtml::submitButton('上传',array('class'=>'subupdate','disabled'=>'disabled')).'<a class="subupdate1" href="index.php?r=sysmanage/dataInput&md=td"> 刷 新</a></p>';
		echo '<p class="p_4"></p>';

		$this->endWidget();
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		/*解析错误*/
		if(isset($_GET['tabla_text'])&&isset($_GET['error']))
		{
			echo "<div class='errorSummary'>".json_decode($_GET['error']);
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
					'pageSize'=>7,
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

	}
	//导入4G数据
	elseif($md=='four')
	{
		echo '<div class="home_p2"><h2 style="border-top:1px solid #ccc">开始导入LTE基站数据：<span class="help_info" onclick="$( \'#help_info_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'tdexcel',
		'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('确定进行此操作吗?');"),
		'enableAjaxValidation'=>false,
		));
		echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'gsmradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
		echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'fourexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
		echo CHtml::submitButton('上传',array('class'=>'subupdate','disabled'=>'disabled')).'<a class="subupdate1" href="index.php?r=sysmanage/dataInput&md=four" > 刷 新</a></p>';
		echo '<p class="p_4"></p>';

		$this->endWidget();
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		/*解析错误*/
		if(isset($_GET['tabla_text'])&&isset($_GET['error']))
		{
			echo "<div class='errorSummary'>".json_decode($_GET['error']);
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
					'pageSize'=>7,
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

	}
	/*生成GIS数据*/
	elseif($md=='ref'){
		if(isset($_GET['error']))
			echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		echo '<div class="home_p relation_table">'.CHtml::openTag('div',array('class'=>'td'));
		echo CHtml::openTag('span',array('class'=>'tag')).'关联表是为地图提供数据的，如果基站有更新或栅格信息表有变动，请及时更新基站表与栅格信息表的关联信息！更新数据表的时间可能较长，请耐心等待！'.CHtml::closeTag('span').'</br>';
		echo CHtml::closeTag('div');
		echo CHtml::submitButton('确定更新',array('class'=>'sub','id'=>'sub','onclick'=>'{if(confirm("进行关联操作时建议不要进行其他操作,确定继续吗?")){$("#referTip").dialog( "open" );}else{return false;}}')).'</div>';
	}
	elseif($md=='gis'){
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		echo '<div class="home_p relation_table">'.CHtml::openTag('div',array('class'=>'td'));
		echo CHtml::openTag('span',array('class'=>'tag')).'地图数据同步是为地图提供数据的，如果您对gsm或td数据表进行了相关的更新操作，请确定更新！更新数据表的时间可能较长，请耐心等待！'.CHtml::closeTag('span').'</br><b style="color:red">此操作期间地图功能可能失效，请务必在系统维护期间执行此操作</b></br>';
		echo CHtml::closeTag('div');
		echo '<div class="updateSitePoints"><INPUT type="checkbox" id="updateSitePoints" name="updateSitePoints" value="1">更新site_points</div>';
		echo CHtml::submitButton('确定同步',array('class'=>'sub','id'=>'gis_sub')).'</div>';
	}

	/*日志模块*/
	elseif($md=='wrong'){
		$model_excel=new ExcelErrorInfo;
		echo '<div class="home_p3">';
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'tag_grid',
			'cssFile'=>false,
			'afterAjaxUpdate'=>'function (id, data) {
			$("#tag_grid_c0").children("a").attr("title","点击按时间排序");
			$("#tag_grid_c1").children("a").attr("title","点击按上传表格类型排序");
			$("#tag_grid_c2").children("a").attr("title","点击按上传操作排序");
			$("#tag_grid_c3").children("a").attr("title","点击按操作状态排序");
			}',
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
		echo '</div>';
		?>
		<div id="dialog" title="查看详情">
			<iframe src="" class="dataview UserViewPage" marginwidth="0" width="1200" height="450" marginheight="0" align="middle" scrolling="No" frameborder="0"></iframe>
		</div>
		<script type="text/javascript">
		$(document).ready(function() {
				$( "#dialog" ).dialog({
					autoOpen: false,
					minWidth: 860 ,
					modal: true,
					resizable: false,
					draggable :true
					});
			});
		</script>
<?php
	} ?>
	</div>
	<div style="display:none" id="help_info_click" title="数据导入帮助">
		<div class="alert_iframe" style="padding:0">		
			<div id="tagshow">
				<p style="color: red;"><span></span><b>表格文件类型</b> ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
				<p><span></span><b>表格内容提示</b> ：‘小区名称’、‘lac’、‘cellId’、‘经度’、‘纬度’、‘角度’不能为空，‘站高’、‘电子倾角’、‘机械倾角’、‘广播控制信道’等可以为空,但GSM中的‘广播控制信道’若为空，则该条记录不导入</p>
				<p><span></span><b>覆盖数据</b> ：此操作将会清空原有数据</p>
				<p><span></span><b>插入数据</b> ：此操作将在原有数据的基础上插入数据</p>
			 <?php if( $md=='gsm'){ ?>
				<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_site_gsm.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>cell_name(小区名称)</th>
							<th>lac(LAC)</th>
							<th>cellId(cellId)</th>
							<th>lng(经度)</th>
							<th>lat(纬度)</th>
							<th>angle(角度)</th>
							<th>height(站高)</th>
							<th>dip_e(电子倾角)</th>
							<th>dip_m(机械倾角)</th>
							<th>bcch(广播控制信道)</th>
							<th>carrierFrequency(逻辑载频数)</th>
							<th>cellularType(蜂窝类型)</th>
							<th>frequencyBand(频段)</th>
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
							<td>20</td>
							<td>12</td>
							<td>12</td>
							<td>60</td>
							<td>4</td>
							<td>室内微蜂窝</td>
							<td>G</td>
						</tr>
					</tbody>
				</table>
			 <?php }elseif( $md=='td'){ ?>
				<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_site_td.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>cell_name(小区名称)</th>
							<th>lac(LAC)</th>
							<th>cellId(cellId)</th>
							<th>lng(经度)</th>
							<th>lat(纬度)</th>
							<th>angle(角度)</th>
							<th>height(站高)</th>
							<th>dip_e(电子倾角)</th>
							<th>dip_m(机械倾角)</th>
							<th>bcch(广播控制信道)</th>
							<th>carrierFrequency(逻辑载频数)</th>
							<th>cellularType(蜂窝类型)</th>
							<th>frequencyBand(频段)</th>
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
							<td>20</td>
							<td>12</td>
							<td>12</td>
							<td>60</td>
							<td>4</td>
							<td>宏站</td>
							<td>A频段</td>
						</tr>
					</tbody>
				</table>
			 <?php }elseif($md=='four'){ ?>
				<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_site_4g.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>cell_name(小区名称)</th>
							<th>lac(eNodeBID)</th>
							<th>cellId(localCellID)</th>
							<th>lng(经度)</th>
							<th>lat(纬度)</th>
							<th>angle(角度)</th>
							<th>height(站高)</th>
							<th>dip_e(电子倾角)</th>
							<th>dip_m(机械倾角)</th>
							<th>bcch(广播控制信道)</th>
							<th>tac(TAC)</th>
							<th>pci(PCI)</th>
							<th>CellName(小区CellName)</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>华西8号塔A</td>
							<td>336187</td>
							<td>1</td>
							<td>120.285</td>
							<td>31.589166</td>
							<td>30</td>
							<td>20</td>
							<td>12</td>
							<td>12</td>
							<td>60</td>
							<td>25108</td>
							<td>195</td>
							<td>L41C82F_1</td>
						</tr>
					</tbody>
				</table>
			 <?php } ?>
			</div>
		</div>
	</div>
	<div style="display:none" id="referTip" title="关联表操作"></br></br></br>
		<div id="viewPageData">
			<h1 align="center" style="">正在进行关联更新操作，请稍候...</h1>
		</div>
	</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#tag_grid_c0").children("a").attr("title","点击按时间排序");
	$("#tag_grid_c1").children("a").attr("title","点击按上传表格类型排序");
	$("#tag_grid_c2").children("a").attr("title","点击按上传操作排序");
	$("#tag_grid_c3").children("a").attr("title","点击按操作状态排序");

	$("#sub").click(function(){
		window.location.href="index.php?r=sysmanage/referdata";
	});
	$('#gis_sub').click(function(){
		if(confirm('您确定执行此操作吗?')){
		    var updateSitePoints = $('#updateSitePoints').is(':checked');
			window.location.href="index.php?r=sysmanage/pgsql_Synchronous&updateSitePoints="+updateSitePoints;
		}
	});
	$( "#help_info_click").dialog({
		autoOpen: false,
			height: 300,
			width: 1000,
			modal: true,
			resizable: false,
			draggable :true
	});
	// $(".filestyle").change(function(){
		// var arytype = ['xls'];
		// var strfile = $(".filestyle").val();
		// var strtype = strfile.split(".");
		// var index = strtype.length-1;
		// var thistype = strtype[index].replace(/(^\s*)|(\s*$)/g, "");
		// if(thistype != ""){
			// if(jQuery.inArray( thistype, arytype ) != -1){
				// alert(111111);
				// $("#sub_site").removeAttr("disabled");
				// $(".p_4").empty();
			// }else{
				// alert(2222222);
				// $(".p_4").empty();
				// $("#sub_site").attr({'disabled':'disabled'});
				// $(".p_4").append('<span calss="label_wran" style="color:red;">文件类型错误</span>');
			// }
		// }else{
			// alert(333333);
			// $(".p_4").empty();
			// $(".p_4").append('<span calss="label_wran" style="color:red;">请选择文件</span>');
			// $("#sub_site").attr({'disabled':'disabled'});
		// }
	// });
});
</script>

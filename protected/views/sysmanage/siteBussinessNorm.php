<?php
	isset($_GET['md'])?$md=$_GET['md']:$md='gsm';
	$md=='gsm'?$gsmsel=' select':$gsmsel='';
	$md=='td'?$tdsel=' select':$tdsel='';
	$md=='four'?$foursel=' select':$foursel='';
	/*标签*/
	?>

	<ul class='table_menu'>
	<li class="li1<?php echo $gsmsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/siteBussinessNorm&md=gsm'>GSM数据操作</a><p class="p2"></p></li>
	<li class="li1<?php echo $tdsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/siteBussinessNorm&md=td'>TD数据操作</a><p class="p2"></p></li>
	<li class="li1<?php echo $foursel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/siteBussinessNorm&md=four'>LTE数据操作</a><p class="p2"></p></li>
	</ul>		

	<div class='main_table'>
	<?PHP

		/*导入GSM数据*/
		if($md=='gsm'){
			$help_tip_text = "开始导入GSM基站业务指标数据";
			$refresh_href = "index.php?r=sysmanage/siteBussinessNorm&md=gsm";
		}elseif($md=='td'){
			$help_tip_text = "开始导入TD基站业务指标数据";
			$refresh_href = "index.php?r=sysmanage/siteBussinessNorm&md=td";
		}elseif($md=='four'){
			$help_tip_text = "开始导入LTE基站业务指标数据";
			$refresh_href = "index.php?r=sysmanage/siteBussinessNorm&md=four";
		}	
		echo '<div class="home_p2"><h2 style="border-top:1px solid #ccc">'.$help_tip_text.'：<span class="help_info" onclick="$( \'#help_info_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
		
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'gsmexcel',
		'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('您的操作会清空原有数据，确定进行此操作吗?');"),
		'enableAjaxValidation'=>false,
		));
		echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'siteNorm_excel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
		echo CHtml::submitButton('上传',array('class'=>'subupdate','disabled'=>'disabled')).'<a class="subupdate1" href='.$refresh_href.'> 刷 新</a></p>';
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
			$excel_json=Yii::app()->db->createCommand("select error_jason from excel_error_info_common where time='".$_GET['tabla_text']."'")->queryrow();
			$excel_date=json_decode($excel_json['error_jason'],true);
			$attr=array('lac','cellId','error');
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
					'lac:raw:lac',
					'cellId:raw:cellId',
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
			<script type='text/javascript'>
			function exprot(){
					$(".load_out").attr("onclick","");
					$("#img_box").show();
					var url='index.php?r=sysmanage/dataoutputCommon';
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
 ?>
	</div>
	<div style="display:none" id="help_info_click" title="数据导入帮助">
		<div class="alert_iframe" style="padding:0">		
			<div id="tagshow">
				<p style="color: red;"><span></span><b>表格文件类型</b> ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
				<p>数据导入操作将会清空原有数据，请谨慎操作！</p>
			  <?php if($md=='gsm'){ ?>
				<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_sitenorm_gsm.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>lac(LAC)</th>
							<th>cellId(cellId)</th>
							<th>cell(CELL)</th>
							<th>无线接通率</th>
							<th>掉话率</th>
							<th>TCH拥塞率</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>21136</td>
							<td>9771</td>
							<td>40977A</td>
							<td>100%</td>
							<td>0.67%</td>
							<td>0.00%</td>
						</tr>
					</tbody>
				</table>
			  <?php }elseif($md=='td'){ ?>
				<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_sitenorm_td.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>lac(LAC)</th>
							<th>cellId(cellId)</th>
							<th>cell(CELL)</th>
							<th>PS接通率</th>
							<th>CS接通率</th>
							<th>PS掉线率</th>
							<th>CS掉话率</th>
							<th>RRC拥塞率</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>53534</td>
							<td>28741</td>
							<td>D4WH74_1</td>
							<td>99.95%</td>
							<td>99.70%</td>
							<td>0.1%</td>
							<td>0.1%</td>
							<td>0.00%</td>
						</tr>
					</tbody>
				</table>
			  <?php }elseif($md=='four'){ ?>
				<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_sitenorm_4g.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>lac（eNodeBID）</th>
							<th>cellId（LocalCellID）</th>
							<th>CellName</th>
							<th>TAC</th>
							<th>无线接通率</th>
							<th>ERAB建立成功率</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>337874</td>
							<td>3</td>
							<td>L40781F_3</td>
							<td>20806</td>
							<td>100.00%</td>
							<td>100.00%</td>
						</tr>
					</tbody>
				</table>
			  <?php } ?>
			</div>
		</div>
	</div>
<script type="text/javascript">
$(document).ready(function(){

	$( "#help_info_click").dialog({
		autoOpen: false,
		height: 190,
		width: 800,
		modal: true,
		resizable: false,
		draggable :true
	});
	
});
</script>

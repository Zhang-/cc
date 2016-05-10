
<ul class='table_menu'>
	<li class="li1 select">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">导入wifi热点信息表</a>
		<p class="p2"></p>

	</li>
</ul>
<?php
	echo '<div class="home_p2"><h2>开始导入wifi热点数据：<span class="help_info" onclick="$( \'#help_info_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'wifiexcel',
	'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('确定进行此操作吗?');"),
	'enableAjaxValidation'=>false,
	));
	echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'wifiradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
	echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'wifiradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
	echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'wifiexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
	echo CHtml::submitButton('上传',array('class'=>'subupdate','disabled'=>'disabled')).'</p>';
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
	}
	echo "</div>";
	echo "</div>";
?>
<div style="display:none" id="help_info_click" title="数据导入帮助">
		<div class="alert_iframe" style="padding:0">		
			<div id="tagshow">
				<p style="color: red;"><span></span>表格文件类型 ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
				<p><span></span>覆盖数据 ：此操作将会清空原有数据</p>
				<p><span></span>插入数据 ：此操作将在原有数据的基础上插入数据</p>
				<p><span></span>表格内容 ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_wifi.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>wifi_name(小区名称)</th>
							<th>lac(LAC)</th>
							<th>cellId(cellId)</th>
							<th>longitude(经度)</th>
							<th>latitude(纬度)</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>桥涯A</td>
							<td>20668</td>
							<td>51231</td>
							<td>120.4227</td>
							<td>31.8325</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<script type="text/javascript">
$(document).ready(function(){
	$( "#help_info_click").dialog({
		autoOpen: false,
		height: 220,
		width: 800,
		modal: true,
		resizable: false,
		draggable :true
	});

	$(".filestyle").change(function(){
		var arytype = ['xls'];
		var strfile = $(".filestyle").val();
		var strtype = strfile.split(".");
		var index = strtype.length-1;
		var thistype = strtype[index].replace(/(^\s*)|(\s*$)/g, "");
		if(thistype != ""){
			if(jQuery.inArray( thistype, arytype ) != -1){
				$(".subupdate").removeAttr("disabled");
				$(".p_4").empty();
			}else{
				$(".p_4").empty();
				$(".subupdate").attr({'disabled':'disabled'});
				$(".p_4").append('<span calss="label_wran" style="color:red;">文件类型错误</span>');
			}
		}else{
			$(".p_4").empty();
			$(".p_4").append('<span calss="label_wran" style="color:red;">请选择文件</span>');
			$(".subupdate").attr({'disabled':'disabled'});
		}
	});
});
</script>
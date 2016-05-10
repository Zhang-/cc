<?php 
$md = isset($_GET['md'])?$_GET['md']:1;
$md==1?$st=' select':$st='';
$md==2?$cell=' select':$cell='';


function getOperate($id){
	echo "<span onclick='modifyKpi(".$id.", 1)' style='color:#417EB7;cursor:pointer;'><u>修改信息</u></span><span>&nbsp;&nbsp;,&nbsp;&nbsp;</span><span onclick='modifyKpi(".$id.",2)' style='color:#417EB7;cursor:pointer;'><u>删除</u></span>";
}
?>
<ul class='table_menu'>
	<li class="li1<?php echo $st;?>"><p class="p1"></p><a href='index.php?r=sysmanage/manage_kpi&md=1'>基站KPI指标设置</a><p class="p2"></p></li>
	<li class="li1<?php echo $cell;?>"><p class="p1"></p><a href='index.php?r=sysmanage/manage_kpi&md=2'>小区KPI信息更新</a><p class="p2"></p></li>
</ul>
<?php if($md == 1){?>

<div class="main_table" style="display:block">
<div class="table_search">
	<ul>
	 <li>
	  <span class="subupdate" onclick="modifyKpi(-2,0)">添加KPI指标</span>
	 </li>
	</ul>
	<div class="clear"></div>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView',array(
	'id'=>'manage_kpi',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table_content',
	'cssFile'=>false,
	'beforeAjaxUpdate'=>'function(id,data){
		$("#loader_container1").css("display","block");
	}',
    'afterAjaxUpdate'=>'function(id,data){
    	$("#loader_container1").css("display","none");
	}',
	'columns'=>array(
		'note',
		'value',
		'influence',
		array(
			'name'=>'不达标范围',
			'value'=>'KpiStandard::getNote($data->note, $data->operate, $data->value)',
		),
		'notice',
		array(
			'name'=>'操作',
			'value'=>'getOperate($data->id)'
		),
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
	'template'=>'{items}{pager}'
));
?>
</div>
<div id="modifyKpi" >
  <iframe src="" marginwidth="0" width="100%" marginheight="0"  frameborder="0" class="dataview UserViewPage"></iframe>
</div>
<?php }else{
	echo '<div class="main_table"><div class="home_p2"><h2 style="border-top:1px solid #ccc">开始导入基站KPI信息：<span class="help_info" onclick="$( \'#help_kpi_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'wifiexcel',
	'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('您的操作会清空数据表kpi中的原有数据，确定进行此操作吗?');"),
	'enableAjaxValidation'=>false,
	));
	echo '<p class="p_1"><span>选择要导入的基站KPI表格文件</span></p>';
	echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'kpiexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
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
		echo "</div>";
	}
	
	$sessionId = Yii::app()->session->sessionID ; //此次会话的session ID
	if($excelErrorFile = Yii::app ()->cache->get('excelErrorFile'.$sessionId ))
	{
		if(!empty($excelErrorFile))
		{
			if(!empty($excelErrorFile))
			{

				?>
				<table class="items" style='margin:0 32px 0px 10px;width:96%'>
					<thead>
						<tr>
							<th>错误行数</th>
							<th>错误类型</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($excelErrorFile as $errorKey=>$errorVal){
					echo
						"<tr>
							<td>".$errorVal['id']."</td>
							<td>".$errorVal['error']."</td>
						</tr>";
						}?>
					</tbody>
				</table>
				<?php
			}
		}
		Yii::app ()->cache->delete('excelErrorFile'.$sessionId );
	}
	echo "</div></div>";
?>

<div id="help_kpi_click" title="数据导入帮助">
	<iframe src="index.php?r=sysmanage/kpiExcelHelp" marginwidth="0" width="1200" height="170" marginheight="0"  frameborder="0"></iframe>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$( "#help_kpi_click").dialog({
		autoOpen: false,
		width: 850,
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
<?php }?>
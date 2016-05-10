<script type="text/javascript">

$(function() {
	//帮助
	$( "#regionInfoHelp").dialog({
		autoOpen: false,
		//height: 500,
		width: 800,
		modal: true,
		resizable: false,
		draggable :true
	});
	//添加、修改窗口
	$( "#regionInfo").dialog({
		autoOpen: false,
		//height: 373,
		width: 800,
		modal: true,
		resizable: false,
		draggable :true,
		close: function() { 
			parent.location.reload(true);
		}
	});
});
//修改、添加区域信息
function changeRegionInfo(id,type)
{
	if(type == 0){
		$('#regionInfo').dialog("option","title", "添加区域信息"); 
		$("#regionInfo").dialog("open");
	}else if(type == 1){
		$('#regionInfo').dialog("option","title", "修改区域信息"); 
		$("#regionInfo").dialog("open");
	}else if(type == 2){
		if( confirm('您确定要删除该区域信息？') ){
			alert('删除成功！');
			parent.location.reload(true);
		}else{
			type = -1;
		}
	}
	var srcval="index.php?r=sysmanage/changeRegionInfo&id="+id+"&type="+type+"";
	$('.dataview').attr('src',srcval);
}
</script>

<?php
	function getOperate($id){
		echo "<span onclick='changeRegionInfo(".$id.", 1)' style='color:#417EB7;cursor:pointer;'><u>修改信息</u></span><span>&nbsp;&nbsp;,&nbsp;&nbsp;</span><span onclick='changeRegionInfo(".$id.",2)' style='color:#417EB7;cursor:pointer;'><u>删除</u></span>";
	}
?>

<ul class='table_menu'>
	<li class="li1 select"><p class="p1"></p><a href='index.php?r=sysmanage/regionManage'>片区信息管理</a><p class="p2"></p></li>
</ul>
<div class="main_table">
<div class="home_p1">
	<h2 style="border-top:1px solid #ccc">添加片区信息：<span class="help_info" onclick="$('#regionInfoHelp').dialog( 'open' )">批量导入帮助</span></h2>
	
	<form method="post" action="index.php?r=sysmanage/regionInfoUpdate" enctype="multipart/form-data" OnSubmit="return confirm('您的操作会清空数据表中的原有数据，确定进行此操作吗?');">
		<span>单个添加：</span> <span onclick="changeRegionInfo(-2,0)" class="subupdate">添加</span>
		
		<p style="line-height:24px"><span style="float:left">批量导入：</span><input class='filestyle' type='file' name="regionexcel"/><input type="submit" disabled="disabled" value="上传" class="subupdate"/></p>
	</form>
	<p class="p_4"></p>
	<div id="loader_container1" style="display:none;">
		<div>
			<div>
			<img src="images/loading2.gif">
			数据计算中...
			</div>
		</div>
	</div>
	<?php
	if(isset($_GET['error']))
		echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
	else if(isset($_GET['success']))
		echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'region_info',
		'dataProvider'=>$model->search(),
		'itemsCssClass'=>'table_content',
		'cssFile'=>false,
		'beforeAjaxUpdate'=>'function(id,data){
			$("#loader_container1").css("display","block");
		}',
		'afterAjaxUpdate'=>'function (id, data) {
			$("#loader_container1").css("display","none");
		}',
		'columns'=>array(
			'city',
			'district',
			'contacts',
			'isreply',
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
</div>

<div id="regionInfo" >
 <iframe src="" marginwidth="0" height="230" width="100%" marginheight="0"  frameborder="0" class="dataview UserViewPage"></iframe>
</div>

<div id="regionInfoHelp" title="批量导入帮助">
	<div id="tagshow">
		<p style="color: red;"><span></span>表格文件类型 ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
		<p>数据导入操作将会清空原有数据，请谨慎操作！</p>
		<p><span></span>表格格式 ：excel文件中必须为单张表格，多张表格请分开导入，默认为您导入第一张表格。</p>
		<p><span></span>表格内容 ：<a href= '<?php echo 'index.php?r=site/download&fn='.json_encode('example_regionInfo.xls')?>' style="color:blue">（点击下载范本文档）</a></p>
		<table class="items">
			<thead>
				<tr>
					<th>市</th>
					<th>区、县</th>
					<th>投诉联系人</th>
					<th>是否回复</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>泰州市</td>
					<td>海陵区</td>
					<td>冯林：13815958606</td>
					<td>是</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
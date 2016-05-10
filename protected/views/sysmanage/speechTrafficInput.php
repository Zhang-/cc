
<?php
	isset($_GET['md'])?$md=$_GET['md']:$md='input';
	$md=='input'?$inputsel=' select':$inputsel='';
	$md=='update'?$updatesel=' select':$updatesel='';
?>
<ul class='table_menu'>
<li class="li1<?php echo $inputsel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/speechTrafficInput'>基站业务信息导入</a><p class="p2"></p></li>
<li class="li1<?php echo $updatesel;?>"><p class="p1"></p><a href='index.php?r=sysmanage/speechTrafficInput&md=update'>业务统计数据更新</a><p class="p2"></p></li>
</ul>

<?php if( $md=='input' ){ ?>
<div class="main_table">
<div class="home_p2">
	<h2 style="border-top:1px solid #ccc">开始导入基站业务数据：<span class="help_info" onclick="$('#speechTrafficInputHelp' ).dialog('open')">导入帮助</span></h2>
	
	<form method="post" action="index.php?r=sysmanage/speechTrafficInput" enctype="multipart/form-data" OnSubmit="return confirm('确定进行此操作吗?');">		
		<p class="p_1">
		<input name='cover_radio' type='radio' value= 1 />覆盖数据
		</p>
		<p class="p_1">
		<input name='cover_radio' type='radio' value= 0 />插入数据
		</p>
		<p style="line-height:24px"><span style="float:left"></span><input class='filestyle' type='file' name="speechTraffic_excel"/><input type="submit" disabled="disabled" value="上传" class="subupdate"/></p>
	</form>
	
	<?php
	if(isset($_GET['error'])&&!isset($_GET['tabla_text']))
		echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
	else if(isset($_GET['success']))
		echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		
	if(isset($_GET['tabla_text'])&&isset($_GET['error']))
	{
		echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
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
	}
	?>
</div>
</div>

<div style="display:none" id="speechTrafficInputHelp" title="数据导入帮助">
	<div class="alert_iframe" style="padding:0">		
		<div id="tagshow">
			<p style="color: red;"><span></span><b>表格文件类型</b> ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
			<p><span></span><b>覆盖数据</b> ：此操作将会清空原有数据</p>
			<p><span></span><b>插入数据</b> ：此操作将在原有数据的基础上插入数据</p>
			<p><span></span><b>表格内容</b> ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_siteinfo.xls');?>' style="color:blue">点击下载范本文档</a></p>
			<table class="items">
				<thead>
					<tr>
						<th>LAC</th>
						<th>CELLID</th>
						<th>日均话务量(Erl)</th>
						<th>日均数据流量(MB)</th>
						<th>日均无线利用率(%)</th>
						<th>时间</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>20668</td>
						<td>51231</td>
						<td>119.30</td>
						<td>4307.26</td>
						<td>49.48</td>
						<td>2014-05</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php }elseif( $md=='update' ){ 
?>
  <div class="main_table">
	<div class="home_p2">
		<span style="margin:4px 0px 4px 16px"> 业务统计数据更新是统计每个栅格单元对应的2G基站数、3G基站数、4G基站数、2G话务量等等业务统计数据，如果导入了新的基站业务信息数据，请及时更新！更新数据表的时间可能较长，请耐心等待！ </span>
		<div class="row select" style="margin:4px 0px 4px 16px">
			<label>请选择时间：</label>
			<select id="operateTime">
				<option value="1" > <?php echo date('Y-m',strtotime( date('Y-m')."- 1 months"));?> </option>
				<option value="2" > <?php echo date('Y-m',strtotime( date('Y-m')."- 2 months"));?> </option>
				<option value="3" > <?php echo date('Y-m',strtotime( date('Y-m')."- 3 months"));?> </option>
			</select>
		</div>
		<div class="row buttons" style="text-align:left;margin:14px 0px 0px 20px">
			<input id="grid_bussiness_update" type="submit" value="确定更新" class="subupdate"/>
		</div>
	<?php
	if(isset($_GET['error']))
		echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
	else if(isset($_GET['success']))
		echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
	?>
	</div>
  </div>
<?php } ?>

<div style="display:none" id="updateTip" title="栅格业务统计更新">
	<div id="viewPageData">
		<h1 align="center" style="">正在更新栅格业务统计数据，请稍候...</h1>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//帮助
	$( "#speechTrafficInputHelp").dialog({
		autoOpen: false,
		//height: 500,
		width: 800,
		modal: true,
		resizable: false,
		draggable :true
	});
	//更新提示
	$( "#updateTip").dialog({
		autoOpen: false,
		//height: 500,
		width: 800,
		modal: true,
		resizable: false,
		draggable :true
	});
	//更新grid_bussiness_info
	$('#grid_bussiness_update').click(function(){
		if(confirm('您确定执行此操作吗?')){
			$('#updateTip').dialog( "open" );
			window.location.href="index.php?r=sysmanage/gridBussinessUpdate&time="+ $('#operateTime').val();
		}
	});
});
</script>
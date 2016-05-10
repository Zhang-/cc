<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ui-lightness/jquery-ui-1.8.20.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="sitelogin toolong">
<div class="main_table">

<?php
	echo '<div class="home_p2"><h2 style="border-top:1px solid #ccc">开始导入口径信息数据：<span class="help_info" onclick="$( \'#help_info_click\' ).dialog( \'open\' )">导入帮助</span></h2>';
?>
	<div class="exprot_box">
		<span class="load_out" title="导出表格" onclick="javascript:statementExprot()"></span>
		<div id="img_box" style="display: none;"></div>
		<div id="adminshow"></div>
	</div>
<?php
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'statementexcel',
	'htmlOptions'=>array('enctype'=>"multipart/form-data","OnSubmit"=>"return confirm('确定进行此操作吗?');"),
	'enableAjaxValidation'=>false,
	));
	?>
	<span>单条添加：</span> <span onclick="$('#statementCreate').dialog( 'open' );" class="subupdate">添加口径信息</span>
	<?php
	echo '<p class="p_1">'.CHtml::openTag('input',array('name'=>'statementradio','type'=>'radio','value'=>'0')).'覆盖数据'.CHtml::closeTag('input').'</p>';
	echo '<p class="p_2">'.CHtml::openTag('input',array('name'=>'statementradio','type'=>'radio','value'=>'1')).'插入数据'.CHtml::closeTag('input').'</p>';
	echo '<p class="p_3">'.CHtml::openTag('input',array('name'=>'statementexcel','type'=>'file','class'=>'filestyle')).CHtml::closeTag('input');
	echo CHtml::submitButton('上传',array('class'=>'subupdate','disabled'=>'disabled')).'</p>';
	echo '<p class="p_4"></p>';
	$this->endWidget();

	if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
		echo "<div class='errorSummary' style='margin-bottom:0'>".json_decode($_GET['error'])."</div>";
	if(isset($_GET['success']))
		echo "<div class='errorSummary oprationseccess' style='margin-bottom:0'>".json_decode($_GET['success'])."</div>";
	/*解析错误*/
	if(isset($_GET['tabla_text'])&&isset($_GET['error']))
	{
		echo "<div class='errorSummary' style='margin-bottom:0'>".json_decode($_GET['error']);
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
							<th>流水号</th>
							<th>错误类型</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($excelErrorFile as $serialIdErrorKey=>$serialIdErrorVal){
					echo
						"<tr>
							<td>".$serialIdErrorKey."</td>
							<td>".$serialIdErrorVal."</td>
						</tr>";
						}?>
					</tbody>
				</table>
				<?php
			}
		}
		Yii::app ()->cache->delete('excelErrorFile'.$sessionId );
	}
	echo "</div>";
	

?>
</div>
 <script type="text/javascript">
 $('.home_p2').wrap("<div class='nano'><div class='content' style='right: -20px;'></div></div>")
 </script>
<div style="display:none" id="help_info_click" title="数据导入帮助">
		<div class="alert_iframe" style="padding:0">		
			<div id="tagshow">
				<p style="color: red;"><span></span>表格文件类型 ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
				<p><span></span>覆盖数据 ：此操作将会清空原有数据</p>
				<p><span></span>插入数据 ：此操作将在原有数据的基础上插入数据</p>
				<p><span></span>表格内容 ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_statement.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<th>流水号</th>
							<th>口径类型</th>
							<th>影响半径(米)</th>
							<th>口径经度</th>
							<th>口径纬度</th>
							<th>开始时间</th>
							<th>结束时间</th>
							<th>口径标题</th>
							<th>口径内容</th>
							<th>影响范围</th>
							<th>影响区(县)</th>
							<th>存在问题</th>
							<th>项目目前状态</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>100233</td>
							<td>弱覆盖</td>
							<td>500</td>
							<td>119.92921</td>
							<td>32.49198</td>
							<td>2009-09-30 00:00:00</td>
							<td>2012-10-30 00:00:00</td>
							<td>玉城名郡</td>
							<td>敬请谅解，谢谢。</td>
							<td>玉城名郡</td>
							<td>海陵</td>
							<td>信号差、主被叫困难</td>
							<td>规划建设中</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
</div>

<div id="statementCreate" title="添加口径信息">
		<iframe src="index.php?r=sysmanage/statementcreate" marginwidth="0" width="1200" height="450" marginheight="0" align="middle" scrolling="no" frameborder="0"></iframe>
</div>
</body>
</html>
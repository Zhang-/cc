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
		<div class="alert_iframe" style="padding:0">		
			<div id="tagshow">
				<p style="color: red;"><span></span>表格文件类型 ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>
				<p><span></span>数据导入操作将会清空原有数据，请谨慎操作！</p>
				<p><span></span>表格内容 ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example_kpi.xls');?>' style="color:blue">点击下载范本文档</a></p>
				<table class="items">
					<thead>
						<tr>
							<?php
							foreach( $help_table as $k=>$val ){
							?>	
								<th><?php echo $k;?></th>
							<?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<?php
							foreach( $help_table as $k=>$val ){
							?>	
								<td><?php echo $val;?></td>
							<?php
							}
							?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>
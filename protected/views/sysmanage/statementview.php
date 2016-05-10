<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="sitelogin toolong">

<?php
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
?>

<?php 
	$this->widget('zii.widgets.CDetailView', 
		array(
			'data'=>$model,
			'attributes'=>
				array(
					'state_type',
					'affect_radius',
					'state_lng',
					'state_lat',
					array(
						'name'=>'startTime',
						'value'=>getDateTime($model->startTime),
					),
					array(
						'name'=>'endTime',
						'value'=>getDateTime($model->endTime),
					),
					array(
						'name'=>'state_title',
						'type'=>'raw',
						'value'=>getShortCellContent("500",$model->state_title,'CDetailView')
					),
					array(
						'name'=>'state_content',
						'type'=>'raw',
						'value'=>getShortCellContent("500",$model->state_content,'CDetailView')
					),
					array(
						'name'=>'affect_scope',
						'type'=>'raw',
						'value'=>getShortCellContent("500",$model->affect_scope,'CDetailView')
					),
					'affect_area',
					array(
						'name'=>'problem',
						'type'=>'raw',
						'value'=>getShortCellContent("500",$model->problem,'CDetailView')
					),
					'project_status',
	
				),
		)
	); 
?>

</body>
</html>



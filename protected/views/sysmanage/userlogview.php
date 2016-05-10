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
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //���뱾ҳ�淽��
?>

<?php 
	$this->widget('zii.widgets.CDetailView', 
		array(
			'data'=>$model,
			'attributes'=>
				array(
					'id',
					'actiontime',
					'userip',
					'userid',
					'username',
					array(
					'name'=>'userrole',
					'value'=>getItemname($model->userrole),
					),
					array(
					'name'=>'actiontype',
					'value'=>getLogTypeTrans($model->actiontype),
					),
					'affectid',
					array(
					'name'=>'url',
					'type'=>'raw',
					'value'=>getLogURL($model->url,$model->actiontype),
					),
				),
		)
	); 
?>

</body>
</html>



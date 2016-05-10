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
	<body class="sitelogin"> 
	
	<?php 
		function getterminalnum($model)
		{
			$count=Yii::app()->db->createCommand("select count(*) from static_information where phoneModel='".$model."'")->queryrow();
			return $count['count(*)'];
		}
		$_model=new ModelNettype;
		$model=$_model->findByPk($_GET['id']);
	$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'phoneBrand',
		'phoneModel',
		'netType',
		array('name'=>'数量','value'=>getterminalnum($model->phoneModel)),
	),
)); 
?>
	</body>
</html>
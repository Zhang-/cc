<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/numeral.js"></script> 
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body style="background:#fff"> 

	<div class="form_modify alert_iframe" style="border:1px solid #ddd">

		<div class="form">
<?php 
	$form=$this->beginWidget('CActiveForm', 
	array(
			'id'=>'edit-brand-form',
			'htmlOptions'=>array('name'=>'kpi-form'),
			'enableAjaxValidation'=>false,
		)
	); 
	$data = $model->attributes;
?>
<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>
<?php 
if(isset($error)&&!empty($error))
	echo "<div class='errorSummary'>".$error."</div>";
?>
	<div class="row" style="display:none">
		<?php echo $form->textField($model,'id',array('style'=>'display:none')); ?>
		<input type="text" style="display:none" value="<?php echo $data['id'];?>" />
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'phoneBrand'); ?>
		<span style="margin-left: 15px;"><?php echo $data['phoneBrand'];?></span>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'phoneBrandCN'); ?>
		<?php echo $form->textField($model,'phoneBrandCN',array('size'=>45,'maxlength'=>45)); ?>
		<input id='cn' type="text" style="display:none" value="<?php echo $data['phoneBrand'];?>" />
	</div>
	<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
		<input id="but_sub" type="submit" value="保存"/>
	</div>
			
<?php 
	$this->endWidget(); 
?>

		</div><!-- form -->
	</div>
</body>
</html>
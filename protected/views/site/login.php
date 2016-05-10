<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="sitelogin sitelogin2" >

<?php 
	HelpTool::getActionInfo(0,4); //Guest访问日志
?>

<script>
<?php if($limitUser==false){?>
alert('本账户已有用户在线，请稍候再试！');
<?php }?>

</script>

<div class="index_all"> 

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	)); ?>
	<div class="row text">
		<?php echo $form->labelEx($model,'账 号'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row text">
		<?php echo $form->labelEx($model,'密 码'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>		
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
		<span><?php echo CHtml::submitButton('登 录'); ?></span>
	</div>
	<div class="row buttons">		
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->

</div>
</body>
</html>
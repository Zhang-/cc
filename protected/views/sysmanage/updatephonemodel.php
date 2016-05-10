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
	<body style="background:#fff"> 
	<div class="form_modify alert_iframe" style="border:1px solid #ddd">
		<div class="form">
			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'manage-list-form',
				'action'=>'index.php?r=sysmanage/updatephone&id='.$model->id,
				//'htmlOptions'=>array('name'=>'manage_form'),
				'enableAjaxValidation'=>false,
			)); ?>

			<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>

				<?php echo $form->errorSummary($model); ?>
				<div class="row" style='display:none'>
					<?php echo $form->labelEx($model,'id'); ?>
					<?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?>
					<?php echo $form->error($model,'id'); ?>
				</div>
				<div class="row">
					<?php echo $form->labelEx($model,'phoneBrand'); ?>
					<?php echo $form->textField($model,'phoneBrand',array('size'=>20,'maxlength'=>20)); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($model,'phoneModel'); ?>
					<?php echo $form->textField($model,'phoneModel',array('size'=>20,'maxlength'=>20)); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($model,'netType'); ?>
					<?php echo $form->textField($model,'netType',array('size'=>20,'maxlength'=>20)); ?>
				</div>
				
				<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
					<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存'); ?>
				</div>
				<?php $this->endWidget(); ?>
		</div>
	</div>
	</body>
</html>
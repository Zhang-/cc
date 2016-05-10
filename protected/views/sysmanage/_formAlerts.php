<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'system-alerts-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>

	<?php echo $form->errorSummary($model);

		if($model->isNewRecord){
	 ?>

	<div class="row">
		<?php echo $form->labelEx($model,'alerts_key'); ?>
		<?php echo $form->textField($model,'alerts_key',array('size'=>20,'maxlength'=>20)); ?>
	</div>
	<?php }?>

	<div class="row">
		<?php echo $form->labelEx($model,'alerts_name'); ?>
		<?php echo $form->textField($model,'alerts_name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alerts_tag'); ?>
		<?php echo $form->textField($model,'alerts_tag',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alerts_link'); ?>
		<?php echo $form->textField($model,'alerts_link',array('size'=>50)); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'alerts_display'); ?>
		<?php echo $form->textField($model,'alerts_display',array('size'=>50,'maxlength'=>1)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alerts_contents'); ?>
		<?php echo $form->textField($model,'alerts_contents',array('size'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创 建' : '保 存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
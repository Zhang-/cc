<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="js/numeral.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
	$("#affect_radius").numeral(4);
	$("#state_lng").numeral(8);
	$("#state_lat").numeral(8);
});
</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'complain-statement-list-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php 
$allProjectStatus = CacheFile::getProjectStatus(); //获取所有项目状态
$allStatementType = CacheFile::getStatementType(); //获取所有口径类型
?>

	<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'state_type'); ?>
		<?php echo $form->dropDownList($model,'state_type',$allStatementType,$htmlOptions=array('class'=>'state_type')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'affect_radius'); ?>
		<?php echo $form->textField($model,'affect_radius',array('id'=>'affect_radius','maxlength'=>'6')); ?>
	</div>
	
		<div class="row">
		<?php echo $form->labelEx($model,'state_lng'); ?>
		<?php echo $form->textField($model,'state_lng',array('id'=>'state_lng','maxlength'=>'12')); ?>
	</div>
	
		<div class="row">
		<?php echo $form->labelEx($model,'state_lat'); ?>
		<?php echo $form->textField($model,'state_lat',array('id'=>'state_lat','maxlength'=>'12')); ?>
	</div>

	<div class="row"> <?php echo $form->labelEx($model,'startTime'); ?>
		<?php 
				$startTime = $model->isNewRecord ? date('Y-m-d H:i:s') : HelpTool::getDateTime($model->startTime) ;
				echo '<input id="startTime" class="Wdate" type="text" name="ComplainStatementList[startTime]" value="'.$startTime.'" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})">';
		?>
	</div>

	<div class="row"> <?php echo $form->labelEx($model,'endTime'); ?>
		<?php 
			$endTime = $model->isNewRecord ? date('Y-m-d H:i:s') : HelpTool::getDateTime($model->endTime) ;
			echo '<input id="Manage_time" class="Wdate" type="text" name="ComplainStatementList[endTime]" value="'.$endTime.'" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\',minDate:startTime.value})">';
		?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'state_title'); ?>
		<?php echo $form->textArea($model,'state_title',array('rows'=>2, 'cols'=>50, 'class'=>'textareas')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'state_content'); ?>
		<?php echo $form->textArea($model,'state_content',array('rows'=>2, 'cols'=>50 , 'class'=>'textareas')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'affect_scope'); ?>
		<?php echo $form->textArea($model,'affect_scope',array('rows'=>2, 'cols'=>50, 'class'=>'textareas')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'affect_area'); ?>
		<?php echo $form->textField($model,'affect_area',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'problem'); ?>
		<?php echo $form->textField($model,'problem',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'project_status'); ?>
		<?php echo $form->dropDownList($model,'project_status',$allProjectStatus,$htmlOptions=array('class'=>'state_type')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
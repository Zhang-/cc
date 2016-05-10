<div class="form" style="border-top:1px solid #ccc">

<?php 
	$form=$this->beginWidget('CActiveForm', 
		array(
			'id'=>'manage-list-form',
			'htmlOptions'=>array('name'=>'manage_form'),
			'enableAjaxValidation'=>false,
		)
	); 
?>

<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
		<input type="text" style="display:none" value="" />
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>50,'maxlength'=>50)); ?>
		<input type="password" style="display:none" value="" />
	</div>

	<div class="row select"> 
		<label >省市</label>
		<select name="ManageList[province]" id="province" onChange = "select()"></select>
		<select name="ManageList[city]" id="city" ></select>
		<script type="text/javascript" src="js/city.js"></script> 
		<script type="text/javascript">
		<?php 
			if(isset($model->province))
			{
		?>
				where[0]= new comefrom("<?php echo $model->province;?>","<?php echo $model->city;?>");
		<?php
			}
		?>
			$(document).ready(function(){
				init();
			});
		</script> 
	  </div>

	<div class="row">
		<?php echo $form->labelEx($model,'address'); ?>
		<?php echo $form->textField($model,'address',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>50,'maxlength'=>50)); ?> 
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row" style="display:none"> <?php echo $form->labelEx($model,'regDateTime'); ?>
		<?php 
			if($model->regDateTime){
				echo $form->textField($model,'regDateTime',array('class'=>'Wdate','onClick'=>'WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})'));
			}else{
				echo '<input id="Manage_time" class="Wdate" type="text" name="ManageList[regDateTime]" value="'.date('Y-m-d H:i:s').'" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})">';
			}  
		?>
	</div>
	
	<div class="row select">
		<?php echo $form->labelEx($model,'itemname'); ?>
		<?php 
			$allRoles=HelpTool::getAllRolesTrans('');
			$isAdmin = HelpTool::isAdmin();
			if( $isAdmin['flag'] == false )
			{
				foreach( $allRoles as $roleKey=>$roleVal)
				{
					if($roleKey==$isAdmin['Admin'])
					{
						unset($allRoles[$roleKey]);
					}
				}
			}
			echo $form->dropDownList($model,'itemname',$allRoles ); 
		?>
		<!--<span class="createRoleButton" onclick="javascript:createRole();">新建角色</span>-->
	</div>

	<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">

		<?php echo CHtml::submitButton($model->isNewRecord ? '创 建' : '保 存'); ?>
	</div>

<?php 
	$this->endWidget(); 
?>

</div><!-- form -->
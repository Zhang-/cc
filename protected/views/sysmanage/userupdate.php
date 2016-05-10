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
				<?php echo $form->error($model,'username'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'password'); ?>
				<?php echo $form->passwordField($model,'password',array('size'=>50,'maxlength'=>50)); ?>
				<?php echo $form->error($model,'password'); ?>
			</div>

			<div class="row select"> 
				<label >省市</label>
				<select name="ManageList[province]" id="province" onChange = "select()"></select>
				<select name="ManageList[city]" id="city" ></select>
				<script type="text/javascript" src="js/city.js"></script> 
				<script type="text/javascript">
					$(document).ready(function(){
						init("<?php echo $model->province;?>","<?php echo $model->city;?>");
						select();
					});
				</script> 
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'address'); ?>
				<?php echo $form->textField($model,'address',array('size'=>60,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'address'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'email'); ?>
				<?php echo $form->textField($model,'email',array('size'=>50,'maxlength'=>50)); ?> 
				<?php echo $form->error($model,'email'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'phone'); ?>
				<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
				<?php echo $form->error($model,'phone'); ?>
			</div>

			<div class="row" style="display:none"> <?php echo $form->labelEx($model,'regDateTime'); ?>
			<?php 
				if($model->regDateTime)
				{
					echo $form->textField($model,'regDateTime',array('class'=>'Wdate','onClick'=>'WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})','onfocus'=>'this.blur();'));
				}
				else{
					echo '<input id="Manage_time" class="Wdate" type="text" name="ManageList[regDateTime]" value="'.date('Y-m-d H:i:s').'" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})">';
				}  
			?>
			<?php echo $form->error($model,'regDateTime'); ?> </div>

			<div class="row select">
				<?php 
				echo $form->labelEx($model,'itemname'); 
				
					$allRoles=HelpTool::getAllRolesTrans('');
					$isAdmin = HelpTool::isAdmin();
					if( $isAdmin['flag'] == false )
					{
						unset($allRoles[$isAdmin['Admin']]);
					}
					echo $form->dropDownList($model,'itemname',$allRoles ); 
				 echo $form->error($model,'itemname'); 
				 ?>
			</div>

			<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
				<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存'); ?>
			</div>

<?php 
	$this->endWidget(); 
?>

		</div><!-- form -->
	</div>

</body>
</html>
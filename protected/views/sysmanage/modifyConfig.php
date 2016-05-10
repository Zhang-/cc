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
			'id'=>'modify-config-form',
			'htmlOptions'=>array('name'=>'kpi-form'),
			'enableAjaxValidation'=>false,
		)
	); 
	$data = $model->attributes;

if(isset($error)&&!empty($error)) echo "<div class='errorSummary'>".$error."</div>";
else echo '<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>';
?>
	<div class="row" style="display:none">
		<input type="text" name="terminalConfig[id]" value="<?php echo $data['id'];?>"/>
	</div>

	<div class="row">
		<label>配置项标签</label>
		<span style="margin-left: 15px;"><?php echo $data['tagname'];?></span>
	</div>

	<div class="row">
		<label>配置项描述</label>
		<span style="margin-left: 15px;"><?php echo $data['tagdes'];?></span>
	</div>
	<div class="row">
		<label>配置项类型</label>
		<span style="margin-left: 15px;"><?php echo $data['datatype'];?></span>
	</div>
	
	<div class="row">
		<label>配置项数值<span class="required">*</span></label>
		<?php if($data['datatype'] == 'boolean'){?>
		<select id="choseF" style="margin-left: 15px;" name="terminalConfig[tagvalue]" >
			<option value='true' <?php if($data['tagvalue']=='true'){?> selected="selected" <?php }?>>true</option>
			<option value='false' <?php if($data['tagvalue']=='false'){?> selected="selected" <?php }?>>false</option>
		</select>
		<?php }else{?>
		<input type="text" id="inputVal" maxlength="15" name="terminalConfig[tagvalue]" value="<?php echo $data['tagvalue'];?>"/>
		<?php }?> 
	</div>

	<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
		<input id="but_sub" type="submit" disabled="disabled" value="保存"/>
	</div>
			
<?php 
	$this->endWidget(); 
?>

	</div><!-- form -->
<script type="text/javascript">
$(function(){
	<?php if($data['datatype'] == 'int'){?>
	$("#inputVal").numeral();
	<?php }?>
	$("#inputVal").keyup(function(){
		$("#but_sub").removeAttr("disabled");
	});
	$("#choseF").change(function(){
		$("#but_sub").removeAttr("disabled");
	});
});
</script>
</div>
</body>
</html>
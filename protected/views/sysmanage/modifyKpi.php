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
			'id'=>'modify-kpi-form',
			'htmlOptions'=>array('name'=>'kpi-form'),
			'enableAjaxValidation'=>false,
		)
	); 
?>
<script type="text/javascript">
$(function(){
	$("#inputVal").numeral(10);
});
</script>
	<?php 
	if(isset($error)&&!empty($error)) echo "<div class='errorSummary'>".$error."</div>";
	else echo '<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>';
		if($type == 0){
	?>
	<div class="row">
		<label>KPI指标<span style="color:red" >&nbsp;*</span> </label>
		<input type="text" name="addKpiStandard[note]" />
	</div>
	
	<div class="row select">
		<label>判断操作符<span style="color:red" >&nbsp;*</span></label>
		<select name="addKpiStandard[operate]">
			<option value="1" >大于</option>
			<option value="2" >等于</option>
			<option value="3" >小于</option>
			<option value="4" >大于等于</option>
			<option value="5" >小于等于</option>
		</select>
	</div>
	
	<div class="row">
		<label>判断临界值<span style="color:red">&nbsp;*</span></label>
		<input type="text" id="inputVal" maxlength="10" name="addKpiStandard[value]" /> 
	</div>
	
	<div class="row select">
		<label>影响范围<span style="color:red" >&nbsp;*</span></label>
		<select name="addKpiStandard[influence]">
			<option value="1" >数据业务</option>
			<option value="2" >语音业务</option>
			<option value="3" >全局业务</option>
		</select>
	</div>
	
	<div class="row">
		<label>提示信息<span style="color:red" >&nbsp;*</span></label>
		<input type="text"  name="addKpiStandard[notice]" /> 
	</div> 
	
	<?php }else{
		$data = $model->attributes;
	?>
	<div class="row" style="display:none">
		<input type="text" name="modifyKpiStandard[id]" value="<?php echo $data['id'];?>"/>
	</div>

	<div class="row">
		 <label>KPI指标<span style="color:red" >&nbsp;*</span></label><span style="margin-left: 15px;"><?php echo $data['note'];?></span>
	</div>
	
	<div class="row select">
		<label>判断操作符<span style="color:red" >&nbsp;*</span></label>
		<select name="modifyKpiStandard[operate]">
			<option value="1" <?php if($data['operate']=='>')echo "selected='selected'";?>>大于</option>
			<option value="2" <?php if($data['operate']=='=')echo "selected='selected'";?>>等于</option>
			<option value="3" <?php if($data['operate']=='<')echo "selected='selected'";?>>小于</option>
			<option value="4" <?php if($data['operate']=='>=')echo "selected='selected'";?>>大于等于</option>
			<option value="5" <?php if($data['operate']=='<=')echo "selected='selected'";?>>小于等于</option>
		</select>
	</div>
	
	<div class="row">
		<label>判断临界值<span style="color:red" >&nbsp;*</span></label>
		<input type="text" id="inputVal" maxlength="10" name="modifyKpiStandard[value]" value="<?php echo $data['value'];?>"/> 
	</div>
	
	<div class="row select">
		<label>影响范围<span style="color:red" >&nbsp;*</span></label>
		<select name="modifyKpiStandard[influence]">
			<option value="1" <?php if($data['influence']=='数据业务')echo "selected='selected'";?>>数据业务</option>
			<option value="2" <?php if($data['influence']=='语音业务')echo "selected='selected'";?>>语音业务</option>
			<option value="3" <?php if($data['influence']=='全局业务')echo "selected='selected'";?> >全局业务</option>
		</select>
	</div>
	
	<div class="row">
		<label>提示信息<span style="color:red" >&nbsp;*</span></label>
		<input type="text"  name="modifyKpiStandard[notice]" value="<?php echo $data['notice'];?>"/> 
	</div> 
<?php } ?>
	
	<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
		<input id="but_sub" type="submit"  value="保存"/>
	</div>
			
<?php 
	$this->endWidget(); 
?>

		</div><!-- form -->
	</div>

</body>
</html>
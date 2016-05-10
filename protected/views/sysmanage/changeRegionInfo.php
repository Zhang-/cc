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
			'id'=>'modify-region-info',
			'htmlOptions'=>array('name'=>'region_info'),
			'enableAjaxValidation'=>false,
		)
	); 
?>

<?php 
	if(isset($error)&&!empty($error)) echo "<div class='errorSummary'>".$error."</div>";
	else echo '<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>';
		if($type == 0){
?>	
	<div class="row select"> 
		<label >市、区、县<span style="color:red" >&nbsp;*</span></label>
		<select name="addRegionInfo[city]" id="city" onChange = "select()"></select>
		<select name="addRegionInfo[district]" id="district" ></select>
		<script type="text/javascript" src="js/district.js"></script>
		<script type="text/javascript">
			<?php $gisConfig = GISConfig::get(); ?>
			var city = "<?php echo $gisConfig->city; ?>";
			var where = new Array(2);
			where[0]= new comefrom("未选择省份","未选择城市");
            where[1]= alldistrict[city];
			$(document).ready(function(){
				init_city();
			});
		</script> 
	</div>
	
	<div class="row">
		<label>投诉联系人<span style="color:red" >&nbsp;*</span></label>
		<input type="text" name="addRegionInfo[contacts]" />
	</div>
	
	<div class="row select">
		<label>是否回复<span style="color:red" >&nbsp;*</span></label>
		<select name="addRegionInfo[isreply]">
			<option value="1" >是</option>
			<option value="2" >否</option>
		</select>
	</div>
	<?php }else{
		$data = $model->attributes;
	?>
	<div class="row" style="display:none">
		<input type="text" name="modifyRegionInfo[id]" value="<?php echo $data['id'];?>"/>
	</div>
	
	<div class="row">
		 <label>市、区、县<span style="color:red" >&nbsp;*</span></label>
		 <span style="margin-left: 20px;">
		 <?php 
			if( $data['district'] == "不限" ){
				echo $data['city'];
			}else{
				echo $data['city'].$data['district'];
			}
		?>
		</span>
	</div>
	
	<div class="row ">
		<label>投诉联系人<span style="color:red" >&nbsp;*</span></label>
		<input type="text" name="modifyRegionInfo[contacts]" value="<?php echo $data['contacts'];?>" />
	</div>
	
	<div class="row select">
		<label>是否回复<span style="color:red" >&nbsp;*</span></label>
		<select name="modifyRegionInfo[isreply]">
			<option value="1" <?php if($data['isreply']=="是")echo "selected='selected'";?>>是</option>
			<option value="2" <?php if($data['isreply']=="否")echo "selected='selected'";?>>否</option>
		</select>
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
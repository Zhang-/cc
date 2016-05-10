<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.20.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/DatePicker/WdatePicker.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ui-lightness/jquery-ui-1.8.20.custom.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<script type="text/javascript"> 
	$(function(){
	  function on_of(the){
	     var a=$(the).attr('checked')
	     if(a=='checked')
	     {$('.back_week,.back_time').show()}
	     else
	     {$('.back_week,.back_time').hide()}
	  }
	  
	  $('.turn_of,.turn_on').each(function(){
	   on_of(this);
	  })
	  
	  $('.turn_of,.turn_on').click(function(){
      on_of(this);
	  })
	
	
	})
</script> 
</head>

<body style="background:#fff">

	<div class="" style="padding:10px;height:205px">

		<div class="back_form">

<?php 
 	$form=$this->beginWidget('CActiveForm', 
		array(
			'action'=>Yii::app()->createUrl($this->route),
			'method'=>'get',
		)
	); 
	?>
	<?php 
		if($error!=='')
		{
			echo "<div class='errorSummary' style=''>".$error."</div>";
		}
	
		$openChkd = isset($main['openDBAutoBackup']) ? 'checked = "checked"' : '';
		$openChkdClass = isset($main['openDBAutoBackup']) ? 'turn_on' : 'turn_of';
		$backupTime = isset($main['backupTime']) ? $main['backupTime'] : '23:59:00';
		
	?>
	<input class="<?php echo $openChkdClass; ?>" type = "checkbox" name = "AutoBackup[openDBAutoBackup]" <?php echo $openChkd; ?> />开启自动备份 
	
	<p class="back_p" style="color:red">说明：选中该项后提交为添加自动备份计划，未选中时提交为取消前一次自动备份计划</p>
	
	<!--选择备份开始日期：
	<input id="startTime" class="Wdate" type="text" name="AutoBackup[startTime]" value="<?php //echo $main['startTime']; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />-->
	<div class="back_week">
	<span>选择备份周期：</span>
	<?php
	foreach($week as $weekKey=>$weekVal)
	{
		$chkd = isset($main['backupDay'][$weekKey]) ? 'on' : '';
		echo CHtml::openTag('input',array('name'=>'AutoBackup[backupDay]['.$weekKey.']','class'=>'backupDay','type'=>'checkbox','checked'=>$chkd)).'<span>'.$weekVal.'</span>'.CHtml::closeTag('input');
	}
	?>
	<div style="clear:both"></div>
    </div>
	<div class="back_time">
	选择备份时间：
	<input id="backupTime" class="Wdate" type="text" name="AutoBackup[backupTime]" value="<?php echo $backupTime; ?>" onclick="WdatePicker({dateFmt:'HH:mm:ss',isShowToday:false})" />

	</div>
	 <input type = "submit" class="btn" value = "提交" />
	<?php $this->endWidget(); ?>
		</div><!-- form -->
    
  
	</div>
</body>
</html>



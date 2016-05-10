
	<script>
		function alertChecked(key,value='false'){
			$.ajax({
				type: 'post',
				url: 'index.php?r=sysmanage/alertsManage',
				data: {
					alertkey: key,
					alertdisplay:value
				},
				//beforeSend: display(true),
				success: function(data){
					alert(data);
				}
			});
		}
	</script>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body style="background:#fff">

	<div class="" style="padding:10px;height:205px">

		<div class="back_form">


	<?php 
		/*if($error!=='')
		{
			echo "<div class='errorSummary' style=''>".$error."</div>";
		}*/

		foreach ($allAlerts as $alertValue) {
			echo $alertValue['alerts_name'].CHtml::checkBox($alertValue['alerts_key'],$alertValue['alerts_display'],array('class'=>'tagshow','type'=>'checkbox','value'=>$alertValue['alerts_key'],'onclick'=>"alertChecked(this.value,$(this).attr('checked'));"));
		}
		
	?>
  
	</div>


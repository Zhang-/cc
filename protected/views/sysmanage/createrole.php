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
<body>
<form method="post" action="index.php?r=sysmanage/createrole" enctype="multipart/form-data">

<div class="createRloeDiv">
<div class="roleName">
自定义角色名：<input type="text"/> [10个字符以内]
</div>
<ul class="first">
<?php 
$pdata = Yii::app()->params->powermenu; //菜单数组

//遍历一级菜单
	foreach($pdata['items'] as $firstMenu)
	{
		$firstMenuLabel = $firstMenu['label'];
		$firstMenuUrl = $firstMenu['url'];
		$firstFormName = 'formArray['.$firstMenu['url'].']';
?>
	<li>
	   <div class="first">
	    <span class="one"><?php echo $firstMenuLabel; ?></span>
		
							<span>管理权限</span>
							<span>只读权限</span>
							<span>无访问权限</span>
							<span>自定义</span>
		
	   </div>
	 <ul class="that">
	 
	<?php 
	//遍历二级菜单
	   
		foreach($firstMenu['items'] as $secondMenu)
		{
			$secondMenuLabel = $secondMenu['label'];
			$secondMenuUrl = $secondMenu['url'];
			$secondFormName = $firstFormName.'['.$secondMenuUrl.']';
	?>
		<li>
		  <div class="second">
			<span><?php echo $secondMenuLabel; ?></span>
				
		  </div>
		  <ul class="this">
		<?php
		//遍历三级菜单
			foreach($secondMenu['items'] as $thirdMenu)
			{
				$thirdMenuLabel = $thirdMenu['label'];
				$thirdMenuUrl = $thirdMenu['url'];
				$thirdFormName = $secondFormName.'['.$thirdMenuUrl.']';
		?>
				<li>
					<div class="lists">
					  <span class="names"><?php echo $thirdMenuLabel; ?></span>
					  <input type="text "value="管理权限">
					  <span class="xiala">下拉</span>
					  <div class="listss">
						<span>管理权限</span>
						<span>只读权限</span>
						<span>无访问权限</span>
					  </div>
					  <div style="clear:both"></div>
					</div>
					
				</li>
		<?php }?>
		<div style="clear:both"></div>
		</ul>		
		</li>
		
	<?php }?>
	
	</ul>
    <div style="clear:both"></div>		
	</li>
	
<?php }?>
  
  </ul>
</ul>
<input type="submit" class="sbmit" value="提交"/>
</div>



</form>
</body>
</html>
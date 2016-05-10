<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/highcharts.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="sitelogin">

<?php		

	echo CHtml::openTag('div',array('id'=>'tagshow'));
		echo '<p><span></span>表格文件类型 ：Microsoft Office Excel 2003/Microsoft Office Excel 2007</p>';
		// echo '<p><span></span>覆盖数据 ：此操作将会清空原有数据</p>';
		// echo '<p><span></span>插入数据 ：此操作将在原有数据的基础上插入数据</p>';
		echo "<p><span></span>表格内容 ：<a href='<?php echo 'index.php?r=site/download&fn='.json_encode('example.xls');?>' style='color:blue'>点击下载范本文档</a></p>";
		echo '<table class="items">
			<thead>
				<tr>
					<th>终端品牌</th>
					<th>终端型号</th>
					<th>终端网络能力</th>	
				</tr>
			</thead>
			<tbody>
			<tr>
					<td>HTC</td>
					<td>HTC Incredible S</td>
					<td>G/T</td>
				</tr>
			</tbody>
		</table>';
		echo CHtml::closeTag('div');
		if(isset($_GET['error']))
			echo "<div class='errorSummary'>此次添加结果：".$_GET['error']."</div>";
?>
<form method="post" action="index.php?r=sysmanage/modelbrandupdate" enctype="multipart/form-data"><input class='filestyle' type='file' name="modelexcel"/><input type="submit" value="提交"/></form> 
	</body>
	</html>
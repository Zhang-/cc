<?php 
	if($select=='usercreate'){
?>
	<ul class="table_menu">
		<li class="select">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">添加系统用户</a>
		<p class="p2"></p>
		</li>

		
	</ul>
	

	<div class="form_modify hellos" >
		<?php echo $this->renderPartial('_formuser', array('model'=>$model)); ?>
	</div>
	
<?php }else{ ?>

	<ul class="table_menu">
		<li class="select">
		<p class="p1"></p>
		<a href="<?php echo $_SERVER["REQUEST_URI"];?>">新建用户详情</a>
		<p class="p2"></p>
		</li>	
	</ul>
	
<?php 
require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
	
$this->widget('zii.widgets.CDetailView', 
	array(
		'data'=>$model,
		'attributes'=>
			array(
				'username',
				array(
					'name'=>'itemname',
					'value'=>getItemname($model->itemname),
				),
				'province',
				'city',
				'address',
				'email',
				'phone',
				'regDateTime',
			),
		)
	); 
}
?>


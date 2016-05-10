<?php
$this->breadcrumbs=array(
	'系统配置'=>array('config/admin'),
	'查看配置：'.$model->tagname,
);
?>
<div class="grid_sbox">
<h1>查看配置：<?php echo $model->tagname; ?></h1>
</div>

<div class="grid_box">
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'tagname',
		'tagvalue',
		'tagdes',
	),
)); ?>
</div>

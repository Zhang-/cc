<?php
$this->breadcrumbs=array(
	'Manage Lists'=>array('index'),
	$model->userid,
);

$this->menu=array(
	array('label'=>'List ManageList', 'url'=>array('index')),
	array('label'=>'Create ManageList', 'url'=>array('create')),
	array('label'=>'Update ManageList', 'url'=>array('update', 'id'=>$model->userid)),
	array('label'=>'Delete ManageList', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->userid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ManageList', 'url'=>array('admin')),
);
?>

<h1>View ManageList #<?php echo $model->userid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'userid',
		'username',
		'password',
		'salt',
		'avatar',
		'province',
		'city',
		'address',
		'email',
		'phone',
		'regDateTime',
		'itemname',
		'expansion',
		'bizrule',
		'data',
	),
)); ?>

<?php
$this->breadcrumbs=array(
	'Manage Lists'=>array('index'),
	$model->userid=>array('view','id'=>$model->userid),
	'Update',
);

$this->menu=array(
	array('label'=>'List ManageList', 'url'=>array('index')),
	array('label'=>'Create ManageList', 'url'=>array('create')),
	array('label'=>'View ManageList', 'url'=>array('view', 'id'=>$model->userid)),
	array('label'=>'Manage ManageList', 'url'=>array('admin')),
);
?>

<h1>Update ManageList <?php echo $model->userid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
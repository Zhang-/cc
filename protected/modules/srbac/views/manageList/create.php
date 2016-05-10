<?php
$this->breadcrumbs=array(
	'Manage Lists'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ManageList', 'url'=>array('index')),
	array('label'=>'Manage ManageList', 'url'=>array('admin')),
);
?>

<h1>Create ManageList</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
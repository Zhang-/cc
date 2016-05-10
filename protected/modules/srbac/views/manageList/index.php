<?php
$this->breadcrumbs=array(
	'Manage Lists',
);

$this->menu=array(
	array('label'=>'Create ManageList', 'url'=>array('create')),
	array('label'=>'Manage ManageList', 'url'=>array('admin')),
);
?>

<h1>Manage Lists</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>

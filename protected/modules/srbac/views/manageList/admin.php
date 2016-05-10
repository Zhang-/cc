	<?php
	$this->breadcrumbs=array(
		'系统管理'=>array('index'),
		'管理员列表',
	);

	$this->menu=array(
		array('label'=>'管理员列表', 'url'=>array('index')),
		array('label'=>'创建管理员', 'url'=>array('create')),
	);

	Yii::app()->clientScript->registerScript('search', "
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('manage-list-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	");
	?>

	<h1>管理员列表</h1>


	<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
	<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search',array(
		'model'=>$model,
	)); ?>
	</div><!-- search-form -->

	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'manage-list-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'userid',
			'username',
			'city',
			'address',
			'email',
			'phone',
			'regDateTime',
			'itemname',
			
			array(
				'class'=>'CButtonColumn',
			),
		),
	)); ?>

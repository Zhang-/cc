<?php
$this->breadcrumbs=array(
	'系统配置'
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('config-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="grid_sbox"><h1>系统配置</h1></div>

<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<div class="grid_box">

<?php
$myroles = Yii::app()->user->getState('myroles');
$vv = in_array('configview',$myroles)?'{view}':'';
$uv = in_array('configupdate',$myroles)?'{update}':'';
$dv = in_array('configdelete',$myroles)?'{delete}':'';
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'config-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		'id',
		'tagname',
		'tagvalue',
		'tagdes',
		array(
			'class'=>'CButtonColumn',
			//'viewButtonOptions'=>array('target'=>'_blank'),
			'template'=>"$vv $uv $dv",
			'header'=>'操作',
			'viewButtonLabel'=>'查看详情',
			'viewButtonImageUrl'=>'assets/ico/viewxq.png',
			'updateButtonLabel'=>'修改信息',
			'updateButtonImageUrl'=>'assets/ico/updatexg.png',
			'deleteButtonLabel'=>'删除记录',
			'deleteButtonImageUrl'=>'assets/ico/deletesc.png',
		),
	),
	'summaryText'=>'第 {start}-{end} 条, 共 {count} 条 当前第 {page} 页，共 {pages} 页',
	'pager'=>array(
		'class'=>'CLinkPager',
		'header'=>'',
		'firstPageLabel'=>'首页',
		'prevPageLabel'=>'上一页',
		'nextPageLabel'=>'下一页',
		'lastPageLabel'=>'尾页',
	),
)); ?>
</div>
	<ul class="table_menu">
	<li class="select">添加终端型号成功</li>
	</ul>
	<div class="form_modify" style="margin:10px 0 0 0;border:1px solid #ddd">
<?php
function getterminalnum($model)
		{
			$count=Yii::app()->db->createCommand("select count(*) from static_information where phoneModel='".$model."'")->queryrow();
			return $count['count(*)'];
		}
		$_model=new ModelNettype;
		$model=$_model->findByPk($_GET['id']);
	$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'phoneBrand',
		'phoneModel',
		'netType',
		array('name'=>'数量','value'=>getterminalnum($model->phoneModel)),
	),
)); ?>
</div>
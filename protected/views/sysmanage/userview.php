<?php
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //���뱾ҳ�淽��
?>
<?php 
$this->widget('zii.widgets.CDetailView', 
	array(
		'data'=>$model,
		'attributes'=>
			array(
				'userid',
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
?>
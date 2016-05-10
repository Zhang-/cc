<ul class="table_menu">
	<li class="select">
	<p class="p1"></p>
	<a href="<?php echo Yii::app()->createUrl($this->route);?>">订单列表</a>
	<p class="p2"></p>
	</li>
</ul>

<div class="main_table">
	<div class="table_search">
		<?php 
			$this->renderPartial('_search',
				array(
					'model'=>$model,
				)
			); 
		?>
	</div>

	<div class="table_order">
		<ul class="order">
			<li><span id="o_status">订单状态</span></li>
			<li><span id="o_time">订单时间</span></li>
		</ul>
		
		<div class="clear"></div>

	</div>
	<!-- 加载提示 -->
		<div id="loader_container1"  style="display:none">
			<div>
				<img src="images/loading2.gif" />
			</div>
		</div>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'place-order-info-grid',
		'dataProvider'=>$model->search(),
		'cssFile'=>false,
		//'filter'=>$model,
		'columns'=>array(
			//'id',
			'tid',
			'did',
			'start',
			'startname',
			'destination',
			//'o_type',
			'o_status',
			'o_time',
			/*
			'desname',
			'o_voice',
			*/
			array(
				'class'=>'CButtonColumn',
			),
		),
	)); ?>
</div>
 
<div id="getUserViewLogPage" title="日志详情">
	<iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="viewPageData" class="UserViewPage"></iframe>
</div>
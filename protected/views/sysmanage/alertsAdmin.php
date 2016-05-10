
<ul class="table_menu">
	<li class="<?php if($md=='aa') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/alertsAdmin');?>">系统提醒</a><p class="p2"></p></li>
	<li class="<?php if($md=='ac' || $md =='av') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/alertsAdmin',array('md'=>'ac'));?>">添加系统提醒子项</a><p class="p2"></p></li>
</ul>


<?php 
if($md == 'aa'){
require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
?>

<div class="main_table">
	<div class="table_search">
		<?php 
			$this->renderPartial('_searchAlerts',
				array(
					'model'=>$model,
				)
			); 
		?>
	</div>
  
		<div class="table_order">
			<ul class="order">
				<li><span id="logsuserrole">关键词</span></li>
				<li><span id="actiontype">系统提醒名称</span></li>
				<li><span id="actiontime">系统提醒标签</span></li>
			</ul>
			
			<div class="clear"></div>
	
		</div>
		<!-- 加载提示 -->
		<div id="loader_container1"  style="display:none">
			<div>
				<img src="images/loading2.gif" />
			</div>
		</div>


<?php 
	$this->widget('zii.widgets.grid.CGridView', 
		array(
			'id'=>'system-alerts-grid',
			'dataProvider'=>$model->search(),
			'cssFile'=>false,
			'beforeAjaxUpdate'=>'function(id,data){
				$("#loader_container1").css("display","block");
				}',
		    'afterAjaxUpdate'=>'function(id,data){
		    	$("#loader_container1").css("display","none");
				}',
			'columns'=>
				array(
					array(
						'name'=>'
							<div class="checks">
								<input class="all" type="checkbox" id="allcheckbtn" value="" />
								<span title="批量操作" class="option" id="alertwindows"></span>
								<div class="allselect">
									<div class="headp">勾选项批量操作</div>
									<p class="p2" onclick="javascript:delcheckalerts();"><span>全部删除</span></p>
									<p class="p2" onclick="javascript:cancelcheck();">取消</p>
								</div>
							</div>',
						'htmlOptions'=>array('class'=>'first_td'),
						'value'=>'getButtonSSAA($data->id)',
					),
					array(
						'name'=>'alerts_key',
						'value'=>'$data->alerts_key',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('alerts_key').'排序'),
					),
					array(
						'name'=>'alerts_name',
						'value'=>'$data->alerts_name',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('alerts_name').'排序'),
					),
					array(
						'name'=>'alerts_tag',
						'value'=>'$data->alerts_tag',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('alerts_tag').'排序'),
					),
					array(
						'name'=>'alerts_contents',
						'value'=>'$data->alerts_contents',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('alerts_contents').'排序'),
					)
				),
			
			'summaryText'=>'第 {start}-{end} 条, 共 {count} 条 当前第 {page} 页，共 {pages} 页',
			'pager'=>
				array(
					'class'=>'CLinkPager',
					'header'=>'',
					'firstPageLabel'=>'首页',
					'prevPageLabel'=>'上一页',
					'nextPageLabel'=>'下一页',
					'lastPageLabel'=>'尾页',
					'cssFile'=>false,
				),
			'template'=>'{summary}{items}{pager}'
		)
	); 
?>

</div>
	 
<div id="alertViewPage" title="系统提醒详情">
	<iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="alertViewFrame" class="UserViewPage"></iframe>
</div>

<div id="alertUpdatePage" title="系统提醒更改">
	<iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="alertUpdateFrame" class="UserViewPage"></iframe>
</div>

<?php }elseif ($md=='ac') { ?>
	<div class="form_modify toolong">
		<?php echo $this->renderPartial('_formAlerts', array('model'=>$model)); ?>
	</div>
<?php }elseif ($md=='av') { 
	$this->renderPartial('alertsView',
		array(
			'model'=>$model,
		)
	); 
} ?>
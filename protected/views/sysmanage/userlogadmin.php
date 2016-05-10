

<?php
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
?>

	<ul class="table_menu">
		<li class="select">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">用户日志</a>
		<p class="p2"></p>
</li>
	</ul>
	
	<div class="main_table">
		<div class="table_search">
			<?php 
				$this->renderPartial('_searchuserlog',
					array(
						'model'=>$model,
					)
				); 
			?>
		</div>
  
		<div class="table_order">
			<ul class="order">
				<li><span id="logsuserrole">用户角色</span></li>
				<li><span id="actiontype">操作类型</span></li>
				<li><span id="actiontime">记录时间</span></li>
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
			'id'=>'view-action-logs-grid',
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
									<p class="p2" onclick="javascript:delcheckuserlog();"><span>全部删除</span></p>
									<p class="p2" onclick="javascript:cancelcheck();">取消</p>
								</div>
							</div>',
						'htmlOptions'=>array('class'=>'first_td'),
						'value'=>'getButtonSULA($data->id)',
						'visible'=>getChecksAvailableSULA(),
					),
					array(
						'name'=>'',
						'value'=>'getViewButtonSULA($data->id)',
						'headerHtmlOptions'=>array('class'=>'first_td'),
						'visible'=>getChecksViewAvailableSULA(),
					),
					array(
						'name'=>'username',
						'value'=>'$data->username',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('username').'排序'),
					),
					array(
						'name'=>'userip',
						'value'=>'$data->userip',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('userip').'排序'),
					),
					array(
						'name'=>'userrole',
						'value'=>'getItemname($data->userrole)',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('userrole').'排序'),
					),
					array(
						'name'=>'actiontype',
						'value'=>'getLogTypeTrans($data->actiontype)',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('actiontype').'排序'),
					),
					array(
						'name'=>'actiontime',
						'value'=>'$data->actiontime',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('actiontime').'排序'),
					),
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
	 
	<div id="getUserViewLogPage" title="日志详情">
		<iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="viewPageData" class="UserViewPage"></iframe>
	</div>

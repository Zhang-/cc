
<?php
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
?>

	<ul class="table_menu">
		<li class="select">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">系统用户管理</a>
		<p class="p2"></p>
		</li>
	</ul>
	
	<div class="main_table">
		<div class="table_search">
			<?php 
				$this->renderPartial('_searchManage',
					array(
						'model'=>$model,
					)
				); 
			?>
		</div>
		
		<div class="table_order">
			<ul class="order">
				<li><span id="username">用户名</span></li>
				<li><span id="regDateTime">注册时间</span></li>
				<li><span id="itemname">用户组</span></li>
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
			'id'=>'manage-list-grid',
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
								<input id="allcheckbtn" class="all" type="checkbox" />
								<span title="批量操作" class="option" id="alertwindows"></span>
								<div class="allselect" >
									<div class="headp">勾选项批量操作</div>
									<p class="p2" onclick="javascript:delcheckuser('.$thisUserID.');"><span>全部删除</span></p>
									<p class="p2" onclick="javascript:cancelcheck();">取消</p>
								</div>
							</div>',
						'htmlOptions'=>array('class'=>'first_td'),
						'value'=>'getButtonSU($data->userid,"'.$thisUserID.'")',
						'visible'=>getChecksAvailableSU(),
					),
					array(
						'name'=>'',
						'value'=>'getViewButtonSU($data->userid)',
						'headerHtmlOptions'=>array('class'=>'first_td'),
						'visible'=>getChecksViewAvailableSU(),
					),
					array(
						'name'=>'username',
						'value'=>'$data->username',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('username').'排序'),
					),
					array(
						'name'=>'itemname',
						'value'=>'getItemname($data->itemname)',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('itemname').'排序'),
					),
					
					array(
						'name'=>'province',
						'value'=>'$data->province',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('province').'排序'),
					),
					array(
						'name'=>'city',
						'value'=>'$data->city',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('city').'排序'),
					),
					array(
						'name'=>'phone',
						'value'=>'$data->phone',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('phone').'排序'),
					),
					array(
						'name'=>'email',
						'value'=>'$data->email',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('email').'排序'),
					),
					array(
						'name'=>'regDateTime',
						'value'=>'$data->regDateTime',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('regDateTime').'排序'),
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
				'cssFile'=>false,
			),
			'template'=>'{summary}{items}{pager}'
		)
	); 
?>
	</div>

	<div id="getUserViewPage" title="用户详情">
	  <iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="viewPageData" class="UserViewPage UserViewPage"></iframe>
	</div>
	
	<div id="userUpdatePage" title="修改信息">
	  <iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" class="dataview UserViewPage"></iframe>
	</div>

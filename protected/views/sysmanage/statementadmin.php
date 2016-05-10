
<?php 
if($md == 'sa'){
?>

<ul class="table_menu">
	<li class="<?php if($md=='sa') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/statementadmin');?>">用户投诉口径管理</a><p class="p2"></p></li>
	<li class="<?php if($md=='su'||$md=='sv') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/statementadmin',array('md'=>'su'));?>">添加口径信息</a><p class="p2"></p></li>
</ul>
<?php
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
?>

<div class="main_table">
		<div class="table_search">
			<?php 
				$this->renderPartial('_searchStatement',
					array(
						'model'=>$model,
					)
				); 
			?>
		</div>
		 <div class="table_order">
		<ul class="order">
				<li><span id="statementType">口径类型</span></li>
				<li><span id="affectArea">影响区(县)</span></li>
				<li><span id="projectStatus">项目目前状态</span></li>
		</ul>
		
    
		<div class="clear"></div>
  </div>
		
<?php 

$this->widget('zii.widgets.grid.CGridView', 
		array(
			'id'=>'complain-statement-list-grid',
			'dataProvider'=>$model->search(),
			'cssFile'=>false,
			'columns'=>
				array(
					array(
						'name'=>'
							<div class="checks">
								<input class="all" type="checkbox" id="allcheckbtn" value="" />
								<span title="批量操作" class="option" id="alertwindows"></span>
								<div class="allselect">
									<div class="headp">勾选项批量操作</div>
									<p class="p2" onclick="javascript:delcheckstatement();"><span>全部删除</span></p>
									<p class="p2" onclick="javascript:cancelcheck();">取消</p>
								</div>
							</div>',
						'htmlOptions'=>array('class'=>'first_td'),
						'value'=>'getButtonSISA($data->id)',
						'visible'=>getChecksAvailableSISA(),
					),
					array(
						'name'=>'',
						'value'=>'getViewButtonSISA($data->id)',
						'headerHtmlOptions'=>array('class'=>'first_td'),
						'visible'=>getChecksViewAvailableSISA(),
					),
					array(
						'name'=>'state_type',
						'value'=>'$data->state_type',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('state_type').'排序'),
					),
					array(
						'name'=>'state_title',
						'value'=>'getShortCellContent("200",$data->state_title,"CGridView")',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('state_title').'排序'),
					),
					array(
						'name'=>'affect_area',
						'value'=>'$data->affect_area',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('affect_area').'排序'),
					),
					array(
						'name'=>'affect_scope',
						'value'=>'getShortCellContent("200",$data->affect_scope,"CGridView")',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('affect_scope').'排序'),
					),
					array(
						'name'=>'problem',
						'value'=>'getShortCellContent("200",$data->problem,"CGridView")',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('problem').'排序'),
					),
					array(
						'name'=>'project_status',
						'value'=>'getShortCellContent("100",$data->project_status,"CGridView")',
						'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('project_status').'排序'),
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
	
	<div id="getStatementViewPage" title="口径详情">
		<iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="viewPageData" class="UserViewPage"></iframe>
	</div>
	
	<div id="statementUpdate" title="口径信息修改">
	  <iframe src="" marginwidth="0" height="450" width="100%" marginheight="0"  frameborder="0" class="dataview"></iframe>
	</div>
	
	<div id="inputData" title="批量导入口径信息">
		<iframe src="" marginwidth="0" height="400" width="100%" marginheight="0"  frameborder="0" id="datainputview"></iframe>
	</div>
<?php }elseif($md == 'su'){?>
<ul class="table_menu">
	<li class="<?php if($md=='sa') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/statementadmin');?>">用户投诉口径管理</a><p class="p2"></p></li>
	<li class="<?php if($md=='su'||$md=='sv') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/statementadmin',array('md'=>'su'));?>">添加口径信息</a><p class="p2"></p></li>
</ul>
<?php
	$this->renderPartial('inputstatement',
		array(
			'model'=>$model,
			'md'=>$md,
		)
	); 
	?>
	
<?php } ?>

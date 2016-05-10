<?php if($isGIS){ header("Content-type: text/html; charset=utf-8"); ?>
	<title>数据列表详情</title>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.20.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" media="screen, projection" />
<?php } ?>

<script type="text/javascript" src="js/complainlist.js"></script>
<script type="text/javascript" src="js/gis.js"></script>
<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.20.custom.css">
<link rel="stylesheet" href="css/complain.css">

<?php 
if($page=='site'):
?>

<ul class='table_menu'>
	<li
		class="li1 <?php if($md==-1) echo 'select';?>">
	<p class="p1"></p>
	<a
		href="<?php echo  Yii::app()->controller->createUrl('GIS/DataList',array('md'=>-1,'gis'=>$isGIS,'queryid'=>$queryid));?>">全部基站</a>
	<p class="p2"></p>
	</li>

	<li
		class="li1 <?php if($md==0) echo 'select';?>">
	<p class="p1"></p>
	<a
		href="<?php echo  Yii::app()->controller->createUrl('GIS/DataList',array('md'=>0,'gis'=>$isGIS,'queryid'=>$queryid));?>">2G基站</a>
	<p class="p2"></p>
	</li>
	<li
		class="li1 <?php if($md==1) echo 'select';?>">
	<p class="p1"></p>
	<a
		href="<?php echo  Yii::app()->controller->createUrl('GIS/DataList',array('md'=>1,'gis'=>$isGIS,'queryid'=>$queryid));?>">3G基站</a>
	<p class="p2"></p>
	</li>	
	<li
		class="li1 <?php if($md==4) echo 'select';?>">
	<p class="p1"></p>
	<a
		href="<?php echo  Yii::app()->controller->createUrl('GIS/DataList',array('md'=>4,'gis'=>$isGIS,'queryid'=>$queryid));?>">4G基站</a>
	<p class="p2"></p>
	</li>
</ul>

<?php endif;?>

<div class="main_table" style="display:block">	
<div class="table_search">
<?php
//搜索模块
/*if(!$isGIS)
	$this->renderPartial("_psearch",array('str'=>$str,'stp'=>$stp,'md'=>$md,'model'=>$model,'count'=>$count));*/
?>
</div>
<div class="table_order">

	<ul class="order"></ul>
<?php //if(HelpTool::checkActionAccess('complainexprotdata')):?>
	
	<!--
	<div class="exprot_box" style=""> 
	<span class="load_out float_right" title="导出表格" onclick="global_exprot_excel('<?php //echo Yii::app()->createUrl('complainList/exprotProblemList', $_GET)?>','exprot_note',<?php //echo $count;?>,true)"></span>
	<div id="img_box" style="display: none;"></div>
	<span class="float_right" id="exprot_note"></span>
	<div id="adminshow"></div>
	</div>
	<div class="clear"></div>-->
<?php //endif;?>
</div>
<div class="nano">
<?php
if($page=='site')
$tempArray = array(
		array(
		    'name'=>'gridId',
		    'value'=>'$data->gridId',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('gridId').'排序'),
		),
		array(
		    'name'=>'cell_name',
		    'value'=>'$data->cell_name',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cell_name').'排序'),
		),
		array(
		    'name'=>'lac',
		    'value'=>'$data->lac',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('lac').'排序'),
		),
		array(
		    'name'=>'cellId',
		    'value'=>'$data->cellId',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cellId').'排序'),
		),
		array(
		    'name'=>'lng',
		    'value'=>'$data->lng',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('lng').'排序'),
		),
		array(
		    'name'=>'lat',
		    'value'=>'$data->lat',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('lat').'排序'),
		),
		array(
		    'name'=>'angle',
		    'value'=>'$data->angle',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('angle').'排序'),
		),
		array(
		    'name'=>'type',
		    'value'=>'$data->type',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('type').'排序'),
		),
		array(
		    'name'=>'height',
		    'value'=>'$data->height',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('height').'排序'),
		),
		array(
		    'name'=>'dip_e',
		    'value'=>'$data->dip_e',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('dip_e').'排序'),
		),
		array(
		    'name'=>'dip_m',
		    'value'=>'$data->dip_m',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('dip_m').'排序'),
		),
		array(
		    'name'=>'bcch',
		    'value'=>'$data->bcch',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('bcch').'排序'),
		),
/*		 array(
		    'name'=>'操作/查看',
		    'value'=>'getOperation($data->id,$data->status,'.$md.')',
	    )*/
	);
else if($page=='2g')
	$tempArray = array(
		array(
		    'name'=>'cell',
		    'value'=>'$data->cell',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cell').'排序'),
		),
		array(
		    'name'=>'lac',
		    'value'=>'$data->lac',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('lac').'排序'),
		),
		array(
		    'name'=>'cellId',
		    'value'=>'$data->cellId',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cellId').'排序'),
		),
		array(
		    'name'=>'wirelessConnRate',
		    'value'=>'HelpTool::convertPercent($data->wirelessConnRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('wirelessConnRate').'排序'),
		),
		array(
		    'name'=>'dropRate',
		    'value'=>'HelpTool::convertPercent($data->dropRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('dropRate').'排序'),
		),
		array(
		    'name'=>'TCHcongestionRate',
		    'value'=>'HelpTool::convertPercent($data->TCHcongestionRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('TCHcongestionRate').'排序'),
		),
		array(
		    'name'=>'updatetime',
		    'value'=>'$data->updatetime',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('updatetime').'排序'),
		)
	);
else if($page=='3g')
	$tempArray = array(
		array(
		    'name'=>'cell',
		    'value'=>'$data->cell',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cell').'排序'),
		),
		array(
		    'name'=>'lac',
		    'value'=>'$data->lac',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('lac').'排序'),
		),
		array(
		    'name'=>'cellId',
		    'value'=>'$data->cellId',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cellId').'排序'),
		),
		array(
		    'name'=>'PSconnRate',
		    'value'=>'HelpTool::convertPercent($data->PSconnRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('PSconnRate').'排序'),
		),
		array(
		    'name'=>'CSconnRate',
		    'value'=>'HelpTool::convertPercent($data->CSconnRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('CSconnRate').'排序'),
		),
		array(
		    'name'=>'PSdropRate',
		    'value'=>'HelpTool::convertPercent($data->PSdropRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('PSdropRate').'排序'),
		),
		array(
		    'name'=>'CSdropRate',
		    'value'=>'HelpTool::convertPercent($data->CSdropRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('CSdropRate').'排序'),
		),
		array(
		    'name'=>'RRCcongestionRate',
		    'value'=>'HelpTool::convertPercent($data->RRCcongestionRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('RRCcongestionRate').'排序'),
		),
		array(
		    'name'=>'updatetime',
		    'value'=>'$data->updatetime',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('updatetime').'排序'),
		)
	);
else if($page=='4g')
	$tempArray = array(
		array(
		    'name'=>'CellName',
		    'value'=>'$data->CellName',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('CellName').'排序'),
		),
		array(
		    'name'=>'tac',
		    'value'=>'$data->tac',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('tac').'排序'),
		),
		array(
		    'name'=>'lac',
		    'value'=>'$data->lac',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('lac').'排序'),
		),
		array(
		    'name'=>'cellId',
		    'value'=>'$data->cellId',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('cellId').'排序'),
		),
		array(
		    'name'=>'wirelessConnRate',
		    'value'=>'HelpTool::convertPercent($data->wirelessConnRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('wirelessConnRate').'排序'),
		),
		array(
		    'name'=>'ERABsuccessRate',
		    'value'=>'HelpTool::convertPercent($data->ERABsuccessRate)',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('ERABsuccessRate').'排序'),
		),
		array(
		    'name'=>'updatetime',
		    'value'=>'$data->updatetime',
		    'headerHtmlOptions'=>array('title'=>'点击按'.$model->getAttributeLabel('updatetime').'排序'),
		)
	);
 $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'complain-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table_content complainlist',
	'cssFile'=>false,
	 'beforeAjaxUpdate'=>'function(id,data){
			$("#loader_container1").css("display","block");
		}',
	    'afterAjaxUpdate'=>'function(id,data){
	    	$("#loader_container1").css("display","none");
		}',
	'columns'=>$tempArray,
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
)); 

/*function getOperation($id,$status,$md){
	$flag = HelpTool::checkActionAccess('complainlistchangestatus');
	$string = "";
	if($md == -1){
		if($status == 0)
			$string = "<span style='color:red'>&nbsp;&nbsp;未处理&nbsp;/&nbsp;</span>";
		elseif ($status == 1)
			$string = "<span>&nbsp;&nbsp;已处理&nbsp;/&nbsp;</span>";
		elseif ($status == 2)
			$string = "<span style='color:green'>无需处理&nbsp;/&nbsp;</span>";
		elseif ($status == 3)
			$string = "<span style='color:orange'>延期处理&nbsp;/&nbsp;</span>";		
	}	
		
	if($status == 0 || $status == 3){
		if($flag)
			$string .= "<span  class='pointer' onclick='showDetail(".$id.")' title='处理投诉' >处理投诉</span>";
		else
			$string .= "<span  class='pointer' onclick='showDetail(".$id.")' title='查看详情' >查看详情</span>";
	}else{
		$string .= "<span  class='pointer' onclick='showDetail(".$id.")' title='查看详情' >查看详情</span>";
	}
	
	echo $string;
}*/

?>
</div>
</div>

<!--<div style="display:none;" id="detail_complain" class="alert_bg" title="投诉详情">
	<div id="detail_client" style="padding:10px 0 0 0" class="form_modify"><div class="dialog_loading"><img src="images/loading1.gif"/></div></div>    
</div>-->
